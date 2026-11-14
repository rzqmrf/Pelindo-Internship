@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="bg-gradient-to-r from-[#002B49] to-[#005B9C] rounded-3xl p-6 sm:p-8 text-white shadow-lg">
        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Dashboard Mentor Teknis</h1>
        <p class="text-blue-100 mt-1 text-sm sm:text-base">Pantau perkembangan harian, berikan tugas/milestone, dan catat sesi bimbingan peserta magang.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Peserta Bimbingan</p>
            <p class="text-3xl font-extrabold text-slate-900 mt-2">{{ $mentoredInterns->count() }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Logbook Menunggu</p>
            <p class="text-3xl font-extrabold text-amber-600 mt-2">{{ $pendingActivitiesCount }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Tugas Aktif</p>
            <p class="text-3xl font-extrabold text-[#005B9C] mt-2">{{ $activeTasksCount }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Tugas Siap Direview</p>
            <p class="text-3xl font-extrabold text-emerald-600 mt-2">{{ $submittedTasksCount }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-extrabold text-slate-900">Antrean Review Logbook</h2>
                <a href="{{ route('mentor.activities') }}" class="text-xs font-bold text-[#005B9C] hover:underline">Lihat Semua</a>
            </div>
            <div class="space-y-3">
                @forelse($recentActivities as $act)
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="flex items-center justify-between">
                            <p class="font-bold text-slate-900 text-sm">{{ $act->intern?->name ?? '-' }}</p>
                            <span class="text-xs font-semibold text-slate-500">{{ $act->date?->format('d M Y') }}</span>
                        </div>
                        <p class="text-xs text-slate-600 mt-1 line-clamp-2">{{ $act->activity_description }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Tidak ada logbook yang menunggu review.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-extrabold text-slate-900">Catatan Mentoring Terbaru</h2>
                <a href="{{ route('mentor.notes') }}" class="text-xs font-bold text-[#005B9C] hover:underline">Kelola Catatan</a>
            </div>
            <div class="space-y-3">
                @forelse($recentNotes as $note)
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="flex items-center justify-between">
                            <p class="font-bold text-slate-900 text-sm">{{ $note->topic }}</p>
                            <span class="text-xs font-semibold text-slate-500">{{ $note->session_date?->format('d M Y') }}</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">Peserta: {{ $note->intern?->name ?? '-' }}</p>
                        <p class="text-xs text-slate-600 mt-1 line-clamp-2">{{ $note->notes }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Belum ada catatan sesi mentoring.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
