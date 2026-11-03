@extends('layouts.app')

@section('content')
<div class="space-y-8">

    <!-- Header -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/70 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="space-y-1.5">
            <h1 class="text-2xl font-black text-[#002B49] tracking-tight">Parameter Operasional &amp; Scheduler</h1>
            <p class="text-xs sm:text-sm font-semibold text-slate-600">
                Konfigurasi dinamis titik lokasi kantor (geofencing), jam batas masuk, tarif uang saku harian, dan pemicu pengingat otomatis.
            </p>
        </div>

        <a href="{{ route('superadmin.system.backup_db') }}"
           class="px-5 py-3 rounded-xl bg-[#002B49] hover:bg-slate-900 text-white font-extrabold text-xs transition flex items-center gap-2 self-start md:self-auto shrink-0">
            <i class="fa-solid fa-database"></i>
            <span>Unduh Backup Database</span>
        </a>
    </div>

    @if ($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-900 p-5 rounded-2xl text-xs font-semibold space-y-1">
            <p class="font-extrabold">Periksa kembali nilai parameter:</p>
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- Form Parameter Utama (7 Kolom) -->
        <form action="{{ route('superadmin.settings.update') }}" method="POST"
              class="lg:col-span-7 bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/70 shadow-xs space-y-7">
            @csrf

            <!-- Bagian 1: Geofencing & Presensi -->
            <div class="space-y-4">
                <div class="border-b border-slate-100 pb-3">
                    <h2 class="text-base font-extrabold text-[#002B49]">1. Geofencing GPS &amp; Jam Presensi</h2>
                    <p class="text-xs font-medium text-slate-500 mt-0.5">Digunakan langsung saat peserta magang melakukan Check-In mode WFO.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="office_lat" class="block text-xs font-bold text-slate-700 mb-1.5">Latitude Titik Kantor</label>
                        <input type="text" id="office_lat" name="office_lat" required
                               value="{{ old('office_lat', $settings['office_lat'] ?? '-7.2023000') }}"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 text-xs font-mono font-bold text-slate-800 focus:outline-none focus:border-[#005B9C]">
                    </div>
                    <div>
                        <label for="office_lng" class="block text-xs font-bold text-slate-700 mb-1.5">Longitude Titik Kantor</label>
                        <input type="text" id="office_lng" name="office_lng" required
                               value="{{ old('office_lng', $settings['office_lng'] ?? '112.7315000') }}"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 text-xs font-mono font-bold text-slate-800 focus:outline-none focus:border-[#005B9C]">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="max_geofence_meters" class="block text-xs font-bold text-slate-700 mb-1.5">Batas Toleransi Radius WFO (Meter)</label>
                        <input type="number" id="max_geofence_meters" name="max_geofence_meters" min="20" max="50000" required
                               value="{{ old('max_geofence_meters', $settings['max_geofence_meters'] ?? '500') }}"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 focus:outline-none focus:border-[#005B9C]">
                    </div>
                    <div>
                        <label for="late_cutoff_time" class="block text-xs font-bold text-slate-700 mb-1.5">Jam Batas Tepat Waktu (WIB)</label>
                        <input type="time" id="late_cutoff_time" name="late_cutoff_time" required
                               value="{{ old('late_cutoff_time', $settings['late_cutoff_time'] ?? '08:30') }}"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 focus:outline-none focus:border-[#005B9C]">
                    </div>
                </div>
            </div>

            <!-- Bagian 2: Tarif Uang Saku Harian -->
            <div class="space-y-4 pt-2">
                <div class="border-b border-slate-100 pb-3">
                    <h2 class="text-base font-extrabold text-[#002B49]">2. Tarif Perhitungan Uang Saku Harian</h2>
                    <p class="text-xs font-medium text-slate-500 mt-0.5">Nominal rupiah per hari kehadiran aktif untuk kalkulasi klaim uang saku peserta.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="allowance_wfo_rate" class="block text-xs font-bold text-slate-700 mb-1.5">Tarif WFO (Rp)</label>
                        <input type="number" id="allowance_wfo_rate" name="allowance_wfo_rate" min="0" required
                               value="{{ old('allowance_wfo_rate', $settings['allowance_wfo_rate'] ?? '71250') }}"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 focus:outline-none focus:border-[#005B9C]">
                    </div>
                    <div>
                        <label for="allowance_wfh_rate" class="block text-xs font-bold text-slate-700 mb-1.5">Tarif WFH (Rp)</label>
                        <input type="number" id="allowance_wfh_rate" name="allowance_wfh_rate" min="0" required
                               value="{{ old('allowance_wfh_rate', $settings['allowance_wfh_rate'] ?? '71250') }}"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 focus:outline-none focus:border-[#005B9C]">
                    </div>
                    <div>
                        <label for="allowance_wfa_rate" class="block text-xs font-bold text-slate-700 mb-1.5">Tarif WFA (Rp)</label>
                        <input type="number" id="allowance_wfa_rate" name="allowance_wfa_rate" min="0" required
                               value="{{ old('allowance_wfa_rate', $settings['allowance_wfa_rate'] ?? '71250') }}"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 focus:outline-none focus:border-[#005B9C]">
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full py-3.5 rounded-xl bg-[#005B9C] hover:bg-[#002B49] text-white font-extrabold text-xs transition">
                Simpan Parameter Operasional
            </button>
        </form>

        <!-- Panel Samping: Token QR Kantor, Scheduler, & WhatsApp Gateway (5 Kolom) -->
        <div class="lg:col-span-5 space-y-6">

            <!-- Token QR Kantor Hari Ini -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/70 shadow-xs space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-extrabold uppercase tracking-wider text-slate-500">Token QR Kantor Hari Ini</span>
                    <span class="px-2.5 py-0.5 rounded bg-blue-50 text-[#005B9C] text-[10px] font-extrabold">{{ now()->format('d/m/Y') }}</span>
                </div>
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                    <code class="text-base font-black text-[#002B49] tracking-wide">{{ $dailyQrToken }}</code>
                    <button type="button" onclick="navigator.clipboard.writeText('{{ $dailyQrToken }}')"
                            class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 hover:bg-slate-100 text-xs font-bold text-slate-700 transition">
                        Salin
                    </button>
                </div>
                <p class="text-[11px] font-medium text-slate-500 leading-relaxed">
                    Token ini berubah otomatis setiap tengah malam berdasarkan <code>APP_KEY</code>. Digunakan untuk layar resepsionis apabila sinyal GPS di dalam gedung terhalang.
                </p>
            </div>

            <!-- Eksekusi Manual Scheduler Pengingat -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/70 shadow-xs space-y-4">
                <div>
                    <h3 class="text-base font-extrabold text-[#002B49]">Eksekusi Pengingat Harian</h3>
                    <p class="text-xs font-medium text-slate-500 mt-1">
                        Jadwal otomatis berjalan setiap Senin s.d. Jumat pukul 17:00 WIB (dan otomatis berhenti saat Mode Pemeliharaan aktif).
                    </p>
                </div>

                <form action="{{ route('superadmin.system.reminders') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full py-3 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-paper-plane"></i>
                        <span>Jalankan Pengingat Sekarang (--force)</span>
                    </button>
                </form>
            </div>

            <!-- Status & Uji Langsung Gateway WhatsApp -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/70 shadow-xs space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-extrabold text-[#002B49]">Diagnostik Gateway WhatsApp</h3>
                    <span class="px-2.5 py-0.5 rounded text-[10px] font-extrabold uppercase {{ $waConfig['driver'] !== 'log' && $waConfig['has_token'] ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-700' }}">
                        Driver: {{ $waConfig['driver'] }}
                    </span>
                </div>
                <div class="text-xs text-slate-600 space-y-1 font-medium">
                    <p>Endpoint API: <code class="text-[11px] bg-slate-100 px-1.5 py-0.5 rounded">{{ $waConfig['url'] }}</code></p>
                    <p>Status API Token: <strong>{{ $waConfig['has_token'] ? 'Terpasang di .env' : 'Mode simulasi ke laravel.log' }}</strong></p>
                </div>

                <form action="{{ route('superadmin.settings.test_wa') }}" method="POST" class="pt-2 border-t border-slate-100 space-y-2.5">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Nomor Tujuan Uji WhatsApp</label>
                        <input type="text" name="target_phone" required placeholder="0812xxxxxxxx"
                               value="{{ Auth::user()->phone ?? '' }}"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-none focus:border-[#005B9C]">
                    </div>
                    <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-[#005B9C] hover:bg-[#002B49] text-white font-extrabold text-xs transition flex items-center justify-center gap-2">
                        <i class="fa-brands fa-whatsapp text-sm"></i>
                        <span>Kirim Pesan Uji WhatsApp</span>
                    </button>
                </form>
            </div>

            <!-- Diagnostik Pengiriman Email SMTP -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/70 shadow-xs space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-extrabold text-[#002B49]">Diagnostik Server Email</h3>
                    <span class="px-2.5 py-0.5 rounded bg-slate-100 text-slate-700 text-[10px] font-extrabold uppercase">
                        Mailer: {{ $mailConfig['mailer'] ?? 'log' }}
                    </span>
                </div>
                <div class="text-xs text-slate-600 space-y-1 font-medium">
                    <p>Pengirim Aktif: <code class="text-[11px] bg-slate-100 px-1.5 py-0.5 rounded">{{ $mailConfig['from'] ?? '-' }}</code></p>
                    <p>SMTP Host: <strong>{{ $mailConfig['host'] ?? '-' }}:{{ $mailConfig['port'] ?? '-' }}</strong></p>
                </div>

                <form action="{{ route('superadmin.settings.test_email') }}" method="POST" class="pt-2 border-t border-slate-100 space-y-2.5">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Alamat Email Tujuan Uji</label>
                        <input type="email" name="target_email" required placeholder="nama@pelindo.co.id"
                               value="{{ Auth::user()->email ?? '' }}"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-none focus:border-[#005B9C]">
                    </div>
                    <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-[#002B49] hover:bg-slate-900 text-white font-extrabold text-xs transition flex items-center justify-center gap-2">
                        <i class="fa-regular fa-envelope"></i>
                        <span>Kirim Email Uji Coba</span>
                    </button>
                </form>
            </div>

        </div>
    </div>

</div>
@endsection
