@extends('layouts.app')

@section('page_title', 'Kelola Pembimbing Lapangan')

@section('content')
<div class="space-y-6" x-data="{ addModalOpen: false, editModalOpen: false, activeSupervisor: null }">

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight">Manajemen Pembimbing Lapangan</h1>
            <p class="text-xs font-semibold text-slate-400 mt-1">Kelola data pembimbing (mentor/supervisor) per divisi di PT Pelabuhan Indonesia.</p>
        </div>
        <button @click="addModalOpen = true" class="px-5 py-2.5 bg-[#0B63E5] hover:bg-blue-700 text-white font-extrabold rounded-2xl text-xs transition shadow-sm flex items-center justify-center gap-2">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Tambah Pembimbing Baru</span>
        </button>
    </div>

    <!-- Search Bar -->
    <div class="bg-white rounded-3xl p-4 border border-slate-100 shadow-sm">
        <form method="GET" action="{{ route('admin.supervisors.index') }}" class="flex items-center gap-4">
            <!-- Search input -->
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama pembimbing, email, atau NIP..."
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold text-slate-850 outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
            </div>

            <!-- Buttons -->
            <div class="flex items-center gap-3">
                <button type="submit" class="px-6 py-2.5 bg-[#0B63E5] hover:bg-blue-750 text-white rounded-2xl text-xs font-extrabold transition flex items-center justify-center space-x-1.5 shadow-2xs">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    <span>Cari</span>
                </button>
                <a href="{{ route('admin.supervisors.index') }}" class="px-4 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-2xl text-xs font-extrabold hover:bg-slate-50 transition flex items-center justify-center space-x-1.5 shadow-2xs">
                    <i class="fa-solid fa-rotate text-xs"></i>
                    <span>Reset</span>
                </a>
            </div>
        </form>
    </div>

    <!-- 4 KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Card 1: Total Pembimbing -->
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Total Pembimbing</p>
                <h3 class="text-3xl font-black text-slate-800">{{ $totalSupervisors }}</h3>
                <p class="text-[10px] font-extrabold text-[#0B63E5] mt-1">
                    <span>Pembimbing Aktif</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50/80 text-[#0B63E5] flex items-center justify-center text-lg shrink-0">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>

        <!-- Card 2: Divisi Tercakup -->
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Divisi Tercakup</p>
                <h3 class="text-3xl font-black text-slate-800">{{ $coveredDivisionsCount }}</h3>
                <p class="text-[10px] font-extrabold text-emerald-600 mt-1">
                    <span>Divisi</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50/80 text-emerald-600 flex items-center justify-center text-lg shrink-0">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>

        <!-- Card 3: Rata-rata Peserta -->
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Rata-rata Peserta</p>
                <h3 class="text-3xl font-black text-slate-800">{{ $avgInternsPerSpv }}</h3>
                <p class="text-[10px] font-extrabold text-purple-600 mt-1">
                    <span>Per Pembimbing</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-purple-50/80 text-purple-605 flex items-center justify-center text-lg shrink-0">
                <i class="fa-solid fa-user"></i>
            </div>
        </div>

        <!-- Card 4: Terdaftar Sejak -->
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Terdaftar Sejak</p>
                <h3 class="text-3xl font-black text-slate-800">{{ $oldestJoiningLabel }}</h3>
                <p class="text-[10px] font-extrabold text-orange-500 mt-1">
                    <span>Bergabung</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-orange-50/80 text-orange-500 flex items-center justify-center text-lg shrink-0">
                <i class="fa-regular fa-calendar"></i>
            </div>
        </div>
    </div>

    <!-- Supervisors Table -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-wider">
                        <th class="py-3.5 px-5">Pembimbing</th>
                        <th class="py-3.5 px-4">NIP & Jabatan</th>
                        <th class="py-3.5 px-4">Divisi Assigned</th>
                        <th class="py-3.5 px-4">Telepon / WA</th>
                        <th class="py-3.5 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-xs font-bold text-slate-700">
                    @forelse($supervisors as $profile)
                        @php
                            $firstLetter = strtoupper(substr($profile->user?->name ?? 'S', 0, 1));
                            
                            // Color code initials
                            $avatarClass = 'bg-blue-50 text-[#0B63E5]';
                            if (in_array($firstLetter, ['G','H','I','J','K','L','M'])) {
                                $avatarClass = 'bg-emerald-50 text-emerald-600';
                            } elseif (in_array($firstLetter, ['N','O','P','Q','R','S','T'])) {
                                $avatarClass = 'bg-purple-50 text-purple-605';
                            } elseif (in_array($firstLetter, ['U','V','W','X','Y','Z'])) {
                                $avatarClass = 'bg-orange-50 text-orange-500';
                            }

                            // Dynamic division badge
                            $divName = $profile->division?->name ?? 'Semua Divisi';
                            $divClass = 'bg-slate-50 text-slate-500 border border-slate-200';
                            if (Str::contains($divName, ['Teknologi', 'IT'])) {
                                $divClass = 'bg-blue-50 text-[#0B63E5] border border-blue-100';
                            } elseif (Str::contains($divName, ['SDM', 'HR', 'Umum'])) {
                                $divClass = 'bg-emerald-50 text-emerald-600 border border-emerald-100';
                            } elseif (Str::contains($divName, ['Operasional', 'Ops', 'Logistik'])) {
                                $divClass = 'bg-purple-50 text-purple-600 border border-purple-100';
                            }
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition">
                            <!-- Column 1: Pembimbing -->
                            <td class="py-3.5 px-5">
                                <div class="flex items-center space-x-3.5">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center font-black text-xs shadow-2xs shrink-0 {{ $avatarClass }}">
                                        {{ $firstLetter }}
                                    </div>
                                    <div>
                                        <p class="font-extrabold text-slate-800 text-sm leading-tight">{{ $profile->user?->name }}</p>
                                        <p class="text-[11px] text-slate-400 font-semibold mt-0.5">{{ $profile->user?->email }}</p>
                                    </div>
                                </div>
                            </td>

                            <!-- Column 2: NIP & Jabatan -->
                            <td class="py-3.5 px-4">
                                <p class="font-extrabold text-slate-800 leading-tight">{{ $profile->position_title ?? 'Pembimbing Lapangan' }}</p>
                                <p class="text-[11px] font-mono text-slate-400 mt-0.5">NIP &ndash; {{ $profile->employee_id_number ?? '-' }}</p>
                            </td>

                            <!-- Column 3: Divisi Assigned -->
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-[11px] font-bold border uppercase tracking-wider {{ $divClass }}">
                                    {{ $divName }}
                                </span>
                            </td>

                            <!-- Column 4: Telepon / WA -->
                            <td class="py-3.5 px-4 text-slate-600 font-extrabold">
                                {{ $profile->phone ?? $profile->user?->phone ?? '-' }}
                            </td>

                            <!-- Column 5: Aksi -->
                            <td class="py-3.5 px-5 text-right space-x-1">
                                <button @click="activeSupervisor = {{ json_encode($profile) }}; editModalOpen = true" 
                                        class="p-2 bg-slate-50 hover:bg-slate-100 text-slate-600 border border-slate-100 rounded-xl transition text-xs font-semibold"
                                        title="Edit Data">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                
                                <form action="{{ route('admin.supervisors.delete', $profile->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun pembimbing ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 bg-rose-50 border border-rose-100 hover:bg-rose-100 text-rose-600 rounded-xl transition text-xs font-semibold"
                                            title="Hapus Data">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400 font-semibold">Tidak ada pembimbing ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($supervisors->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/20">
                {{ $supervisors->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Tambah Pembimbing -->
    <div x-show="addModalOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4" x-cloak>
        <div @click.outside="addModalOpen = false" class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-base font-black text-slate-800">Tambah Pembimbing Lapangan</h3>
                <button @click="addModalOpen = false" class="text-slate-400 hover:text-slate-650"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <form action="{{ route('admin.supervisors.store') }}" method="POST" class="space-y-4 text-xs font-bold text-slate-700">
                @csrf
                
                <div>
                    <label class="block font-black text-slate-700 mb-1.5">Nama Pembimbing *</label>
                    <input type="text" name="name" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                </div>

                <div>
                    <label class="block font-black text-slate-700 mb-1.5">Email Perusahaan *</label>
                    <input type="email" name="email" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                </div>

                <div>
                    <label class="block font-black text-slate-700 mb-1.5">NIP (Nomor Induk Pegawai) *</label>
                    <input type="text" name="employee_id_number" placeholder="Contoh: NIP-198504122010121003" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                </div>

                <div>
                    <label class="block font-black text-slate-700 mb-1.5">Jabatan *</label>
                    <input type="text" name="position_title" placeholder="Contoh: Senior IT Specialist" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                </div>

                <div>
                    <label class="block font-black text-slate-700 mb-1.5">Divisi Assigned *</label>
                    <select name="division_id" required class="w-full px-3 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                        <option value="">-- Pilih Divisi --</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}">{{ $div->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-black text-slate-700 mb-1.5">No. Telepon / WA</label>
                    <input type="text" name="phone" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                </div>

                <div>
                    <label class="block font-black text-slate-700 mb-1.5">Password Akun *</label>
                    <input type="password" name="password" required value="password123" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                </div>

                <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                    <button type="button" @click="addModalOpen = false" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition font-extrabold">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-[#0B63E5] hover:bg-blue-750 text-white rounded-xl transition font-extrabold">Simpan Pembimbing</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Pembimbing -->
    <div x-show="editModalOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4" x-cloak>
        <template x-if="activeSupervisor">
            <div @click.outside="editModalOpen = false" class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-black text-slate-800">Edit Data Pembimbing</h3>
                    <button @click="editModalOpen = false" class="text-slate-400 hover:text-slate-650"><i class="fa-solid fa-xmark text-lg"></i></button>
                </div>

                <form :action="'{{ url('/admin/supervisors') }}/' + activeSupervisor.id" method="POST" class="space-y-4 text-xs font-bold text-slate-700">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label class="block font-black text-slate-700 mb-1.5">Nama Pembimbing *</label>
                        <input type="text" name="name" :value="activeSupervisor.user.name" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                    </div>

                    <div>
                        <label class="block font-black text-slate-700 mb-1.5">Email *</label>
                        <input type="email" name="email" :value="activeSupervisor.user.email" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                    </div>

                    <div>
                        <label class="block font-black text-slate-700 mb-1.5">NIP *</label>
                        <input type="text" name="employee_id_number" :value="activeSupervisor.employee_id_number" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                    </div>

                    <div>
                        <label class="block font-black text-slate-700 mb-1.5">Jabatan *</label>
                        <input type="text" name="position_title" :value="activeSupervisor.position_title" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                    </div>

                    <div>
                        <label class="block font-black text-slate-700 mb-1.5">Divisi Assigned *</label>
                        <select name="division_id" required class="w-full px-3 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                            @foreach($divisions as $div)
                                <option value="{{ $div->id }}" :selected="activeSupervisor.division_id == {{ $div->id }}">{{ $div->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-black text-slate-700 mb-1.5">No. Telepon</label>
                        <input type="text" name="phone" :value="activeSupervisor.phone" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                    </div>

                    <div>
                        <label class="block font-black text-slate-700 mb-1.5">Reset Password (Opsional)</label>
                        <input type="password" name="password" placeholder="Kosongkan jika tidak diubah" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                        <button type="button" @click="editModalOpen = false" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition font-extrabold">Batal</button>
                        <button type="submit" class="px-5 py-2.5 bg-[#0B63E5] hover:bg-blue-755 text-white rounded-xl transition font-extrabold">Update Pembimbing</button>
                    </div>
                </form>
            </div>
        </template>
    </div>

</div>
@endsection
