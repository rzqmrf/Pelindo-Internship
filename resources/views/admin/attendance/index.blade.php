@extends('layouts.app')

@section('page_title', 'Rekap Presensi Harian Seluruh Divisi')

@section('content')
<div class="space-y-6">
    <!-- Centralized Page Header -->
    <x-page-header
        title="Rekap Kehadiran Peserta Magang (HR)"
        subtitle="Monitoring kepatuhan jam kerja dan distribusi kehadiran WFO/WFH/WFA seluruh divisi."
        icon="fa-solid fa-clipboard-user" />

    <!-- 4 KPI Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <x-stat-card
            title="Total Hadir"
            value="{{ $totalPresentToday }} / {{ $allActiveInterns }}"
            trend="Peserta magang"
            trend-type="up"
            icon="fa-solid fa-user-check"
            color="emerald" />

        <x-stat-card
            title="WFO (Kantor)"
            value="{{ $totalWfoToday }}"
            trend="Kehadiran Fisik"
            trend-type="up"
            icon="fa-solid fa-building"
            color="blue" />

        <x-stat-card
            title="WFH (Remote)"
            value="{{ $totalWfhToday }}"
            trend="Bekerja Remote"
            trend-type="up"
            icon="fa-solid fa-[#005B9C]"
            color="indigo" />

        <x-stat-card
            title="Terlambat"
            value="{{ $totalLateToday }}"
            trend="Perlu diperhatikan"
            trend-type="down"
            icon="fa-solid fa-clock font-black"
            color="amber" />
    </div>

    <!-- Filter & Search Bar wrapped in Card -->
    <x-card>
        <form method="GET" action="{{ route('admin.attendance.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-center">
            <x-form-input
                type="date"
                name="date"
                label="Pilih Tanggal Presensi"
                value="{{ $date }}" />

            <x-form-select name="division_id" label="Filter Divisi">
                <option value="">Semua Divisi</option>
                @foreach($divisions as $div)
                <option value="{{ $div->id }}" {{ $divisionId == $div->id ? 'selected' : '' }}>{{ $div->name }}</option>
                @endforeach
            </x-form-select>

            <div class="flex items-center gap-3 pt-6">
                <x-button type="submit" variant="primary" class="flex-1 justify-center" icon="fa-solid fa-filter">
                    Filter Presensi
                </x-button>
                <x-button href="{{ route('admin.attendance.index') }}" variant="outline" icon="fa-solid fa-rotate">
                    Reset
                </x-button>
            </div>
        </form>
    </x-card>

    <!-- Attendance Table Card -->
    <x-card padding="p-0">
        <x-slot name="title">Data Presensi Tanggal {{ \Carbon\Carbon::parse($date)->isoFormat('dddd, D MMMM YYYY') }}</x-slot>
        <x-slot name="subtitle">Daftar presensi seluruh peserta magang yang tercatat di sistem pada tanggal yang dipilih.</x-slot>
        <x-slot name="icon">fa-solid fa-list-check</x-slot>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100 text-xs font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-4 px-6">Peserta Magang</th>
                        <th class="py-4 px-4">Divisi / Pembimbing</th>
                        <th class="py-4 px-4 text-center">Mode</th>
                        <th class="py-4 px-4 text-center">Masuk</th>
                        <th class="py-4 px-4 text-center">Pulang</th>
                        <th class="py-4 px-4 text-center">Jarak</th>
                        <th class="py-4 px-6 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm font-semibold text-slate-700">
                    @forelse($attendances as $att)
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="py-4 px-6">
                            <span class="font-extrabold text-slate-900 text-sm block leading-snug">{{ $att->intern?->name }}</span>
                            <span class="text-xs text-slate-500 font-medium">{{ $att->intern?->internProfile?->institution ?? '-' }}</span>
                        </td>
                        <td class="py-4 px-4">
                            <span class="font-bold text-slate-800 block text-xs leading-snug">{{ $att->intern?->internProfile?->division?->name ?? '-' }}</span>
                            <span class="text-xs text-slate-500 font-medium">Spv: {{ $att->intern?->internProfile?->supervisor?->name ?? '-' }}</span>
                        </td>
                        <td class="py-4 px-4 text-center">
                            <x-badge :status="$att->work_location" />
                        </td>
                        <td class="py-4 px-4 text-center font-mono font-bold text-slate-800 text-sm">
                            {{ $att->check_in_time ? substr($att->check_in_time, 0, 5) : '-' }}
                        </td>
                        <td class="py-4 px-4 text-center font-mono font-bold text-slate-800 text-sm">
                            {{ $att->check_out_time ? substr($att->check_out_time, 0, 5) : '-' }}
                        </td>
                        <td class="py-4 px-4 text-center text-slate-600 font-medium text-xs">
                            {{ $att->check_in_distance_meters !== null ? $att->check_in_distance_meters . ' m' : '-' }}
                        </td>
                        <td class="py-4 px-6 text-right">
                            <x-badge :status="$att->status" />
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8">
                            <x-empty-state
                                title="Tidak ada data presensi"
                                subtitle="Belum ada pencatatan presensi peserta magang pada tanggal ini."
                                icon="fa-solid fa-calendar-xmark" />
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($attendances->hasPages())
        <div class="p-5 border-t border-slate-100 bg-slate-50/50 rounded-b-3xl">
            {{ $attendances->links() }}
        </div>
        @endif
    </x-card>

    <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-base font-extrabold text-slate-900">Ekspor Rekap Presensi Bulanan</h2>
                <p class="text-xs text-slate-500">Unduh laporan presensi bulanan seluruh peserta atau per divisi dalam format CSV.</p>
            </div>
            <form action="{{ route('admin.attendance.export_monthly') }}" method="GET" class="flex flex-wrap gap-2 items-center">
                <input type="month" name="month" value="{{ substr($date ?? date('Y-m-d'), 0, 7) }}" class="px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold">
                <select name="division_id" class="px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold">
                    <option value="">Semua Divisi</option>
                    @foreach($divisions as $div)
                        <option value="{{ $div->id }}" {{ ($divisionId ?? '') == $div->id ? 'selected' : '' }}>{{ $div->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold">Ekspor CSV Bulanan</button>
            </form>
        </div>
    </div>

    @if(isset($pendingCorrections) && $pendingCorrections->isNotEmpty())
    <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs space-y-4">
        <h2 class="text-base font-extrabold text-slate-900">Koreksi Presensi Menunggu (Override Admin HR)</h2>
        <div class="space-y-3">
            @foreach($pendingCorrections as $corr)
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <p class="font-bold text-slate-900 text-sm">{{ $corr->intern?->name ?? '-' }} ({{ $corr->intern?->internProfile?->division?->name ?? '-' }})</p>
                        <p class="text-xs text-slate-600">Tanggal: {{ $corr->date?->format('d M Y') }} &bull; {{ $corr->correction_type }} &bull; {{ $corr->reason }}</p>
                    </div>
                    <div class="flex gap-2 shrink-0">
                        <form action="{{ route('admin.attendance.corrections.approve', $corr->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-xl text-xs font-bold">Setujui</button>
                        </form>
                        <form action="{{ route('admin.attendance.corrections.reject', $corr->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-rose-600 text-white rounded-xl text-xs font-bold">Tolak</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection