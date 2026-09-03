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
                            navy: '#0F2A4A',
                            blue: '#0B63E5',
                            accent: '#005B9C',
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

<body class="h-full bg-white font-sans text-slate-800 antialiased">

    <!-- Full-bleed 2-Column Split Screen matching exact screenshot layout -->
    <div class="min-h-screen w-full grid grid-cols-1 lg:grid-cols-12">

        <!-- Left Column (6/12 cols): Light Blue Branding Showcase -->
        <div class="lg:col-span-6 bg-gradient-to-b from-blue-50/50 via-slate-50 to-blue-100/40 p-8 sm:p-12 lg:p-16 flex flex-col justify-between relative overflow-hidden border-r border-slate-100 min-h-[550px] lg:min-h-screen">

            <!-- Top Logo -->
            <div class="relative z-10">
                <img src="{{ asset('images/pelindo-logo-transparent.png') }}" alt="PELINDO SOLUSI DIGITAL" class="h-12 sm:h-14 w-auto object-contain">
            </div>

            <!-- Middle Content Showcase -->
            <div class="relative z-10 my-auto py-8 space-y-6 max-w-lg">
                <p class="text-xs sm:text-sm font-extrabold text-[#0B63E5]">Pelindo Internship Portal (PRO)</p>

                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-[#0F2A4A] tracking-tight leading-[1.15]">
                    Kelola Program Magang dalam Satu Platform
                </h1>

                <p class="text-xs sm:text-sm text-slate-500 font-medium leading-relaxed">
                    Platform terpadu untuk mendukung kelancaran aktivitas magang di lingkungan Pelindo secara digital, terstruktur, dan terverifikasi.
                </p>

                <div class="border-t border-slate-200/80 my-6"></div>

                <!-- 3 Circular Icon Feature Columns -->
                <div class="grid grid-cols-3 gap-4 pt-2">
                    <!-- Feature 1 -->
                    <div class="space-y-2">
                        <div class="w-10 h-10 rounded-full bg-blue-100/70 text-[#0B63E5] flex items-center justify-center text-sm shadow-xs">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <h3 class="font-extrabold text-slate-800 text-xs sm:text-sm">Presensi GPS</h3>
                        <p class="text-[11px] text-slate-500 font-medium leading-snug">Geofencing & QR Code kantor untuk presensi yang akurat.</p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="space-y-2">
                        <div class="w-10 h-10 rounded-full bg-blue-100/70 text-[#0B63E5] flex items-center justify-center text-sm shadow-xs">
                            <i class="fa-solid fa-file-signature"></i>
                        </div>
                        <h3 class="font-extrabold text-slate-800 text-xs sm:text-sm">Logbook Digital</h3>
                        <p class="text-[11px] text-slate-500 font-medium leading-snug">Pencatatan kegiatan harian dan bimbingan lapangan.</p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="space-y-2">
                        <div class="w-10 h-10 rounded-full bg-blue-100/70 text-[#0B63E5] flex items-center justify-center text-sm shadow-xs">
                            <i class="fa-solid fa-certificate"></i>
                        </div>
                        <h3 class="font-extrabold text-slate-800 text-xs sm:text-sm">E-Sertifikat</h3>
                        <p class="text-[11px] text-slate-500 font-medium leading-snug">Verifikasi publik QR Code & transkrip nilai secara digital.</p>
                    </div>
                </div>
            </div>

            <!-- Port Container Terminal Crane Background Graphic at Bottom Left -->
            <div class="absolute inset-x-0 bottom-0 opacity-25 pointer-events-none mix-blend-multiply">
                <img src="{{ asset('images/pelindo-port-crane-bg.jpg') }}" alt="Pelindo Port Crane Terminal Background" class="w-full h-48 sm:h-56 object-cover object-bottom">
            </div>

            <!-- Bottom Left/Right Footer text -->
            <div class="relative z-10 pt-6 text-[11px] font-semibold text-slate-400 flex items-center justify-between border-t border-slate-200/60">
                <p>&copy; {{ date('Y') }} PT Pelabuhan Indonesia (Persero). All Rights Reserved.</p>
                <p class="font-bold text-slate-500">Regional 3 Surabaya</p>
            </div>
        </div>

        <!-- Right Column (6/12 cols): Login Form -->
        <div class="lg:col-span-6 bg-white p-8 sm:p-12 lg:p-16 flex flex-col justify-between min-h-screen" x-data="{ showPassword: false }">

            <!-- Top Right Action Link -->
            <div class="flex justify-end">
                <a href="{{ route('certificate.verify', ['token' => 'demo']) }}" class="inline-flex items-center space-x-2 text-xs font-bold text-[#0B63E5] hover:text-blue-700 transition">
                    <i class="fa-shield-halved fa-solid text-sm"></i>
                    <span>Verifikasi Sertifikat</span>
                </a>
            </div>

            <!-- Center Form Container -->
            <div class="max-w-md mx-auto w-full my-auto space-y-6">

                <!-- Title -->
                <div class="space-y-1 text-left">
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                        <span>Selamat Datang Kembali</span>
                        <span>👋</span>
                    </h2>
                    <p class="text-xs sm:text-sm font-semibold text-slate-500">Silakan masuk menggunakan akun terdaftar Anda.</p>
                </div>

                <!-- Error Alert Box -->
                @if ($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl text-xs font-semibold space-y-1">
                    <div class="flex items-center space-x-2 font-extrabold text-rose-700">
                        <i class="fa-solid fa-circle-exclamation text-sm"></i>
                        <span>Gagal Masuk:</span>
                    </div>
                    <ul class="list-disc list-inside text-[11px] text-rose-700 font-medium">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('login') }}" method="POST" class="space-y-5">
                    @csrf

                    <!-- Email Field -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700">Alamat Email</label>
                        <div class="relative flex items-center">
                            <div class="absolute left-3.5 text-slate-400 pointer-events-none">
                                <i class="fa-regular fa-envelope text-sm"></i>
                            </div>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                                placeholder="intern1@pelindo.co.id"
                                class="w-full pl-10 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#0B63E5] focus:ring-1 focus:ring-[#0B63E5] transition">
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700">Kata Sandi</label>
                        <div class="relative flex items-center">
                            <div class="absolute left-3.5 text-slate-400 pointer-events-none">
                                <i class="fa-solid fa-lock text-sm"></i>
                            </div>
                            <input :type="showPassword ? 'text' : 'password'" id="password" name="password" required
                                placeholder="••••••••••••"
                                class="w-full pl-10 pr-10 py-3 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#0B63E5] focus:ring-1 focus:ring-[#0B63E5] transition">
                            <button type="button" @click="showPassword = !showPassword" class="absolute right-3 text-slate-400 hover:text-slate-600">
                                <i class="fa-regular text-xs" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember & Forgot Password -->
                    <div class="flex items-center justify-between text-xs pt-1">
                        <label class="flex items-center space-x-2 text-slate-600 font-bold cursor-pointer select-none">
                            <input type="checkbox" name="remember" class="rounded border-slate-300 text-[#0B63E5] focus:ring-[#0B63E5] w-4 h-4">
                            <span>Ingat Saya</span>
                        </label>
                        <a href="#" onclick="alert('Silakan hubungi HR Admin Pelindo untuk reset password.')" class="text-[#0B63E5] hover:underline font-bold">Lupa Password?</a>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full py-3 bg-[#0B63E5] hover:bg-blue-700 text-white font-extrabold rounded-xl text-xs sm:text-sm transition shadow-md flex items-center justify-center space-x-2 active:scale-98">
                        <span>Masuk Sekarang</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </button>
                </form>

                <!-- Quick Demo Presets Grid -->
                <div class="pt-4 border-t border-slate-100 space-y-4">
                    <div class="relative flex py-1 items-center">
                        <div class="flex-grow border-t border-slate-200"></div>
                        <span class="flex-shrink mx-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Uji Coba Akun Demo</span>
                        <div class="flex-grow border-t border-slate-200"></div>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <button type="button" onclick="fillLogin('admin@pelindo.co.id', 'password123')"
                            class="p-3 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl text-center text-xs transition font-extrabold text-slate-700 shadow-2xs flex items-center justify-center space-x-1.5">
                            <i class="fa-solid fa-user-shield text-[#0B63E5]"></i>
                            <span>HR Admin</span>
                        </button>
                        <button type="button" onclick="fillLogin('pembimbing.it@pelindo.co.id', 'password123')"
                            class="p-3 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl text-center text-xs transition font-extrabold text-slate-700 shadow-2xs flex items-center justify-center space-x-1.5">
                            <i class="fa-solid fa-user-tie text-[#0B63E5]"></i>
                            <span>Supervisor</span>
                        </button>
                        <button type="button" onclick="fillLogin('intern1@pelindo.co.id', 'password123')"
                            class="p-3 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl text-center text-xs transition font-extrabold text-slate-700 shadow-2xs flex items-center justify-center space-x-1.5">
                            <i class="fa-solid fa-user-graduate text-[#0B63E5]"></i>
                            <span>Intern</span>
                        </button>
                    </div>
                </div>

            </div>

            <div></div>
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