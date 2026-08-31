@extends('layouts.app')

@section('title', 'Presensi Harian & Geofencing')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-location-dot text-blue-600"></i>
                <span>Presensi Kehadiran & Geofencing</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">Catat jam masuk & pulang harian Anda dengan validasi lokasi kerja WFO / WFH.</p>
        </div>
        <div class="text-xs font-semibold text-slate-500 bg-slate-50 px-4 py-2 rounded-2xl border border-slate-200/60 inline-flex items-center gap-2">
            <i class="fa-regular fa-clock text-blue-600"></i>
            <span id="liveClock" class="font-bold text-slate-800 font-mono text-sm">--:--:--</span>
        </div>
    </div>

    <!-- Quick Check-in / Check-out Interactive Card -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Live Action Card (2 Cols) -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8 space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <h2 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-fingerprint text-blue-600"></i>
                    <span>Status Presensi Hari Ini ({{ \Carbon\Carbon::today()->isoFormat('dddd, D MMMM YYYY') }})</span>
                </h2>
                @if($todayAttendance)
                    @if($todayAttendance->status === 'present')
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-200">
                            <i class="fa-solid fa-check mr-1"></i> Hadir Tepat Waktu
                        </span>
                    @elseif($todayAttendance->status === 'late')
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-600 border border-amber-200">
                            <i class="fa-solid fa-clock-rotate-left mr-1"></i> Terlambat
                        </span>
                    @endif
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-600 border border-rose-200">
                        <i class="fa-solid fa-circle-exclamation mr-1"></i> Belum Presensi
                    </span>
                @endif
            </div>

            <!-- GPS Status Banner -->
            <div id="gpsStatusBox" class="p-3.5 rounded-2xl bg-blue-50/60 border border-blue-100 text-xs text-blue-800 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-satellite-dish text-blue-600 animate-pulse"></i>
                    <span id="gpsStatusText">Mendeteksi koordinat lokasi GPS Anda...</span>
                </div>
                <button type="button" onclick="detectGPS()" class="text-[11px] font-bold text-blue-600 hover:underline">
                    Refresh GPS
                </button>
            </div>

            <!-- Forms Container -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Check-in Box -->
                <div class="p-5 rounded-2xl border {{ $todayAttendance?->check_in_time ? 'bg-emerald-50/40 border-emerald-200' : 'bg-slate-50 border-slate-200' }} space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Jam Masuk (Clock-in)</span>
                        <i class="fa-solid fa-arrow-right-to-bracket text-emerald-600"></i>
                    </div>

                    @if($todayAttendance?->check_in_time)
                        <div class="py-2">
                            <span class="text-2xl font-black text-slate-900 font-mono">{{ substr($todayAttendance->check_in_time, 0, 5) }}</span>
                            <span class="text-xs font-semibold text-slate-500 block mt-1">
                                Mode: <strong>{{ $todayAttendance->work_location }}</strong>
                                @if($todayAttendance->check_in_distance_meters !== null)
                                    &bull; Jarak: {{ $todayAttendance->check_in_distance_meters }}m dari kantor
                                @endif
                            </span>
                        </div>
                        <button disabled class="w-full py-2.5 rounded-xl bg-emerald-100 text-emerald-700 font-bold text-xs cursor-not-allowed">
                            <i class="fa-solid fa-check mr-1"></i> Sudah Check-In
                        </button>
                    @else
                        <form action="{{ route('intern.attendance.checkin') }}" method="POST" id="formCheckIn" class="space-y-3">
                            @csrf
                            <input type="hidden" name="latitude" id="checkin_lat">
                            <input type="hidden" name="longitude" id="checkin_lng">

                            <div>
                                <label class="text-[11px] font-bold text-slate-600 block mb-1">Lokasi Kerja</label>
                                <select name="work_location" class="w-full text-xs rounded-xl border border-slate-200 p-2.5 bg-white font-semibold outline-none focus:border-blue-500">
                                    <option value="WFO">WFO (Work From Office - Kantor Pelindo)</option>
                                    <option value="WFH">WFH (Work From Home / Remote)</option>
                                </select>
                            </div>

                            <button type="submit" class="w-full py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/25 transition">
                                <i class="fa-solid fa-arrow-right-to-bracket mr-1.5"></i> Check-In Masuk
                            </button>
                        </form>
                    @endif
                </div>

                <!-- Check-out Box -->
                <div class="p-5 rounded-2xl border {{ $todayAttendance?->check_out_time ? 'bg-indigo-50/40 border-indigo-200' : 'bg-slate-50 border-slate-200' }} space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Jam Pulang (Clock-out)</span>
                        <i class="fa-solid fa-arrow-right-from-bracket text-indigo-600"></i>
                    </div>

                    @if($todayAttendance?->check_out_time)
                        <div class="py-2">
                            <span class="text-2xl font-black text-slate-900 font-mono">{{ substr($todayAttendance->check_out_time, 0, 5) }}</span>
                            <span class="text-xs font-semibold text-slate-500 block mt-1">Presensi hari ini selesai</span>
                        </div>
                        <button disabled class="w-full py-2.5 rounded-xl bg-indigo-100 text-indigo-700 font-bold text-xs cursor-not-allowed">
                            <i class="fa-solid fa-check mr-1"></i> Sudah Check-Out
                        </button>
                    @elseif($todayAttendance?->check_in_time)
                        <form action="{{ route('intern.attendance.checkout') }}" method="POST" id="formCheckOut" class="space-y-3">
                            @csrf
                            <input type="hidden" name="latitude" id="checkout_lat">
                            <input type="hidden" name="longitude" id="checkout_lng">

                            <p class="text-xs text-slate-500 py-1">Lakukan check-out saat jam kerja berakhir untuk merekam durasi kerja.</p>

                            <button type="submit" class="w-full py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-500/25 transition">
                                <i class="fa-solid fa-arrow-right-from-bracket mr-1.5"></i> Check-Out Pulang
                            </button>
                        </form>
                    @else
                        <div class="py-2">
                            <span class="text-xs text-slate-400 block">Silakan lakukan Check-In terlebih dahulu untuk mengaktifkan tombol Check-Out.</span>
                        </div>
                        <button disabled class="w-full py-2.5 rounded-xl bg-slate-200 text-slate-400 font-bold text-xs cursor-not-allowed">
                            Belum Check-In
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Monthly Stats & Office Info (1 Col) -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-5">
            <h2 class="text-sm font-extrabold text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-3">
                <i class="fa-solid fa-chart-pie text-blue-600"></i>
                <span>Statistik Kehadiran</span>
            </h2>

            <div class="grid grid-cols-2 gap-3">
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Hadir</span>
                    <span class="text-xl font-black text-slate-900 block mt-0.5">{{ $totalPresent }} Hari</span>
                </div>
                <div class="p-3.5 rounded-2xl bg-amber-50/60 border border-amber-100">
                    <span class="text-[10px] font-bold text-amber-600 uppercase tracking-wider block">Terlambat</span>
                    <span class="text-xl font-black text-amber-700 block mt-0.5">{{ $totalLate }} Hari</span>
                </div>
                <div class="p-3.5 rounded-2xl bg-blue-50/60 border border-blue-100">
                    <span class="text-[10px] font-bold text-blue-600 uppercase tracking-wider block">WFO (Kantor)</span>
                    <span class="text-xl font-black text-blue-700 block mt-0.5">{{ $totalWfo }} Hari</span>
                </div>
                <div class="p-3.5 rounded-2xl bg-indigo-50/60 border border-indigo-100">
                    <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-wider block">WFH (Remote)</span>
                    <span class="text-xl font-black text-indigo-700 block mt-0.5">{{ $totalWfh }} Hari</span>
                </div>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 text-[11px] text-slate-600 space-y-1.5">
                <span class="font-bold text-slate-800 flex items-center gap-1.5">
                    <i class="fa-solid fa-building text-blue-600"></i>
                    <span>Ketentuan Jam Kerja Pelindo:</span>
                </span>
                <p>&bull; Jam Masuk: 07:30 - 08:30 WIB</p>
                <p>&bull; Jam Pulang: 16:30 WIB</p>
                <p>&bull; Radius Kantor WFO: Maks. 500 meter</p>
            </div>
        </div>
    </div>

    <!-- Monthly History Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <h2 class="text-sm font-bold text-slate-800">Riwayat Presensi Bulanan</h2>

            <form method="GET" action="{{ route('intern.attendance.index') }}" class="flex items-center space-x-2">
                <input type="month" name="month" value="{{ $selectedMonth }}" onchange="this.form.submit()" class="text-xs rounded-xl border border-slate-200 py-1.5 px-3 focus:border-blue-500 outline-none">
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-100 text-[11px] font-extrabold uppercase tracking-wider text-slate-400">
                        <th class="py-3.5 px-5">Hari / Tanggal</th>
                        <th class="py-3.5 px-5 text-center">Lokasi</th>
                        <th class="py-3.5 px-5 text-center">Check-In</th>
                        <th class="py-3.5 px-5 text-center">Check-Out</th>
                        <th class="py-3.5 px-5 text-center">Jarak Kantor</th>
                        <th class="py-3.5 px-5 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($attendances as $item)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-4 px-5">
                                <span class="font-bold text-slate-900 block">{{ $item->date ? $item->date->isoFormat('dddd, D MMMM YYYY') : '-' }}</span>
                            </td>
                            <td class="py-4 px-5 text-center">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold {{ $item->work_location === 'WFO' ? 'bg-blue-50 text-blue-600 border border-blue-100' : 'bg-indigo-50 text-indigo-600 border border-indigo-100' }}">
                                    {{ $item->work_location }}
                                </span>
                            </td>
                            <td class="py-4 px-5 text-center font-mono font-bold text-slate-800">
                                {{ $item->check_in_time ? substr($item->check_in_time, 0, 5) : '-' }}
                            </td>
                            <td class="py-4 px-5 text-center font-mono font-bold text-slate-800">
                                {{ $item->check_out_time ? substr($item->check_out_time, 0, 5) : '-' }}
                            </td>
                            <td class="py-4 px-5 text-center text-slate-500">
                                {{ $item->check_in_distance_meters !== null ? $item->check_in_distance_meters . ' m' : '-' }}
                            </td>
                            <td class="py-4 px-5 text-right">
                                @if($item->status === 'present')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200">
                                        Tepat Waktu
                                    </span>
                                @elseif($item->status === 'late')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-200">
                                        Terlambat
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 text-slate-400">
                                Belum ada data presensi pada bulan yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Live Digital Clock
