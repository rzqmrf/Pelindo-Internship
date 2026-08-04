@extends('layouts.app')

@section('page_title', 'Peserta Magang Bimbingan')

@section('content')
<div class="space-y-6">

    <!-- Header Card -->
    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center space-x-3.5">
            <div class="w-12 h-12 rounded-full bg-blue-50 text-[#0B63E5] flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-user-group text-sm"></i>
            </div>
            <div>
                <h1 class="text-xl font-black text-slate-850 tracking-tight">Peserta Magang Bimbingan Saya</h1>
                <p class="text-xs font-semibold text-slate-400 mt-1">Daftar seluruh mahasiswa / siswa magang yang Anda bimbing di PT Pelabuhan Indonesia.</p>
            </div>
        </div>
    </div>

    <!-- Search Bar Card -->
    <div class="bg-white rounded-3xl p-4 border border-slate-100 shadow-sm">
        <form method="GET" action="{{ route('supervisor.interns') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
            <!-- Search field -->
            <div class="relative md:col-span-3">
                <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama peserta, NIM, atau instansi..."
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold text-slate-850 outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
            </div>

            <!-- Buttons -->
            <div class="flex items-center gap-3">
                <button type="submit" class="flex-1 py-2.5 bg-[#0B63E5] hover:bg-blue-750 text-white rounded-2xl text-xs font-extrabold transition flex items-center justify-center space-x-1.5 shadow-2xs">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    <span>Cari</span>
                </button>
                <a href="{{ route('supervisor.interns') }}" class="px-4 py-2.5 bg-white border border-slate-200 text-slate-650 rounded-2xl text-xs font-extrabold hover:bg-slate-50 transition flex items-center justify-center space-x-1.5 shadow-2xs">
                    <i class="fa-solid fa-rotate text-xs"></i>
                    <span>Reset</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Interns Cards Grid (2 Columns) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @forelse($interns as $profile)
            @php
                $totalLogs = $profile->user?->dailyActivities ? $profile->user->dailyActivities->count() : 0;
                $approvedLogs = $profile->user?->dailyActivities ? $profile->user->dailyActivities->where('status', 'approved')->count() : 0;
                $progressPercent = $totalLogs > 0 ? round(($approvedLogs / $totalLogs) * 100) : 0;
            @endphp
            <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-sm space-y-5 flex flex-col justify-between hover:border-blue-200 transition">
                
                <div class="space-y-4">
                    <!-- Top section -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3.5">
                            <div class="w-12 h-12 rounded-full bg-blue-50 text-[#0B63E5] flex items-center justify-center font-black text-lg shadow-2xs shrink-0">
                                {{ strtoupper(substr($profile->user?->name ?? 'I', 0, 1)) }}
                            </div>
                            <div>
                                <h3 class="font-extrabold text-slate-850 text-sm leading-tight">{{ $profile->user?->name }}</h3>
                                <p class="text-[11px] text-slate-400 font-semibold mt-0.5">{{ $profile->user?->email }}</p>
                            </div>
                        </div>
                        <button class="text-slate-400 hover:text-slate-600 p-1.5 transition">
                            <i class="fa-solid fa-ellipsis-vertical text-sm"></i>
                        </button>
                    </div>

                    <!-- Inner card details -->
                    <div class="bg-slate-50 border border-slate-200/50 p-4 rounded-3xl flex items-start space-x-3.5">
                        <div class="w-9 h-9 rounded-full bg-blue-100/50 text-[#0B63E5] flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-graduation-cap text-xs"></i>
                        </div>
                        <div class="flex-1 space-y-0.5">
                            <p class="font-extrabold text-slate-850 text-[11px] leading-tight">{{ $profile->institution }}</p>
                            <p class="text-[10px] text-slate-400 font-semibold">{{ $profile->major }}</p>
                            <p class="text-[10px] text-slate-400 font-semibold">NIM: {{ $profile->student_id_number }}</p>
                            <div class="text-[10px] text-slate-500 font-semibold pt-1 flex items-center">
                                <i class="fa-regular fa-calendar text-slate-400 mr-1.5 text-[9px]"></i> 
                                <span>{{ $profile->start_date ? $profile->start_date->format('d M Y') : '-' }} &ndash; {{ $profile->end_date ? $profile->end_date->format('d M Y') : '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Stats / Progress Section -->
                    <div class="space-y-1.5">
                        <div class="flex justify-between text-[11px] font-extrabold">
                            <span class="text-slate-600">Approval Laporan</span>
                            <span class="text-[#0B63E5]">{{ $approvedLogs }} / {{ $totalLogs }} Disetujui ({{ $progressPercent }}%)</span>
                        </div>
                        <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500 rounded-full transition-all" style="width: {{ $progressPercent }}%"></div>
                        </div>
                        <div class="text-[10px] text-slate-400 font-semibold text-center mt-1 block">
                            {{ $progressPercent }}%
                        </div>
                    </div>
                </div>

                <!-- Footer Action Buttons inside card -->
                <div class="pt-4 border-t border-slate-100 flex items-center gap-3">
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $profile->user?->phone ?? '') }}" target="_blank"
                       class="flex-1 py-2.5 bg-emerald-50 text-emerald-600 rounded-2xl text-xs font-extrabold hover:bg-emerald-100/50 transition flex items-center justify-center space-x-1.5 shadow-2xs">
                        <i class="fa-brands fa-whatsapp text-sm"></i>
                        <span>Hubungi via WhatsApp</span>
                    </a>

                    <a href="{{ route('supervisor.activities') }}?intern_id={{ $profile->user_id }}" 
                       class="flex-1 py-2.5 bg-[#0B63E5] hover:bg-blue-750 text-white rounded-2xl text-xs font-extrabold transition flex items-center justify-center space-x-1.5 shadow-2xs">
                        <span>Lihat Laporan</span>
                        <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </a>
                </div>

            </div>
        @empty
            <div class="col-span-2 py-12 bg-white rounded-3xl border border-slate-100 text-center text-slate-400 font-semibold text-xs">
                <i class="fa-solid fa-user-graduate text-3xl mb-2 text-slate-300 block"></i>
                Tidak ada peserta magang bimbingan ditemukan.
            </div>
        @endforelse
    </div>

    <!-- Summary Metrics Footer -->
    <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center space-x-3.5">
            <div class="w-12 h-12 rounded-2xl bg-blue-50/80 text-[#0B63E5] flex items-center justify-center text-lg shrink-0">
                <i class="fa-regular fa-file-lines"></i>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 sm:gap-12">
                <div class="space-y-0.5">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Total Peserta Bimbingan</p>
                    <h4 class="text-2xl font-black text-slate-850">{{ $totalSupervisedInterns }}</h4>
                </div>
                <div class="space-y-0.5">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Laporan Disetujui</p>
                    <h4 class="text-2xl font-black text-emerald-600">{{ $approvedReportsCount }}</h4>
                </div>
                <div class="space-y-0.5">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Menunggu Approval</p>
                    <h4 class="text-2xl font-black text-orange-500">{{ $pendingReportsCount }}</h4>
                </div>
                <div class="space-y-0.5">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Revisi</p>
                    <h4 class="text-2xl font-black text-purple-650">{{ $revisionReportsCount }}</h4>
                </div>
            </div>
        </div>
        
        @if($interns->hasPages())
            <div class="shrink-0">
                {{ $interns->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
