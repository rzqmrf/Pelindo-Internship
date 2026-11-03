@extends('layouts.app')

@section('page_title', 'Kelola Peserta Magang')

@section('content')
<div class="space-y-6">

    <!-- Centralized Page Header -->
    <x-page-header
        title="Manajemen Peserta Magang"
        subtitle="Kelola data peserta magang, pembagian divisi, pembimbing, dan durasi magang di Pelindo."
        icon="fa-solid fa-users"
        action-route="{{ route('admin.interns.create') }}"
        action-label="Tambah Peserta Baru"
        action-icon="fa-solid fa-user-plus" />

    <!-- 4 KPI Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <x-stat-card
            title="Total Peserta Aktif"
            value="{{ $activeInternsCount }}"
            trend="12% dari bulan lalu"
            trend-type="up"
            icon="fa-solid fa-user-group"
            color="blue" />

        <x-stat-card
            title="Pembimbing Lapangan"
            value="{{ $activeSupervisorsCount }}"
            trend="2% dari bulan lalu"
            trend-type="up"
            icon="fa-solid fa-graduation-cap"
            color="purple" />

        <x-stat-card
            title="Menunggu Approval"
            value="{{ $pendingReportsCount }}"
            trend="1% dari bulan lalu"
            trend-type="down"
            icon="fa-solid fa-clock-rotate-left"
            color="amber" />

        <x-stat-card
            title="Laporan Disetujui"
            value="{{ $completedReportsCount }}"
            trend="15% dari bulan lalu"
            trend-type="up"
            icon="fa-solid fa-circle-check"
            color="emerald" />
    </div>

    <!-- Filter & Search Bar wrapped in Card -->
    <x-card>
        <form method="GET" action="{{ route('admin.interns.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-center">
            <!-- Search -->
            <x-form-input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari nama, NIM, instansi..."
                icon="fa-solid fa-magnifying-glass" />

            <!-- Division Filter -->
            <x-form-select name="division_id" icon="fa-solid fa-building">
                <option value="">Semua Divisi</option>
                @foreach($divisions as $div)
                <option value="{{ $div->id }}" {{ request('division_id') == $div->id ? 'selected' : '' }}>{{ str_replace('Divisi ', '', $div->name) }}</option>
                @endforeach
            </x-form-select>

            <!-- Status Filter -->
            <x-form-select name="status" icon="fa-solid fa-[#005B9C]">
                <option value="">Semua Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai Magang</option>
                <option value="extended" {{ request('status') == 'extended' ? 'selected' : '' }}>Diperpanjang</option>
                <option value="terminated" {{ request('status') == 'terminated' ? 'selected' : '' }}>Nonaktif / Keluar</option>
            </x-form-select>

            <!-- Action Buttons -->
            <div class="flex items-center gap-3">
                <x-button type="submit" variant="primary" class="flex-1 justify-center" icon="fa-solid fa-filter">
                    Filter
                </x-button>
                <x-button href="{{ route('admin.interns.index') }}" variant="outline" icon="fa-solid fa-rotate">
                    Reset
                </x-button>
            </div>
        </form>
    </x-card>

    <!-- Interns Table -->
    <x-card padding="p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100 text-xs font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-4 px-6">Peserta & Kontak</th>
                        <th class="py-4 px-4">NIM & Instansi</th>
                        <th class="py-4 px-4">Divisi & Pembimbing</th>
                        <th class="py-4 px-4">Periode Magang</th>
                        <th class="py-4 px-4">Status</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm font-semibold text-slate-700">
                    @forelse($interns as $profile)
                    <tr class="hover:bg-slate-50/60 transition">
                        <!-- Column 1: Peserta & Kontak -->
                        <td class="py-4 px-6">
                            <div class="flex items-center space-x-3.5">
                                <div class="w-10 h-10 rounded-2xl bg-blue-50 text-[#005B9C] border border-blue-100 flex items-center justify-center font-black text-sm shrink-0">
                                    {{ strtoupper(substr($profile->user?->name ?? 'U', 0, 1)) }}
                                </div>
                                <div class="space-y-0.5">
                                    <a href="{{ route('admin.interns.show', $profile->id) }}" class="font-extrabold text-slate-900 text-sm leading-snug hover:text-[#005B9C] transition">
                                        {{ $profile->user?->name }}
                                    </a>
                                    <div class="flex items-center space-x-1.5 text-xs text-slate-500 font-medium">
                                        <i class="fa-regular fa-envelope text-slate-400"></i>
                                        <span>{{ $profile->user?->email }}</span>
                                    </div>
                                    <div class="flex items-center space-x-1.5 text-xs text-slate-500 font-medium">
                                        <i class="fa-solid fa-phone text-slate-400 text-xs"></i>
                                        <span>{{ $profile->user?->phone ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Column 2: NIM & Instansi -->
                        <td class="py-4 px-4">
                            <p class="font-extrabold text-slate-900 leading-snug">{{ $profile->institution }}</p>
                            <p class="text-xs text-slate-500 font-medium mt-0.5">{{ $profile->major }}</p>
                            <p class="text-xs text-slate-400 font-normal">NIM: {{ $profile->student_id_number }}</p>
                        </td>

                        <!-- Column 3: Divisi & Pembimbing -->
                        <td class="py-4 px-4">
                            <p class="font-extrabold text-[#005B9C] leading-snug">
                                {{ $profile->division ? str_replace('Divisi ', '', $profile->division->name) : 'Belum Diplot' }}
                            </p>
                            <div class="flex items-center space-x-1.5 text-xs text-slate-500 font-medium mt-0.5">
                                <i class="fa-regular fa-user text-slate-400"></i>
                                <span>Spv: {{ $profile->supervisor?->name ?? 'Belum Ditentukan' }}</span>
                            </div>
                        </td>

                        <!-- Column 4: Periode Magang -->
                        <td class="py-4 px-4 text-slate-800 font-extrabold whitespace-nowrap leading-relaxed">
                            {{ $profile->start_date ? $profile->start_date->format('d/m/Y') : '-' }} s.d.<br>
                            {{ $profile->end_date ? $profile->end_date->format('d/m/Y') : '-' }}
                        </td>

                        <!-- Column 5: Status -->
                        <td class="py-4 px-4">
                            <x-badge :status="$profile->status" />
                        </td>

                        <!-- Column 6: Aksi -->
                        <td class="py-4 px-6 text-right space-x-1.5 whitespace-nowrap">
                            <x-button
                                href="{{ route('admin.interns.show', $profile->id) }}"
                                variant="outline"
                                size="sm"
                                icon="fa-solid fa-eye"
                                title="Detail Track Record" />

                            <x-button
                                href="{{ route('admin.interns.edit', $profile->id) }}"
                                variant="secondary"
                                size="sm"
                                icon="fa-solid fa-pen-to-square"
                                title="Edit Data" />

                            <form action="{{ route('admin.interns.delete', $profile->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus peserta ini?')">
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
                        <td colspan="6" class="py-8">
                            <x-empty-state
                                title="Tidak ada data peserta magang"
                                subtitle="Coba ubah kata kunci pencarian atau filter divisi/status Anda."
                                icon="fa-solid fa-users-slash" />
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($interns->hasPages())
        <div class="p-5 border-t border-slate-100 bg-slate-50/50 rounded-b-3xl">
            {{ $interns->links() }}
        </div>
        @endif
    </x-card>

</div>
@endsection