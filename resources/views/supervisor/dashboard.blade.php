@extends('layouts.app')

@section('page_title', 'Dashboard Pembimbing Lapangan')

@section('content')
<div class="space-y-6">

    <!-- Hero Banner matching Supervisor screenshot -->
    <div class="bg-gradient-to-r from-blue-100/90 via-indigo-50/80 to-blue-50/60 rounded-3xl p-7 border border-blue-100/80 shadow-xs relative overflow-hidden">
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">

            <div class="space-y-4 flex-1">
                <div class="flex items-center space-x-2">
                    <span class="px-3 py-1 bg-blue-600 text-white rounded-full text-[10px] font-extrabold uppercase tracking-wider">
                        PELINDO FIELD SUPERVISOR PANEL
                    </span>
                </div>

                <h1 class="text-2xl lg:text-3xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                    Selamat Datang, {{ Auth::user()->name }}! 👋
                </h1>

                <p class="text-xs text-slate-600 font-medium max-w-xl leading-relaxed">
                    Monitoring bimbingan magang harian, persetujuan laporan aktivitas, serta evaluasi progres mahasiswa/siswa magang di divisi Anda.
                </p>

                <!-- Widgets inside Hero Card bottom -->
                <div class="flex flex-wrap items-center gap-4 pt-1">
                    <div class="px-4 py-2.5 bg-white/80 backdrop-blur-md rounded-2xl border border-slate-100 text-xs font-semibold text-slate-700 flex items-center space-x-2">
                        <i class="fa-regular fa-calendar text-blue-600"></i>
                        <div>
                            <p class="text-[9px] font-bold text-slate-400 uppercase">Periode Magang</p>
                            <p class="text-xs font-black text-slate-800">20 Juli 2026 - 14 Agustus 2026</p>
                        </div>
                    </div>

                    <div class="px-4 py-2.5 bg-white/80 backdrop-blur-md rounded-2xl border border-slate-100 text-xs font-semibold text-slate-700 flex items-center space-x-3 min-w-[200px]">
                        <div>
                            <p class="text-[9px] font-bold text-slate-400 uppercase">Progres Hari Kerja</p>
                            <p class="text-xs font-black text-slate-800">6 / 20 hari</p>
                        </div>
                        <div class="flex-1 space-y-1">
                            <div class="w-full bg-slate-200 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-blue-600 h-1.5 rounded-full" style="width: 30%"></div>
                            </div>
                            <span class="text-[9px] font-extrabold text-blue-600 block text-right">30%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Graphic & Dropdown Button Right -->
            <div class="flex items-center gap-6 shrink-0">
                <!-- 3D Clipboard Graphic Illustration -->
                <div class="hidden sm:block relative w-32 h-28 transform hover:scale-105 transition">
                    <div class="w-24 h-28 bg-white rounded-2xl shadow-xl border border-blue-100 p-3 transform rotate-3 flex flex-col justify-between">
                        <div class="space-y-1.5">
                            <div class="h-1.5 w-10 bg-blue-500 rounded-full"></div>
                            <div class="h-1.5 w-14 bg-slate-200 rounded-full"></div>
                            <div class="h-1.5 w-12 bg-slate-200 rounded-full"></div>
                        </div>
                        <div class="w-8 h-8 rounded-full bg-blue-500 text-white flex items-center justify-center text-xs font-bold mx-auto shadow-md">
                            <i class="fa-solid fa-list-check"></i>
                        </div>
                    </div>
                </div>

                <!-- Action Dropdown Button -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="px-5 py-3 bg-white text-slate-800 hover:bg-slate-50 font-extrabold rounded-2xl text-xs shadow-xs border border-slate-200 flex items-center space-x-2">
                        <i class="fa-solid fa-list-check text-blue-600"></i>
                        <span>Tinjau Laporan</span>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
                    </button>
                    <div x-show="open" @click.outside="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-xl border border-slate-100 py-1.5 z-30">
                        <a href="{{ route('supervisor.activities') }}" class="block px-4 py-2 text-xs font-bold text-slate-700 hover:bg-blue-50 hover:text-blue-600">
                            Antrean Pending ({{ $pendingApprovalsCount }})
                        </a>
                        <a href="{{ route('supervisor.interns') }}" class="block px-4 py-2 text-xs font-bold text-slate-700 hover:bg-blue-50 hover:text-blue-600">
                            Peserta Bimbingan
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- 4 Metric Cards Matching Screenshot -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        <!-- Metric 1: My Interns -->
        <a href="{{ route('supervisor.interns') }}" class="bg-white rounded-2xl p-4 border border-slate-100 shadow-xs flex items-center justify-between hover:border-blue-200 transition group">
            <div class="space-y-0.5">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">MY INTERNS</p>
                <h3 class="text-2xl font-black text-slate-800">{{ $myInternsCount }}</h3>
                <p class="text-[10px] text-slate-400 font-semibold">Peserta Bimbingan</p>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-users"></i>
                </div>
                <i class="fa-solid fa-chevron-right text-xs text-slate-300 group-hover:text-blue-600 group-hover:translate-x-0.5 transition"></i>
            </div>
        </a>

        <!-- Metric 2: Pending Approvals -->
        <a href="{{ route('supervisor.activities') }}" class="bg-white rounded-2xl p-4 border border-slate-100 shadow-xs flex items-center justify-between hover:border-amber-200 transition group">
            <div class="space-y-0.5">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">PENDING APPROVALS</p>
                <h3 class="text-2xl font-black text-slate-800">{{ $pendingApprovalsCount }}</h3>
                <p class="text-[10px] text-slate-400 font-semibold">Perlu Peninjauan</p>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-hourglass-half"></i>
                </div>
                <i class="fa-solid fa-chevron-right text-xs text-slate-300 group-hover:text-amber-600 group-hover:translate-x-0.5 transition"></i>
            </div>
        </a>

        <!-- Metric 3: Today's Activities -->
        <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-xs flex items-center justify-between">
            <div class="space-y-0.5">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">TODAY'S ACTIVITIES</p>
                <h3 class="text-2xl font-black text-slate-800">{{ $todaysActivitiesCount }}</h3>
                <p class="text-[10px] text-slate-400 font-semibold">Laporan Masuk Hari Ini</p>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
                <i class="fa-regular fa-clipboard"></i>
            </div>
        </div>

        <!-- Metric 4: Total Approved -->
        <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-xs flex items-center justify-between">
            <div class="space-y-0.5">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">TOTAL APPROVED</p>
                <h3 class="text-2xl font-black text-slate-800">
                    {{ \App\Models\DailyActivity::where('supervisor_id', Auth::id())->where('status', 'approved')->count() }}
                </h3>
                <p class="text-[10px] text-slate-400 font-semibold">Telah Diverifikasi</p>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-lg">
                <i class="fa-regular fa-circle-check"></i>
            </div>
        </div>

    </div>

    <!-- 2 Column Main Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Left Column: Antrian Approval Laporan (Pending) -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-xs space-y-4 flex flex-col justify-between">
            <div class="space-y-4">
                <div class="flex items-center justify-between border-b border-slate-50 pb-3">
                    <div>
                        <h3 class="font-extrabold text-slate-800 text-sm flex items-center gap-2">
                            <i class="fa-solid fa-bell text-amber-500"></i>
                            <span>Antrian Approval Laporan (Pending)</span>
                        </h3>
                        <p class="text-[11px] text-slate-400 font-medium">Laporan harian yang membutuhkan keputusan Anda</p>
                    </div>
                    <a href="{{ route('supervisor.activities') }}?status=pending" class="text-xs font-bold text-blue-600 hover:underline">Lihat Semua</a>
                </div>

                <div class="space-y-3">
                    @forelse($pendingQueue as $act)
                    <div class="p-4 bg-slate-50/60 rounded-2xl border border-slate-100 flex items-center justify-between gap-4 hover:bg-slate-100/50 transition">
                        <div class="flex items-start space-x-3.5">
                            <div class="w-10 h-10 rounded-full bg-blue-500 text-white flex items-center justify-center font-black text-sm shrink-0 shadow-xs">
                                {{ strtoupper(substr($act->intern?->name ?? 'I', 0, 1)) }}
                            </div>
                            <div class="space-y-1">
                                <div class="flex items-center space-x-2">
                                    <span class="font-extrabold text-slate-800 text-xs">{{ $act->intern?->name }}</span>
                                    <span class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase bg-blue-100 text-blue-800">
                                        {{ $act->work_location }}
                                    </span>
                                </div>
                                <p class="text-xs font-medium text-slate-600 leading-relaxed line-clamp-2">{{ $act->activity_description }}</p>
                                <p class="text-[10px] text-slate-400 font-semibold">
                                    <i class="fa-regular fa-calendar mr-1"></i> {{ $act->date->format('d M Y') }} &bull; {{ $act->created_at ? $act->created_at->format('H:i') . ' WIB' : '' }}
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('supervisor.activities') }}" class="px-4 py-2 bg-[#005B9C] hover:bg-[#002B49] text-white text-xs font-extrabold rounded-xl transition shadow-xs shrink-0">
                            Tinjau
                        </a>
                    </div>
                    @empty
                    <div class="py-8 text-center text-slate-400 text-xs">
                        <i class="fa-solid fa-circle-check text-2xl text-emerald-400 mb-2 block"></i>
                        Semua laporan harian telah selesai diverifikasi!
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Bottom summary banner matching screenshot -->
            <a href="{{ route('supervisor.activities') }}" class="p-3 bg-slate-50 hover:bg-blue-50/50 rounded-2xl border border-slate-100 flex items-center justify-between text-xs text-slate-600 font-bold transition">
                <span>Total {{ $pendingQueue->count() }} laporan pending</span>
                <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
            </a>
        </div>

        <!-- Right Column: Activity Timeline Bimbingan -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-xs space-y-4 flex flex-col justify-between">
            <div class="space-y-4">
                <div class="border-b border-slate-50 pb-3">
                    <h3 class="font-extrabold text-slate-800 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-blue-600"></i>
                        <span>Activity Timeline Bimbingan</span>
                    </h3>
                    <p class="text-[11px] text-slate-400 font-medium">Riwayat aktivitas terbaru dari seluruh peserta bimbingan Anda</p>
                </div>

                <div class="space-y-3.5">
                    @forelse($activityTimeline as $act)
                    <div class="flex items-start space-x-3 text-xs">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs shrink-0
                                {{ $act->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($act->status === 'revision' ? 'bg-blue-100 text-blue-700' : ($act->status === 'rejected' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700')) }}">
                            <i class="fa-solid {{ $act->status === 'approved' ? 'fa-check' : ($act->status === 'revision' ? 'fa-rotate' : 'fa-hourglass') }}"></i>
                        </div>
                        <div class="flex-1 bg-slate-50/80 p-3.5 rounded-2xl border border-slate-100/80 space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="font-extrabold text-slate-800 text-xs">{{ $act->intern?->name }}</span>
                                <span class="text-[10px] text-slate-400 font-semibold">{{ $act->updated_at ? $act->updated_at->diffForHumans() : '' }}</span>
                            </div>
                            <p class="text-slate-600 font-medium line-clamp-1 text-xs">{{ $act->activity_description }}</p>
                            @if($act->supervisor_note)
                            <div class="p-2 bg-amber-50 border border-amber-200/60 rounded-xl text-[11px] text-amber-900 font-semibold mt-1.5">
                                <strong>Catatan:</strong> {{ $act->supervisor_note }}
                            </div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <p class="py-8 text-center text-slate-400 text-xs">Belum ada riwayat aktivitas bimbingan.</p>
                    @endforelse
                </div>
            </div>

            <!-- Bottom button matching screenshot -->
            <a href="{{ route('supervisor.activities') }}" class="p-3 bg-slate-50 hover:bg-blue-50/50 rounded-2xl border border-slate-100 flex items-center justify-between text-xs text-blue-600 font-bold transition">
                <span>Lihat Semua Aktivitas</span>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
            </a>
        </div>
    </div>

    <!-- Papan Informasi HR Card -->
    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-xs space-y-4">
        <div class="flex items-center justify-between border-b border-slate-50 pb-3">
            <h3 class="font-extrabold text-slate-800 text-sm flex items-center gap-2">
                <i class="fa-solid fa-bullhorn text-blue-600"></i>
                <span>Pengumuman HR Terkini</span>
            </h3>
            <span class="px-2.5 py-1 bg-slate-50 border border-slate-200 text-slate-650 rounded-xl text-[10px] font-extrabold uppercase">Informasi Pusat</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @forelse($announcements as $ann)
            @php
            $annStyle = match($ann->category) {
            'important' => 'bg-rose-50/50 border-rose-150/40',
            'event' => 'bg-purple-50/50 border-purple-150/40',
            default => 'bg-slate-50/50 border-slate-200/80',
            };
            $categoryLabel = match($ann->category) {
            'important' => 'Penting',
            'event' => 'Event',
            default => 'Info',
            };
            $categoryBadge = match($ann->category) {
            'important' => 'bg-rose-100 text-rose-700',
            'event' => 'bg-purple-100 text-purple-700',
            default => 'bg-blue-100 text-blue-700',
            };
            @endphp
            <div class="p-4 rounded-2xl border {{ $annStyle }} flex flex-col justify-between gap-3 hover:bg-slate-50/60 transition">
                <div class="space-y-2">
                    <div class="flex items-center justify-between gap-1.5">
                        <span class="px-2 py-0.5 text-[8px] font-black uppercase tracking-wider rounded {{ $categoryBadge }}">
                            {{ $categoryLabel }}
                        </span>
                        <span class="text-[9px] text-slate-400 font-bold">
                            {{ $ann->created_at ? $ann->created_at->diffForHumans() : '-' }}
                        </span>
                    </div>
                    <h4 class="font-extrabold text-slate-800 text-xs leading-snug">{{ $ann->title }}</h4>
                    <p class="text-[10px] text-slate-500 font-medium leading-relaxed whitespace-pre-line">{{ Str::limit($ann->content, 140) }}</p>
                </div>
                <div class="text-[9px] text-slate-400 font-bold border-t border-slate-200/40 pt-2 flex items-center gap-1.5">
                    <i class="fa-regular fa-user text-[10px]"></i>
                    <span>Oleh: {{ $ann->creator?->name ?? 'HR Admin' }}</span>
                </div>
            </div>
            @empty
            <div class="col-span-3 py-6 text-center text-slate-400 text-xs">Belum ada pengumuman terbaru dari HR.</div>
            @endforelse
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Chart 1: Activity Submission Trend (Line) -->
        <div class="lg:col-span-2 bg-white rounded-3xl p-6 border border-slate-100 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-50 pb-3">
                <div>
                    <h3 class="font-extrabold text-slate-800 text-sm">Tren Pengiriman Aktivitas (14 Hari)</h3>
                    <p class="text-xs text-slate-400">Grafik pengiriman logbook peserta bimbingan Anda</p>
                </div>
                <span class="px-2.5 py-1 bg-blue-50 text-blue-600 rounded-lg text-[10px] font-extrabold uppercase">Real-time</span>
            </div>
            <div class="h-60 relative">
                <canvas id="spvTrendChart"></canvas>
            </div>
        </div>

        <!-- Chart 2: Status Approval Doughnut -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-xs space-y-4 flex flex-col justify-between">
            <div class="border-b border-slate-50 pb-3">
                <h3 class="font-extrabold text-slate-800 text-sm">Status Approval Laporan</h3>
                <p class="text-xs text-slate-400">Distribusi status persetujuan</p>
            </div>

            <div class="h-44 relative my-auto">
                <canvas id="spvStatusChart"></canvas>
            </div>

            <div class="space-y-1.5 text-xs font-semibold text-slate-600 pt-2 border-t border-slate-50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        <span>Approved</span>
                    </div>
                    <span class="font-extrabold text-slate-800">3 (50%)</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                        <span>Pending</span>
                    </div>
                    <span class="font-extrabold text-slate-800">2 (33.3%)</span>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctxTrend = document.getElementById('spvTrendChart').getContext('2d');
        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: ['10 Jul', '12 Jul', '14 Jul', '16 Jul', '18 Jul', '20 Jul', '22 Jul', '23 Jul'],
                datasets: [{
                    label: 'Laporan Masuk',
                    data: [1, 2, 0, 3, 2, 4, 3, 2],
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.05)',
                    fill: true,
                    tension: 0.35,
                    borderWidth: 3,
                    pointBackgroundColor: '#3B82F6',
                    pointRadius: 4
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
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });

        const ctxStatus = document.getElementById('spvStatusChart').getContext('2d');
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: ['Approved', 'Pending', 'Revision'],
                datasets: [{
                    data: [3, 2, 1],
                    backgroundColor: ['#10B981', '#F59E0B', '#3B82F6'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
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
                cutout: '72%'
            }
        });
    });
</script>
@endpush
@endsection