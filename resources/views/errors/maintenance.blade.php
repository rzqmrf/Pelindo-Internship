<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemeliharaan Sistem - Pelindo Internship Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-[#F8FAFC] text-slate-800 flex flex-col justify-between p-6 sm:p-10">

    <header class="max-w-3xl w-full mx-auto flex items-center justify-between">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/pelindo-logo-transparent.png') }}" alt="Pelindo Solusi Digital" class="h-10 w-auto object-contain">
            <span class="px-2.5 py-1 bg-amber-100 text-amber-900 text-xs font-extrabold rounded-lg uppercase tracking-wider">HTTP 503</span>
        </div>
        <a href="{{ route('login') }}" class="px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-xs font-extrabold text-[#002B49] hover:bg-slate-50 transition focus:outline-2 focus:outline-[#005B9C]">
            <i class="fa-solid fa-user-gear mr-1.5 text-[#005B9C]"></i>
            Masuk Pengembang
        </a>
    </header>

    <main class="max-w-2xl w-full mx-auto my-10">
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-8 sm:p-12 space-y-6">
            <div class="w-14 h-14 rounded-2xl bg-[#E6F0FA] text-[#005B9C] flex items-center justify-center text-2xl">
                <i class="fa-solid fa-screwdriver-wrench"></i>
            </div>

            <div class="space-y-3">
                <h1 class="text-2xl sm:text-3xl font-black text-[#002B49] tracking-tight">
                    {{ $maintenance['title'] ?? 'Pemeliharaan Sistem Terjadwal' }}
                </h1>
                <p class="text-sm sm:text-base text-slate-600 font-medium leading-relaxed">
                    {{ $maintenance['message'] ?? 'Kami sedang melakukan pemeliharaan berkala untuk menjaga keandalan layanan Pelindo Internship Portal.' }}
                </p>
            </div>

            @if(!empty($maintenance['eta']))
                @php
                    try {
                        $etaCarbon = \Carbon\Carbon::parse($maintenance['eta'])->locale('id');
                        $etaFormatted = $etaCarbon->isoFormat('dddd, D MMMM YYYY [pukul] HH:mm') . ' WIB';
                        $etaIso = $etaCarbon->toIso8601String();
                    } catch (\Throwable $e) {
                        $etaFormatted = $maintenance['eta'];
                        $etaIso = null;
                    }
                @endphp
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2" @if($etaIso) data-eta="{{ $etaIso }}" id="eta-box" @endif>
                    <p class="text-xs font-extrabold uppercase tracking-wider text-slate-500">Estimasi Selesai Pemeliharaan</p>
                    <p class="text-base font-extrabold text-[#002B49]">{{ $etaFormatted }}</p>
                    @if($etaIso)
                        <p id="eta-countdown" class="text-xs font-bold text-[#005B9C]"></p>
                    @endif
                </div>
            @endif

            <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="text-xs text-slate-600 font-semibold">
                    Butuh bantuan mendesak? Hubungi tim IT:
                    <a href="mailto:{{ $maintenance['contact'] ?? 'it.helpdesk@pelindo.co.id' }}" class="text-[#005B9C] font-extrabold hover:underline">
                        {{ $maintenance['contact'] ?? 'it.helpdesk@pelindo.co.id' }}
                    </a>
                </div>
                <button type="button" onclick="window.location.reload()" class="px-5 py-3 rounded-xl bg-[#005B9C] hover:bg-[#002B49] text-white text-xs font-extrabold transition focus:outline-2 focus:outline-offset-2 focus:outline-[#005B9C]">
                    <i class="fa-solid fa-rotate-right mr-1.5"></i>
                    Muat Ulang Halaman
                </button>
            </div>
        </div>
    </main>

    <footer class="max-w-3xl w-full mx-auto text-center text-xs font-semibold text-slate-500">
        &copy; {{ date('Y') }} PT Pelabuhan Indonesia (Persero). Seluruh hak cipta dilindungi.
    </footer>

    <script>
        (function () {
            const box = document.getElementById('eta-box');
            const label = document.getElementById('eta-countdown');
            if (!box || !label) return;
            const target = new Date(box.dataset.eta).getTime();
            if (isNaN(target)) return;

            function tick() {
                const diff = target - Date.now();
                if (diff <= 0) {
                    label.textContent = 'Waktu estimasi telah tercapai. Silakan muat ulang halaman.';
                    return;
                }
                const hours = Math.floor(diff / 3600000);
                const mins = Math.floor((diff % 3600000) / 60000);
                const secs = Math.floor((diff % 60000) / 1000);
                label.textContent = 'Hitung mundur: ' + hours + ' jam ' + mins + ' menit ' + secs + ' detik';
            }
            tick();
            setInterval(tick, 1000);
        })();
    </script>
</body>
</html>
