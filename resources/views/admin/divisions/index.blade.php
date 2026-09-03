@extends('layouts.app')

@section('page_title', 'Kelola Divisi Pelindo')

@section('content')
<div class="space-y-6" x-data="{ addModalOpen: false, editModalOpen: false, activeDivision: null }">

    <!-- Centralized Page Header -->
    <x-page-header 
        title="Manajemen Divisi & Kuota Magang" 
        subtitle="Kelola daftar divisi, kuota peserta magang, dan kepala divisi di PT Pelabuhan Indonesia." 
        icon="fa-solid fa-sitemap"
    >
        <x-slot:action>
            <button @click="addModalOpen = true" class="px-5 py-2.5 bg-[#0B63E5] hover:bg-blue-700 text-white font-extrabold rounded-2xl text-xs transition shadow-sm flex items-center justify-center gap-2">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Tambah Divisi Baru</span>
            </button>
        </x-slot:action>
    </x-page-header>

    <!-- 4 KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Card 1: Total Divisi -->
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Total Divisi</p>
                <h3 class="text-3xl font-black text-slate-800">{{ $totalDivisions }}</h3>
                <p class="text-[10px] font-extrabold text-[#0B63E5] mt-1">
                    <span>Divisi Aktif</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50/80 text-[#0B63E5] flex items-center justify-center text-lg shrink-0">
                <i class="fa-solid fa-building"></i>
            </div>
        </div>

        <!-- Card 2: Total Kuota -->
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Total Kuota</p>
                <h3 class="text-3xl font-black text-slate-800">{{ $totalQuota }}</h3>
                <p class="text-[10px] font-extrabold text-emerald-600 mt-1">
                    <span>Seluruh Divisi</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50/80 text-emerald-600 flex items-center justify-center text-lg shrink-0">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>

        <!-- Card 3: Kuota Terisi -->
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Kuota Terisi</p>
                <h3 class="text-3xl font-black text-slate-800">{{ $filledQuota }}</h3>
                <p class="text-[10px] font-extrabold text-purple-600 mt-1">
                    <span>{{ $pctFilled }}% <span class="text-slate-450 font-bold ml-0.5">dari total kuota</span></span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-purple-50/80 text-purple-650 flex items-center justify-center text-lg shrink-0">
                <i class="fa-solid fa-user-check"></i>
            </div>
        </div>

        <!-- Card 4: Sisa Kuota -->
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Sisa Kuota</p>
                <h3 class="text-3xl font-black text-slate-800">{{ $remainingQuota }}</h3>
                <p class="text-[10px] font-extrabold text-orange-500 mt-1">
                    <span>{{ $pctRemaining }}% <span class="text-slate-450 font-bold ml-0.5">tersedia</span></span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-orange-50/80 text-orange-500 flex items-center justify-center text-lg shrink-0">
                <i class="fa-solid fa-chart-pie"></i>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="bg-white rounded-3xl p-4 border border-slate-100 shadow-sm">
        <form method="GET" action="{{ route('admin.divisions.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
            <!-- Search input -->
            <div class="relative md:col-span-2">
                <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama divisi, kode, atau kepala divisi..."
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold text-slate-850 outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
            </div>

            <!-- Status Dropdown (UI match) -->
            <div class="relative">
                <select class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold text-slate-650 outline-none appearance-none">
                    <option value="">Semua Status</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-400">
                    <i class="fa-solid fa-chevron-down text-[10px]"></i>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex items-center gap-3">
                <button type="submit" class="flex-1 py-2.5 bg-[#0B63E5] hover:bg-blue-750 text-white rounded-2xl text-xs font-extrabold transition flex items-center justify-center space-x-1.5 shadow-2xs">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    <span>Cari</span>
                </button>
                <a href="{{ route('admin.divisions.index') }}" class="px-4 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-2xl text-xs font-extrabold hover:bg-slate-50 transition flex items-center justify-center space-x-1.5 shadow-2xs">
                    <i class="fa-solid fa-rotate text-xs"></i>
                    <span>Reset</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Divisions Table -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-wider">
                        <th class="py-3.5 px-5">Kode & Nama Divisi</th>
                        <th class="py-3.5 px-4">Kepala Divisi</th>
                        <th class="py-3.5 px-4">Kuota Magang</th>
                        <th class="py-3.5 px-4">Pembimbing</th>
                        <th class="py-3.5 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-xs font-bold text-slate-700">
                    @forelse($divisions as $div)
                        @php
                            $quotaPercentage = $div->quota > 0 ? min(100, round(($div->interns_count / $div->quota) * 100)) : 0;
                            
                            // Short code mapping
                            $shortCode = match($div->code) {
                                'IT-DIGITAL' => 'ITD',
                                'HR-GA' => 'SDM',
                                'OPS-LOG' => 'OPS',
                                'FIN-TREASURY' => 'FIN',
                                'LEGAL-CORP' => 'LEG',
                                'COMM-DEV' => 'COM',
                                default => substr($div->code, 0, 3)
                            };

                            // Dynamic badge style matching screenshot
                            $badgeClass = match($shortCode) {
                                'ITD' => 'bg-blue-50 text-[#0B63E5]',
                                'SDM' => 'bg-emerald-50 text-emerald-600',
                                'OPS' => 'bg-orange-50 text-orange-500',
                                'FIN' => 'bg-purple-50 text-purple-600',
                                'LEG' => 'bg-cyan-50 text-cyan-600',
                                default => 'bg-pink-50 text-pink-650',
                            };
                            
                            $spvBadgeClass = match($shortCode) {
                                'ITD' => 'bg-blue-50 text-[#0B63E5]',
                                'SDM' => 'bg-emerald-50 text-emerald-600',
                                'OPS' => 'bg-orange-50 text-orange-500',
                                default => 'bg-slate-50 text-slate-500',
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition">
                            <!-- Column 1: Kode & Nama Divisi -->
                            <td class="py-3.5 px-5">
                                <div class="flex items-center space-x-3.5">
                                    <span class="w-9 h-9 rounded-xl flex items-center justify-center font-black text-xs shrink-0 {{ $badgeClass }}">
                                        {{ $shortCode }}
                                    </span>
                                    <div>
                                        <p class="font-extrabold text-slate-800 text-sm leading-tight">{{ $div->name }}</p>
                                        <p class="text-[11px] text-slate-400 font-semibold line-clamp-1 max-w-sm mt-0.5">{{ $div->description ?? 'Tidak ada deskripsi.' }}</p>
                                    </div>
                                </div>
                            </td>

                            <!-- Column 2: Kepala Divisi -->
                            <td class="py-3.5 px-4">
                                <p class="font-extrabold text-slate-850 leading-tight">{{ $div->head_name ?? '-' }}</p>
                                <p class="text-[10px] text-slate-400 font-bold uppercase mt-0.5">Manager</p>
                            </td>

                            <!-- Column 3: Kuota Magang -->
                            <td class="py-3.5 px-4">
                                <div class="w-36">
                                    <div class="flex justify-between text-[11px] font-black text-slate-700 mb-1 leading-none">
                                        <span>{{ $div->interns_count }}/{{ $div->quota }} Peserta</span>
                                        <span>{{ $quotaPercentage }}%</span>
                                    </div>
                                    <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                        <div class="h-1.5 bg-[#0B63E5] rounded-full" style="width: {{ $quotaPercentage }}%"></div>
                                    </div>
                                </div>
                            </td>

                            <!-- Column 4: Pembimbing -->
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold {{ $spvBadgeClass }}">
                                    {{ $div->supervisors_count }} Pembimbing
                                </span>
                            </td>

                            <!-- Column 5: Aksi -->
                            <td class="py-3.5 px-5 text-right space-x-1">
                                <button @click="activeDivision = {{ json_encode($div) }}; editModalOpen = true" 
                                        class="p-2 bg-slate-50 hover:bg-slate-100 text-slate-600 border border-slate-100 rounded-xl transition text-xs font-semibold"
                                        title="Edit Data">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                
                                <form action="{{ route('admin.divisions.destroy', $div->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus divisi ini?')">
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
                            <td colspan="5" class="py-8 text-center text-slate-400 font-semibold">Tidak ada divisi ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($divisions->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/20">
                {{ $divisions->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Tambah Divisi -->
    <div x-show="addModalOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4" x-cloak>
        <div @click.outside="addModalOpen = false" class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-base font-black text-slate-800">Tambah Divisi Baru</h3>
                <button @click="addModalOpen = false" class="text-slate-400 hover:text-slate-650"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <form action="{{ route('admin.divisions.store') }}" method="POST" class="space-y-4 text-xs font-bold text-slate-700">
                @csrf
                
                <div>
                    <label class="block font-black text-slate-700 mb-1.5">Kode Divisi *</label>
                    <input type="text" name="code" placeholder="Contoh: IT-DIGITAL" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition uppercase">
                </div>

                <div>
                    <label class="block font-black text-slate-700 mb-1.5">Nama Divisi *</label>
                    <input type="text" name="name" placeholder="Contoh: Divisi Teknologi Informasi" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                </div>

                <div>
                    <label class="block font-black text-slate-700 mb-1.5">Kepala Divisi</label>
                    <input type="text" name="head_name" placeholder="Contoh: Ir. Budi Santoso, M.T." class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                </div>

                <div>
                    <label class="block font-black text-slate-700 mb-1.5">Kuota Maksimal Peserta Magang *</label>
                    <input type="number" name="quota" value="10" min="1" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                </div>

                <div>
                    <label class="block font-black text-slate-700 mb-1.5">Deskripsi Divisi</label>
                    <textarea name="description" rows="3" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition"></textarea>
                </div>

                <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                    <button type="button" @click="addModalOpen = false" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition font-extrabold">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-[#0B63E5] hover:bg-blue-750 text-white rounded-xl transition font-extrabold">Simpan Divisi</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Divisi -->
    <div x-show="editModalOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4" x-cloak>
        <template x-if="activeDivision">
            <div @click.outside="editModalOpen = false" class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-black text-slate-800">Edit Data Divisi</h3>
                    <button @click="editModalOpen = false" class="text-slate-400 hover:text-slate-650"><i class="fa-solid fa-xmark text-lg"></i></button>
                </div>

                <form :action="'{{ url('/admin/divisions') }}/' + activeDivision.id" method="POST" class="space-y-4 text-xs font-bold text-slate-700">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label class="block font-black text-slate-700 mb-1.5">Kode Divisi *</label>
                        <input type="text" name="code" :value="activeDivision.code" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition uppercase">
                    </div>

                    <div>
                        <label class="block font-black text-slate-700 mb-1.5">Nama Divisi *</label>
                        <input type="text" name="name" :value="activeDivision.name" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                    </div>

                    <div>
                        <label class="block font-black text-slate-700 mb-1.5">Kepala Divisi</label>
                        <input type="text" name="head_name" :value="activeDivision.head_name" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                    </div>

                    <div>
                        <label class="block font-black text-slate-700 mb-1.5">Kuota Maksimal *</label>
                        <input type="number" name="quota" :value="activeDivision.quota" min="1" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                    </div>

                    <div>
                        <label class="block font-black text-slate-700 mb-1.5">Deskripsi Divisi</label>
                        <textarea name="description" rows="3" :value="activeDivision.description" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition"></textarea>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                        <button type="button" @click="editModalOpen = false" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition font-extrabold">Batal</button>
                        <button type="submit" class="px-5 py-2.5 bg-[#0B63E5] hover:bg-blue-755 text-white rounded-xl transition font-extrabold">Update Divisi</button>
                    </div>
                </form>
            </div>
        </template>
    </div>

</div>
@endsection
