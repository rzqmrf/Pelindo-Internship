@extends('layouts.app')

@section('page_title', 'Dashboard Peserta Magang')

@section('content')
<div class="space-y-6">

    <!-- Hero Banner matching exact screenshot design -->
    <div class="bg-gradient-to-r from-blue-100/90 via-indigo-50/80 to-blue-50/60 rounded-3xl p-7 border border-blue-100/80 shadow-xs relative overflow-hidden">
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">

            <div class="space-y-3 flex-1">
                <div class="flex items-center space-x-2">
                    <span class="px-3 py-1 bg-blue-600 text-white rounded-full text-[10px] font-extrabold uppercase tracking-wider">
                        PESERTA MAGANG
                    </span>
                    <span class="text-xs text-slate-500 font-semibold">&bull; {{ $internProfile?->division?->name ?? 'Divisi Teknologi Informasi & Digitalisasi' }}</span>
                </div>

                <h1 class="text-2xl lg:text-3xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                    Selamat Datang, {{ Auth::user()->name }} 👋
                </h1>

                <p class="text-xs text-slate-600 font-medium">
                    Pembimbing Lapangan: <strong class="text-blue-700 font-bold">{{ $internProfile?->supervisor?->name ?? 'Ahmad Rizky, S.T.' }}</strong>
                </p>
            </div>

            <!-- Logbook 3D Graphic & Action Box on Right -->
            <div class="flex items-center gap-6 shrink-0">
                <!-- 3D Logbook Graphic Illustration -->
                <div class="hidden sm:block relative w-32 h-28 transform hover:scale-105 transition">
                    <div class="w-24 h-28 bg-white rounded-2xl shadow-xl border border-blue-100 p-3 transform -rotate-6 flex flex-col justify-between">
                        <div class="space-y-1.5">
                            <div class="h-1.5 w-12 bg-blue-500 rounded-full"></div>
                            <div class="h-1.5 w-16 bg-slate-200 rounded-full"></div>
                            <div class="h-1.5 w-10 bg-slate-200 rounded-full"></div>
                        </div>
                        <div class="w-8 h-8 rounded-full bg-blue-500 text-white flex items-center justify-center text-xs font-bold mx-auto shadow-md">
                            <i class="fa-solid fa-check"></i>
                        </div>
                    </div>
                </div>

                <!-- Action Box Card -->
                <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 max-w-xs w-full space-y-3">
                    <div class="flex items-start space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-500 text-white flex items-center justify-center text-base shrink-0 shadow-sm">
                            <i class="fa-regular fa-pen-to-square"></i>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-800 text-xs">Isi Laporan Hari Ini</h3>
                            <p class="text-[10px] text-slate-500 mt-0.5 leading-snug">Catat aktivitas dan laporkan progres magangmu hari ini.</p>
                        </div>
                    </div>

                    <a href="{{ route('intern.activities.create') }}" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-extrabold rounded-xl text-xs transition shadow-sm flex items-center justify-center space-x-2 group">
                        <span>Mulai Isi Laporan</span>
                        <i class="fa-solid fa-chevron-right text-[10px] group-hover:translate-x-0.5 transition"></i>
                    </a>
                </div>
            </div>

        </div>
    </div>

    <!-- Certificate Ready Banner (if published) -->
    @if($certificate)
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-2xl p-4 shadow-md flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur text-white flex items-center justify-center text-lg font-bold">
                <i class="fa-solid fa-award"></i>
            </div>
            <div>
                <h3 class="font-extrabold text-xs">E-Sertifikat Resmi Magang Telah Terbit!</h3>
                <p class="text-[11px] text-blue-100">No: {{ $certificate->certificate_number }} &bull; Predikat telah disahkan oleh HR Pelindo.</p>
            </div>
        </div>
        <a href="{{ route('intern.certificate.index') }}" class="px-4 py-2 bg-white text-blue-700 font-bold rounded-xl text-xs hover:bg-blue-50 transition shadow-sm">
            Lihat & Unduh
        </a>
    </div>
    @endif

    <!-- Attendance & Daily Log Dual Status Banners -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Attendance Quick Card -->
        <div class="p-4 rounded-2xl border {{ $todayAttendance ? 'bg-emerald-50/80 border-emerald-200/80 text-emerald-900' : 'bg-blue-50/80 border-blue-200/80 text-blue-900' }} shadow-xs flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-full {{ $todayAttendance ? 'bg-emerald-500' : 'bg-blue-500' }} text-white flex items-center justify-center text-sm font-bold shadow-xs">
                    <i class="fa-solid {{ $todayAttendance ? 'fa-fingerprint' : 'fa-location-dot' }}"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-xs">
                        {{ $todayAttendance ? 'Presensi: ' . ($todayAttendance->check_in_time ? 'Masuk ' . substr($todayAttendance->check_in_time, 0, 5) : 'Hadir') : 'Presensi Hari Ini' }}
                    </h3>
                    <p class="text-[11px] {{ $todayAttendance ? 'text-emerald-700' : 'text-blue-700' }}">
                        {{ $todayAttendance ? 'Mode Kerja: ' . $todayAttendance->work_location : 'Belum melakukan Check-in kehadiran hari ini.' }}
                    </p>
                </div>
            </div>
            <a href="{{ route('intern.attendance.index') }}" class="px-3.5 py-1.5 {{ $todayAttendance ? 'bg-white text-emerald-700 border border-emerald-200' : 'bg-blue-600 text-white' }} font-bold rounded-xl text-xs transition shadow-xs">
                {{ $todayAttendance ? 'Detail' : 'Check-In' }}
            </a>
        </div>

        <!-- Logbook Card -->
        @if(!$todayActivity)
        <div class="bg-amber-50/90 border border-amber-200/80 rounded-2xl p-4 text-amber-900 shadow-xs flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-full bg-amber-500 text-white flex items-center justify-center text-sm font-bold shadow-xs">
                    <i class="fa-solid fa-exclamation"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-xs">Logbook Hari Ini Belum Diisi</h3>
                    <p class="text-[11px] text-amber-700">Segera catat rincian pengerjaan tugas.</p>
                </div>
            </div>
            <a href="{{ route('intern.activities.create') }}" class="px-3.5 py-1.5 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-xl text-xs transition shadow-xs">
                Isi Sekarang
            </a>
        </div>
        @else
        <div class="bg-emerald-50/80 border border-emerald-200/80 rounded-2xl p-4 text-emerald-900 shadow-xs flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center text-sm font-bold shadow-xs">
                    <i class="fa-solid fa-check"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-xs">Logbook Hari Ini Terisi</h3>
                    <p class="text-[11px] text-emerald-700">Status: <strong class="uppercase text-emerald-800">{{ $todayActivity->status }}</strong></p>
                </div>
            </div>
            <a href="{{ route('intern.activities.index') }}" class="px-3.5 py-1.5 bg-white text-emerald-700 border border-emerald-200 font-extrabold rounded-xl text-xs hover:bg-emerald-50 transition shadow-xs">
                Detail
            </a>
        </div>
        @endif
    </div>

    <!-- 4 Metric Cards Matching Screenshot -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        <!-- Metric 1: Status Hari Ini -->
        <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-xs flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">STATUS HARI INI</p>
                <div class="flex items-center space-x-2">
                    <h3 class="text-base font-black uppercase text-amber-500">
                        {{ $todayActivity ? $todayActivity->status : 'PENDING' }}
                    </h3>
                    <span class="px-2 py-0.5 rounded-md bg-amber-100 text-amber-800 text-[10px] font-extrabold">
                        {{ $todayActivity ? $todayActivity->work_location : 'WFO' }}
                    </span>
                </div>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center text-lg">
                <i class="fa-regular fa-calendar-check"></i>
            </div>
        </div>

        <!-- Metric 2: Total Laporan -->
        <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-xs flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">TOTAL LAPORAN</p>
                <h3 class="text-2xl font-black text-slate-800">{{ $totalSubmitted }}</h3>
                <p class="text-[10px] text-slate-400 font-semibold">Dari 20 hari</p>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
                <i class="fa-regular fa-file-lines"></i>
            </div>
        </div>

        <!-- Metric 3: Pending Review -->
        <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-xs flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">PENDING REVIEW</p>
                <h3 class="text-2xl font-black text-slate-800">{{ $pendingApprovalCount }}</h3>
                <p class="text-[10px] text-slate-400 font-semibold">Menunggu persetujuan</p>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-lg">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
        </div>

        <!-- Metric 4: Disetujui -->
        <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-xs flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">DISETUJUI</p>
                <h3 class="text-2xl font-black text-slate-800">{{ $approvedCount }}</h3>
                <p class="text-[10px] text-slate-400 font-semibold">Laporan disetujui</p>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
                <i class="fa-regular fa-circle-check"></i>
            </div>
        </div>

    </div>

    <!-- 2 Column Main Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left Column: Riwayat Aktivitas Terakhir (Timeline list) -->
        <div class="lg:col-span-2 bg-white rounded-3xl p-6 border border-slate-100 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-50 pb-3">
                <h3 class="font-extrabold text-slate-800 text-sm">Riwayat Aktivitas Terakhir</h3>
                <a href="{{ route('intern.activities.index') }}" class="text-xs font-bold text-blue-600 hover:underline">Lihat Semua</a>
            </div>

            <div class="space-y-4">
                @forelse($recentActivities as $act)
                <div class="p-4 bg-slate-50/50 rounded-2xl border border-slate-100/80 flex items-start justify-between gap-4">
                    <div class="flex items-start space-x-3.5">
                        <!-- Date Badge Pill -->
                        <div class="w-12 h-12 rounded-2xl bg-white border border-slate-200 text-center flex flex-col items-center justify-center shrink-0 shadow-2xs">
                            <span class="text-sm font-black text-slate-800 leading-none">{{ $act->date->format('d') }}</span>
                            <span class="text-[9px] font-bold text-slate-400 uppercase mt-0.5 leading-none">{{ strtoupper($act->date->format('M')) }}</span>
                        </div>

                        <div class="space-y-1">
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase bg-blue-100 text-blue-800">
                                    {{ $act->work_location }}
                                </span>
                            </div>
                            <p class="text-xs font-medium text-slate-700 leading-relaxed">{{ $act->activity_description }}</p>
                        </div>
                    </div>

                    <!-- Status Pill right -->
                    <div class="shrink-0">
                        @php
                        $statusStyle = match($act->status) {
                        'approved' => 'bg-emerald-100/80 text-emerald-800 border-emerald-200',
                        'pending' => 'bg-amber-100/80 text-amber-800 border-amber-200',
                        'revision' => 'bg-blue-100/80 text-blue-800 border-blue-200',
                        'rejected' => 'bg-rose-100/80 text-rose-800 border-rose-200',
                        default => 'bg-slate-100 text-slate-600 border-slate-200',
                        };
                        @endphp
                        <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider border {{ $statusStyle }}">
                            {{ strtoupper($act->status) }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="py-8 text-center text-slate-400 text-xs">Belum ada aktivitas terdaftar.</div>
                @endforelse
            </div>
        </div>

        <!-- Right Column: Progress Magang & Motivation Card -->
        <div class="space-y-6">

            <!-- Papan Informasi HR Card -->
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-xs space-y-4">
                <div class="flex items-center justify-between border-b border-slate-50 pb-3">
                    <h3 class="font-extrabold text-slate-800 text-sm flex items-center gap-1.5">
                        <i class="fa-solid fa-bullhorn text-blue-600"></i>
                        <span>Pengumuman HR</span>
                    </h3>
                    <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                </div>

                <div class="space-y-3.5">
                    @forelse($announcements as $ann)
                    @php
                    $annStyle = match($ann->category) {
                    'important' => 'bg-rose-50/50 border-rose-100',
                    'event' => 'bg-purple-50/50 border-purple-100',
                    default => 'bg-slate-50/50 border-slate-200/60',
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
                    <div class="p-3.5 rounded-2xl border {{ $annStyle }} space-y-2">
                        <div class="flex items-center justify-between gap-1.5">
                            <span class="px-2 py-0.5 text-[8px] font-black uppercase tracking-wider rounded {{ $categoryBadge }}">
                                {{ $categoryLabel }}
                            </span>
                            <span class="text-[9px] text-slate-400 font-bold">
                                {{ $ann->created_at ? $ann->created_at->diffForHumans() : '-' }}
                            </span>
                        </div>
                        <h4 class="font-extrabold text-slate-800 text-xs leading-snug">{{ $ann->title }}</h4>
                        <p class="text-[10px] text-slate-500 font-medium leading-relaxed whitespace-pre-line">{{ Str::limit($ann->content, 120) }}</p>
                    </div>
                    @empty
                    <div class="py-6 text-center text-slate-400 text-[11px] font-bold">Tidak ada pengumuman terbaru dari HR.</div>
                    @endforelse
                </div>
            </div>

            <!-- Progress Magang Card -->
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-xs space-y-4">
                <div class="flex items-center justify-between border-b border-slate-50 pb-3">
                    <h3 class="font-extrabold text-slate-800 text-sm">Progress Magang</h3>
                    <span class="px-2.5 py-1 bg-slate-50 border border-slate-200 text-slate-600 rounded-xl text-[11px] font-bold">20 Hari Kerja</span>
                </div>

                <div class="py-2 flex items-center justify-around">
                    <!-- Gauge Donut Chart -->
                    <div class="relative w-28 h-28 flex items-center justify-center">
                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                            <path class="text-slate-100" stroke-width="3.5" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <path class="text-blue-600" stroke-dasharray="30, 100" stroke-width="3.5" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        </svg>
                        <div class="absolute text-center">
                            <span class="text-base font-black text-slate-900 block leading-tight">30%</span>
                            <span class="text-[9px] font-bold text-slate-400 uppercase">Selesai</span>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div class="text-right">
                            <span class="text-xl font-black text-slate-800">6 / 20</span>
                            <span class="text-xs text-slate-400 font-bold"> Hari Kerja</span>
                        </div>
                        <div class="w-32 bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: 30%"></div>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center space-x-2 text-xs text-slate-500 font-semibold">
                    <i class="fa-regular fa-calendar text-blue-600"></i>
                    <span>Periode Magang: <strong>20 Juli 2026 - 14 Agustus 2026</strong></span>
                </div>
            </div>

            <!-- Motivation Rocket Banner Card -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-3xl p-5 border border-blue-100/80 shadow-xs relative overflow-hidden flex items-center justify-between">
                <div class="space-y-1 z-10 max-w-[200px]">
                    <div class="flex items-center space-x-1.5 text-blue-700 font-extrabold text-xs">
                        <i class="fa-solid fa-[#005B9C] fa-bullseye"></i>
                        <span>Tetap Semangat!</span>
                    </div>
                    <p class="text-[11px] text-slate-600 font-medium leading-relaxed">
                        Konsistensi hari ini adalah pencapaian besar di masa depan.
                    </p>
                </div>

                <div class="text-blue-500 text-4xl transform rotate-12 opacity-80">
                    <i class="fa-solid fa-rocket text-blue-600"></i>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection