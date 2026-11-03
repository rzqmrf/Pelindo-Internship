@extends('layouts.app')

@section('page_title', 'Statistik Bimbingan Magang')

@section('content')
<div class="space-y-6">

    <!-- Centralized Header -->
    <x-page-header
        title="Statistik Performa Bimbingan Magang"
        subtitle="Evaluasi kedisiplinan dan rasio persetujuan laporan harian peserta magang bimbingan Anda."
        icon="fa-solid fa-chart-column" />

    <!-- 5 Centralized KPI Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
        <x-stat-card
            title="Total Peserta Bimbingan"
            value="{{ $totalSupervisedCount }}"
            trend="Aktif terdaftar"
            trend-type="up"
            icon="fa-solid fa-user-group"
            color="blue" />

        <x-stat-card
            title="Rata-rata Approval Rate"
            value="{{ $avgApprovalRate }}%"
            trend="+12% dari bulan lalu"
            trend-type="up"
            icon="fa-solid fa-circle-check"
            color="emerald" />

        <x-stat-card
            title="Total Log Masuk"
            value="{{ $totalReportsAll }}"
            trend="Seluruh peserta"
            trend-type="up"
            icon="fa-regular fa-file-lines"
            color="navy" />

        <x-stat-card
            title="Menunggu Persetujuan"
            value="{{ $totalPendingAll }}"
            trend="Perlu ditinjau"
            trend-type="down"
            icon="fa-solid fa-clock-rotate-left"
            color="amber" />

        <x-stat-card
            title="Total Revisi"
            value="{{ $totalRevisionAll }}"
            trend="Perlu perbaikan"
            trend-type="down"
            icon="fa-solid fa-rotate"
            color="purple" />
    </div>

    <!-- Stats Table per Intern wrapped in Centralized Card -->
    <x-card padding="p-0">
        <x-slot name="title">Rekapitulasi Persetujuan Laporan per Peserta</x-slot>
        <x-slot name="subtitle">Ringkasan statistik tingkat persetujuan dan heatmap aktivitas 30 hari terakhir.</x-slot>
        <x-slot name="icon">fa-solid fa-sliders</x-slot>
        <x-slot name="headerAction">
            <span class="bg-slate-100 border border-slate-200 text-slate-700 rounded-xl px-3 py-1 text-xs font-bold uppercase tracking-wider">Total: {{ count($statsPerIntern) }} Peserta</span>
        </x-slot>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100 text-xs font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-4 px-6">Peserta Magang & Instansi</th>
                        <th class="py-4 px-4">Total Log</th>
                        <th class="py-4 px-4 text-emerald-600">Approved</th>
                        <th class="py-4 px-4 text-amber-600">Pending</th>
                        <th class="py-4 px-4 text-sky-600">Revisi</th>
                        <th class="py-4 px-4">Heatmap (30 Hari)</th>
                        <th class="py-4 px-4">Rasio Approval</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm font-semibold text-slate-700">
                    @forelse($statsPerIntern as $stat)
                    @php
                    $firstLetter = strtoupper(substr($stat['name'], 0, 1));

                    $avatarClass = 'bg-blue-50 text-[#005B9C] border border-blue-100';
                    if (in_array($firstLetter, ['G','H','I','J','K','L','M'])) {
                        $avatarClass = 'bg-emerald-50 text-emerald-600 border border-emerald-100';
                    } elseif (in_array($firstLetter, ['N','O','P','Q','R','S','T'])) {
                        $avatarClass = 'bg-purple-50 text-purple-600 border border-purple-100';
                    } elseif (in_array($firstLetter, ['U','V','W','X','Y','Z'])) {
                        $avatarClass = 'bg-amber-50 text-amber-600 border border-amber-100';
                    }
                    @endphp
                    <tr class="hover:bg-slate-50/60 transition">
                        <!-- Column 1: Peserta & Instansi -->
                        <td class="py-4 px-6">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-2xl flex items-center justify-center font-black text-sm shrink-0 {{ $avatarClass }}">
                                    {{ $firstLetter }}
                                </div>
                                <div>
                                    <p class="font-extrabold text-slate-900 text-sm leading-snug">{{ $stat['name'] }}</p>
                                    <p class="text-xs text-slate-500 font-medium mt-0.5">{{ $stat['institution'] }}</p>
                                </div>
                            </div>
                        </td>

                        <!-- Column 2: Total Log Masuk -->
                        <td class="py-4 px-4 text-slate-800 font-extrabold whitespace-nowrap">
                            {{ $stat['total'] }} Laporan
                        </td>

                        <!-- Column 3: Approved -->
                        <td class="py-4 px-4 text-emerald-600 font-extrabold text-sm">
                            {{ $stat['approved'] }}
                        </td>

                        <!-- Column 4: Pending -->
                        <td class="py-4 px-4 text-amber-600 font-extrabold text-sm">
                            {{ $stat['pending'] }}
                        </td>

                        <!-- Column 5: Revision -->
                        <td class="py-4 px-4 text-sky-600 font-extrabold text-sm">
                            {{ $stat['revision'] }}
                        </td>

                        <!-- Column 6: Heatmap Aktivitas 30 Hari -->
                        <td class="py-4 px-4">
                            <div class="flex items-center gap-1 flex-wrap max-w-[140px]">
                                @foreach($stat['heatmap'] as $day)
                                @php
                                $color = match($day['status']) {
                                    'approved' => 'bg-emerald-500 hover:bg-emerald-600',
                                    'pending' => 'bg-amber-400 hover:bg-amber-500',
                                    'revision' => 'bg-sky-400 hover:bg-sky-500',
                                    'rejected' => 'bg-rose-500 hover:bg-rose-600',
                                    default => 'bg-slate-100 hover:bg-slate-200',
                                };
                                @endphp
                                <div class="relative group">
                                    <span class="w-3 h-3 rounded-xs block {{ $color }} transition-colors cursor-pointer"></span>
                                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-1.5 hidden group-hover:block bg-slate-900 text-white text-xs px-2.5 py-1 rounded-lg whitespace-nowrap z-30 font-bold shadow-md">
                                        {{ $day['date'] }}: {{ $day['status'] === 'none' ? 'No Activity' : ucfirst($day['status']) }}
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </td>

                        <!-- Column 7: Rasio Approval -->
                        <td class="py-4 px-4">
                            <div class="w-28 space-y-1">
                                <p class="font-extrabold text-slate-800 text-xs leading-none">{{ $stat['approval_rate'] }}%</p>
                                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                    <div class="h-2 bg-emerald-500 rounded-full" style="width: {{ $stat['approval_rate'] }}%"></div>
                                </div>
                            </div>
                        </td>

                        <!-- Column 8: Aksi -->
                        <td class="py-4 px-6 text-right">
                            <x-button
                                href="{{ route('supervisor.activities') }}?intern_id={{ $stat['user_id'] }}"
                                variant="outline"
                                size="sm"
                                icon="fa-regular fa-eye">
                                Detail
                            </x-button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-8">
                            <x-empty-state
                                title="Belum ada statistik bimbingan"
                                subtitle="Data rekapitulasi presensi dan logbook peserta bimbingan belum tersedia."
                                icon="fa-solid fa-chart-line" />
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Heatmap Legend Footer inside Table Card -->
        <div class="p-5 border-t border-slate-100 bg-slate-50/50 flex flex-wrap gap-4 items-center justify-between text-xs text-slate-600 font-bold rounded-b-3xl">
            <span class="uppercase tracking-wider">Keterangan Kalender Kontribusi (30 Hari)</span>
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-1.5">
                    <span class="w-3 h-3 rounded-xs bg-slate-100 block border border-slate-200"></span>
                    <span>Kosong</span>
                </div>
                <div class="flex items-center space-x-1.5">
                    <span class="w-3 h-3 rounded-xs bg-amber-400 block"></span>
                    <span>Pending</span>
                </div>
                <div class="flex items-center space-x-1.5">
                    <span class="w-3 h-3 rounded-xs bg-emerald-500 block"></span>
                    <span>Disetujui</span>
                </div>
                <div class="flex items-center space-x-1.5">
                    <span class="w-3 h-3 rounded-xs bg-sky-400 block"></span>
                    <span>Revisi</span>
                </div>
                <div class="flex items-center space-x-1.5">
                    <span class="w-3 h-3 rounded-xs bg-rose-500 block"></span>
                    <span>Ditolak</span>
                </div>
            </div>
        </div>
    </x-card>

    <!-- Insight Card at the Bottom -->
    <div class="bg-blue-50/70 border border-blue-100 rounded-3xl p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-xs">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-[#005B9C]/10 text-[#005B9C] flex items-center justify-center text-xl shrink-0">
                <i class="fa-regular fa-lightbulb"></i>
            </div>
            <div>
                <h4 class="font-extrabold text-slate-900 text-base">Rekomendasi Bimbingan</h4>
                <p class="text-sm font-medium text-slate-600 mt-0.5">Approval rate keseluruhan dapat ditingkatkan dengan memberikan dorongan bagi peserta magang untuk mengisi logbook secara berkala.</p>
            </div>
        </div>
    </div>

</div>
@endsection