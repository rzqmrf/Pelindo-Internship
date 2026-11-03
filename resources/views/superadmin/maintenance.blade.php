@extends('layouts.app')

@section('content')
<div class="space-y-8">

    <!-- Header Halaman -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/70 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="space-y-1.5">
            <h1 class="text-2xl font-black text-[#002B49] tracking-tight">
                Kontrol Pemeliharaan &amp; Pemberitahuan Sistem
            </h1>
            <p class="text-xs sm:text-sm font-semibold text-slate-600">
                Pasang banner jadwal pemeliharaan saat sistem tetap berjalan, atau aktifkan kunci pemeliharaan penuh (HTTP 503).
            </p>
        </div>

        @if($lastSettingUpdate)
            <div class="text-xs font-semibold text-slate-500 bg-slate-50 border border-slate-200 px-4 py-2.5 rounded-xl shrink-0">
                Terakhir diperbarui: <strong class="text-slate-800">{{ $lastSettingUpdate->updated_at?->diffForHumans() }}</strong>
                @if($lastSettingUpdate->updater)
                    oleh <strong class="text-slate-800">{{ $lastSettingUpdate->updater->name }}</strong>
                @endif
            </div>
        @endif
    </div>

    @if ($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-900 p-5 rounded-2xl text-xs font-semibold space-y-1">
            <p class="font-extrabold">Periksa kembali isian formulir:</p>
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">

        <!-- Kolom 1: Banner Pemberitahuan / Pra-Maintenance (Sistem Tetap Aktif) -->
        <form action="{{ route('superadmin.maintenance.update') }}" method="POST"
              x-data="{
                  enabled: {{ ($settings['banner_enabled'] ?? '0') === '1' ? 'true' : 'false' }},
                  level: '{{ old('banner_level', $settings['banner_level'] ?? 'info') }}',
                  title: @js(old('banner_title', $settings['banner_title'] ?? 'Jadwal Pemeliharaan Berkala')),
                  message: @js(old('banner_message', $settings['banner_message'] ?? '')),
                  schedule: @js(old('banner_schedule_at', $settings['banner_schedule_at'] ?? ''))
              }"
              class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/70 shadow-xs space-y-6">
            @csrf
            <input type="hidden" name="section" value="banner">

            <div class="flex items-start justify-between gap-4 pb-4 border-b border-slate-100">
                <div>
                    <span class="px-2.5 py-1 rounded-md bg-blue-50 text-[#005B9C] text-[11px] font-extrabold uppercase tracking-wider">Mekanisme 1: Sistem Tetap Aktif</span>
                    <h2 class="text-lg font-extrabold text-[#002B49] mt-2">Banner Pengumuman Teknis</h2>
                    <p class="text-xs font-medium text-slate-600 mt-1">
                        Tampil di bagian atas layar seluruh pengguna (Admin HR, Pembimbing, Peserta Magang, serta halaman Login).
                    </p>
                </div>

                <label class="inline-flex items-center cursor-pointer shrink-0 mt-1">
                    <input type="hidden" name="banner_enabled" value="0">
                    <input type="checkbox" name="banner_enabled" value="1" x-model="enabled" class="sr-only peer">
                    <div class="w-12 h-7 bg-slate-200 peer-focus:outline-2 peer-focus:outline-[#005B9C] rounded-full peer peer-checked:bg-[#005B9C] relative transition after:content-[''] after:absolute after:top-1 after:left-1 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-5"></div>
                </label>
            </div>

            <div class="space-y-4">
                <div>
                    <label for="banner_level" class="block text-xs font-bold text-slate-700 mb-1.5">Tingkat Kepentingan Banner</label>
                    <select id="banner_level" name="banner_level" x-model="level"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-800 focus:outline-none focus:border-[#005B9C] focus:ring-1 focus:ring-[#005B9C]">
                        <option value="info">Informasi Umum (Biru Pelindo)</option>
                        <option value="warning">Peringatan Jadwal Maintenance (Kuning Amber)</option>
                        <option value="critical">Gangguan / Darurat Sistem (Merah)</option>
                    </select>
                </div>

                <div>
                    <label for="banner_title" class="block text-xs font-bold text-slate-700 mb-1.5">Judul Singkat Banner</label>
                    <input type="text" id="banner_title" name="banner_title" x-model="title"
                           placeholder="Contoh: Jadwal Pemeliharaan Server"
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#005B9C] focus:ring-1 focus:ring-[#005B9C]">
                </div>

                <div>
                    <label for="banner_message" class="block text-xs font-bold text-slate-700 mb-1.5">Isi Pesan Pemberitahuan</label>
                    <textarea id="banner_message" name="banner_message" rows="3" x-model="message"
                              placeholder="Contoh: Sistem akan menjalani pemeliharaan database pada Sabtu pukul 22:00 WIB. Mohon simpan draft logbook Anda sebelum waktu tersebut."
                              class="w-full px-4 py-3 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#005B9C] focus:ring-1 focus:ring-[#005B9C]"></textarea>
                </div>

                <div>
                    <label for="banner_schedule_at" class="block text-xs font-bold text-slate-700 mb-1.5">Label Waktu Pelaksanaan (Opsional)</label>
                    <input type="text" id="banner_schedule_at" name="banner_schedule_at" x-model="schedule"
                           placeholder="Contoh: Sabtu, 7 Nov 2026 22:00 - 23:30 WIB"
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#005B9C] focus:ring-1 focus:ring-[#005B9C]">
                </div>
            </div>

            <!-- Pratinjau Langsung -->
            <div class="space-y-2 pt-2">
                <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Pratinjau Tampilan Banner</p>
                <div class="p-3.5 rounded-2xl border text-xs leading-relaxed"
                     :class="{
                         'bg-rose-50 border-rose-200 text-rose-950': level === 'critical',
                         'bg-amber-50 border-amber-200 text-amber-950': level === 'warning',
                         'bg-blue-50 border-blue-200 text-slate-900': level === 'info'
                     }">
                    <strong class="font-extrabold" x-text="(title || 'Pemberitahuan') + ':'"></strong>
                    <span class="font-semibold ml-1" x-text="message || 'Tulis isi pesan untuk melihat pratinjau banner di sini.'"></span>
                    <template x-if="schedule">
                        <span class="inline-block ml-1.5 px-2 py-0.5 rounded bg-white border border-slate-300 text-slate-800 text-[10px] font-bold" x-text="'Jadwal: ' + schedule"></span>
                    </template>
                </div>
            </div>

            <button type="submit" class="w-full py-3.5 rounded-xl bg-[#005B9C] hover:bg-[#002B49] text-white font-extrabold text-xs transition">
                Simpan Pengaturan Banner
            </button>
        </form>

        <!-- Kolom 2: Full Maintenance Mode (Kunci Akses Non-Superadmin) -->
        <form action="{{ route('superadmin.maintenance.update') }}" method="POST"
              x-data="{ active: {{ ($settings['maintenance_mode'] ?? '0') === '1' ? 'true' : 'false' }} }"
              class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/70 shadow-xs space-y-6">
            @csrf
            <input type="hidden" name="section" value="maintenance">

            <div class="flex items-start justify-between gap-4 pb-4 border-b border-slate-100">
                <div>
                    <span class="px-2.5 py-1 rounded-md text-[11px] font-extrabold uppercase tracking-wider"
                          :class="active ? 'bg-rose-100 text-rose-800' : 'bg-slate-100 text-slate-700'">
                        <span x-text="active ? 'Mode Terkunci (HTTP 503 Aktif)' : 'Mekanisme 2: Kunci Akses Penuh'"></span>
                    </span>
                    <h2 class="text-lg font-extrabold text-[#002B49] mt-2">Mode Pemeliharaan Penuh</h2>
                    <p class="text-xs font-medium text-slate-600 mt-1">
                        Saat aktif, pengguna biasa diarahkan ke halaman pemeliharaan. Akun Superadmin tetap bebas mengakses seluruh fitur.
                    </p>
                </div>

                <label class="inline-flex items-center cursor-pointer shrink-0 mt-1">
                    <input type="hidden" name="maintenance_mode" value="0">
                    <input type="checkbox" name="maintenance_mode" value="1" x-model="active" class="sr-only peer">
                    <div class="w-12 h-7 bg-slate-200 peer-focus:outline-2 peer-focus:outline-rose-600 rounded-full peer peer-checked:bg-rose-600 relative transition after:content-[''] after:absolute after:top-1 after:left-1 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-5"></div>
                </label>
            </div>

            <div class="space-y-4">
                <div>
                    <label for="maintenance_title" class="block text-xs font-bold text-slate-700 mb-1.5">Judul Halaman Pemeliharaan</label>
                    <input type="text" id="maintenance_title" name="maintenance_title" required
                           value="{{ old('maintenance_title', $settings['maintenance_title'] ?? 'Pemeliharaan Sistem Terjadwal') }}"
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#005B9C] focus:ring-1 focus:ring-[#005B9C]">
                </div>

                <div>
                    <label for="maintenance_message" class="block text-xs font-bold text-slate-700 mb-1.5">Pesan Penjelasan untuk Pengguna</label>
                    <textarea id="maintenance_message" name="maintenance_message" rows="3" required
                              class="w-full px-4 py-3 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#005B9C] focus:ring-1 focus:ring-[#005B9C]">{{ old('maintenance_message', $settings['maintenance_message'] ?? '') }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="maintenance_eta" class="block text-xs font-bold text-slate-700 mb-1.5">Estimasi Selesai (Hitung Mundur)</label>
                        @php
                            $etaInputVal = '';
                            if (!empty($settings['maintenance_eta'])) {
                                try {
                                    $etaInputVal = \Carbon\Carbon::parse($settings['maintenance_eta'])->format('Y-m-d\TH:i');
                                } catch (\Throwable $e) {
                                    $etaInputVal = '';
                                }
                            }
                        @endphp
                        <input type="datetime-local" id="maintenance_eta" name="maintenance_eta"
                               value="{{ old('maintenance_eta', $etaInputVal) }}"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#005B9C] focus:ring-1 focus:ring-[#005B9C]">
                    </div>

                    <div>
                        <label for="maintenance_contact" class="block text-xs font-bold text-slate-700 mb-1.5">Email Kontak Bantuan IT</label>
                        <input type="email" id="maintenance_contact" name="maintenance_contact"
                               value="{{ old('maintenance_contact', $settings['maintenance_contact'] ?? 'it.helpdesk@pelindo.co.id') }}"
                               placeholder="it.helpdesk@pelindo.co.id"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#005B9C] focus:ring-1 focus:ring-[#005B9C]">
                    </div>
                </div>
                <div class="pt-1">
                    <label class="flex items-start gap-3 p-3.5 rounded-2xl bg-slate-50 border border-slate-200 cursor-pointer">
                        <input type="hidden" name="maintenance_allow_cert_verify" value="0">
                        <input type="checkbox" name="maintenance_allow_cert_verify" value="1"
                               @checked(($settings['maintenance_allow_cert_verify'] ?? '1') === '1')
                               class="mt-0.5 rounded border-slate-300 text-[#005B9C] focus:ring-[#005B9C] w-4 h-4">
                        <span class="text-xs font-semibold text-slate-700 leading-relaxed">
                            <strong class="text-slate-900 block">Tetap Buka Verifikasi Publik QR E-Sertifikat</strong>
                            Pihak eksternal/kampus yang memindai QR Code sertifikat tetap dapat memvalidasi keaslian sertifikat selama pemeliharaan internal berlangsung.
                        </span>
                    </label>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-700 space-y-1">
                <p class="font-extrabold text-slate-900">Catatan Akses Pengembang:</p>
                <p class="font-medium leading-relaxed">
                    Meskipun Mode Pemeliharaan Penuh diaktifkan, halaman <code>/login</code> tetap menyediakan akses masuk khusus akun ber-role <strong>Superadmin IT</strong>, termasuk fitur <em>User Impersonation</em> untuk menguji tampilan peserta atau admin sebelum portal dibuka kembali.
                </p>
            </div>

            <button type="submit"
                    class="w-full py-3.5 rounded-xl text-white font-extrabold text-xs transition"
                    :class="active ? 'bg-rose-600 hover:bg-rose-700' : 'bg-[#002B49] hover:bg-slate-900'">
                <span x-text="active ? 'Terapkan & Kunci Akses Pengguna (HTTP 503)' : 'Simpan & Buka Akses Normal Portal'"></span>
            </button>
        </form>

    </div>

    <!-- Tautan Pratinjau Bypass Pemeliharaan (Secret Access Link) -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/70 shadow-xs flex flex-col lg:flex-row lg:items-center justify-between gap-5"
         x-data="{ copied: false }">
        <div class="space-y-1.5 max-w-2xl">
            <span class="px-2.5 py-1 rounded-md bg-slate-100 text-slate-700 text-[11px] font-extrabold uppercase tracking-wider">Tautan Akses Pratinjau Khusus</span>
            <h2 class="text-lg font-extrabold text-[#002B49]">Bypass Pemeliharaan untuk Penguji / Manajemen</h2>
            <p class="text-xs font-medium text-slate-600 leading-relaxed">
                Bagikan tautan rahasia di bawah ini apabila Admin HR atau pimpinan perlu meninjau portal dari perangkat mereka saat Mode Pemeliharaan (HTTP 503) masih aktif. Mengacak ulang token akan mencabut seluruh akses tautan sebelumnya secara seketika.
            </p>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 w-full lg:w-auto">
            <input type="text" readonly value="{{ $bypassUrl }}"
                   class="px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 font-mono text-xs font-bold text-slate-700 w-full sm:w-80 focus:outline-none">

            <button type="button"
                    @click="navigator.clipboard.writeText(@js($bypassUrl)); copied = true; setTimeout(() => copied = false, 2000)"
                    class="px-4 py-3 rounded-xl bg-[#005B9C] hover:bg-[#002B49] text-white font-extrabold text-xs transition whitespace-nowrap">
                <span x-text="copied ? 'Tersalin!' : 'Salin Tautan'"></span>
            </button>

            <form action="{{ route('superadmin.maintenance.regenerate_bypass') }}" method="POST"
                  onsubmit="return confirm('Acak ulang token bypass? Tautan pratinjau sebelumnya tidak dapat dipakai lagi.')">
                @csrf
                <button type="submit" class="w-full sm:w-auto px-4 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-extrabold text-xs transition whitespace-nowrap">
                    Acak Ulang Token
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