function updateClock() {
    const now = new Date();
    const h = String(now.getHours()).padStart(2, '0');
    const m = String(now.getMinutes()).padStart(2, '0');
    const s = String(now.getSeconds()).padStart(2, '0');
    document.getElementById('liveClock').innerText = `${h}:${m}:${s} WIB`;
}
setInterval(updateClock, 1000);
updateClock();

// Geolocation Detection
function detectGPS() {
    const statusText = document.getElementById('gpsStatusText');
    const statusBox = document.getElementById('gpsStatusBox');

    if (!navigator.geolocation) {
        statusText.innerText = "Browser tidak mendukung Geolokasi GPS.";
        return;
    }

    statusText.innerText = "Mencari sinyal GPS...";

    navigator.geolocation.getCurrentPosition(
        function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;

            if (document.getElementById('checkin_lat')) document.getElementById('checkin_lat').value = lat;
            if (document.getElementById('checkin_lng')) document.getElementById('checkin_lng').value = lng;
            if (document.getElementById('checkout_lat')) document.getElementById('checkout_lat').value = lat;
            if (document.getElementById('checkout_lng')) document.getElementById('checkout_lng').value = lng;

            statusText.innerText = `Lokasi GPS Terkunci: (${lat.toFixed(4)}, ${lng.toFixed(4)})`;
            statusBox.classList.remove('bg-blue-50/60', 'text-blue-800');
            statusBox.classList.add('bg-emerald-50/60', 'text-emerald-800', 'border-emerald-100');
        },
        function(error) {
            statusText.innerText = "Izin GPS ditolak atau tidak tersedia. Mode manual/WFH aktif.";
        },
        { enableHighAccuracy: true, timeout: 8000 }
    );
}

document.addEventListener('DOMContentLoaded', detectGPS);
</script>
@endsection
