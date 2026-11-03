<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#005B9C">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="manifest" href="/manifest.json">
    <title>{{ config('app.name', 'Pelindo Internship Management System') }}</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- FontAwesome 6, Chart.js, Tailwind CSS & Alpine.js -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        pelindo: {
                            navy: '#002B49',
                            blue: '#005B9C',
                            light: '#E6F0FA',
                            accent: '#00A3E0',
                        }
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-800" x-data="{ sidebarOpen: false }">

    <div class="min-h-screen flex">
        
        <!-- Sidebar Navigation: Clean White Theme -->
        <aside class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-slate-200 text-slate-700 flex flex-col transition-all duration-300 ease-in-out md:sticky md:top-0 md:h-screen shrink-0 shadow-sm"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">
            
            <!-- Brand Logo Header -->
            <div class="h-20 flex items-center px-6 justify-between bg-white border-b border-slate-100">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/pelindo-logo-transparent.png') }}" alt="PELINDO SOLUSI DIGITAL" class="h-10 w-auto object-contain">
                    <span class="px-2 py-0.5 bg-blue-100 text-[#005B9C] text-xs font-extrabold rounded-md uppercase tracking-wider">PRO</span>
                </div>
                <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-slate-700 p-1">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <!-- User Info Badge in Sidebar -->
            <div class="mx-5 my-4 p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between shadow-xs hover:bg-blue-50/50 hover:border-blue-200 transition cursor-pointer">
                <div class="flex items-center space-x-3.5 overflow-hidden min-w-0">
                    <div class="w-11 h-11 rounded-xl bg-[#005B9C] text-white flex items-center justify-center font-extrabold text-base shrink-0 shadow-xs">
                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="overflow-hidden min-w-0">
                        <p class="text-sm font-bold text-slate-900 truncate leading-snug">{{ Auth::user()->name }}</p>
                        <div class="flex items-center space-x-1.5 mt-0.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span class="text-xs font-bold text-[#005B9C] uppercase tracking-wider">
                                {{ Auth::user()->roleLabel() }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Navigation Links -->
            <nav class="flex-1 px-5 py-3 space-y-2 overflow-y-auto">
                
                @if(Auth::user()->isSuperAdmin())
                    <p class="px-3 text-xs font-bold tracking-wider uppercase text-slate-500 mb-2 mt-2">KONTROL SISTEM</p>

                    <a href="{{ route('superadmin.dashboard') }}" class="flex items-center space-x-3.5 px-4 py-3 rounded-xl font-bold text-sm transition {{ request()->routeIs('superadmin.dashboard') ? 'bg-[#005B9C] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                        <i class="fa-solid fa-server text-base w-6 text-center"></i>
                        <span>Kesehatan Sistem</span>
                    </a>

                    <a href="{{ route('superadmin.maintenance') }}" class="flex items-center justify-between px-4 py-3 rounded-xl font-bold text-sm transition {{ request()->routeIs('superadmin.maintenance*') ? 'bg-[#005B9C] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                        <div class="flex items-center space-x-3.5">
                            <i class="fa-solid fa-screwdriver-wrench text-base w-6 text-center"></i>
                            <span>Pemeliharaan &amp; Banner</span>
                        </div>
                        @if(!empty($globalMaintenanceState['enabled']))
                            <span class="px-2 py-0.5 text-[10px] font-extrabold rounded-md bg-rose-600 text-white uppercase">ON</span>
                        @endif
                    </a>

                    <a href="{{ route('superadmin.settings') }}" class="flex items-center space-x-3.5 px-4 py-3 rounded-xl font-bold text-sm transition {{ request()->routeIs('superadmin.settings*') ? 'bg-[#005B9C] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                        <i class="fa-solid fa-sliders text-base w-6 text-center"></i>
                        <span>Parameter &amp; Diagnostik</span>
                    </a>

                    <a href="{{ route('superadmin.users') }}" class="flex items-center space-x-3.5 px-4 py-3 rounded-xl font-bold text-sm transition {{ request()->routeIs('superadmin.users*') ? 'bg-[#005B9C] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                        <i class="fa-solid fa-users-gear text-base w-6 text-center"></i>
                        <span>Akun &amp; Impersonasi</span>
                    </a>

                    <a href="{{ route('superadmin.roles') }}" class="flex items-center space-x-3.5 px-4 py-3 rounded-xl font-bold text-sm transition {{ request()->routeIs('superadmin.roles*') ? 'bg-[#005B9C] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                        <i class="fa-solid fa-user-lock text-base w-6 text-center"></i>
                        <span>Role &amp; Hak Akses</span>
                    </a>

                    <a href="{{ route('superadmin.sessions') }}" class="flex items-center space-x-3.5 px-4 py-3 rounded-xl font-bold text-sm transition {{ request()->routeIs('superadmin.sessions*') ? 'bg-[#005B9C] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                        <i class="fa-solid fa-network-wired text-base w-6 text-center"></i>
                        <span>Sesi &amp; Antrean Job</span>
                    </a>

                    <a href="{{ route('superadmin.logs') }}" class="flex items-center space-x-3.5 px-4 py-3 rounded-xl font-bold text-sm transition {{ request()->routeIs('superadmin.logs*') ? 'bg-[#005B9C] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                        <i class="fa-solid fa-bug text-base w-6 text-center"></i>
                        <span>Log Error Laravel</span>
                    </a>

                    <a href="{{ route('superadmin.audit_logs') }}" class="flex items-center space-x-3.5 px-4 py-3 rounded-xl font-bold text-sm transition {{ request()->routeIs('superadmin.audit_logs*') ? 'bg-[#005B9C] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                        <i class="fa-solid fa-shield-halved text-base w-6 text-center"></i>
                        <span>Audit Keamanan</span>
                    </a>
                @endif

                @if(!Auth::user()->isSuperAdmin() && Auth::user()->usesAdminPortal())
                    <p class="px-3 text-xs font-bold tracking-wider uppercase text-slate-400 mb-2 mt-2">MENU UTAMA</p>
                    
                    @if(Auth::user()->hasPermission('admin.dashboard'))
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3.5 px-4 py-3 rounded-xl font-bold text-sm transition {{ request()->routeIs('admin.dashboard') ? 'bg-[#005B9C] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                            <i class="fa-solid fa-house text-base w-6 text-center"></i>
                            <span>Dashboard</span>
                        </a>
                    @endif

                    @if(Auth::user()->hasPermission('interns.manage'))
                        <a href="{{ route('admin.interns.index') }}" class="flex items-center space-x-3.5 px-4 py-3 rounded-xl font-bold text-sm transition {{ request()->routeIs('admin.interns*') ? 'bg-[#005B9C] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                            <i class="fa-solid fa-user-graduate text-base w-6 text-center"></i>
                            <span>Peserta Magang</span>
                        </a>
                    @endif

                    @if(Auth::user()->hasPermission('supervisors.manage'))
                        <a href="{{ route('admin.supervisors.index') }}" class="flex items-center space-x-3.5 px-4 py-3 rounded-xl font-bold text-sm transition {{ request()->routeIs('admin.supervisors*') ? 'bg-[#005B9C] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                            <i class="fa-solid fa-user-tie text-base w-6 text-center"></i>
                            <span>Pembimbing</span>
                        </a>
                    @endif

                    @if(Auth::user()->hasPermission('divisions.manage'))
                        <a href="{{ route('admin.divisions.index') }}" class="flex items-center space-x-3.5 px-4 py-3 rounded-xl font-bold text-sm transition {{ request()->routeIs('admin.divisions*') ? 'bg-[#005B9C] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                            <i class="fa-solid fa-sitemap text-base w-6 text-center"></i>
                            <span>Kelola Divisi</span>
                        </a>
                    @endif

                    @if(Auth::user()->hasPermission('attendance.view'))
                        <a href="{{ route('admin.attendance.index') }}" class="flex items-center space-x-3.5 px-4 py-3 rounded-xl font-bold text-sm transition {{ request()->routeIs('admin.attendance*') ? 'bg-[#005B9C] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                            <i class="fa-solid fa-clipboard-user text-base w-6 text-center"></i>
                            <span>Rekap Presensi</span>
                        </a>
                    @endif

                    @if(Auth::user()->hasPermission('monitoring.view'))
                        <a href="{{ route('admin.monitoring') }}" class="flex items-center space-x-3.5 px-4 py-3 rounded-xl font-bold text-sm transition {{ request()->routeIs('admin.monitoring*') ? 'bg-[#005B9C] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                            <i class="fa-solid fa-eye text-base w-6 text-center"></i>
                            <span>Monitoring Logbook</span>
                        </a>
                    @endif

                    @if(Auth::user()->hasPermission('announcements.manage'))
                        <a href="{{ route('admin.announcements.index') }}" class="flex items-center space-x-3.5 px-4 py-3 rounded-xl font-bold text-sm transition {{ request()->routeIs('admin.announcements*') ? 'bg-[#005B9C] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                            <i class="fa-solid fa-bullhorn text-base w-6 text-center"></i>
                            <span>Pengumuman HR</span>
                        </a>
                    @endif

                    @if(Auth::user()->hasPermission('allowance.manage'))
                        <a href="{{ route('admin.allowance.index') }}" class="flex items-center space-x-3.5 px-4 py-3 rounded-xl font-bold text-sm transition {{ request()->routeIs('admin.allowance*') ? 'bg-[#005B9C] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                            <i class="fa-solid fa-coins text-base w-6 text-center"></i>
                            <span>Klaim Uang Saku</span>
                        </a>
                    @endif

                    @if(Auth::user()->hasPermission('assessments.manage'))
                        <a href="{{ route('admin.assessments.index') }}" class="flex items-center space-x-3.5 px-4 py-3 rounded-xl font-bold text-sm transition {{ request()->routeIs('admin.assessments*') ? 'bg-[#005B9C] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                            <i class="fa-solid fa-award text-base w-6 text-center"></i>
                            <span>Kelola E-Sertifikat</span>
                        </a>
                    @endif

                    @if(Auth::user()->hasPermission('surveys.view'))
                        <a href="{{ route('admin.surveys.index') }}" class="flex items-center space-x-3.5 px-4 py-3 rounded-xl font-bold text-sm transition {{ request()->routeIs('admin.surveys*') ? 'bg-[#005B9C] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                            <i class="fa-solid fa-clipboard-question text-base w-6 text-center"></i>
                            <span>Survei Kepuasan</span>
                        </a>
                    @endif
                @endif

                @if(Auth::user()->isSupervisor())
                    <p class="px-3 text-xs font-bold tracking-wider uppercase text-slate-400 mb-2 mt-2">MENU UTAMA</p>

                    <a href="{{ route('supervisor.dashboard') }}" class="flex items-center space-x-3.5 px-4 py-3 rounded-xl font-bold text-sm transition {{ request()->routeIs('supervisor.dashboard') ? 'bg-[#005B9C] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                        <i class="fa-solid fa-house text-base w-6 text-center"></i>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('supervisor.interns') }}" class="flex items-center space-x-3.5 px-4 py-3 rounded-xl font-bold text-sm transition {{ request()->routeIs('supervisor.interns*') ? 'bg-[#005B9C] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                        <i class="fa-solid fa-users text-base w-6 text-center"></i>
                        <span>Peserta Bimbingan</span>
                    </a>

                    <a href="{{ route('supervisor.attendance.index') }}" class="flex items-center space-x-3.5 px-4 py-3 rounded-xl font-bold text-sm transition {{ request()->routeIs('supervisor.attendance*') ? 'bg-[#005B9C] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                        <i class="fa-solid fa-calendar-check text-base w-6 text-center"></i>
                        <span>Monitoring Presensi</span>
                    </a>

                    <a href="{{ route('supervisor.activities') }}" class="flex items-center space-x-3.5 px-4 py-3 rounded-xl font-bold text-sm transition {{ request()->routeIs('supervisor.activities*') ? 'bg-[#005B9C] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                        <i class="fa-solid fa-list-check text-base w-6 text-center"></i>
                        <span>Approval Logbook</span>
                    </a>

                    <a href="{{ route('supervisor.statistics') }}" class="flex items-center space-x-3.5 px-4 py-3 rounded-xl font-bold text-sm transition {{ request()->routeIs('supervisor.statistics*') ? 'bg-[#005B9C] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                        <i class="fa-solid fa-chart-line text-base w-6 text-center"></i>
                        <span>Statistik Magang</span>
                    </a>

                    <a href="{{ route('supervisor.leaves.index') }}" class="flex items-center space-x-3.5 px-4 py-3 rounded-xl font-bold text-sm transition {{ request()->routeIs('supervisor.leaves.index*') ? 'bg-[#005B9C] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                        <i class="fa-regular fa-calendar-check text-base w-6 text-center"></i>
                        <span>Approval Izin</span>
                    </a>

                    <a href="{{ route('supervisor.allowance.index') }}" class="flex items-center space-x-3.5 px-4 py-3 rounded-xl font-bold text-sm transition {{ request()->routeIs('supervisor.allowance*') ? 'bg-[#005B9C] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                        <i class="fa-solid fa-file-invoice-dollar text-base w-6 text-center"></i>
                        <span>Approval Uang Saku</span>
                    </a>

                    <a href="{{ route('supervisor.assessments.index') }}" class="flex items-center space-x-3.5 px-4 py-3 rounded-xl font-bold text-sm transition {{ request()->routeIs('supervisor.assessments*') ? 'bg-[#005B9C] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                        <i class="fa-solid fa-graduation-cap text-base w-6 text-center"></i>
                        <span>Penilaian & Nilai Akhir</span>
                    </a>
                @endif

                @if(Auth::user()->isIntern())
                    <p class="px-3 text-xs font-bold tracking-wider uppercase text-slate-400 mb-2 mt-2">MENU UTAMA</p>

                    <a href="{{ route('intern.dashboard') }}" class="flex items-center space-x-3.5 px-4 py-3 rounded-xl font-bold text-sm transition {{ request()->routeIs('intern.dashboard') ? 'bg-[#005B9C] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                        <i class="fa-solid fa-house text-base w-6 text-center"></i>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('intern.attendance.index') }}" class="flex items-center space-x-3.5 px-4 py-3 rounded-xl font-bold text-sm transition {{ request()->routeIs('intern.attendance*') ? 'bg-[#005B9C] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                        <i class="fa-solid fa-location-dot text-base w-6 text-center"></i>
                        <span>Presensi & Geofencing</span>
                    </a>

                    <a href="{{ route('intern.activities.create') }}" class="flex items-center space-x-3.5 px-4 py-3 rounded-xl font-bold text-sm transition {{ request()->routeIs('intern.activities.create') ? 'bg-[#005B9C] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                        <i class="fa-regular fa-pen-to-square text-base w-6 text-center"></i>
                        <span>Isi Aktivitas Hari Ini</span>
                    </a>

                    <a href="{{ route('intern.activities.index') }}" class="flex items-center space-x-3.5 px-4 py-3 rounded-xl font-bold text-sm transition {{ request()->routeIs('intern.activities.index') || request()->routeIs('intern.activities.edit') ? 'bg-[#005B9C] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                        <i class="fa-regular fa-clock text-base w-6 text-center"></i>
                        <span>Riwayat Logbook</span>
                    </a>

                    <a href="{{ route('intern.leaves.index') }}" class="flex items-center space-x-3.5 px-4 py-3 rounded-xl font-bold text-sm transition {{ request()->routeIs('intern.leaves.index*') ? 'bg-[#005B9C] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                        <i class="fa-regular fa-calendar-xmark text-base w-6 text-center"></i>
                        <span>Pengajuan Izin & Sakit</span>
                    </a>

                    <a href="{{ route('intern.allowance.index') }}" class="flex items-center space-x-3.5 px-4 py-3 rounded-xl font-bold text-sm transition {{ request()->routeIs('intern.allowance*') ? 'bg-[#005B9C] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                        <i class="fa-solid fa-coins text-base w-6 text-center"></i>
                        <span>Klaim Uang Saku</span>
                    </a>

                    <a href="{{ route('intern.certificate.index') }}" class="flex items-center space-x-3.5 px-4 py-3 rounded-xl font-bold text-sm transition {{ request()->routeIs('intern.certificate*') ? 'bg-[#005B9C] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                        <i class="fa-solid fa-certificate text-base w-6 text-center"></i>
                        <span>E-Sertifikat & Nilai</span>
                    </a>

                    <a href="{{ route('intern.survey.index') }}" class="flex items-center space-x-3.5 px-4 py-3 rounded-xl font-bold text-sm transition {{ request()->routeIs('intern.survey*') ? 'bg-[#005B9C] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                        <i class="fa-solid fa-star text-amber-500 text-base w-6 text-center"></i>
                        <span>Survei Kepuasan</span>
                    </a>
                @endif

                <!-- Shared Reports & Profile Section -->
                <p class="px-3 text-xs font-bold tracking-wider uppercase text-slate-400 mt-6 mb-2">
                    {{ Auth::user()->isIntern() ? 'LAPORAN & PENGATURAN' : 'PENGATURAN' }}
                </p>

                @if(Auth::user()->isIntern())
                    <a href="{{ route('reports.index') }}" class="flex items-center space-x-3.5 px-4 py-3 rounded-xl font-bold text-sm transition {{ request()->routeIs('reports*') ? 'bg-[#005B9C] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                        <i class="fa-solid fa-file-pdf text-rose-500 text-base w-6 text-center"></i>
                        <span>Cetak PDF & Excel</span>
                    </a>
                @endif

                <a href="{{ route('profile.show') }}" class="flex items-center space-x-3.5 px-4 py-3 rounded-xl font-bold text-sm transition {{ request()->routeIs('profile.*') ? 'bg-[#005B9C] text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                    <i class="fa-regular fa-id-badge text-base w-6 text-center"></i>
                    <span>Pengaturan Profil</span>
                </a>
            </nav>
        </aside>

        <!-- Overlay backdrop for mobile -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition.opacity class="fixed inset-0 bg-slate-900/50 z-40 md:hidden"></div>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0">

            {{-- Active Impersonation Bar --}}
            @if(!empty($activeImpersonator))
                <div class="bg-[#002B49] text-white px-6 py-2.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2 z-40 border-b border-slate-700">
                    <div class="flex items-center gap-2.5 text-xs font-semibold">
                        <span class="px-2 py-0.5 rounded-md bg-amber-400 text-slate-950 font-extrabold uppercase tracking-wider text-[10px]">Mode Impersonasi</span>
                        <span>
                            Anda sedang melihat sistem sebagai <strong>{{ Auth::user()->name }}</strong> ({{ Auth::user()->roleLabel() }}). Sesi asli: {{ $activeImpersonator->name }}.
                        </span>
                    </div>
                    <form action="{{ route('impersonate.leave') }}" method="POST" class="shrink-0">
                        @csrf
                        <button type="submit" class="px-3.5 py-1.5 rounded-lg bg-white text-[#002B49] hover:bg-slate-100 font-extrabold text-xs transition focus:outline-2 focus:outline-offset-2 focus:outline-white">
                            <i class="fa-solid fa-arrow-rotate-left mr-1"></i>
                            Kembali ke Superadmin
                        </button>
                    </form>
                </div>
            @endif

            {{-- Superadmin Bypass Notice when Full Maintenance Mode is Active --}}
            @if(!empty($globalMaintenanceState['enabled']) && (Auth::user()->isSuperAdmin() || !empty($activeImpersonator)))
                <div class="bg-rose-700 text-white px-6 py-2.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs font-semibold">
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid fa-triangle-exclamation text-sm"></i>
                        <span>
                            <strong>Mode Pemeliharaan Penuh Sedang Aktif:</strong> Pengguna non-superadmin saat ini dialihkan ke halaman pemeliharaan (HTTP 503).
                        </span>
                    </div>
                    @if(Auth::user()->isSuperAdmin())
                        <a href="{{ route('superadmin.maintenance') }}" class="underline font-extrabold hover:text-rose-100 shrink-0">
                            Kelola Status Pemeliharaan
                        </a>
                    @endif
                </div>
            @endif

            {{-- Global System Broadcast / Scheduled Maintenance Banner --}}
            @if(!empty($globalSystemBanner['enabled']) && !empty($globalSystemBanner['message']))
                @php
                    $bannerStyle = match($globalSystemBanner['level'] ?? 'info') {
                        'critical' => 'bg-rose-50 border-b border-rose-200 text-rose-950',
                        'warning' => 'bg-amber-50 border-b border-amber-200 text-amber-950',
                        default => 'bg-blue-50 border-b border-blue-200 text-slate-900',
                    };
                    $bannerIcon = match($globalSystemBanner['level'] ?? 'info') {
                        'critical' => 'fa-solid fa-circle-exclamation text-rose-700',
                        'warning' => 'fa-solid fa-screwdriver-wrench text-amber-700',
                        default => 'fa-solid fa-circle-info text-[#005B9C]',
                    };
                @endphp
                <div x-data="{ openBanner: true }" x-show="openBanner" class="{{ $bannerStyle }} px-6 sm:px-8 py-3 flex items-start sm:items-center justify-between gap-4">
                    <div class="flex items-start sm:items-center gap-3 text-xs sm:text-sm">
                        <i class="{{ $bannerIcon }} text-base mt-0.5 sm:mt-0 shrink-0"></i>
                        <div class="leading-relaxed">
                            @if(!empty($globalSystemBanner['title']))
                                <strong class="font-extrabold mr-1.5">{{ $globalSystemBanner['title'] }}:</strong>
                            @endif
                            <span class="font-semibold">{{ $globalSystemBanner['message'] }}</span>
                            @if(!empty($globalSystemBanner['schedule_at']))
                                <span class="inline-block ml-1.5 px-2 py-0.5 rounded-md bg-white/80 border border-slate-300 text-slate-800 text-[11px] font-bold">
                                    Jadwal: {{ $globalSystemBanner['schedule_at'] }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <button type="button" @click="openBanner = false" aria-label="Tutup pemberitahuan" class="text-slate-600 hover:text-slate-900 p-1.5 rounded-lg focus:outline-2 focus:outline-[#005B9C]">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif
            
            <!-- Clean Top Navigation Header -->
            <header class="h-20 bg-white border-b border-slate-200 px-6 sm:px-8 flex items-center justify-between sticky top-0 z-30 shadow-xs">
                
                <div class="flex items-center space-x-4">
                    <button @click="sidebarOpen = !sidebarOpen" class="text-slate-700 hover:text-slate-900 md:hidden p-2.5 rounded-xl bg-slate-100 border border-slate-200">
                        <i class="fa-solid fa-bars text-base"></i>
                    </button>
                    <span class="text-base font-bold text-slate-800 hidden sm:block">Pelindo Internship Portal</span>
                </div>

                <div class="flex items-center space-x-4">
                    
                    <!-- Today Date Pill Badge -->
                    <div class="hidden md:flex items-center space-x-2 bg-slate-50 border border-slate-200 px-4 py-2 rounded-xl text-sm font-semibold text-slate-700">
                        <i class="fa-regular fa-calendar text-[#005B9C]"></i>
                        <span>{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM YYYY') }}</span>
                    </div>

                    <!-- Notification Bell Dropdown -->
                    @php
                        $notifications = Auth::user()->notifications()->latest()->take(5)->get();
                        $unreadCount = Auth::user()->notifications()->where('is_read', false)->count();
                    @endphp
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="w-11 h-11 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-700 hover:text-slate-900 hover:bg-slate-100 transition relative">
                            <i class="fa-regular fa-bell text-base"></i>
                            @if($unreadCount > 0)
                                <span class="absolute -top-1 -right-1 w-5 h-5 bg-rose-600 text-white rounded-full flex items-center justify-center font-bold text-xs border-2 border-white">
                                    {{ $unreadCount }}
                                </span>
                            @endif
                        </button>

                        <div x-show="open" @click.outside="open = false" x-transition.origin.top.right 
                             class="absolute right-0 mt-3 w-80 sm:w-96 bg-white rounded-2xl shadow-xl border border-slate-200 py-3 z-50 space-y-2">
                            
                            <!-- Header -->
                            <div class="px-4 pb-2.5 border-b border-slate-100 flex items-center justify-between">
                                <div>
                                    <h4 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                                        <i class="fa-solid fa-bell text-[#005B9C]"></i>
                                        <span>Notifikasi</span>
                                    </h4>
                                    <p class="text-xs text-slate-500 font-medium mt-0.5">{{ $unreadCount }} notifikasi belum dibaca</p>
                                </div>
                                @if($unreadCount > 0)
                                    <form action="{{ route('notifications.read_all') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-xs text-[#005B9C] hover:underline font-bold">
                                            Tandai dibaca
                                        </button>
                                    </form>
                                @endif
                            </div>

                            <!-- List -->
                            <div class="max-h-80 overflow-y-auto px-2 space-y-1.5">
                                @forelse($notifications as $notif)
                                    @php
                                        $iconClass = 'bg-blue-50 text-[#005B9C]';
                                        $iconHtml = '<i class="fa-regular fa-bell"></i>';

                                        if (Str::contains($notif->title, ['Disetujui', 'Setuju'])) {
                                            $iconClass = 'bg-emerald-50 text-emerald-600';
                                            $iconHtml = '<i class="fa-regular fa-circle-check"></i>';
                                        } elseif (Str::contains($notif->title, ['Ditolak', 'Tolak'])) {
                                            $iconClass = 'bg-rose-50 text-rose-600';
                                            $iconHtml = '<i class="fa-regular fa-circle-xmark"></i>';
                                        } elseif (Str::contains($notif->title, ['Revisi'])) {
                                            $iconClass = 'bg-amber-50 text-amber-600';
                                            $iconHtml = '<i class="fa-solid fa-rotate"></i>';
                                        }
                                    @endphp
                                    <div class="p-3 rounded-xl border transition flex gap-3 cursor-pointer items-start
                                         {{ !$notif->is_read ? 'bg-blue-50/40 border-blue-200 hover:bg-blue-50' : 'bg-white border-slate-100 hover:bg-slate-50' }}"
                                         @click="
                                            if (!{{ $notif->is_read ? 'true' : 'false' }}) {
                                                fetch('{{ route('notifications.read', $notif->id) }}', {
                                                    method: 'POST',
                                                    headers: {
                                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                        'Content-Type': 'application/json'
                                                    }
                                                }).then(() => {
                                                    window.location.reload();
                                                });
                                            }
                                         ">
                                        <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 text-sm {{ $iconClass }}">
                                            {!! $iconHtml !!}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between gap-2">
                                                <h5 class="font-bold text-slate-900 text-xs truncate">{{ $notif->title }}</h5>
                                                <span class="text-[10px] text-slate-400 font-semibold shrink-0">
                                                    {{ $notif->created_at ? $notif->created_at->diffForHumans(null, true) : '-' }}
                                                </span>
                                            </div>
                                            <p class="text-xs text-slate-600 font-medium leading-relaxed mt-0.5">{{ $notif->message }}</p>
                                        </div>
                                    </div>
                                @empty
                                    <div class="py-8 text-center text-slate-400 space-y-1">
                                        <i class="fa-regular fa-bell-slash text-2xl text-slate-300"></i>
                                        <p class="text-xs font-semibold">Tidak ada notifikasi baru</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- User Profile Dropdown Pill -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-3 p-1.5 rounded-xl bg-slate-50 border border-slate-200 hover:border-slate-300 transition shadow-xs">
                            <div class="w-9 h-9 rounded-lg bg-[#002B49] text-white flex items-center justify-center font-bold text-sm">
                                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="text-left hidden sm:block pr-1">
                                <p class="text-xs font-bold text-slate-900 leading-tight">{{ Auth::user()->name }}</p>
                                <p class="text-[10px] font-bold text-[#005B9C] uppercase tracking-wider">{{ Auth::user()->roleLabel() }}</p>
                            </div>
                            <i class="fa-solid fa-chevron-down text-xs text-slate-400 pr-1"></i>
                        </button>

                        <div x-show="open" @click.outside="open = false" x-transition.origin.top.right 
                             class="absolute right-0 mt-2 w-60 bg-white rounded-2xl shadow-xl border border-slate-200 py-2 z-50">
                            <div class="px-4 py-3 border-b border-slate-100">
                                <p class="text-sm font-bold text-slate-900">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-slate-500 truncate mt-0.5">{{ Auth::user()->email }}</p>
                            </div>

                            <a href="{{ route('profile.show') }}" class="flex items-center space-x-3 px-4 py-3 text-xs text-slate-700 hover:bg-slate-50 font-bold">
                                <i class="fa-regular fa-user text-slate-400 text-sm"></i>
                                <span>Pengaturan Profil</span>
                            </a>

                            <div class="border-t border-slate-100 my-1"></div>

                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full flex items-center space-x-3 px-4 py-3 text-xs text-rose-600 hover:bg-rose-50 font-bold">
                                    <i class="fa-solid fa-right-from-bracket text-rose-500 text-sm"></i>
                                    <span>Keluar Akun</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Page Content -->
            <main class="flex-1 px-6 sm:px-8 py-8 max-w-7xl w-full mx-auto space-y-8">
                @if(session('success'))
                    <div x-data="{ show: true }" x-show="show" x-transition class="bg-emerald-50 border border-emerald-200 text-emerald-900 px-5 py-4 rounded-2xl flex items-center justify-between shadow-xs">
                        <div class="flex items-center space-x-3">
                            <i class="fa-solid fa-circle-check text-emerald-600 text-xl"></i>
                            <span class="text-sm font-bold">{{ session('success') }}</span>
                        </div>
                        <button @click="show = false" class="text-emerald-500 hover:text-emerald-700 text-base"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                @endif

                @if(session('error'))
                    <div x-data="{ show: true }" x-show="show" x-transition class="bg-rose-50 border border-rose-200 text-rose-900 px-5 py-4 rounded-2xl flex items-center justify-between shadow-xs">
                        <div class="flex items-center space-x-3">
                            <i class="fa-solid fa-circle-exclamation text-rose-600 text-xl"></i>
                            <span class="text-sm font-bold">{{ session('error') }}</span>
                        </div>
                        <button @click="show = false" class="text-rose-500 hover:text-rose-700 text-base"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                @endif

                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="py-6 px-8 text-center text-xs font-medium text-slate-500 border-t border-slate-200/60 bg-white">
                &copy; {{ date('Y') }} PT Pelabuhan Indonesia (Persero). All Rights Reserved.
            </footer>
        </div>
    </div>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js').then(function(reg) {
                    console.log('Pelindo PRO ServiceWorker registered:', reg.scope);
                }).catch(function(err) {
                    console.log('ServiceWorker registration failed:', err);
                });
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
