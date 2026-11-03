@extends('layouts.app')

@section('page_title', 'Kelola Pembimbing Lapangan')

@section('content')
<div class="space-y-6" x-data="{ addModalOpen: false, editModalOpen: false, activeSupervisor: null }">

    <!-- Centralized Page Header -->
    <x-page-header
        title="Manajemen Pembimbing Lapangan"
        subtitle="Kelola data pembimbing (mentor/supervisor) per divisi di PT Pelabuhan Indonesia."
        icon="fa-solid fa-graduation-cap">
        <x-slot:action>
            <x-button type="button" @click="addModalOpen = true" variant="primary" icon="fa-solid fa-plus">
                Tambah Pembimbing Baru
            </x-button>
        </x-slot:action>
    </x-page-header>

    <!-- 4 KPI Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <x-stat-card
            title="Total Pembimbing"
            value="{{ $totalSupervisors }}"
            trend="Pembimbing Aktif"
            trend-type="up"
            icon="fa-solid fa-users"
            color="blue" />

        <x-stat-card
            title="Divisi Tercakup"
            value="{{ $coveredDivisionsCount }}"
            trend="Divisi Operasional"
            trend-type="up"
            icon="fa-solid fa-check-double"
            color="emerald" />

        <x-stat-card
            title="Rata-Rata Peserta"
            value="{{ $avgInternsPerSpv }}"
            trend="Per Pembimbing"
            trend-type="up"
            icon="fa-solid fa-user-graduate"
            color="purple" />

        <x-stat-card
            title="Terdaftar Sejak"
            value="{{ date('M Y') }}"
            trend="Terhubung Pelindo"
            trend-type="up"
            icon="fa-solid fa-calendar-days"
            color="amber" />
    </div>

    <!-- Search Bar wrapped in Card -->
    <x-card>
        <form method="GET" action="{{ route('admin.supervisors.index') }}" class="flex items-center gap-4">
            <!-- Search input -->
            <div class="flex-1">
                <x-form-input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nama pembimbing, email, atau NIP..."
                    icon="fa-solid fa-magnifying-glass" />
            </div>

            <!-- Buttons -->
            <div class="flex items-center gap-3">
                <x-button type="submit" variant="primary" icon="fa-solid fa-magnifying-glass">
                    Cari
                </x-button>
                <x-button href="{{ route('admin.supervisors.index') }}" variant="outline" icon="fa-solid fa-rotate">
                    Reset
                </x-button>
            </div>
        </form>
    </x-card>

    <!-- Supervisors Table -->
    <x-card padding="p-0">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100 text-xs font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-4 px-6">Pembimbing</th>
                        <th class="py-4 px-4">NIP & Jabatan</th>
                        <th class="py-4 px-4">Divisi Assigned</th>
                        <th class="py-4 px-4">Telepon / WA</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm font-semibold text-slate-700">
                    @forelse($supervisors as $profile)
                    @php
                    $firstLetter = strtoupper(substr($profile->user?->name ?? 'S', 0, 1));

                    $avatarClass = 'bg-blue-50 text-[#005B9C] border border-blue-100';
                    if (in_array($firstLetter, ['G','H','I','J','K','L','M'])) {
                        $avatarClass = 'bg-emerald-50 text-emerald-600 border border-emerald-100';
                    } elseif (in_array($firstLetter, ['N','O','P','Q','R','S','T'])) {
                        $avatarClass = 'bg-purple-50 text-purple-600 border border-purple-100';
                    } elseif (in_array($firstLetter, ['U','V','W','X','Y','Z'])) {
                        $avatarClass = 'bg-amber-50 text-amber-600 border border-amber-100';
                    }

                    $divName = $profile->division?->name ?? 'Semua Divisi';
                    $divClass = 'bg-slate-50 text-slate-700 border border-slate-200';
                    if (Str::contains($divName, ['Teknologi', 'IT'])) {
                        $divClass = 'bg-blue-50 text-[#005B9C] border border-blue-100';
                    } elseif (Str::contains($divName, ['SDM', 'HR', 'Umum'])) {
                        $divClass = 'bg-emerald-50 text-emerald-600 border border-emerald-100';
                    } elseif (Str::contains($divName, ['Operasional', 'Ops', 'Logistik'])) {
                        $divClass = 'bg-purple-50 text-purple-600 border border-purple-100';
                    }
                    @endphp
                    <tr class="hover:bg-slate-50/60 transition">
                        <!-- Column 1: Pembimbing -->
                        <td class="py-4 px-6">
                            <div class="flex items-center space-x-3.5">
                                <div class="w-10 h-10 rounded-2xl flex items-center justify-center font-black text-sm shrink-0 {{ $avatarClass }}">
                                    {{ $firstLetter }}
                                </div>
                                <div>
                                    <p class="font-extrabold text-slate-900 text-sm leading-snug">{{ $profile->user?->name }}</p>
                                    <p class="text-xs text-slate-500 font-medium mt-0.5">{{ $profile->user?->email }}</p>
                                </div>
                            </div>
                        </td>

                        <!-- Column 2: NIP & Jabatan -->
                        <td class="py-4 px-4">
                            <p class="font-extrabold text-slate-900 leading-snug">{{ $profile->position_title ?? 'Pembimbing Lapangan' }}</p>
                            <p class="text-xs font-mono text-slate-500 mt-0.5">NIP: {{ $profile->employee_id_number ?? '-' }}</p>
                        </td>

                        <!-- Column 3: Divisi Assigned -->
                        <td class="py-4 px-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-xl text-xs font-bold border uppercase tracking-wider {{ $divClass }}">
                                {{ $divName }}
                            </span>
                        </td>

                        <!-- Column 4: Telepon / WA -->
                        <td class="py-4 px-4 text-slate-800 font-extrabold">
                            {{ $profile->phone ?? $profile->user?->phone ?? '-' }}
                        </td>

                        <!-- Column 5: Aksi -->
                        <td class="py-4 px-6 text-right space-x-1.5 whitespace-nowrap">
                            <x-button
                                type="button"
                                @click="activeSupervisor = {{ json_encode($profile) }}; editModalOpen = true"
                                variant="secondary"
                                size="sm"
                                icon="fa-solid fa-pen-to-square"
                                title="Edit Data" />

                            <form action="{{ route('admin.supervisors.delete', $profile->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun pembimbing ini?')">
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
                                title="Tidak ada pembimbing ditemukan"
                                subtitle="Coba ubah kata kunci pencarian akun pembimbing Anda."
                                icon="fa-solid fa-user-xmark" />
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($supervisors->hasPages())
        <div class="p-5 border-t border-slate-100 bg-slate-50/50 rounded-b-3xl">
            {{ $supervisors->links() }}
        </div>
        @endif
    </x-card>

    <!-- Modal Tambah Pembimbing -->
    <div x-show="addModalOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4" x-cloak>
        <div @click.outside="addModalOpen = false" class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-5 max-h-[90vh] overflow-y-auto border border-slate-100">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <h3 class="text-base font-extrabold text-slate-900">Tambah Pembimbing Lapangan</h3>
                <button @click="addModalOpen = false" class="text-slate-400 hover:text-slate-600 transition"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <form action="{{ route('admin.supervisors.store') }}" method="POST" class="space-y-4 text-sm font-semibold">
                @csrf

                <x-form-input type="text" name="name" label="Nama Pembimbing *" required />
                <x-form-input type="email" name="email" label="Email Perusahaan *" required />
                <x-form-input type="text" name="employee_id_number" label="NIP (Nomor Induk Pegawai) *" placeholder="Contoh: NIP-198504122010121003" required />
                <x-form-input type="text" name="position_title" label="Jabatan *" placeholder="Contoh: Senior IT Specialist" required />

                <x-form-select name="division_id" label="Divisi Assigned *" required>
                    <option value="">-- Pilih Divisi --</option>
                    @foreach($divisions as $div)
                    <option value="{{ $div->id }}">{{ $div->name }}</option>
                    @endforeach
                </x-form-select>

                <x-form-input type="text" name="phone" label="No. Telepon / WA" />
                <x-form-input type="password" name="password" label="Password Akun *" value="password123" required />

                <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
                    <x-button type="button" @click="addModalOpen = false" variant="ghost" size="sm">Batal</x-button>
                    <x-button type="submit" variant="primary" size="sm">Simpan Pembimbing</x-button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Pembimbing -->
    <div x-show="editModalOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4" x-cloak>
        <template x-if="activeSupervisor">
            <div @click.outside="editModalOpen = false" class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-5 max-h-[90vh] overflow-y-auto border border-slate-100">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <h3 class="text-base font-extrabold text-slate-900">Edit Data Pembimbing</h3>
                    <button @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600 transition"><i class="fa-solid fa-xmark text-lg"></i></button>
                </div>

                <form :action="'{{ url('/admin/supervisors') }}/' + activeSupervisor.id" method="POST" class="space-y-4 text-sm font-semibold">
                    @csrf
                    @method('PUT')

                    <x-form-input type="text" name="name" label="Nama Pembimbing *" x-model="activeSupervisor.user.name" required />
                    <x-form-input type="email" name="email" label="Email *" x-model="activeSupervisor.user.email" required />
                    <x-form-input type="text" name="employee_id_number" label="NIP *" x-model="activeSupervisor.employee_id_number" required />
                    <x-form-input type="text" name="position_title" label="Jabatan *" x-model="activeSupervisor.position_title" required />

                    <x-form-select name="division_id" label="Divisi Assigned *" x-model="activeSupervisor.division_id" required>
                        <option value="">-- Pilih Divisi --</option>
                        @foreach($divisions as $div)
                        <option value="{{ $div->id }}">{{ $div->name }}</option>
                        @endforeach
                    </x-form-select>

                    <x-form-input type="text" name="phone" label="No. Telepon" x-model="activeSupervisor.phone" />
                    <x-form-input type="password" name="password" label="Reset Password (Opsional)" placeholder="Kosongkan jika tidak diubah" />

                    <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
                        <x-button type="button" @click="editModalOpen = false" variant="ghost" size="sm">Batal</x-button>
                        <x-button type="submit" variant="primary" size="sm">Update Pembimbing</x-button>
                    </div>
                </form>
            </div>
        </template>
    </div>

</div>
@endsection