@extends('layouts.app')

@section('title', 'Rekap Presensi Harian Seluruh Divisi')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-clipboard-user text-blue-600"></i>
                <span>Rekap Kehadiran Peserta Magang (HR)</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">Monitoring kepatuhan jam kerja dan distribusi kehadiran WFO/WFH seluruh divisi.</p>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('admin.attendance.index') }}" class="flex flex-wrap items-center gap-2">
            <input type="date" name="date" value="{{ $date }}" class="text-xs rounded-xl border border-slate-200 py-2 px-3 focus:border-blue-500 outline-none">
            <select name="division_id" class="text-xs rounded-xl border border-slate-200 py-2 px-3 focus:border-blue-500 outline-none">
                <option value="">Semua Divisi</option>
                @foreach($divisions as $div)
                    <option value="{{ $div->id }}" {{ $divisionId == $div->id ? 'selected' : '' }}>{{ $div->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2 rounded-xl transition">
                Filter
            </button>
        </form>
    </div>

    <!-- Counters -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Hadir</span>
            <span class="text-2xl font-black text-slate-900 block mt-1">{{ $totalPresentToday }} / {{ $allActiveInterns }}</span>
        </div>
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-bold text-blue-600 uppercase tracking-wider block">WFO (Kantor)</span>
            <span class="text-2xl font-black text-blue-700 block mt-1">{{ $totalWfoToday }}</span>
        </div>
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-bold text-indigo-600 uppercase tracking-wider block">WFH (Remote)</span>
            <span class="text-2xl font-black text-indigo-700 block mt-1">{{ $totalWfhToday }}</span>
        </div>
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-bold text-amber-600 uppercase tracking-wider block">Terlambat</span>
            <span class="text-2xl font-black text-amber-700 block mt-1">{{ $totalLateToday }}</span>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-800">
                Data Presensi Tanggal {{ \Carbon\Carbon::parse($date)->isoFormat('dddd, D MMMM YYYY') }}
            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-100 text-[11px] font-extrabold uppercase tracking-wider text-slate-400">
                        <th class="py-3.5 px-5">Peserta Magang</th>
                        <th class="py-3.5 px-5">Divisi / Pembimbing</th>
                        <th class="py-3.5 px-5 text-center">Mode</th>
                        <th class="py-3.5 px-5 text-center">Masuk</th>
                        <th class="py-3.5 px-5 text-center">Pulang</th>
                        <th class="py-3.5 px-5 text-center">Jarak</th>
                        <th class="py-3.5 px-5 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($attendances as $att)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-4 px-5">
                                <span class="font-bold text-slate-900 block">{{ $att->intern?->name }}</span>
                                <span class="text-[11px] text-slate-400">{{ $att->intern?->internProfile?->institution ?? '-' }}</span>
                            </td>
                            <td class="py-4 px-5">
                                <span class="font-bold text-slate-800 block">{{ $att->intern?->internProfile?->division?->name ?? '-' }}</span>
                                <span class="text-[11px] text-slate-500">Spv: {{ $att->intern?->internProfile?->supervisor?->name ?? '-' }}</span>
                            </td>
                            <td class="py-4 px-5 text-center">
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-extrabold {{ $att->work_location === 'WFO' ? 'bg-blue-50 text-blue-600' : 'bg-indigo-50 text-indigo-600' }}">
                                    {{ $att->work_location }}
                                </span>
                            </td>
                            <td class="py-4 px-5 text-center font-mono font-bold text-slate-800">
                                {{ $att->check_in_time ? substr($att->check_in_time, 0, 5) : '-' }}
                            </td>
                            <td class="py-4 px-5 text-center font-mono font-bold text-slate-800">
                                {{ $att->check_out_time ? substr($att->check_out_time, 0, 5) : '-' }}
                            </td>
                            <td class="py-4 px-5 text-center text-slate-500">
                                {{ $att->check_in_distance_meters !== null ? $att->check_in_distance_meters . ' m' : '-' }}
                            </td>
                            <td class="py-4 px-5 text-right">
                                @if($att->status === 'present')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200">
                                        Tepat Waktu
                                    </span>
                                @elseif($att->status === 'late')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-200">
                                        Terlambat
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-10 text-slate-400">
                                Tidak ada data presensi pada tanggal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($attendances->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $attendances->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
