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
                            navy: '#0F2A4A',
                            blue: '#3B82F6',
                            cyan: '#06B6D4',
                            indigo: '#4F46E5',
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
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: #f8fafc; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-[#F8FAFC] font-sans antialiased text-slate-800" x-data="{ sidebarOpen: false }">

    <div class="min-h-screen flex">
        
        <!-- Clean Fixed Left Sidebar matching exact screenshot design -->
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-100 text-slate-700 flex flex-col transition-all duration-300 ease-in-out md:sticky md:top-0 md:h-screen shrink-0"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">
            
            <!-- Brand Logo Header -->
            <div class="h-20 flex items-center px-6 justify-between bg-white">
                <div class="flex items-center space-x-2">
                    <img src="{{ asset('images/pelindo-logo-transparent.png') }}" alt="PELINDO SOLUSI DIGITAL" class="h-10 w-auto object-contain">
                    <span class="px-1.5 py-0.5 bg-blue-100 text-blue-600 text-[9px] font-extrabold rounded-md uppercase">PRO</span>
                </div>
                <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-slate-700">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- User Info Badge in Sidebar matching screenshot dropdown style -->
            <div class="mx-4 my-4 p-3.5 rounded-2xl bg-white border border-slate-100 flex items-center justify-between shadow-2xs hover:bg-slate-50 transition cursor-pointer">
                <div class="flex items-center space-x-3 overflow-hidden min-w-0">
                    <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-black text-sm shrink-0">
                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="overflow-hidden min-w-0 leading-tight">
                        <p class="text-xs font-black text-slate-800 truncate leading-snug">{{ Auth::user()->name }}</p>
                        <div class="flex items-center space-x-1 mt-0.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span class="text-[9px] font-extrabold text-blue-600 uppercase tracking-wider">
                                {{ Auth::user()->role }}
                            </span>
                        </div>
                    </div>
                </div>
                <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 shrink-0 pl-2"></i>
            </div>

            <!-- Sidebar Navigation Links -->
            <nav class="flex-1 px-4 py-2 space-y-1.5 overflow-y-auto">
                
                @if(Auth::user()->isAdmin())
                    <p class="px-3 text-[10px] font-black tracking-widest uppercase text-slate-400 mb-2 mt-2">MENU UTAMA</p>
                    
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-3.5 py-3 rounded-2xl font-bold text-xs transition {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50/70 text-blue-600 border border-blue-100/50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <i class="fa-solid fa-house text-sm w-5"></i>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('admin.interns.index') }}" class="flex items-center space-x-3 px-3.5 py-3 rounded-2xl font-bold text-xs transition {{ request()->routeIs('admin.interns*') ? 'bg-blue-50/70 text-blue-600 border border-blue-100/50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <i class="fa-solid fa-user-graduate text-sm w-5"></i>
                        <span>Peserta Magang</span>
                    </a>

                    <a href="{{ route('admin.supervisors.index') }}" class="flex items-center space-x-3 px-3.5 py-3 rounded-2xl font-bold text-xs transition {{ request()->routeIs('admin.supervisors*') ? 'bg-blue-50/70 text-blue-600 border border-blue-100/50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <i class="fa-solid fa-user-tie text-sm w-5"></i>
                        <span>Pembimbing</span>
                    </a>

                    <a href="{{ route('admin.divisions.index') }}" class="flex items-center space-x-3 px-3.5 py-3 rounded-2xl font-bold text-xs transition {{ request()->routeIs('admin.divisions*') ? 'bg-blue-50/70 text-blue-600 border border-blue-100/50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <i class="fa-solid fa-sitemap text-sm w-5"></i>
                        <span>Kelola Divisi</span>
                    </a>

                    <a href="{{ route('admin.attendance.index') }}" class="flex items-center space-x-3 px-3.5 py-3 rounded-2xl font-bold text-xs transition {{ request()->routeIs('admin.attendance*') ? 'bg-blue-50/70 text-blue-600 border border-blue-100/50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <i class="fa-solid fa-clipboard-user text-sm w-5"></i>
                        <span>Rekap Presensi</span>
                    </a>

                    <a href="{{ route('admin.monitoring') }}" class="flex items-center space-x-3 px-3.5 py-3 rounded-2xl font-bold text-xs transition {{ request()->routeIs('admin.monitoring*') ? 'bg-blue-50/70 text-blue-600 border border-blue-100/50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <i class="fa-solid fa-eye text-sm w-5"></i>
                        <span>Monitoring Logbook</span>
                    </a>

                    <a href="{{ route('admin.announcements.index') }}" class="flex items-center space-x-3 px-3.5 py-3 rounded-2xl font-bold text-xs transition {{ request()->routeIs('admin.announcements*') ? 'bg-blue-50/70 text-blue-600 border border-blue-100/50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <i class="fa-solid fa-bullhorn text-sm w-5"></i>
                        <span>Pengumuman HR</span>
                    </a>

                    <a href="{{ route('admin.allowance.index') }}" class="flex items-center space-x-3 px-3.5 py-3 rounded-2xl font-bold text-xs transition {{ request()->routeIs('admin.allowance*') ? 'bg-blue-50/70 text-blue-600 border border-blue-100/50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <i class="fa-solid fa-coins text-sm w-5"></i>
                        <span>Klaim Uang Saku</span>
                    </a>

                    <a href="{{ route('admin.assessments.index') }}" class="flex items-center space-x-3 px-3.5 py-3 rounded-2xl font-bold text-xs transition {{ request()->routeIs('admin.assessments*') ? 'bg-blue-50/70 text-blue-600 border border-blue-100/50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <i class="fa-solid fa-award text-sm w-5"></i>
                        <span>Kelola E-Sertifikat</span>
                    </a>
                @endif

                @if(Auth::user()->isSupervisor())
                    <p class="px-3 text-[10px] font-black tracking-widest uppercase text-slate-400 mb-2 mt-2">MENU UTAMA</p>

                    <a href="{{ route('supervisor.dashboard') }}" class="flex items-center space-x-3 px-3.5 py-3 rounded-2xl font-bold text-xs transition {{ request()->routeIs('supervisor.dashboard') ? 'bg-blue-50/70 text-blue-600 border border-blue-100/50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <i class="fa-solid fa-house text-sm w-5"></i>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('supervisor.interns') }}" class="flex items-center space-x-3 px-3.5 py-3 rounded-2xl font-bold text-xs transition {{ request()->routeIs('supervisor.interns*') ? 'bg-blue-50/70 text-blue-600 border border-blue-100/50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <i class="fa-solid fa-users text-sm w-5"></i>
                        <span>Peserta Bimbingan</span>
                    </a>

                    <a href="{{ route('supervisor.attendance.index') }}" class="flex items-center space-x-3 px-3.5 py-3 rounded-2xl font-bold text-xs transition {{ request()->routeIs('supervisor.attendance*') ? 'bg-blue-50/70 text-blue-600 border border-blue-100/50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <i class="fa-solid fa-calendar-check text-sm w-5"></i>
                        <span>Monitoring Presensi</span>
                    </a>

                    <a href="{{ route('supervisor.activities') }}" class="flex items-center space-x-3 px-3.5 py-3 rounded-2xl font-bold text-xs transition {{ request()->routeIs('supervisor.activities*') ? 'bg-blue-50/70 text-blue-600 border border-blue-100/50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <i class="fa-solid fa-list-check text-sm w-5"></i>
                        <span>Approval Logbook</span>
                    </a>

                    <a href="{{ route('supervisor.statistics') }}" class="flex items-center space-x-3 px-3.5 py-3 rounded-2xl font-bold text-xs transition {{ request()->routeIs('supervisor.statistics*') ? 'bg-blue-50/70 text-blue-600 border border-blue-100/50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <i class="fa-solid fa-chart-line text-sm w-5"></i>
                        <span>Statistik Magang</span>
                    </a>

                    <a href="{{ route('supervisor.leaves.index') }}" class="flex items-center space-x-3 px-3.5 py-3 rounded-2xl font-bold text-xs transition {{ request()->routeIs('supervisor.leaves.index*') ? 'bg-blue-50/70 text-blue-600 border border-blue-100/50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <i class="fa-regular fa-calendar-check text-sm w-5"></i>
                        <span>Approval Izin</span>
                    </a>

                    <a href="{{ route('supervisor.allowance.index') }}" class="flex items-center space-x-3 px-3.5 py-3 rounded-2xl font-bold text-xs transition {{ request()->routeIs('supervisor.allowance*') ? 'bg-blue-50/70 text-blue-600 border border-blue-100/50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <i class="fa-solid fa-file-invoice-dollar text-sm w-5"></i>
                        <span>Approval Uang Saku</span>
                    </a>

                    <a href="{{ route('supervisor.assessments.index') }}" class="flex items-center space-x-3 px-3.5 py-3 rounded-2xl font-bold text-xs transition {{ request()->routeIs('supervisor.assessments*') ? 'bg-blue-50/70 text-blue-600 border border-blue-100/50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <i class="fa-solid fa-graduation-cap text-sm w-5"></i>
                        <span>Penilaian & Nilai Akhir</span>
                    </a>
                @endif

                @if(Auth::user()->isIntern())
                    <p class="px-3 text-[10px] font-black tracking-widest uppercase text-slate-400 mb-2 mt-2">MENU UTAMA</p>

                    <a href="{{ route('intern.dashboard') }}" class="flex items-center space-x-3 px-3.5 py-3 rounded-2xl font-bold text-xs transition {{ request()->routeIs('intern.dashboard') ? 'bg-blue-50/70 text-blue-600 border border-blue-100/50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <i class="fa-solid fa-house text-sm w-5"></i>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('intern.attendance.index') }}" class="flex items-center space-x-3 px-3.5 py-3 rounded-2xl font-bold text-xs transition {{ request()->routeIs('intern.attendance*') ? 'bg-blue-50/70 text-blue-600 border border-blue-100/50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <i class="fa-solid fa-location-dot text-sm w-5"></i>
                        <span>Presensi & Geofencing</span>
                    </a>

                    <a href="{{ route('intern.activities.create') }}" class="flex items-center space-x-3 px-3.5 py-3 rounded-2xl font-bold text-xs transition {{ request()->routeIs('intern.activities.create') ? 'bg-blue-50/70 text-blue-600 border border-blue-100/50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <i class="fa-regular fa-pen-to-square text-sm w-5"></i>
                        <span>Isi Aktivitas Hari Ini</span>
                    </a>

                    <a href="{{ route('intern.activities.index') }}" class="flex items-center space-x-3 px-3.5 py-3 rounded-2xl font-bold text-xs transition {{ request()->routeIs('intern.activities.index') || request()->routeIs('intern.activities.edit') ? 'bg-blue-50/70 text-blue-600 border border-blue-100/50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <i class="fa-regular fa-clock text-sm w-5"></i>
                        <span>Riwayat Logbook</span>
                    </a>

                    <a href="{{ route('intern.leaves.index') }}" class="flex items-center space-x-3 px-3.5 py-3 rounded-2xl font-bold text-xs transition {{ request()->routeIs('intern.leaves.index*') ? 'bg-blue-50/70 text-blue-600 border border-blue-100/50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <i class="fa-regular fa-calendar-xmark text-sm w-5"></i>
                        <span>Pengajuan Izin & Sakit</span>
                    </a>

                    <a href="{{ route('intern.allowance.index') }}" class="flex items-center space-x-3 px-3.5 py-3 rounded-2xl font-bold text-xs transition {{ request()->routeIs('intern.allowance*') ? 'bg-blue-50/70 text-blue-600 border border-blue-100/50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <i class="fa-solid fa-coins text-sm w-5"></i>
                        <span>Klaim Uang Saku</span>
                    </a>

                    <a href="{{ route('intern.certificate.index') }}" class="flex items-center space-x-3 px-3.5 py-3 rounded-2xl font-bold text-xs transition {{ request()->routeIs('intern.certificate*') ? 'bg-blue-50/70 text-blue-600 border border-blue-100/50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <i class="fa-solid fa-certificate text-sm w-5"></i>
                        <span>E-Sertifikat & Nilai</span>
                    </a>

                    <a href="{{ route('intern.survey.index') }}" class="flex items-center space-x-3 px-3.5 py-3 rounded-2xl font-bold text-xs transition {{ request()->routeIs('intern.survey*') ? 'bg-blue-50/70 text-blue-600 border border-blue-100/50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <i class="fa-solid fa-star text-amber-500 text-sm w-5"></i>
                        <span>Survei Kepuasan</span>
                    </a>
                @endif

                <!-- Shared Reports & Profile Section -->
                <p class="px-3 text-[10px] font-black tracking-widest uppercase text-slate-400 mt-6 mb-2">
                    {{ Auth::user()->isIntern() ? 'LAPORAN & PENGATURAN' : 'PENGATURAN' }}
                </p>

                @if(Auth::user()->isIntern())
                    <a href="{{ route('reports.index') }}" class="flex items-center space-x-3 px-3.5 py-3 rounded-2xl font-bold text-xs transition {{ request()->routeIs('reports*') ? 'bg-blue-50/70 text-blue-600 border border-blue-100/50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <i class="fa-solid fa-file-pdf text-rose-500 text-sm w-5"></i>
                        <span>Cetak PDF & Excel</span>
                    </a>
                @endif

                <a href="{{ route('profile.show') }}" class="flex items-center space-x-3 px-3.5 py-3 rounded-2xl font-bold text-xs transition {{ request()->routeIs('profile*') ? 'bg-blue-50/70 text-blue-600 border border-blue-100/50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <i class="fa-regular fa-user text-slate-400 text-sm w-5"></i>
                    <span>Profil</span>
                </a>

                <!-- Illustration checklist card in sidebar for Interns matching screenshot -->
                @if(Auth::user()->isIntern())
                    <div class="bg-blue-50/30 rounded-3xl p-4 border border-blue-50/60 text-center space-y-3 mt-6">
                        <!-- Checklist Illustration SVG -->
                        <svg class="mx-auto w-24 h-24" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="26" y="22" width="48" height="58" rx="8" fill="#FFFFFF" stroke="#3B82F6" stroke-width="2.5"/>
                            <path d="M40 22V17C40 15.8954 40.8954 15 42 15H58C59.1046 15 60 15.8954 60 17V22" stroke="#3B82F6" stroke-width="2.5" fill="#3B82F6"/>
                            <circle cx="39" cy="37" r="4.5" fill="#3B82F6"/>
                            <path d="M37 37L38.5 38.5L41 35.5" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
                            <line x1="48" y1="37" x2="63" y2="37" stroke="#E2E8F0" stroke-width="2" stroke-linecap="round"/>
                            
                            <circle cx="39" cy="49" r="4.5" fill="#3B82F6"/>
                            <path d="M37 49L38.5 50.5L41 47.5" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
                            <line x1="48" y1="49" x2="63" y2="49" stroke="#E2E8F0" stroke-width="2" stroke-linecap="round"/>
                            
                            <circle cx="39" cy="61" r="4.5" fill="#3B82F6"/>
                            <path d="M37 61L38.5 62.5L41 59.5" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
                            <line x1="48" y1="61" x2="63" y2="61" stroke="#E2E8F0" stroke-width="2" stroke-linecap="round"/>
                            
                            <!-- Plant illustration -->
                            <path d="M74 80C74 74 77 69 77 59" stroke="#1E40AF" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="M77 59C75 62 72 64 72 67C72 70 75 71 77 71C79 71 82 70 82 67C82 64 79 62 77 59Z" fill="#3B82F6"/>
                            <path d="M75 67C72 69 70 72 70 75C70 78 73 79 75 79C77 79 80 78 80 75C80 72 77 69 75 67Z" fill="#1E3A8A"/>
                        </svg>
                        <h4 class="font-extrabold text-slate-800 text-[11px] leading-tight">Catat kegiatanmu setiap hari</h4>
                        <p class="text-[9px] text-slate-400 font-semibold leading-normal px-2">Pantau progres dan selesaikan program magang dengan baik.</p>
                    </div>
                @endif
            </nav>

            <!-- Bottom Exit Button matching screenshot -->
            <div class="p-4 border-t border-slate-50 bg-white">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center space-x-2 px-4 py-2.5 bg-slate-50/60 hover:bg-rose-50 text-slate-600 hover:text-rose-600 rounded-2xl text-xs font-bold transition border border-slate-200/60 shadow-2xs">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Keluar Akun</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Overlay backdrop for mobile -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition.opacity class="fixed inset-0 bg-slate-900/40 z-40 md:hidden"></div>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0">
            
            <!-- Clean Header Bar matching screenshot -->
            <header class="h-20 bg-[#F8FAFC] border-b border-transparent px-8 flex items-center justify-between sticky top-0 z-30">
                
                <div class="flex items-center space-x-4">
                    <button @click="sidebarOpen = !sidebarOpen" class="text-slate-600 hover:text-slate-900 md:hidden p-2 rounded-xl bg-white border border-slate-200">
                        <i class="fa-solid fa-bars text-sm"></i>
                    </button>
                </div>

                <div class="flex items-center space-x-4">
                    
                    <!-- Today Date Pill Badge -->
                    <div class="flex items-center space-x-2 bg-white border border-slate-200/80 px-4 py-2 rounded-2xl text-xs font-bold text-slate-700 shadow-xs">
                        <i class="fa-regular fa-calendar text-blue-600"></i>
                        <span>{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM YYYY') }}</span>
                    </div>

                    <!-- Notification Bell Dropdown -->
                    @php
                        $notifications = Auth::user()->notifications()->latest()->take(5)->get();
                        $unreadCount = Auth::user()->notifications()->where('is_read', false)->count();
                    @endphp
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="w-10 h-10 rounded-2xl bg-white border border-slate-200/80 flex items-center justify-center text-slate-650 hover:text-slate-900 hover:border-slate-300 transition shadow-xs relative">
                            <i class="fa-regular fa-bell text-sm"></i>
                            @if($unreadCount > 0)
                                <span class="absolute -top-1 -right-1 w-5 h-5 bg-rose-500 text-white rounded-full flex items-center justify-center font-black text-[9px] border-2 border-white animate-pulse">
                                    {{ $unreadCount }}
                                </span>
                            @endif
                        </button>

                        <div x-show="open" @click.outside="open = false" x-transition.origin.top.right 
                             class="absolute right-0 mt-3 w-80 bg-white rounded-3xl shadow-xl border border-slate-100 py-3 z-50 space-y-2.5">
                            
                            <!-- Header -->
                            <div class="px-4 pb-2 border-b border-slate-100 flex items-center justify-between">
                                <div>
                                    <h4 class="font-black text-slate-800 text-xs flex items-center gap-1.5">
                                        <i class="fa-solid fa-bell text-blue-600"></i>
                                        <span>Notifikasi</span>
                                    </h4>
                                    <p class="text-[9px] text-slate-400 font-semibold mt-0.5">Anda memiliki {{ $unreadCount }} notifikasi belum dibaca</p>
                                </div>
                                @if($unreadCount > 0)
                                    <form action="{{ route('notifications.read_all') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-[10px] text-[#0B63E5] hover:text-blue-700 hover:underline font-extrabold bg-transparent border-none p-0 cursor-pointer transition">
                                            Baca Semua
                                        </button>
                                    </form>
                                @endif
                            </div>

                            <!-- List -->
                            <div class="max-h-80 overflow-y-auto px-2 space-y-1">
                                @forelse($notifications as $notif)
                                    @php
                                        $iconClass = 'bg-blue-50 text-blue-600';
                                        $iconHtml = '<i class="fa-regular fa-bell"></i>';

                                        if (Str::contains($notif->title, ['Disetujui', 'Setuju'])) {
                                            $iconClass = 'bg-emerald-50 text-emerald-600';
                                            $iconHtml = '<i class="fa-regular fa-circle-check"></i>';
                                        } elseif (Str::contains($notif->title, ['Ditolak', 'Tolak'])) {
                                            $iconClass = 'bg-rose-50 text-rose-600';
                                            $iconHtml = '<i class="fa-regular fa-circle-xmark"></i>';
                                        } elseif (Str::contains($notif->title, ['Revisi'])) {
                                            $iconClass = 'bg-purple-50 text-purple-600';
                                            $iconHtml = '<i class="fa-solid fa-rotate"></i>';
                                        } elseif (Str::contains($notif->title, ['Baru', 'Diajukan'])) {
                                            $iconClass = 'bg-blue-50 text-blue-600';
                                            $iconHtml = '<i class="fa-regular fa-file-lines"></i>';
                                        }
                                    @endphp
                                    <div class="p-3 rounded-2xl border-l-4 transition flex gap-3 cursor-pointer items-start
                                         {{ !$notif->is_read ? 'bg-blue-50/20 border-l-blue-600 hover:bg-blue-50/30' : 'bg-white border-l-transparent hover:bg-slate-50' }}"
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
                                        
                                        <!-- Left Icon Circle -->
                                        <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 text-xs {{ $iconClass }}">
                                            {!! $iconHtml !!}
                                        </div>

                                        <!-- Right Content -->
                                        <div class="flex-1 min-w-0 space-y-0.5">
                                            <div class="flex items-center justify-between gap-1.5">
                                                <h5 class="font-black text-slate-800 text-[11px] truncate leading-tight">{{ $notif->title }}</h5>
                                                <span class="text-[9px] text-slate-400 font-bold shrink-0 leading-none">
                                                    {{ $notif->created_at ? $notif->created_at->diffForHumans(null, true) : '-' }}
                                                </span>
                                            </div>
                                            <p class="text-[10px] text-slate-500 font-medium leading-normal">{{ $notif->message }}</p>
                                        </div>
                                    </div>
                                @empty
                                    <div class="py-10 text-center text-slate-400 space-y-1.5">
                                        <div class="w-10 h-10 bg-slate-50 text-slate-400 rounded-full flex items-center justify-center text-base mx-auto">
                                            <i class="fa-regular fa-bell-slash"></i>
                                        </div>
                                        <p class="text-[10px] font-bold">Tidak ada notifikasi baru</p>
                                        <p class="text-[8px] text-slate-400 font-medium">Notifikasi aktivitas akan muncul di sini.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- User Profile Dropdown Pill -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-3 p-1.5 rounded-2xl bg-white border border-transparent hover:border-slate-200 transition shadow-xs">
                            <div class="w-8 h-8 rounded-xl bg-[#0F2A4A] text-white flex items-center justify-center font-extrabold text-xs">
                                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="text-left hidden sm:block">
                                <p class="text-xs font-extrabold text-slate-800 leading-tight">{{ Auth::user()->name }}</p>
                                <p class="text-[9px] font-extrabold text-blue-600 uppercase">{{ Auth::user()->role }}</p>
                            </div>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 pr-1"></i>
                        </button>

                        <div x-show="open" @click.outside="open = false" x-transition.origin.top.right 
                             class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-50">
                            <div class="px-4 py-2 border-b border-slate-100">
                                <p class="text-xs font-bold text-slate-800">{{ Auth::user()->name }}</p>
                                <p class="text-[11px] text-slate-500 truncate">{{ Auth::user()->email }}</p>
                            </div>

                            <a href="{{ route('profile.show') }}" class="flex items-center space-x-2.5 px-4 py-2.5 text-xs text-slate-700 hover:bg-slate-50 font-bold">
                                <i class="fa-regular fa-user text-slate-400"></i>
                                <span>Profil</span>
                            </a>

                            <div class="border-t border-slate-100 my-1"></div>

                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full flex items-center space-x-2.5 px-4 py-2.5 text-xs text-rose-600 hover:bg-rose-50 font-bold">
                                    <i class="fa-solid fa-right-from-bracket text-rose-400"></i>
                                    <span>Keluar Akun</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Page Content -->
            <main class="flex-1 px-8 py-4 max-w-7xl w-full mx-auto space-y-6">
                @if(session('success'))
                    <div x-data="{ show: true }" x-show="show" x-transition class="bg-emerald-50 border border-emerald-200 text-emerald-900 px-4 py-3 rounded-2xl flex items-center justify-between shadow-xs">
                        <div class="flex items-center space-x-3">
                            <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                            <span class="text-xs font-bold">{{ session('success') }}</span>
                        </div>
                        <button @click="show = false" class="text-emerald-500 hover:text-emerald-700 text-sm"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                @endif

                @if(session('error'))
                    <div x-data="{ show: true }" x-show="show" x-transition class="bg-rose-50 border border-rose-200 text-rose-900 px-4 py-3 rounded-2xl flex items-center justify-between shadow-xs">
                        <div class="flex items-center space-x-3">
                            <i class="fa-solid fa-circle-exclamation text-rose-600 text-lg"></i>
                            <span class="text-xs font-bold">{{ session('error') }}</span>
                        </div>
                        <button @click="show = false" class="text-rose-500 hover:text-rose-700 text-sm"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                @endif

                @yield('content')
            </main>

            <!-- Footer matching screenshot -->
            <footer class="py-6 px-8 text-center text-xs font-semibold text-slate-400">
                &copy; {{ date('Y') }} PT Pelindo. All rights reserved.
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
