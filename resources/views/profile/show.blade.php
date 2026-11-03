@extends('layouts.app')

@section('page_title', 'Profil Saya')

@section('content')
<div class="space-y-6">

    <!-- Avatar Upload Hidden Form -->
    <form id="avatar-form" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="hidden">
        @csrf
        @method('PUT')
        <input type="hidden" name="name" value="{{ $user->name }}">
        <input type="hidden" name="phone" value="{{ $user->phone }}">
        <input type="file" name="avatar" id="avatar-input" accept="image/*" onchange="document.getElementById('avatar-form').submit()">
    </form>

    <!-- Header Card -->
    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 relative overflow-hidden">
        <!-- Background decorative wave gradient to mimic premium look -->
        <div class="absolute right-0 top-0 h-full w-1/3 bg-gradient-to-l from-blue-50/20 to-transparent pointer-events-none rounded-r-3xl hidden lg:block"></div>

        <div class="flex items-center space-x-6 relative z-10">
            <div class="relative shrink-0">
                @if($user->avatar)
                <img src="{{ asset('storage/' . $user->avatar) }}" class="w-28 h-28 rounded-full object-cover border-4 border-white shadow-md">
                @else
                <div class="w-28 h-28 rounded-full bg-[#0B63E5] text-white flex items-center justify-center font-black text-5xl shadow-md border-4 border-white shrink-0">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                @endif
                <button type="button" onclick="document.getElementById('avatar-input').click()"
                    class="absolute bottom-1 right-1 w-8 h-8 rounded-full bg-white border border-slate-100 shadow-md flex items-center justify-center cursor-pointer hover:bg-slate-50 transition focus:outline-none"
                    title="Ubah Foto Profil">
                    <i class="fa-solid fa-camera text-slate-500 text-xs"></i>
                </button>
            </div>

            <div class="space-y-1">
                <h2 class="text-2xl font-black text-slate-800 tracking-tight leading-none">{{ $user->name }}</h2>
                <div class="pt-1">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-[#0B63E5] text-white text-[9px] font-black tracking-wider uppercase">
                        {{ $user->roleLabel() }}
                    </span>
                </div>
                <div class="flex flex-col space-y-1.5 pt-3">
                    <div class="flex items-center space-x-2 text-slate-500 text-xs">
                        <i class="fa-regular fa-envelope text-slate-400 w-4"></i>
                        <span class="font-bold text-slate-600">{{ $user->email }}</span>
                    </div>
                    <div class="flex items-center space-x-2 text-slate-500 text-xs">
                        <i class="fa-regular fa-calendar text-slate-400 w-4"></i>
                        <span class="font-bold text-slate-600">
                            Bergabung sejak {{ ($user->internProfile && $user->internProfile->start_date) ? $user->internProfile->start_date->locale('id')->isoFormat('D MMMM YYYY') : ($user->created_at ? $user->created_at->locale('id')->isoFormat('D MMMM YYYY') : '-') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        @if($user->isIntern() && $user->internProfile)
        <div class="flex flex-col space-y-3.5 min-w-[280px] w-full lg:w-auto relative z-10 border-t border-slate-50 lg:border-t-0 pt-4 lg:pt-0">
            <!-- Program -->
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-xl bg-blue-50/80 flex items-center justify-center text-blue-600 shrink-0">
                    <i class="fa-solid fa-briefcase text-sm text-[#0B63E5]"></i>
                </div>
                <div>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider leading-none mb-0.5">Program</p>
                    <p class="text-xs font-black text-slate-800">
                        Program Magang
                    </p>
                </div>
            </div>

            <!-- Pembimbing -->
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-xl bg-blue-50/80 flex items-center justify-center text-blue-600 shrink-0">
                    <i class="fa-regular fa-user text-sm text-[#0B63E5]"></i>
                </div>
                <div>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider leading-none mb-0.5">Pembimbing</p>
                    <p class="text-xs font-black text-slate-800">
                        {{ $user->internProfile->supervisor->name ?? 'Pak Nasrul' }}
                    </p>
                </div>
            </div>

            <!-- Periode Magang -->
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-xl bg-blue-50/80 flex items-center justify-center text-blue-600 shrink-0">
                    <i class="fa-regular fa-clock text-sm text-[#0B63E5]"></i>
                </div>
                <div>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider leading-none mb-0.5">Periode Magang</p>
                    <p class="text-xs font-black text-slate-800">
                        {{ $user->internProfile->start_date ? $user->internProfile->start_date->isoFormat('D MMMM YYYY') : '1 Juni 2026' }} &ndash; {{ $user->internProfile->end_date ? $user->internProfile->end_date->isoFormat('D MMMM YYYY') : '31 Agustus 2026' }}
                    </p>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Forms Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left Column: Informasi Pribadi -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-6">
                <!-- Title Block -->
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-50/80 text-blue-600 flex items-center justify-center rounded-2xl shrink-0">
                        <i class="fa-regular fa-user text-lg text-[#0B63E5]"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-800">Informasi Pribadi</h3>
                        <p class="text-[11px] font-semibold text-slate-400">Lengkapi dan perbarui informasi pribadi Anda.</p>
                    </div>
                </div>

                <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                        <!-- Nama Lengkap -->
                        <div>
                            <label class="block text-[11px] font-black text-slate-700 mb-1.5">Nama Lengkap <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400">
                                    <i class="fa-regular fa-user"></i>
                                </span>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                    class="w-full pl-11 pr-4 py-3 bg-white border border-slate-200 rounded-2xl text-xs font-bold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            </div>
                        </div>

                        <!-- Email Perusahaan -->
                        <div>
                            <label class="block text-[11px] font-black text-slate-700 mb-1.5">Email Perusahaan <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400">
                                    <i class="fa-regular fa-envelope"></i>
                                </span>
                                <input type="email" value="{{ $user->email }}" disabled
                                    class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold text-slate-400 cursor-not-allowed">
                            </div>
                        </div>

                        <!-- No. Telepon / WA -->
                        <div>
                            <label class="block text-[11px] font-black text-slate-700 mb-1.5">No. Telepon / WA <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400">
                                    <i class="fa-solid fa-phone"></i>
                                </span>
                                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" required
                                    class="w-full pl-11 pr-4 py-3 bg-white border border-slate-200 rounded-2xl text-xs font-bold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            </div>
                        </div>

                        @if($user->isIntern() && $user->internProfile)
                        <!-- NIK / No. KTP -->
                        <div>
                            <label class="block text-[11px] font-black text-slate-700 mb-1.5">NIK / No. KTP <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400">
                                    <i class="fa-regular fa-id-card"></i>
                                </span>
                                <input type="text" name="nik_number" value="{{ old('nik_number', $user->internProfile->nik_number ?? '') }}" required
                                    class="w-full pl-11 pr-4 py-3 bg-white border border-slate-200 rounded-2xl text-xs font-bold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            </div>
                        </div>

                        <!-- Instansi / Perguruan Tinggi -->
                        <div>
                            <label class="block text-[11px] font-black text-slate-700 mb-1.5">Instansi / Perguruan Tinggi <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400">
                                    <i class="fa-solid fa-building"></i>
                                </span>
                                <input type="text" name="institution" value="{{ old('institution', $user->internProfile->institution ?? '') }}" required
                                    class="w-full pl-11 pr-4 py-3 bg-white border border-slate-200 rounded-2xl text-xs font-bold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            </div>
                        </div>

                        <!-- Jurusan -->
                        <div>
                            <label class="block text-[11px] font-black text-slate-700 mb-1.5">Jurusan <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400">
                                    <i class="fa-solid fa-graduation-cap"></i>
                                </span>
                                <input type="text" name="major" value="{{ old('major', $user->internProfile->major ?? '') }}" required
                                    class="w-full pl-11 pr-4 py-3 bg-white border border-slate-200 rounded-2xl text-xs font-bold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            </div>
                        </div>

                        <!-- NIM / NIS -->
                        <div>
                            <label class="block text-[11px] font-black text-slate-700 mb-1.5">NIM / NIS <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400">
                                    <i class="fa-regular fa-address-card"></i>
                                </span>
                                <input type="text" name="student_id_number" value="{{ old('student_id_number', $user->internProfile->student_id_number ?? '') }}" required
                                    class="w-full pl-11 pr-4 py-3 bg-white border border-slate-200 rounded-2xl text-xs font-bold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            </div>
                        </div>

                        <!-- Alamat -->
                        <div>
                            <label class="block text-[11px] font-black text-slate-700 mb-1.5">Alamat (Opsional)</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400">
                                    <i class="fa-solid fa-location-dot"></i>
                                </span>
                                <input type="text" name="address" value="{{ old('address', $user->internProfile->address ?? '') }}" placeholder="Masukkan alamat Anda"
                                    class="w-full pl-11 pr-4 py-3 bg-white border border-slate-200 rounded-2xl text-xs font-bold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            </div>
                        </div>
                        @endif
                    </div>

                    @if($user->isIntern() && $user->internProfile)
                    <div class="pt-4 border-t border-slate-100 space-y-4">
                        <h4 class="text-xs font-extrabold text-[#0B63E5] flex items-center space-x-2">
                            <i class="fa-regular fa-credit-card"></i>
                            <span>Informasi Rekening Pencairan Uang Saku Magang</span>
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                            <div>
                                <label class="block text-[11px] font-black text-slate-700 mb-1.5">Nama Bank</label>
                                <input type="text" name="bank_name" value="{{ old('bank_name', $user->internProfile->bank_name ?? '') }}" placeholder="Contoh: Bank Mandiri / BNI"
                                    class="w-full px-4 py-3 bg-white border border-slate-200 rounded-2xl text-xs font-bold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-slate-700 mb-1.5">No. Rekening</label>
                                <input type="text" name="bank_account_number" value="{{ old('bank_account_number', $user->internProfile->bank_account_number ?? '') }}" placeholder="Nomor Rekening"
                                    class="w-full px-4 py-3 bg-white border border-slate-200 rounded-2xl text-xs font-bold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-[11px] font-black text-slate-700 mb-1.5">Atas Nama Rekening</label>
                                <input type="text" name="bank_account_holder" value="{{ old('bank_account_holder', $user->internProfile->bank_account_holder ?? '') }}" placeholder="Nama Pemilik Rekening (Sesuai Buku Tabungan)"
                                    class="w-full px-4 py-3 bg-white border border-slate-200 rounded-2xl text-xs font-bold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="bg-blue-50/50 border border-blue-100 rounded-2xl p-4 flex items-start space-x-3 text-blue-700">
                        <i class="fa-solid fa-circle-info text-blue-600 mt-0.5 shrink-0"></i>
                        <p class="text-[11px] font-extrabold leading-normal">
                            Pastikan data yang Anda isi sudah benar. Perubahan data akan diverifikasi oleh tim HR.
                        </p>
                    </div>

                    <button type="submit" class="w-full py-3.5 bg-[#0B63E5] hover:bg-blue-700 text-white font-extrabold rounded-2xl text-xs transition shadow-md hover:shadow-lg flex items-center justify-center space-x-2">
                        <i class="fa-regular fa-floppy-disk text-sm"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Right Column: Ubah Password & Keamanan Akun -->
        <div class="space-y-6">

            <!-- Ubah Kata Sandi -->
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-6">
                <!-- Title Block -->
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-50/80 text-blue-600 flex items-center justify-center rounded-2xl shrink-0">
                        <i class="fa-solid fa-lock text-lg text-[#0B63E5]"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-800">Ubah Kata Sandi</h3>
                        <p class="text-[11px] font-semibold text-slate-400">Pastikan kata sandi Anda aman dan tidak mudah ditebak.</p>
                    </div>
                </div>

                <form action="{{ route('profile.password') }}" method="POST" class="space-y-4" x-data="{ showCurrent: false, showNew: false, showConfirm: false }">
                    @csrf
                    @method('PUT')

                    <!-- Password Saat Ini -->
                    <div>
                        <label class="block text-[11px] font-black text-slate-700 mb-1.5">Password Saat Ini <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400">
                                <i class="fa-solid fa-lock"></i>
                            </span>
                            <input :type="showCurrent ? 'text' : 'password'" name="current_password" required placeholder="Masukkan password saat ini"
                                class="w-full pl-11 pr-10 py-3 bg-white border border-slate-200 rounded-2xl text-xs font-bold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            <button type="button" @click="showCurrent = !showCurrent" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600">
                                <i class="fa-regular" :class="showCurrent ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Password Baru -->
                    <div>
                        <label class="block text-[11px] font-black text-slate-700 mb-1.5">Password Baru <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400">
                                <i class="fa-solid fa-lock"></i>
                            </span>
                            <input :type="showNew ? 'text' : 'password'" name="password" required placeholder="Masukkan password baru"
                                class="w-full pl-11 pr-10 py-3 bg-white border border-slate-200 rounded-2xl text-xs font-bold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            <button type="button" @click="showNew = !showNew" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600">
                                <i class="fa-regular" :class="showNew ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Konfirmasi Password Baru -->
                    <div>
                        <label class="block text-[11px] font-black text-slate-700 mb-1.5">Konfirmasi Password Baru <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400">
                                <i class="fa-solid fa-lock"></i>
                            </span>
                            <input :type="showConfirm ? 'text' : 'password'" name="password_confirmation" required placeholder="Konfirmasi password baru"
                                class="w-full pl-11 pr-10 py-3 bg-white border border-slate-200 rounded-2xl text-xs font-bold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            <button type="button" @click="showConfirm = !showConfirm" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600">
                                <i class="fa-regular" :class="showConfirm ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-3.5 bg-[#0B63E5] hover:bg-blue-700 text-white font-extrabold rounded-2xl text-xs transition shadow-md hover:shadow-lg flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-shield-halved"></i>
                        <span>Update Password</span>
                    </button>
                </form>
            </div>

            <!-- Keamanan Akun -->
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-6">
                <!-- Title Block -->
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-50/80 text-blue-600 flex items-center justify-center rounded-2xl shrink-0">
                        <i class="fa-solid fa-shield text-lg text-[#0B63E5]"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-800">Keamanan Akun</h3>
                        <p class="text-[11px] font-semibold text-slate-400">Informasi keamanan akun Anda.</p>
                    </div>
                </div>

                <div class="space-y-3">
                    <!-- Password Terakhir Diubah -->
                    <div class="flex items-center justify-between p-3 bg-slate-50/40 border border-slate-100/60 rounded-2xl">
                        <div class="flex items-center space-x-3 overflow-hidden">
                            <div class="w-9 h-9 rounded-xl bg-blue-50/60 flex items-center justify-center text-blue-600 shrink-0">
                                <i class="fa-solid fa-rotate-left text-sm text-[#0B63E5]"></i>
                            </div>
                            <div class="overflow-hidden">
                                <p class="text-[11px] font-bold text-slate-800">Password terakhir diubah</p>
                                <p class="text-[9px] text-slate-400 font-bold truncate">
                                    {{ $user->updated_at ? $user->updated_at->isoFormat('D MMMM YYYY, HH:mm') . ' WIB' : '12 Juli 2026, 10:32 WIB' }}
                                </p>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 bg-emerald-50 text-emerald-600 text-[9px] font-bold rounded-md shrink-0">Aman</span>
                    </div>

                    <!-- Login Terakhir -->
                    <div class="flex items-center justify-between p-3 bg-slate-50/40 border border-slate-100/60 rounded-2xl">
                        <div class="flex items-center space-x-3 overflow-hidden">
                            <div class="w-9 h-9 rounded-xl bg-blue-50/60 flex items-center justify-center text-blue-600 shrink-0">
                                <i class="fa-solid fa-right-to-bracket text-sm text-[#0B63E5]"></i>
                            </div>
                            <div class="overflow-hidden">
                                <p class="text-[11px] font-bold text-slate-800">Login terakhir</p>
                                <p class="text-[9px] text-slate-400 font-bold truncate">Hari ini, 09:15 WIB</p>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 bg-emerald-50 text-emerald-600 text-[9px] font-bold rounded-md shrink-0">Aman</span>
                    </div>

                    <!-- Status Verifikasi Email -->
                    <div class="flex items-center justify-between p-3 bg-slate-50/40 border border-slate-100/60 rounded-2xl">
                        <div class="flex items-center space-x-3 overflow-hidden">
                            <div class="w-9 h-9 rounded-xl bg-blue-50/60 flex items-center justify-center text-blue-600 shrink-0">
                                <i class="fa-regular fa-envelope text-sm text-[#0B63E5]"></i>
                            </div>
                            <div class="overflow-hidden">
                                <p class="text-[11px] font-bold text-slate-800">Status Verifikasi Email</p>
                                <p class="text-[9px] text-slate-400 font-bold truncate">
                                    Email Anda telah diverifikasi
                                </p>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 bg-emerald-50 text-emerald-600 text-[9px] font-bold rounded-md shrink-0">Terverifikasi</span>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection