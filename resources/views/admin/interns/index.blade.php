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
        action-icon="fa-solid fa-user-plus"
    />

    <!-- 4 KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Active Interns -->
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Total Peserta Aktif</p>
                <h3 class="text-3xl font-black text-slate-800">{{ $activeInternsCount }}</h3>
                <p class="text-[10px] font-extrabold text-emerald-600 flex items-center gap-0.5 mt-1">
                    <span><i class="fa-solid fa-arrow-up"></i> 12%</span>
                    <span class="text-slate-400 font-bold ml-0.5">dari bulan lalu</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50/80 text-[#0B63E5] flex items-center justify-center text-lg shrink-0">
                <i class="fa-solid fa-user-group"></i>
            </div>
        </div>

        <!-- Active Supervisors -->
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Pembimbing Lapangan</p>
                <h3 class="text-3xl font-black text-slate-800">{{ $activeSupervisorsCount }}</h3>
                <p class="text-[10px] font-extrabold text-emerald-600 flex items-center gap-0.5 mt-1">
                    <span><i class="fa-solid fa-arrow-up"></i> 2%</span>
                    <span class="text-slate-400 font-bold ml-0.5">dari bulan lalu</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-purple-50/80 text-purple-600 flex items-center justify-center text-lg shrink-0">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
        </div>

        <!-- Pending Reports -->
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Menunggu Approval</p>
                <h3 class="text-3xl font-black text-slate-800">{{ $pendingReportsCount }}</h3>
                <p class="text-[10px] font-extrabold text-orange-500 flex items-center gap-0.5 mt-1">
                    <span><i class="fa-solid fa-arrow-down"></i> 1%</span>
                    <span class="text-slate-400 font-bold ml-0.5">dari bulan lalu</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-orange-50/80 text-orange-500 flex items-center justify-center text-lg shrink-0">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
        </div>

        <!-- Completed Reports -->
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Laporan Disetujui</p>
                <h3 class="text-3xl font-black text-slate-800">{{ $completedReportsCount }}</h3>
                <p class="text-[10px] font-extrabold text-emerald-600 flex items-center gap-0.5 mt-1">
                    <span><i class="fa-solid fa-arrow-up"></i> 15%</span>
                    <span class="text-slate-400 font-bold ml-0.5">dari bulan lalu</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50/80 text-emerald-600 flex items-center justify-center text-lg shrink-0">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-3xl p-4 border border-slate-100 shadow-sm">
        <form method="GET" action="{{ route('admin.interns.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-center">
            <!-- Search -->
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIM, atau instansi..."
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold text-slate-850 outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
            </div>

            <!-- Division Filter -->
            <div class="relative">
                <select name="division_id" class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold text-slate-600 outline-none appearance-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
                    <option value="">Semua Divisi</option>
                    @foreach($divisions as $div)
                        <option value="{{ $div->id }}" {{ request('division_id') == $div->id ? 'selected' : '' }}>{{ str_replace('Divisi ', '', $div->name) }}</option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-400">
                    <i class="fa-solid fa-chevron-down text-[10px]"></i>
                </div>
            </div>

            <!-- Status Filter -->
            <div class="relative">
                <select name="status" class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold text-slate-600 outline-none appearance-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai Magang</option>
                    <option value="extended" {{ request('status') == 'extended' ? 'selected' : '' }}>Diperpanjang</option>
                    <option value="terminated" {{ request('status') == 'terminated' ? 'selected' : '' }}>Nonaktif / Keluar</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-400">
                    <i class="fa-solid fa-chevron-down text-[10px]"></i>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex items-center gap-3">
                <button type="submit" class="flex-1 py-2.5 bg-[#0B63E5] text-white rounded-2xl text-xs font-extrabold hover:bg-blue-750 transition flex items-center justify-center space-x-1.5 shadow-2xs">
                    <i class="fa-solid fa-filter"></i>
                    <span>Filter</span>
                </button>
                <a href="{{ route('admin.interns.index') }}" class="px-4 py-2.5 bg-white text-slate-600 rounded-2xl text-xs font-extrabold hover:bg-slate-50 border border-slate-200 transition flex items-center justify-center space-x-1.5 shadow-2xs">
                    <i class="fa-solid fa-rotate text-xs"></i>
                    <span>Reset</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Interns Table -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-wider">
                        <th class="py-3.5 px-5">Peserta & Kontak</th>
                        <th class="py-3.5 px-4">NIM & Instansi</th>
                        <th class="py-3.5 px-4">Divisi & Pembimbing</th>
                        <th class="py-3.5 px-4">Periode Magang</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-xs font-bold text-slate-700">
                    @forelse($interns as $profile)
                        <tr class="hover:bg-slate-50/50 transition">
                            <!-- Column 1: Peserta & Kontak -->
                            <td class="py-3.5 px-5">
                                <div class="flex items-center space-x-3.5">
                                    <div class="w-9 h-9 rounded-full bg-blue-50/80 text-[#0B63E5] flex items-center justify-center font-black text-xs shadow-2xs shrink-0">
                                        {{ strtoupper(substr($profile->user?->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div class="space-y-0.5">
                                        <a href="{{ route('admin.interns.show', $profile->id) }}" class="font-extrabold text-slate-800 text-sm leading-tight hover:text-[#0B63E5] transition">
                                            {{ $profile->user?->name }}
                                        </a>
                                        <div class="flex items-center space-x-1 text-[11px] text-slate-400 font-semibold">
                                            <i class="fa-regular fa-envelope w-3.5 text-slate-400"></i>
                                            <span>{{ $profile->user?->email }}</span>
                                        </div>
                                        <div class="flex items-center space-x-1 text-[11px] text-slate-400 font-semibold">
                                            <i class="fa-solid fa-phone w-3.5 text-slate-400 text-[10px]"></i>
                                            <span>{{ $profile->user?->phone ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Column 2: NIM & Instansi -->
                            <td class="py-3.5 px-4">
                                <p class="font-extrabold text-slate-800 leading-tight">{{ $profile->institution }}</p>
                                <p class="text-[11px] text-slate-400 font-semibold mt-0.5">{{ $profile->major }}</p>
                                <p class="text-[11px] text-slate-400 font-semibold">NIM: {{ $profile->student_id_number }}</p>
                            </td>

                            <!-- Column 3: Divisi & Pembimbing -->
                            <td class="py-3.5 px-4">
                                <p class="font-extrabold text-[#0B63E5] leading-tight">
                                    {{ $profile->division ? str_replace('Divisi ', '', $profile->division->name) : 'Belum Diplot' }}
                                </p>
                                <div class="flex items-center space-x-1 text-[11px] text-slate-500 font-semibold mt-0.5">
                                    <i class="fa-regular fa-user text-slate-400 text-[10px]"></i>
                                    <span>Spv: {{ $profile->supervisor?->name ?? 'Belum Ditentukan' }}</span>
                                </div>
                            </td>

                            <!-- Column 4: Periode Magang -->
                            <td class="py-3.5 px-4 text-slate-600 font-extrabold whitespace-nowrap leading-relaxed">
                                {{ $profile->start_date ? $profile->start_date->format('d/m/Y') : '-' }} s.d.<br>
                                {{ $profile->end_date ? $profile->end_date->format('d/m/Y') : '-' }}
                            </td>

                            <!-- Column 5: Status -->
                            <td class="py-3.5 px-4">
                                @php
                                    $statusData = match($profile->status) {
                                        'active' => ['bg-emerald-50 text-emerald-600 border-emerald-100 bg-dot', 'bg-emerald-500', 'ACTIVE'],
                                        'completed' => ['bg-blue-50 text-blue-600 border-blue-100 bg-dot', 'bg-blue-500', 'COMPLETED'],
                                        'extended' => ['bg-amber-50 text-amber-600 border-amber-100 bg-dot', 'bg-amber-500', 'EXTENDED'],
                                        default => ['bg-slate-50 text-slate-500 border-slate-200 bg-dot', 'bg-slate-400', 'TERMINATED'],
                                    };
                                @endphp
                                <span class="inline-flex items-center space-x-1.5 px-2.5 py-1.5 rounded-full text-[9px] font-black border uppercase tracking-wider {{ $statusData[0] }}">
                                    <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $statusData[1] }}"></span>
                                    <span>{{ $statusData[2] }}</span>
                                </span>
                            </td>

                            <!-- Column 6: Aksi (Dedicated Pages) -->
                            <td class="py-3.5 px-5 text-right space-x-1 whitespace-nowrap">
                                <a href="{{ route('admin.interns.show', $profile->id) }}" 
                                   class="p-2 bg-blue-50 hover:bg-blue-600 hover:text-white text-blue-600 border border-blue-100 rounded-xl transition text-xs font-semibold inline-flex items-center justify-center"
                                   title="Detail Track Record">
                                    <i class="fa-solid fa-eye"></i>
                                </a>

                                <a href="{{ route('admin.interns.edit', $profile->id) }}" 
                                   class="p-2 bg-amber-50 hover:bg-amber-500 hover:text-white text-amber-600 border border-amber-100 rounded-xl transition text-xs font-semibold inline-flex items-center justify-center"
                                   title="Edit Data">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                
                                <form action="{{ route('admin.interns.delete', $profile->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus peserta ini?')">
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
                            <td colspan="6" class="py-8 text-center text-slate-400 font-semibold">Tidak ada data peserta magang ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($interns->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/20">
                {{ $interns->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
