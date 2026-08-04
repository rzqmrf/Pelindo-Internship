@extends('layouts.app')

@section('page_title', 'Statistik Bimbingan Magang')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center space-x-3.5">
            <div class="w-12 h-12 rounded-full bg-blue-50 text-[#0B63E5] flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-chart-column text-sm"></i>
            </div>
            <div>
                <h1 class="text-xl font-black text-slate-850 tracking-tight">Statistik Performa Bimbingan Magang</h1>
                <p class="text-xs font-semibold text-slate-400 mt-1">Evaluasi kedisiplinan dan rasio persetujuan laporan harian peserta magang bimbingan Anda.</p>
            </div>
        </div>
    </div>

    <!-- 5 KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Card 1: Total Peserta Bimbingan -->
        <div class="bg-white rounded-3xl p-4 border border-slate-100 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Total Peserta Bimbingan</p>
                <h3 class="text-2xl font-black text-slate-800">{{ $totalSupervisedCount }}</h3>
                <p class="text-[10px] font-extrabold text-[#0B63E5] mt-1">
                    <span>Aktif terdaftar</span>
                </p>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-blue-50/80 text-[#0B63E5] flex items-center justify-center text-base shrink-0">
                <i class="fa-solid fa-user-group"></i>
            </div>
        </div>

        <!-- Card 2: Rata-rata Approval Rate -->
        <div class="bg-white rounded-3xl p-4 border border-slate-100 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Rata-rata Approval Rate</p>
                <h3 class="text-2xl font-black text-slate-800">{{ $avgApprovalRate }}%</h3>
                <p class="text-[10px] font-extrabold text-emerald-600 flex items-center gap-0.5">
                    <span><i class="fa-solid fa-arrow-up"></i> +12%</span>
                    <span class="text-slate-400 font-bold ml-0.5">dari bulan lalu</span>
                </p>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-emerald-50/80 text-emerald-600 flex items-center justify-center text-base shrink-0">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>

        <!-- Card 3: Total Log Masuk -->
        <div class="bg-white rounded-3xl p-4 border border-slate-100 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Total Log Masuk</p>
                <h3 class="text-2xl font-black text-slate-800">{{ $totalReportsAll }}</h3>
                <p class="text-[10px] font-extrabold text-[#0B63E5] mt-1">
                    <span>Seluruh peserta</span>
                </p>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-blue-50/80 text-[#0B63E5] flex items-center justify-center text-base shrink-0">
                <i class="fa-regular fa-file-lines"></i>
            </div>
        </div>

        <!-- Card 4: Menunggu Persetujuan -->
        <div class="bg-white rounded-3xl p-4 border border-slate-100 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Menunggu Persetujuan</p>
                <h3 class="text-2xl font-black text-slate-800">{{ $totalPendingAll }}</h3>
                <p class="text-[10px] font-extrabold text-orange-500 mt-1">
                    <span>Perlu ditinjau</span>
                </p>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-orange-50/80 text-orange-500 flex items-center justify-center text-base shrink-0">
                <i class="fa-regular fa-circle text-lg font-black"></i>
            </div>
        </div>

        <!-- Card 5: Total Revisi -->
        <div class="bg-white rounded-3xl p-4 border border-slate-100 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Total Revisi</p>
                <h3 class="text-2xl font-black text-slate-800">{{ $totalRevisionAll }}</h3>
                <p class="text-[10px] font-extrabold text-purple-655 mt-1">
                    <span>Perlu perbaikan</span>
                </p>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-purple-50/80 text-purple-655 flex items-center justify-center text-base shrink-0">
                <i class="fa-solid fa-rotate"></i>
            </div>
        </div>
    </div>

    <!-- Stats Table per Intern -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-50 flex justify-between items-center bg-white">
            <div class="flex items-center space-x-2">
                <i class="fa-solid fa-sliders text-[#0B63E5]"></i>
                <h3 class="font-black text-slate-800 text-sm">Rekapitulasi Persetujuan Laporan per Peserta</h3>
            </div>
            <span class="bg-slate-50 border border-slate-200/50 text-slate-500 rounded-xl px-3 py-1 text-[10px] font-black uppercase tracking-wider">Total: {{ count($statsPerIntern) }} Peserta</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-wider">
                        <th class="py-3.5 px-5">Peserta Magang & Instansi</th>
                        <th class="py-3.5 px-4">Total Log Masuk</th>
                        <th class="py-3.5 px-4 text-emerald-600">Disetujui (Approved)</th>
                        <th class="py-3.5 px-4 text-orange-500">Pending</th>
                        <th class="py-3.5 px-4 text-blue-650">Revisi</th>
                        <th class="py-3.5 px-4">Heatmap Aktivitas (30 Hari)</th>
                        <th class="py-3.5 px-4">Rasio Approval</th>
                        <th class="py-3.5 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-xs font-bold text-slate-700">
                    @forelse($statsPerIntern as $stat)
                        @php
                            $firstLetter = strtoupper(substr($stat['name'], 0, 1));
                            
                            $avatarClass = 'bg-blue-50 text-[#0B63E5]';
                            if (in_array($firstLetter, ['G','H','I','J','K','L','M'])) {
                                $avatarClass = 'bg-emerald-50 text-emerald-600';
                            } elseif (in_array($firstLetter, ['N','O','P','Q','R','S','T'])) {
                                $avatarClass = 'bg-purple-50 text-purple-605';
                            } elseif (in_array($firstLetter, ['U','V','W','X','Y','Z'])) {
                                $avatarClass = 'bg-orange-50 text-orange-500';
                            }
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition">
                            <!-- Column 1: Peserta & Instansi -->
                            <td class="py-3.5 px-5">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center font-black text-xs shadow-2xs shrink-0 {{ $avatarClass }}">
                                        {{ $firstLetter }}
                                    </div>
                                    <div>
                                        <p class="font-extrabold text-slate-800 text-sm leading-tight">{{ $stat['name'] }}</p>
                                        <p class="text-[11px] text-slate-400 font-semibold mt-0.5">{{ $stat['institution'] }}</p>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Column 2: Total Log Masuk -->
                            <td class="py-3.5 px-4 text-slate-700 font-extrabold whitespace-nowrap">
                                {{ $stat['total'] }} Laporan
                            </td>
                            
                            <!-- Column 3: Approved -->
                            <td class="py-3.5 px-4 text-emerald-600 font-extrabold text-sm">
                                {{ $stat['approved'] }}
                            </td>
                            
                            <!-- Column 4: Pending -->
                            <td class="py-3.5 px-4 text-orange-500 font-extrabold text-sm">
                                {{ $stat['pending'] }}
                            </td>
                            
                            <!-- Column 5: Revision -->
                            <td class="py-3.5 px-4 text-blue-600 font-extrabold text-sm">
                                {{ $stat['revision'] }}
                            </td>
                            
                            <!-- Column 6: Heatmap Aktivitas 30 Hari -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-0.5 flex-wrap max-w-[130px]">
                                    @foreach($stat['heatmap'] as $day)
                                        @php
                                            $color = match($day['status']) {
                                                'approved' => 'bg-emerald-500 hover:bg-emerald-600',
                                                'pending' => 'bg-amber-400 hover:bg-amber-550',
                                                'revision' => 'bg-blue-400 hover:bg-blue-500',
                                                'rejected' => 'bg-rose-500 hover:bg-rose-600',
                                                default => 'bg-slate-100 hover:bg-slate-200',
                                            };
                                        @endphp
                                        <div class="relative group">
                                            <span class="w-2.5 h-2.5 rounded-xs block {{ $color }} transition-colors cursor-pointer"></span>
                                            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-1.5 hidden group-hover:block bg-slate-900 text-white text-[9px] px-2 py-1 rounded-lg whitespace-nowrap z-30 font-bold shadow-md leading-none">
                                                {{ $day['date'] }}: {{ $day['status'] === 'none' ? 'No Activity' : ucfirst($day['status']) }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            
                            <!-- Column 7: Rasio Approval -->
                            <td class="py-3.5 px-4">
                                <div class="w-24 space-y-1">
                                    <p class="font-black text-slate-850 text-xs leading-none">{{ $stat['approval_rate'] }}%</p>
                                    <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                        <div class="h-1.5 bg-emerald-500 rounded-full" style="width: {{ $stat['approval_rate'] }}%"></div>
                                    </div>
                                </div>
                            </td>

                            <!-- Column 8: Aksi -->
                            <td class="py-3.5 px-5 text-right">
                                <a href="{{ route('supervisor.activities') }}?intern_id={{ $stat['user_id'] }}"
                                   class="px-3.5 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-black rounded-xl text-xs flex items-center transition shadow-2xs justify-center gap-1.5 inline-flex">
                                    <i class="fa-regular fa-eye text-slate-400"></i>
                                    <span>Lihat Detail</span>
                                    <i class="fa-solid fa-chevron-right text-[9px] text-slate-400"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400 font-semibold">Belum ada statistik bimbingan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Heatmap Legend Footer inside Table Card -->
        <div class="p-4 border-t border-slate-50 bg-slate-50/20 flex flex-wrap gap-4 items-center justify-between text-[10px] text-slate-400 font-black">
            <span class="uppercase tracking-wider">Keterangan Kalender Kontribusi</span>
            <div class="flex items-center space-x-3.5">
                <div class="flex items-center space-x-1">
                    <span class="w-2.5 h-2.5 rounded-xs bg-slate-100 block border border-slate-200/20"></span>
                    <span>Kosong</span>
                </div>
                <div class="flex items-center space-x-1">
                    <span class="w-2.5 h-2.5 rounded-xs bg-amber-400 block"></span>
                    <span>Pending</span>
                </div>
                <div class="flex items-center space-x-1">
                    <span class="w-2.5 h-2.5 rounded-xs bg-emerald-500 block"></span>
                    <span>Disetujui</span>
                </div>
                <div class="flex items-center space-x-1">
                    <span class="w-2.5 h-2.5 rounded-xs bg-blue-400 block"></span>
                    <span>Revisi</span>
                </div>
                <div class="flex items-center space-x-1">
                    <span class="w-2.5 h-2.5 rounded-xs bg-rose-500 block"></span>
                    <span>Ditolak</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Insight Card at the Bottom -->
    <div class="bg-blue-50/50 border border-blue-100 rounded-3xl p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center space-x-3.5">
            <div class="w-10 h-10 rounded-full bg-blue-100/50 text-[#0B63E5] flex items-center justify-center shrink-0">
                <i class="fa-regular fa-lightbulb"></i>
            </div>
            <div>
                <h4 class="font-extrabold text-slate-800 text-sm">Insight</h4>
                <p class="text-xs font-semibold text-slate-500 mt-0.5">Approval rate keseluruhan masih dapat ditingkatkan. Pastikan peserta magang rutin mengisi logbook sesuai pedoman.</p>
            </div>
        </div>
    </div>

</div>
@endsection
