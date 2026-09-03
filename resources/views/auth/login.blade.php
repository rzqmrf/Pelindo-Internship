<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - PT Pelabuhan Indonesia (Persero)</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        pelindo: {
                            navy: '#002B49',
                            blue: '#005B9C',
                            light: '#0084D6',
                        }
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full bg-slate-900 font-sans text-slate-800 antialiased">

    <!-- Full-bleed 2-Column Split Screen Container -->
    <div class="min-h-screen w-full grid grid-cols-1 lg:grid-cols-12 bg-white">
        
        <!-- Left Side (7/12 cols on LG): Hero Brand Showcase with Dark Pelindo Gradient -->
        <div class="lg:col-span-7 bg-gradient-to-br from-[#001D33] via-[#004273] to-[#005B9C] p-8 lg:p-16 flex flex-col justify-between relative overflow-hidden text-white min-h-[400px] lg:min-h-screen">
            
            <!-- Background Ambient Glow & Harbor Wave Pattern -->
            <div class="absolute -top-32 -left-32 w-96 h-96 bg-blue-400/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-cyan-400/20 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="absolute inset-x-0 bottom-0 opacity-10 pointer-events-none">
                <svg viewBox="0 0 500 150" preserveAspectRatio="none" class="w-full h-48 fill-white">
                    <path d="M0,40 C150,90 350,-10 500,40 L500,150 L0,150 Z"></path>
                </svg>
            </div>

            <!-- Top Header Logo -->
            <div class="relative z-10 flex items-center justify-between">
                <div class="flex items-center space-x-3 bg-white/10 backdrop-blur-md px-4 py-2 rounded-2xl border border-white/15">
                    <img src="{{ asset('images/pelindo-logo-transparent.png') }}" alt="Pelindo Logo" class="h-10 w-auto object-contain">
                </div>

                <a href="{{ route('certificate.verify', ['token' => 'demo']) }}" class="hidden sm:inline-flex items-center space-x-2 text-xs font-bold text-blue-100 bg-white/10 hover:bg-white/20 backdrop-blur-md px-4 py-2 rounded-xl border border-white/20 transition">
                    <i class="fa-solid fa-qrcode"></i>
                    <span>Verifikasi Sertifikat</span>
                </a>
            </div>

            <!-- Middle Hero Content -->
            <div class="relative z-10 my-auto py-12 space-y-8 max-w-xl">
                <div class="inline-flex items-center space-x-2 bg-gradient-to-r from-cyan-400/20 to-blue-400/20 backdrop-blur-md px-3.5 py-1.5 rounded-full border border-cyan-300/30 text-xs font-extrabold text-cyan-200">
                    <span class="w-2 h-2 rounded-full bg-cyan-400 animate-ping"></span>
                    <span>Pelindo Internship Portal (PRO)</span>
                </div>

                <div class="space-y-3">
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight leading-tight">
                        Transformasi Digital Talenta Muda Pelindo
                    </h1>
                    <p class="text-xs sm:text-sm text-blue-100/80 font-medium leading-relaxed">
                        Platform terpadu pengelolaan program magang, presensi berbasis geofencing GPS, pelaporan logbook harian, dan penerbitan e-sertifikat digital terverifikasi.
                    </p>
                </div>

                <!-- 3 Feature Badges Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 pt-4">
                    <div class="p-4 bg-white/10 backdrop-blur-md rounded-2xl border border-white/15 space-y-1.5 hover:bg-white/15 transition">
                        <div class="w-8 h-8 rounded-xl bg-cyan-400/20 text-cyan-300 flex items-center justify-center text-sm font-bold">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <h3 class="font-extrabold text-xs text-white">Presensi GPS</h3>
                        <p class="text-[11px] text-blue-100/70 font-semibold leading-tight">Geofencing & Dynamic QR Code kantor.</p>
                    </div>

                    <div class="p-4 bg-white/10 backdrop-blur-md rounded-2xl border border-white/15 space-y-1.5 hover:bg-white/15 transition">
                        <div class="w-8 h-8 rounded-xl bg-amber-400/20 text-amber-300 flex items-center justify-center text-sm font-bold">
                            <i class="fa-solid fa-file-signature"></i>
                        </div>
                        <h3 class="font-extrabold text-xs text-white">Logbook Digital</h3>
                        <p class="text-[11px] text-blue-100/70 font-semibold leading-tight">Persetujuan harian pembimbing lapangan.</p>
                    </div>

                    <div class="p-4 bg-white/10 backdrop-blur-md rounded-2xl border border-white/15 space-y-1.5 hover:bg-white/15 transition">
                        <div class="w-8 h-8 rounded-xl bg-emerald-400/20 text-emerald-300 flex items-center justify-center text-sm font-bold">
                            <i class="fa-solid fa-certificate"></i>
                        </div>
                        <h3 class="font-extrabold text-xs text-white">E-Sertifikat</h3>
                        <p class="text-[11px] text-blue-100/70 font-semibold leading-tight">Verifikasi publik QR Code & transkrip nilai.</p>
                    </div>
                </div>
            </div>

            <!-- Bottom Left Footer -->
            <div class="relative z-10 pt-6 text-xs text-blue-200/60 font-semibold flex items-center justify-between border-t border-white/10">
                <p>&copy; {{ date('Y') }} PT Pelabuhan Indonesia (Persero)</p>
                <p>Regional 3 Surabaya</p>
            </div>
        </div>

        <!-- Right Side (5/12 cols on LG): Clean Modern Login Panel -->
        <div class="lg:col-span-5 bg-white p-8 lg:p-16 flex flex-col justify-between min-h-screen" x-data="{ showPassword: false }">
            
            <div class="max-w-md mx-auto w-full my-auto space-y-8">
                
                <!-- Login Header -->
                <div class="space-y-2 text-left">
                    <div class="w-12 h-12 bg-[#005B9C] text-white rounded-2xl flex items-center justify-center text-xl font-black shadow-lg shadow-blue-900/20 mb-4">
                        <i class="fa-solid fa-anchor"></i>
                    </div>
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight">Selamat Datang Kembali 👋</h2>
                    <p class="text-xs font-semibold text-slate-500">Silakan masuk menggunakan akun terdaftar Anda.</p>
                </div>

                @if ($errors->any())
                    <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl text-xs font-semibold space-y-1">
                        <div class="flex items-center space-x-2 font-bold text-rose-700">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <span>Gagal Masuk:</span>
                        </div>
                        <ul class="list-disc list-inside text-[11px] text-rose-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST" class="space-y-5">
                    @csrf
                    
                    <!-- Email Input -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-extrabold text-slate-700">Alamat Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-regular fa-envelope text-sm"></i>
                            </div>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                                   placeholder="nama@pelindo.co.id"
                                   class="w-full pl-10 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-blue-600 focus:bg-white transition">
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-extrabold text-slate-700">Kata Sandi</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-lock text-sm"></i>
                            </div>
                            <input :type="showPassword ? 'text' : 'password'" id="password" name="password" required
                                   placeholder="••••••••••••"
                                   class="w-full pl-10 pr-10 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-blue-600 focus:bg-white transition">
                            <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600">
                                <i class="fa-regular text-xs" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs pt-1">
                        <label class="flex items-center space-x-2 text-slate-600 font-extrabold cursor-pointer select-none">
                            <input type="checkbox" name="remember" class="rounded border-slate-300 text-blue-600 focus:ring-blue-600 w-4 h-4">
                            <span>Ingat Saya</span>
                        </label>
                        <a href="#" onclick="alert('Silakan hubungi HR Admin Pelindo untuk reset password.')" class="text-blue-600 hover:underline font-extrabold">Lupa Password?</a>
                    </div>

                    <button type="submit" 
                            class="w-full py-3.5 bg-[#005B9C] hover:bg-[#002B49] text-white font-extrabold rounded-2xl text-xs transition shadow-lg shadow-blue-900/20 flex items-center justify-center space-x-2 active:scale-98">
                        <span>Masuk Sekarang</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </button>
                </form>

                <!-- Quick Demo Presets Card Grid -->
                <div class="pt-4 border-t border-slate-100 space-y-3">
                    <div class="relative flex py-1 items-center">
                        <div class="flex-grow border-t border-slate-200"></div>
                        <span class="flex-shrink mx-4 text-[10px] font-black uppercase text-slate-400 tracking-wider">UJI COBA AKUN DEMO</span>
                        <div class="flex-grow border-t border-slate-200"></div>
                    </div>

                    <div class="grid grid-cols-3 gap-2.5">
                        <button type="button" onclick="fillLogin('admin@pelindo.co.id', 'password')"
                                class="p-3 bg-slate-50 hover:bg-blue-50/70 border border-slate-200 rounded-2xl text-center text-xs transition font-extrabold text-slate-700 shadow-2xs">
                            <i class="fa-solid fa-user-shield text-[#005B9C] block mb-1"></i>
                            <span class="text-[11px]">HR Admin</span>
                        </button>
                        <button type="button" onclick="fillLogin('ahmad@pelindo.co.id', 'password')"
                                class="p-3 bg-slate-50 hover:bg-cyan-50/70 border border-slate-200 rounded-2xl text-center text-xs transition font-extrabold text-slate-700 shadow-2xs">
                            <i class="fa-solid fa-user-tie text-cyan-600 block mb-1"></i>
                            <span class="text-[11px]">Supervisor</span>
                        </button>
                        <button type="button" onclick="fillLogin('bagas@pelindo.co.id', 'password')"
                                class="p-3 bg-slate-50 hover:bg-emerald-50/70 border border-slate-200 rounded-2xl text-center text-xs transition font-extrabold text-slate-700 shadow-2xs">
                            <i class="fa-solid fa-user-graduate text-emerald-600 block mb-1"></i>
                            <span class="text-[11px]">Intern</span>
                        </button>
                    </div>
                </div>

            </div>

            <footer class="text-center text-xs font-semibold text-slate-400 pt-6">
                &copy; {{ date('Y') }} PT Pelabuhan Indonesia (Persero). All Rights Reserved.
            </footer>

        </div>

    </div>

    <script>
        function fillLogin(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
        }
    </script>
</body>
</html>
