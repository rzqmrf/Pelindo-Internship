@extends('layouts.app')

@section('page_title', 'Dashboard HR Admin')

@section('content')
<div class="space-y-6">

    <!-- Welcome Banner Card -->
    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <span>Selamat Datang, Admin HR Pelindo</span>
                <span>👋</span>
            </h1>
            <p class="text-xs font-semibold text-slate-400 mt-1">Monitor program magang, kelola peserta, pembimbing, dan laporan dengan mudah.</p>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <a href="{{ route('admin.interns.index') }}" class="px-5 py-2.5 bg-[#0B63E5] hover:bg-blue-700 text-white font-extrabold rounded-2xl text-xs transition shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-user-plus text-xs"></i>
                <span>Tambah Peserta</span>
            </a>
        </div>
    </div>

    <!-- 4 KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

        <!-- Active Interns -->
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Peserta Magang Aktif</p>
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

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Line Chart -->
        <div class="lg:col-span-2 bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-black text-slate-800 text-sm">Tren Pengiriman Aktivitas ({{ $days }} Hari Terakhir)</h3>
                    <p class="text-xs font-semibold text-slate-400">Grafik jumlah logbook yang dikirim per hari</p>
                </div>

                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="px-3.5 py-1.5 bg-slate-50 border border-slate-200/80 rounded-xl text-xs font-extrabold text-slate-600 flex items-center space-x-1.5 hover:bg-slate-100 transition">
                        <span>{{ $days }} Hari Terakhir</span>
                        <i class="fa-solid fa-chevron-down text-[9px] text-slate-400"></i>
                    </button>
                    <div x-show="open" @click.outside="open = false" x-transition.origin.top.right
                        class="absolute right-0 mt-1 w-40 bg-white border border-slate-100 rounded-xl shadow-lg py-1.5 z-20 text-xs text-slate-700 font-semibold" x-cloak>
                        <a href="{{ route('admin.dashboard', ['days' => 7]) }}" class="block px-3 py-1.5 hover:bg-slate-50 @if($days == 7) font-bold text-[#0B63E5] bg-slate-50/50 @endif">7 Hari Terakhir</a>
                        <a href="{{ route('admin.dashboard', ['days' => 14]) }}" class="block px-3 py-1.5 hover:bg-slate-50 @if($days == 14) font-bold text-[#0B63E5] bg-slate-50/50 @endif">14 Hari Terakhir</a>
                        <a href="{{ route('admin.dashboard', ['days' => 30]) }}" class="block px-3 py-1.5 hover:bg-slate-50 @if($days == 30) font-bold text-[#0B63E5] bg-slate-50/50 @endif">30 Hari Terakhir</a>
                    </div>
                </div>
            </div>
            <div class="h-64 relative">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        <!-- Doughnut Chart -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="font-black text-slate-800 text-sm">Status Approval Laporan</h3>
                <p class="text-xs font-semibold text-slate-400">Progres status verifikasi pembimbing</p>
            </div>

            <div class="h-44 relative my-6">
                <canvas id="statusChart"></canvas>
                <!-- Center text overlay -->
                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none mt-1">
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider leading-none">Total</span>
                    <span class="text-2xl font-black text-slate-800 leading-snug my-0.5">{{ array_sum($statusCounts) }}</span>
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider leading-none">Laporan</span>
                </div>
            </div>

            @php
            $totalReports = array_sum($statusCounts);
            $pctApproved = $totalReports > 0 ? round(($statusCounts['Approved'] / $totalReports) * 100) : 0;
            $pctPending = $totalReports > 0 ? round(($statusCounts['Pending'] / $totalReports) * 100) : 0;
            $pctRevision = $totalReports > 0 ? round(($statusCounts['Revision'] / $totalReports) * 100) : 0;
            $pctRejected = $totalReports > 0 ? round(($statusCounts['Rejected'] / $totalReports) * 100) : 0;
            @endphp
            <div class="grid grid-cols-2 gap-x-4 gap-y-3 text-[11px] font-bold text-slate-600 pt-4 border-t border-slate-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-1.5 overflow-hidden">
                        <span class="w-2 h-2 rounded-full bg-[#10B981] shrink-0"></span>
                        <span class="text-slate-400 font-semibold truncate">Approved ({{ $statusCounts['Approved'] }})</span>
                    </div>
                    <span class="text-slate-800 font-extrabold">{{ $pctApproved }}%</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-1.5 overflow-hidden">
                        <span class="w-2 h-2 rounded-full bg-[#F59E0B] shrink-0"></span>
                        <span class="text-slate-400 font-semibold truncate">Pending ({{ $statusCounts['Pending'] }})</span>
                    </div>
                    <span class="text-slate-800 font-extrabold">{{ $pctPending }}%</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-1.5 overflow-hidden">
                        <span class="w-2 h-2 rounded-full bg-[#3B82F6] shrink-0"></span>
                        <span class="text-slate-400 font-semibold truncate">Revision ({{ $statusCounts['Revision'] }})</span>
                    </div>
                    <span class="text-slate-800 font-extrabold">{{ $pctRevision }}%</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-1.5 overflow-hidden">
                        <span class="w-2 h-2 rounded-full bg-[#EF4444] shrink-0"></span>
                        <span class="text-slate-400 font-semibold truncate">Rejected ({{ $statusCounts['Rejected'] }})</span>
                    </div>
                    <span class="text-slate-800 font-extrabold">{{ $pctRejected }}%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Division Table Card (Full Width) -->
    @php
    $sortedDivisions = collect($divisionsData)->sortByDesc('active_interns_count')->values();
    @endphp
    <div x-data="{ showAllDivisions: false }" class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-6">
        <div class="border-b border-slate-50 pb-4">
            <h3 class="font-black text-slate-800 text-sm">Distribusi Peserta Magang per Divisi</h3>
            <p class="text-xs font-semibold text-slate-400">Jumlah peserta magang yang berada di setiap divisi</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-wider">
                        <th class="py-3 px-4">Divisi</th>
                        <th class="py-3 px-4">Peserta Aktif</th>
                        <th class="py-3 px-4">Pembimbing</th>
                        <th class="py-3 px-4">Laporan Dikirim</th>
                        <th class="py-3 px-4">Menunggu Approval</th>
                        <th class="py-3 px-4">Progress</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-xs font-bold text-slate-700">
                    @foreach($sortedDivisions as $index => $div)
                    @php
                    // Mapping icons
                    $iconClass = 'fa-solid fa-sitemap text-slate-600 bg-slate-50';
                    if (Str::contains($div['name'], ['SDM', 'HR', 'Human Capital', 'Umum'])) {
                    $iconClass = 'fa-solid fa-users text-blue-600 bg-blue-50';
                    } elseif (Str::contains($div['name'], ['Teknologi', 'IT', 'Digitalisasi'])) {
                    $iconClass = 'fa-solid fa-code text-emerald-600 bg-emerald-50';
                    } elseif (Str::contains($div['name'], ['Keuangan', 'Fin', 'Perbendaharaan'])) {
                    $iconClass = 'fa-solid fa-wallet text-indigo-600 bg-indigo-50';
                    } elseif (Str::contains($div['name'], ['Operasional', 'Ops', 'Logistik'])) {
                    $iconClass = 'fa-solid fa-gears text-orange-600 bg-orange-50';
                    }
                    @endphp
                    <tr class="hover:bg-slate-50/50 transition" x-show="{{ $index }} < 4 || showAllDivisions" x-cloak>
                        <td class="py-3.5 px-4 flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-xl flex items-center justify-center text-xs shrink-0 {{ explode(' ', $iconClass)[2] }}">
                                <i class="{{ implode(' ', array_slice(explode(' ', $iconClass), 0, 2)) }}"></i>
                            </div>
                            <span class="font-extrabold text-slate-800">
                                {{ str_replace('Divisi ', '', $div['name']) }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-slate-500 font-semibold">{{ $div['active_interns_count'] }} Peserta</td>
                        <td class="py-3.5 px-4 text-slate-500 font-semibold">{{ $div['supervisors_count'] }} Pembimbing</td>
                        <td class="py-3.5 px-4 text-slate-500 font-semibold">{{ $div['total_reports'] }} Laporan</td>
                        <td class="py-3.5 px-4 text-slate-500 font-semibold">{{ $div['pending_reports'] }} Laporan</td>
                        <td class="py-3.5 px-4">
                            <div class="flex items-center space-x-3">
                                <span class="text-slate-800 font-extrabold w-8 text-right">{{ $div['progress'] }}%</span>
                                <div class="w-28 bg-slate-100 rounded-full h-1.5 overflow-hidden shrink-0">
                                    <div class="bg-[#0B63E5] h-1.5 rounded-full" style="width: {{ $div['progress'] }}%"></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($sortedDivisions->count() > 4)
        <div class="flex justify-center pt-2">
            <button @click="showAllDivisions = !showAllDivisions" class="px-5 py-2 bg-white border border-slate-200 rounded-2xl text-xs font-bold text-slate-600 hover:text-slate-900 shadow-2xs hover:bg-slate-50 transition flex items-center space-x-1.5">
                <span x-text="showAllDivisions ? 'Sembunyikan' : 'Lihat Semua Divisi'">Lihat Semua Divisi</span>
                <i class="fa-solid text-[9px] transition-transform duration-200" :class="showAllDivisions ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
            </button>
        </div>
        @endif
    </div>

    <!-- Tables Row: Pending Approvals & Upcoming Internship End (Side-by-Side) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Pending Approvals Table -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-50 pb-3">
                <h3 class="font-black text-slate-800 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-hourglass-half text-amber-500"></i>
                    <span>Antrean Approval Terbaru</span>
                </h3>
                <a href="{{ route('admin.monitoring') }}?status=pending" class="text-xs font-bold text-[#0B63E5] hover:underline">Lihat Semua</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-wider">
                            <th class="py-2.5 px-3">Peserta</th>
                            <th class="py-2.5 px-3">Tanggal</th>
                            <th class="py-2.5 px-3">Lokasi</th>
                            <th class="py-2.5 px-3">Pembimbing</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 text-xs font-bold text-slate-700">
                        @forelse($pendingActivities as $act)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-3 px-3">
                                <p class="font-extrabold text-slate-800">{{ $act->intern?->name }}</p>
                                <p class="text-[10px] text-slate-400 font-semibold">{{ $act->intern?->internProfile?->institution }}</p>
                            </td>
                            <td class="py-3 px-3 text-slate-500 font-semibold">
                                {{ $act->date->format('d/m/Y') }}
                            </td>
                            <td class="py-3 px-3">
                                <span class="px-2 py-0.5 rounded-md text-[9px] font-extrabold uppercase {{ $act->work_location === 'WFO' ? 'bg-blue-50 text-blue-700' : ($act->work_location === 'WFH' ? 'bg-purple-50 text-purple-700' : 'bg-amber-50 text-amber-700') }}">
                                    {{ $act->work_location }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-slate-500 font-semibold">
                                {{ $act->supervisor?->name ?? 'Belum Diplot' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-6 text-center text-slate-400 text-xs font-semibold">Tidak ada antrean pending saat ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Upcoming Internship End Table -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-50 pb-3">
                <h3 class="font-black text-slate-800 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-calendar-xmark text-rose-500"></i>
                    <span>Peserta Akan Selesai Magang (&le; 30 Hari)</span>
                </h3>
                <a href="{{ route('admin.interns.index') }}" class="text-xs font-bold text-[#0B63E5] hover:underline">Kelola Peserta</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-wider">
                            <th class="py-2.5 px-3">Peserta</th>
                            <th class="py-2.5 px-3">Divisi</th>
                            <th class="py-2.5 px-3">Selesai Magang</th>
                            <th class="py-2.5 px-3">Sisa Hari</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 text-xs font-bold text-slate-700">
                        @forelse($upcomingEndingInterns as $profile)
                        @php
                        $daysLeft = \Carbon\Carbon::today()->diffInDays($profile->end_date, false);
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-3 px-3">
                                <p class="font-extrabold text-slate-800">{{ $profile->user?->name }}</p>
                                <p class="text-[10px] text-slate-400 font-semibold">{{ $profile->institution }}</p>
                            </td>
                            <td class="py-3 px-3 text-slate-500 font-semibold">
                                {{ $profile->division?->code ?? '-' }}
                            </td>
                            <td class="py-3 px-3 text-slate-500 font-semibold">
                                {{ $profile->end_date->format('d M Y') }}
                            </td>
                            <td class="py-3 px-3">
                                <span class="px-2.5 py-0.5 rounded-md text-[10px] font-extrabold {{ $daysLeft <= 7 ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $daysLeft }} Hari
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-6 text-center text-slate-400 text-xs font-semibold">Tidak ada peserta yang selesai magang dalam 30 hari ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Line Chart
        const ctxTrend = document.getElementById('trendChart').getContext('2d');
        const trendGradient = ctxTrend.createLinearGradient(0, 0, 0, 240);
        trendGradient.addColorStop(0, 'rgba(11, 99, 229, 0.15)');
        trendGradient.addColorStop(1, 'rgba(11, 99, 229, 0.0)');

        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: {
                    !!json_encode($trendLabels) !!
                },
                datasets: [{
                    label: 'Jumlah Laporan Masuk',
                    data: {
                        !!json_encode($trendData) !!
                    },
                    borderColor: '#0B63E5',
                    backgroundColor: trendGradient,
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2.5,
                    pointBackgroundColor: '#0B63E5',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 1.5,
                    pointRadius: 3,
                    pointHoverRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 10,
                                weight: '600',
                                family: 'Plus Jakarta Sans'
                            },
                            color: '#94a3b8'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            font: {
                                size: 10,
                                weight: '600',
                                family: 'Plus Jakarta Sans'
                            },
                            color: '#94a3b8'
                        },
                        grid: {
                            color: '#f8fafc',
                            drawBorder: false
                        }
                    }
                }
            }
        });

        // Doughnut Chart
        const ctxStatus = document.getElementById('statusChart').getContext('2d');
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: ['Approved', 'Pending', 'Revision', 'Rejected'],
                datasets: [{
                    data: [{
                            {
                                $statusCounts['Approved']
                            }
                        },
                        {
                            {
                                $statusCounts['Pending']
                            }
                        },
                        {
                            {
                                $statusCounts['Revision']
                            }
                        },
                        {
                            {
                                $statusCounts['Rejected']
                            }
                        }
                    ],
                    backgroundColor: ['#10B981', '#F59E0B', '#3B82F6', '#EF4444'],
                    borderWidth: 3,
                    borderColor: '#ffffff',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                cutout: '76%'
            }
        });
    });
</script>
@endpush
@endsection