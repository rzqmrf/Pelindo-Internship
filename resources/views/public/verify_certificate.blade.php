<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Keaslian Sertifikat Magang - PT Pelabuhan Indonesia (Persero)</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen text-slate-800 flex flex-col justify-between" x-data="{ showScanner: false }">

    <!-- Navbar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-700 to-blue-500 flex items-center justify-center text-white shadow-md shadow-blue-500/20">
                    <i class="fa-solid fa-anchor text-lg"></i>
                </div>
                <div>
                    <h1 class="text-base font-black tracking-tight text-slate-900 leading-none">PELINDO</h1>
                    <p class="text-[10px] font-bold text-blue-600 tracking-wider uppercase mt-0.5">Sistem Verifikasi Sertifikat</p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <button @click="showScanner = !showScanner" class="text-xs font-extrabold text-blue-700 bg-blue-50 hover:bg-blue-100 px-3.5 py-1.5 rounded-lg border border-blue-200 transition flex items-center gap-1.5">
                    <i class="fa-solid fa-qrcode"></i>
                    <span x-text="showScanner ? 'Tutup Kamera' : 'Scan QR Kamera'">Scan QR Kamera</span>
                </button>
                <a href="{{ route('login') }}" class="text-xs font-semibold text-slate-600 hover:text-blue-600 px-3 py-1.5 rounded-lg border border-slate-200 hover:border-blue-300 transition">
                    <i class="fa-solid fa-right-to-bracket mr-1"></i> Masuk Portal
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-3xl mx-auto px-4 py-10 w-full flex-1 flex flex-col justify-center">
        @if($certificate && $certificate->status === 'published')
            <!-- Verified Success Card -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xl overflow-hidden">
                <!-- Status Banner -->
                <div class="bg-emerald-50 border-b border-emerald-100 p-6 sm:p-8 text-center relative overflow-hidden">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-500 text-white shadow-lg shadow-emerald-500/30 mb-4 animate-bounce">
                        <i class="fa-solid fa-circle-check text-3xl"></i>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-black text-emerald-950">SERTIFIKAT RESMI & TERVERIFIKASI</h2>
                    <p class="text-xs sm:text-sm text-emerald-700 font-medium mt-1">Dokumen ini sah dan terdaftar resmi pada basis data PT Pelabuhan Indonesia (Persero).</p>
                    
                    <div class="mt-4 inline-flex items-center space-x-2 bg-white/80 backdrop-blur px-4 py-1.5 rounded-full border border-emerald-200 text-xs font-bold text-emerald-800 shadow-sm">
                        <i class="fa-solid fa-fingerprint text-emerald-600"></i>
                        <span>No: {{ $certificate->certificate_number }}</span>
                    </div>
                </div>

                <!-- Detail Body -->
                <div class="p-6 sm:p-8 space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Nama Peserta Magang</span>
                            <span class="text-base font-extrabold text-slate-900 block">{{ $certificate->intern?->name }}</span>
                            <span class="text-xs text-slate-500 mt-0.5 block">NIM/NIS: {{ $certificate->intern?->internProfile?->student_id_number ?? '-' }}</span>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Institusi / Universitas</span>
                            <span class="text-sm font-bold text-slate-900 block">{{ $certificate->intern?->internProfile?->institution ?? '-' }}</span>
                            <span class="text-xs text-slate-500 mt-0.5 block">{{ $certificate->intern?->internProfile?->major ?? 'Program Studi' }}</span>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Penempatan Divisi</span>
                            <span class="text-sm font-bold text-slate-900 block">{{ $certificate->intern?->internProfile?->division?->name ?? 'Divisi Pelindo' }}</span>
                            <span class="text-xs text-slate-500 mt-0.5 block">Regional 3 Surabaya</span>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Hasil Evaluasi Kelulusan</span>
                            <span class="text-sm font-extrabold text-emerald-600 block">
                                {{ $certificate->assessment?->predicate ?? 'Sangat Baik (A)' }}
                            </span>
                            <span class="text-xs text-slate-500 mt-0.5 block">Skor: {{ number_format($certificate->assessment?->final_score ?? 85, 2) }} / 100</span>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 pt-5 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                        <div>
                            <span>Tanggal Terbit: <strong>{{ $certificate->issue_date ? $certificate->issue_date->isoFormat('D MMMM YYYY') : '-' }}</strong></span>
                        </div>
                        <a href="{{ route('certificate.download', ['token' => $certificate->verification_token]) }}" class="inline-flex items-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 py-2.5 rounded-xl shadow-lg shadow-blue-500/25 transition">
                            <i class="fa-solid fa-file-pdf"></i>
                            <span>Unduh Salinan PDF</span>
                        </a>
                    </div>
                </div>
            </div>
        @else
            <!-- Invalid / Not Found Card -->
            <div class="bg-white rounded-3xl border border-rose-200 shadow-xl p-8 text-center max-w-md mx-auto">
                <div class="w-16 h-16 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto mb-4 text-2xl">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <h2 class="text-xl font-bold text-slate-900">Sertifikat Tidak Ditemukan</h2>
                <p class="text-xs text-slate-500 mt-2">Nomor token verifikasi <strong>{{ $token }}</strong> tidak valid, telah dicabut, atau belum diterbitkan oleh pihak manajemen Pelindo.</p>
                <div class="mt-6">
                    <a href="{{ url('/') }}" class="inline-block bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold px-5 py-2.5 rounded-xl transition">
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
        @endif
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-400">
        <p>&copy; {{ date('Y') }} PT Pelabuhan Indonesia (Persero). All Rights Reserved.</p>
    </footer>

</body>
</html>
