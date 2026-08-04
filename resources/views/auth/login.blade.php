<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PT Pelabuhan Indonesia (Persero)</title>
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
                            blue: '#005B9C',
                            cyan: '#38BDF8',
                            red: '#EF4444'
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
<body class="bg-[#F1F5F9] min-h-screen flex flex-col items-center justify-center p-4 sm:p-6 font-sans text-slate-800">

    <div class="max-w-5xl w-full bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-200/80 grid grid-cols-1 lg:grid-cols-2">
        
        <!-- Left Side: Brand Showcase & Features matching exact screenshot -->
        <div class="bg-gradient-to-b from-blue-50/60 via-slate-50 to-blue-100/40 p-8 lg:p-12 flex flex-col justify-between relative overflow-hidden border-r border-slate-100">
            
            <div class="space-y-8 relative z-10">
                <!-- Official Pelindo Logo Image (Transparent PNG) uploaded by user -->
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/pelindo-logo-transparent.png') }}" alt="PELINDO SOLUSI DIGITAL" class="h-14 w-auto object-contain">
                </div>

                <div class="space-y-2">
                    <h1 class="text-lg lg:text-xl font-black text-slate-900 tracking-tight uppercase">PT PELABUHAN INDONESIA (PERSERO)</h1>
                    <p class="text-xs font-bold text-slate-500">Sistem Informasi Manajemen Magang (InternTrack)</p>
                    <p class="text-xs text-slate-600 font-medium leading-relaxed pt-1">
                        Platform digital untuk monitoring, evaluasi, dan pengelolaan program magang di lingkungan Pelindo.
                    </p>
                </div>

                <!-- 3 Feature Cards matching screenshot -->
                <div class="space-y-4 pt-2">
                    <!-- Feature 1 -->
                    <div class="flex items-center space-x-4 p-3.5 bg-white/80 backdrop-blur-md rounded-2xl border border-slate-100 shadow-xs">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg shrink-0">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-800 text-xs">Aman & Terpercaya</h3>
                            <p class="text-[11px] text-slate-500 font-medium">Data Anda terlindungi dengan sistem keamanan berstandar perusahaan.</p>
                        </div>
                    </div>

                    <!-- Feature 2 -->
                    <div class="flex items-center space-x-4 p-3.5 bg-white/80 backdrop-blur-md rounded-2xl border border-slate-100 shadow-xs">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg shrink-0">
                            <i class="fa-solid fa-chart-line"></i>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-800 text-xs">Monitoring Real-time</h3>
                            <p class="text-[11px] text-slate-500 font-medium">Pantau aktivitas dan progres magang secara real-time dan terpusat.</p>
                        </div>
                    </div>

                    <!-- Feature 3 -->
                    <div class="flex items-center space-x-4 p-3.5 bg-white/80 backdrop-blur-md rounded-2xl border border-slate-100 shadow-xs">
                        <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-lg shrink-0">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-800 text-xs">Kolaborasi Efektif</h3>
                            <p class="text-[11px] text-slate-500 font-medium">Memudahkan komunikasi dan kolaborasi antara intern, pembimbing, dan tim HR.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Port Terminal Container Ships Waves Background Illustration -->
            <div class="absolute inset-x-0 bottom-0 opacity-15 pointer-events-none">
                <svg viewBox="0 0 500 150" preserveAspectRatio="none" class="w-full h-32 text-blue-900 fill-current">
                    <path d="M0,40 C150,90 350,-10 500,40 L500,150 L0,150 Z"></path>
                </svg>
            </div>

        </div>

        <!-- Right Side: Login Form matching screenshot -->
        <div class="p-8 lg:p-12 flex flex-col justify-between bg-white" x-data="{ showPassword: false }">
            
            <div class="space-y-6">
                <!-- Header P Logo Badge -->
                <div class="text-center space-y-3">
                    <div class="w-14 h-14 bg-[#0F2A4A] text-white rounded-2xl mx-auto flex items-center justify-center text-2xl font-black shadow-md">
                        P
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-slate-900 tracking-tight">Selamat Datang Kembali 👋</h2>
                        <p class="text-xs font-semibold text-slate-500 mt-1">Masuk untuk melanjutkan ke akun Anda</p>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="bg-rose-50 border border-rose-200 text-rose-800 p-3.5 rounded-2xl text-xs font-semibold space-y-1">
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

                <form action="{{ route('login') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <!-- Email Input -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Alamat Email</label>
                        <div class="flex rounded-2xl border border-slate-200 overflow-hidden focus-within:border-blue-600 focus-within:ring-1 focus-within:ring-blue-600 transition">
                            <div class="px-3.5 bg-slate-50 flex items-center text-blue-600 border-r border-slate-200">
                                <i class="fa-regular fa-envelope text-sm"></i>
                            </div>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                                   placeholder="rozaq@gmail.com"
                                   class="w-full px-4 py-3 bg-white text-xs font-semibold text-slate-800 outline-none">
                        </div>
                    </div>

                    <!-- Password Input with Toggle Eye -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Kata Sandi</label>
                        <div class="flex rounded-2xl border border-slate-200 overflow-hidden focus-within:border-blue-600 focus-within:ring-1 focus-within:ring-blue-600 transition relative">
                            <div class="px-3.5 bg-slate-50 flex items-center text-blue-600 border-r border-slate-200">
                                <i class="fa-solid fa-lock text-sm"></i>
                            </div>
                            <input :type="showPassword ? 'text' : 'password'" id="password" name="password" required
                                   placeholder="••••••••••••"
                                   class="w-full px-4 py-3 bg-white text-xs font-semibold text-slate-800 outline-none pr-10">
                            <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-3 text-slate-400 hover:text-slate-600">
                                <i class="fa-regular" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs pt-1">
                        <label class="flex items-center space-x-2 text-slate-600 font-bold cursor-pointer select-none">
                            <input type="checkbox" name="remember" class="rounded border-slate-300 text-blue-600 focus:ring-blue-600 w-4 h-4">
                            <span>Ingat Saya</span>
                        </label>
                        <a href="#" onclick="alert('Silakan hubungi HR Admin Pelindo untuk reset password.')" class="text-blue-600 hover:underline font-bold">Lupa Kata Sandi?</a>
                    </div>

                    <button type="submit" 
                            class="w-full py-3.5 bg-[#005B9C] hover:bg-[#002B49] text-white font-extrabold rounded-2xl text-xs transition shadow-md flex items-center justify-center space-x-2 active:scale-98">
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
                        <button type="button" onclick="fillLogin('admin@pelindo.co.id', 'password123')"
                                class="p-3 bg-white hover:bg-blue-50/50 border border-slate-200 rounded-2xl text-center text-xs transition font-extrabold text-slate-700 shadow-2xs">
                            <i class="fa-solid fa-user-shield text-[#005B9C] block mb-1"></i>
                            <span class="text-[11px]">HR Admin</span>
                        </button>
                        <button type="button" onclick="fillLogin('pembimbing.it@pelindo.co.id', 'password123')"
                                class="p-3 bg-white hover:bg-cyan-50/50 border border-slate-200 rounded-2xl text-center text-xs transition font-extrabold text-slate-700 shadow-2xs">
                            <i class="fa-solid fa-user-tie text-cyan-600 block mb-1"></i>
                            <span class="text-[11px]">Supervisor</span>
                        </button>
                        <button type="button" onclick="fillLogin('intern1@pelindo.co.id', 'password123')"
                                class="p-3 bg-white hover:bg-emerald-50/50 border border-slate-200 rounded-2xl text-center text-xs transition font-extrabold text-slate-700 shadow-2xs">
                            <i class="fa-solid fa-user-graduate text-emerald-600 block mb-1"></i>
                            <span class="text-[11px]">Intern</span>
                        </button>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- Centered Bottom Copyright Footer matching screenshot -->
    <footer class="mt-6 text-center text-xs font-semibold text-slate-400">
        &copy; {{ date('Y') }} PT Pelabuhan Indonesia (Persero). All rights reserved.
    </footer>

    <script>
        function fillLogin(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
        }
    </script>
</body>
</html>
