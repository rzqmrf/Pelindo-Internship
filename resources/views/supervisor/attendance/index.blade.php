@extends('layouts.app')

@section('title', 'Monitoring Presensi Peserta Bimbingan')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-calendar-check text-blue-600"></i>
                <span>Monitoring Presensi Peserta Bimbingan</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">Pantau status kehadiran, jam masuk/pulang, dan mode kerja WFO/WFH peserta.</p>
        </div>

        <!-- Date Picker Filter -->
        <form method="GET" action="{{ route('supervisor.attendance.index') }}" class="flex items-center space-x-2">
            <span class="text-xs font-bold text-slate-500">Tanggal:</span>
            <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()" class="text-xs rounded-xl border border-slate-200 py-2 px-3 focus:border-blue-500 outline-none">
        </form>
    </div>

    <!-- Attendance Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-800">
                Presensi Peserta pada {{ \Carbon\Carbon::parse($date)->isoFormat('dddd, D MMMM YYYY') }}
            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-100 text-[11px] font-extrabold uppercase tracking-wider text-slate-400">
                        <th class="py-3.5 px-5">Peserta Magang</th>
                        <th class="py-3.5 px-5 text-center">Mode Kerja</th>
                        <th class="py-3.5 px-5 text-center">Jam Masuk</th>
                        <th class="py-3.5 px-5 text-center">Jam Pulang</th>
                        <th class="py-3.5 px-5 text-center">Jarak Kantor</th>
                        <th class="py-3.5 px-5 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($interns as $intern)
                        @php
                            $att = $intern->attendances->first();
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-4 px-5">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs uppercase">
                                        {{ substr($intern->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <span class="font-bold text-slate-900 block">{{ $intern->name }}</span>
                                        <span class="text-[11px] text-slate-400">{{ $intern->internProfile?->institution ?? '-' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-5 text-center">
                                @if($att)
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold {{ $att->work_location === 'WFO' ? 'bg-blue-50 text-blue-600 border border-blue-100' : 'bg-indigo-50 text-indigo-600 border border-indigo-100' }}">
                                        {{ $att->work_location }}
                                    </span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="py-4 px-5 text-center font-mono font-bold text-slate-800">
                                {{ $att?->check_in_time ? substr($att->check_in_time, 0, 5) : '-' }}
                            </td>
                            <td class="py-4 px-5 text-center font-mono font-bold text-slate-800">
                                {{ $att?->check_out_time ? substr($att->check_out_time, 0, 5) : '-' }}
                            </td>
                            <td class="py-4 px-5 text-center text-slate-500">
                                {{ $att?->check_in_distance_meters !== null ? $att->check_in_distance_meters . ' m' : '-' }}
                            </td>
                            <td class="py-4 px-5 text-right">
                                @if(!$att || !$att->check_in_time)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-200">
                                        Belum Hadir
                                    </span>
                                @elseif($att->status === 'present')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200">
                                        Tepat Waktu
                                    </span>
                                @elseif($att->status === 'late')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-200">
                                        Terlambat
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 text-slate-400">
                                Belum ada peserta magang yang dibimbing.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
