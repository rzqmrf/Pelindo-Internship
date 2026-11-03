@extends('layouts.app')

@section('page_title', 'Kelola Divisi Pelindo')

@section('content')
<div class="space-y-6" x-data="{ addModalOpen: false, editModalOpen: false, activeDivision: null }">

    <!-- Centralized Page Header -->
    <x-page-header
        title="Manajemen Divisi & Kuota Magang"
        subtitle="Kelola daftar divisi, kuota peserta magang, dan kepala divisi di PT Pelabuhan Indonesia."
        icon="fa-solid fa-sitemap">
        <x-slot:action>
            <x-button type="button" @click="addModalOpen = true" variant="primary" icon="fa-solid fa-plus">
                Tambah Divisi Baru
            </x-button>
        </x-slot:action>
    </x-page-header>

    <!-- 4 KPI Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <x-stat-card
            title="Total Divisi"
            value="{{ $totalDivisions }}"
            trend="Divisi Aktif"
            trend-type="up"
            icon="fa-solid fa-building"
            color="blue" />

        <x-stat-card
            title="Total Kuota"
            value="{{ $totalQuota }}"
            trend="Seluruh Divisi"
            trend-type="up"
            icon="fa-solid fa-users"
            color="emerald" />

        <x-stat-card
            title="Kuota Terisi"
            value="{{ $filledQuota }}"
            trend="{{ $pctFilled }}% terisi"
            trend-type="up"
            icon="fa-solid fa-user-check"
            color="purple" />

        <x-stat-card
            title="Sisa Kuota"
            value="{{ $remainingQuota }}"
            trend="{{ $pctRemaining }}% tersedia"
            trend-type="down"
            icon="fa-solid fa-chart-pie"
            color="amber" />
    </div>

    <!-- Search Bar wrapped in Card -->
    <x-card>
        <form method="GET" action="{{ route('admin.divisions.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
            <!-- Search input -->
            <div class="md:col-span-3">
                <x-form-input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nama divisi, kode, atau kepala divisi..."
                    icon="fa-solid fa-magnifying-glass" />
            </div>

            <!-- Buttons -->
            <div class="flex items-center gap-3">
                <x-button type="submit" variant="primary" class="flex-1 justify-center" icon="fa-solid fa-magnifying-glass">
                    Cari
                </x-button>
                <x-button href="{{ route('admin.divisions.index') }}" variant="outline" icon="fa-solid fa-rotate">
                    Reset
                </x-button>
            </div>
        </form>
    </x-card>

    <!-- Divisions Table -->
    <x-card padding="p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100 text-xs font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-4 px-6">Kode & Nama Divisi</th>
                        <th class="py-4 px-4">Kepala Divisi</th>
                        <th class="py-4 px-4">Kuota Magang</th>
                        <th class="py-4 px-4">Pembimbing</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm font-semibold text-slate-700">
                    @forelse($divisions as $div)
                    @php
                    $quotaPercentage = $div->quota > 0 ? min(100, round(($div->interns_count / $div->quota) * 100)) : 0;

                    $shortCode = match($div->code) {
                        'IT-DIGITAL' => 'ITD',
                        'HR-GA' => 'SDM',
                        'OPS-LOG' => 'OPS',
                        'FIN-TREASURY' => 'FIN',
                        'LEGAL-CORP' => 'LEG',
                        'COMM-DEV' => 'COM',
                        default => substr($div->code, 0, 3)
                    };

                    $badgeClass = match($shortCode) {
                        'ITD' => 'bg-blue-50 text-[#005B9C] border border-blue-100',
                        'SDM' => 'bg-emerald-50 text-emerald-600 border border-emerald-100',
                        'OPS' => 'bg-amber-50 text-amber-600 border border-amber-100',
                        'FIN' => 'bg-purple-50 text-purple-600 border border-purple-100',
                        'LEG' => 'bg-cyan-50 text-cyan-600 border border-cyan-100',
                        default => 'bg-pink-50 text-pink-600 border border-pink-100',
                    };

                    $spvBadgeClass = match($shortCode) {
                        'ITD' => 'bg-blue-50 text-[#005B9C]',
                        'SDM' => 'bg-emerald-50 text-emerald-600',
                        'OPS' => 'bg-amber-50 text-amber-600',
                        default => 'bg-slate-100 text-slate-700',
                    };
                    @endphp
                    <tr class="hover:bg-slate-50/60 transition">
                        <!-- Column 1: Kode & Nama Divisi -->
                        <td class="py-4 px-6">
                            <div class="flex items-center space-x-3.5">
                                <span class="w-10 h-10 rounded-2xl flex items-center justify-center font-black text-xs shrink-0 {{ $badgeClass }}">
                                    {{ $shortCode }}
                                </span>
                                <div>
                                    <p class="font-extrabold text-slate-900 text-sm leading-snug">{{ $div->name }}</p>
                                    <p class="text-xs text-slate-500 font-medium line-clamp-1 max-w-sm mt-0.5">{{ $div->description ?? 'Tidak ada deskripsi.' }}</p>
                                </div>
                            </div>
                        </td>

                        <!-- Column 2: Kepala Divisi -->
                        <td class="py-4 px-4">
                            <p class="font-extrabold text-slate-900 leading-snug">{{ $div->head_name ?? '-' }}</p>
                            <p class="text-xs text-slate-400 font-medium uppercase tracking-wider mt-0.5">Manager</p>
                        </td>

                        <!-- Column 3: Kuota Magang -->
                        <td class="py-4 px-4">
                            <div class="w-40 space-y-1">
                                <div class="flex justify-between text-xs font-bold text-slate-800 leading-none">
                                    <span>{{ $div->interns_count }}/{{ $div->quota }} Peserta</span>
                                    <span>{{ $quotaPercentage }}%</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                    <div class="h-2 bg-[#005B9C] rounded-full" style="width: {{ $quotaPercentage }}%"></div>
                                </div>
                            </div>
                        </td>

                        <!-- Column 4: Pembimbing -->
                        <td class="py-4 px-4">
                            <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold {{ $spvBadgeClass }}">
                                {{ $div->supervisors_count }} Pembimbing
                            </span>
                        </td>

                        <!-- Column 5: Aksi -->
                        <td class="py-4 px-6 text-right space-x-1.5 whitespace-nowrap">
                            <x-button
                                type="button"
                                @click="activeDivision = {{ json_encode($div) }}; editModalOpen = true"
                                variant="secondary"
                                size="sm"
                                icon="fa-solid fa-pen-to-square"
                                title="Edit Data" />

                            <form action="{{ route('admin.divisions.destroy', $div->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus divisi ini?')">
                                @csrf
                                @method('DELETE')
                                <x-button
                                    type="submit"
                                    variant="danger"
                                    size="sm"
                                    icon="fa-solid fa-trash-can"
                                    title="Hapus Data" />
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-8">
                            <x-empty-state
                                title="Tidak ada divisi ditemukan"
                                subtitle="Coba ubah kata kunci pencarian divisi Anda."
                                icon="fa-solid fa-sitemap" />
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($divisions->hasPages())
        <div class="p-5 border-t border-slate-100 bg-slate-50/50 rounded-b-3xl">
            {{ $divisions->links() }}
        </div>
        @endif
    </x-card>

    <!-- Modal Tambah Divisi -->
    <div x-show="addModalOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4" x-cloak>
        <div @click.outside="addModalOpen = false" class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-5 border border-slate-100">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <h3 class="text-base font-extrabold text-slate-900">Tambah Divisi Baru</h3>
                <button @click="addModalOpen = false" class="text-slate-400 hover:text-slate-600 transition"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <form action="{{ route('admin.divisions.store') }}" method="POST" class="space-y-4 text-sm font-semibold">
                @csrf

                <x-form-input type="text" name="code" label="Kode Divisi *" placeholder="Contoh: IT-DIGITAL" required class="uppercase" />
                <x-form-input type="text" name="name" label="Nama Divisi *" placeholder="Contoh: Divisi Teknologi Informasi" required />
                <x-form-input type="text" name="head_name" label="Kepala Divisi" placeholder="Contoh: Ir. Budi Santoso, M.T." />
                <x-form-input type="number" name="quota" label="Kuota Maksimal Peserta Magang *" value="10" min="1" required />
                <x-form-textarea name="description" label="Deskripsi Divisi" rows="3" />

                <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
                    <x-button type="button" @click="addModalOpen = false" variant="ghost" size="sm">Batal</x-button>
                    <x-button type="submit" variant="primary" size="sm">Simpan Divisi</x-button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Divisi -->
    <div x-show="editModalOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4" x-cloak>
        <template x-if="activeDivision">
            <div @click.outside="editModalOpen = false" class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-5 border border-slate-100">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <h3 class="text-base font-extrabold text-slate-900">Edit Data Divisi</h3>
                    <button @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600 transition"><i class="fa-solid fa-xmark text-lg"></i></button>
                </div>

                <form :action="'{{ url('/admin/divisions') }}/' + activeDivision.id" method="POST" class="space-y-4 text-sm font-semibold">
                    @csrf
                    @method('PUT')

                    <x-form-input type="text" name="code" label="Kode Divisi *" x-model="activeDivision.code" required class="uppercase" />
                    <x-form-input type="text" name="name" label="Nama Divisi *" x-model="activeDivision.name" required />
                    <x-form-input type="text" name="head_name" label="Kepala Divisi" x-model="activeDivision.head_name" />
                    <x-form-input type="number" name="quota" label="Kuota Maksimal *" x-model="activeDivision.quota" min="1" required />
                    <x-form-textarea name="description" label="Deskripsi Divisi" rows="3" x-model="activeDivision.description" />

                    <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
                        <x-button type="button" @click="editModalOpen = false" variant="ghost" size="sm">Batal</x-button>
                        <x-button type="submit" variant="primary" size="sm">Update Divisi</x-button>
                    </div>
                </form>
            </div>
        </template>
    </div>

</div>
@endsection