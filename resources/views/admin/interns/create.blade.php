@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    <!-- Centralized Page Header -->
    <x-page-header 
        title="Tambah Peserta Magang Baru" 
        subtitle="Daftarkan peserta magang baru dengan integrasi Pusat Master Data SDM Pelindo." 
        icon="fa-solid fa-user-plus"
    >
        <x-slot:action>
            <a href="{{ route('admin.interns.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-2xl text-xs transition inline-flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali ke Daftar</span>
            </a>
        </x-slot:action>
    </x-page-header>

    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs space-y-1">
            <span class="font-bold block">Terdapat beberapa kesalahan input:</span>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.interns.store') }}" method="POST" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm space-y-8">
        @csrf

        <!-- Section 1: Informasi Akun & Biodata -->
        <div class="space-y-4">
            <h2 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3">
                <i class="fa-solid fa-id-card text-[#0B63E5]"></i>
                <span>1. Biodata & Akun Login</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Nama Lengkap Peserta <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Budi Santoso" class="w-full text-xs rounded-xl border border-slate-200 p-3 outline-none focus:border-[#0B63E5]">
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Email Pelindo / Pribadi <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="budi@pelindo.co.id" class="w-full text-xs rounded-xl border border-slate-200 p-3 outline-none focus:border-[#0B63E5]">
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Nomor WhatsApp / HP</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="081234567890" class="w-full text-xs rounded-xl border border-slate-200 p-3 outline-none focus:border-[#0B63E5]">
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Nomor Induk Kependudukan (NIK)</label>
                    <input type="text" name="nik_number" value="{{ old('nik_number') }}" placeholder="3578123456780001" class="w-full text-xs rounded-xl border border-slate-200 p-3 outline-none focus:border-[#0B63E5]">
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Agama <span class="text-slate-400 font-normal">(Master Data)</span></label>
                    <select name="religion" class="w-full text-xs rounded-xl border border-slate-200 p-3 outline-none focus:border-[#0B63E5] bg-white font-semibold">
                        <option value="">-- Pilih Agama --</option>
                        @foreach(App\Helpers\MasterDataHelper::getReligions() as $key => $label)
                            <option value="{{ $key }}" {{ old('religion') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Jenis Kelamin</label>
                    <select name="gender" class="w-full text-xs rounded-xl border border-slate-200 p-3 outline-none focus:border-[#0B63E5] bg-white font-semibold">
                        <option value="">-- Pilih Jenis Kelamin --</option>
                        @foreach(App\Helpers\MasterDataHelper::getGenders() as $key => $label)
                            <option value="{{ $key }}" {{ old('gender') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="text-xs font-bold text-slate-700 block mb-1">Alamat Tinggal / Domisili</label>
                <textarea name="address" rows="2" placeholder="Alamat domisili peserta magang..." class="w-full text-xs rounded-xl border border-slate-200 p-3 outline-none focus:border-[#0B63E5]">{{ old('address') }}</textarea>
            </div>
        </div>

        <!-- Section 2: Institusi Pendidikan & Penempatan Magang -->
        <div class="space-y-4">
            <h2 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3">
                <i class="fa-solid fa-graduation-cap text-[#0B63E5]"></i>
                <span>2. Akademik & Divisi Pelindo</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">NIM / NISM <span class="text-rose-500">*</span></label>
                    <input type="text" name="student_id_number" value="{{ old('student_id_number') }}" required placeholder="NIM Kampus" class="w-full text-xs rounded-xl border border-slate-200 p-3 outline-none focus:border-[#0B63E5]">
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Perguruan Tinggi / Sekolah <span class="text-rose-500">*</span></label>
                    <input type="text" name="institution" list="institutionList" value="{{ old('institution') }}" required placeholder="Contoh: ITS Surabaya" class="w-full text-xs rounded-xl border border-slate-200 p-3 outline-none focus:border-[#0B63E5]">
                    <datalist id="institutionList">
                        @foreach(App\Helpers\MasterDataHelper::getInstitutions() as $inst)
                            <option value="{{ $inst }}">
                        @endforeach
                    </datalist>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Program Studi / Jurusan <span class="text-rose-500">*</span></label>
                    <input type="text" name="major" value="{{ old('major') }}" required placeholder="Teknik Informatika" class="w-full text-xs rounded-xl border border-slate-200 p-3 outline-none focus:border-[#0B63E5]">
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Divisi Penempatan <span class="text-rose-500">*</span></label>
                    <select name="division_id" required class="w-full text-xs rounded-xl border border-slate-200 p-3 outline-none focus:border-[#0B63E5] bg-white font-semibold">
                        <option value="">-- Pilih Divisi --</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}" {{ old('division_id') == $div->id ? 'selected' : '' }}>
                                {{ $div->name }} (Kuota: {{ $div->quota }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Pembimbing Lapangan <span class="text-rose-500">*</span></label>
                    <select name="supervisor_id" required class="w-full text-xs rounded-xl border border-slate-200 p-3 outline-none focus:border-[#0B63E5] bg-white font-semibold">
                        <option value="">-- Pilih Pembimbing --</option>
                        @foreach($supervisors as $spv)
                            <option value="{{ $spv->id }}" {{ old('supervisor_id') == $spv->id ? 'selected' : '' }}>
                                {{ $spv->name }} ({{ $spv->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Kata Sandi Akun <span class="text-rose-500">*</span></label>
                    <input type="password" name="password" required value="password123" class="w-full text-xs rounded-xl border border-slate-200 p-3 outline-none focus:border-[#0B63E5]">
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Tanggal Mulai Magang <span class="text-rose-500">*</span></label>
                    <input type="date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required class="w-full text-xs rounded-xl border border-slate-200 p-3 outline-none focus:border-[#0B63E5]">
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Tanggal Selesai Magang <span class="text-rose-500">*</span></label>
                    <input type="date" name="end_date" value="{{ old('end_date', date('Y-m-d', strtotime('+3 months'))) }}" required class="w-full text-xs rounded-xl border border-slate-200 p-3 outline-none focus:border-[#0B63E5]">
                </div>
            </div>
        </div>

        <!-- Section 3: Rekening Perbankan (Master Bank) -->
        <div class="space-y-4">
            <h2 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3">
                <i class="fa-solid fa-[#0B63E5] fa-building-columns text-[#0B63E5]"></i>
                <span>3. Rekening Pencairan Uang Saku</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Nama Bank <span class="text-slate-400 font-normal">(Master Bank)</span></label>
                    <select name="bank_name" class="w-full text-xs rounded-xl border border-slate-200 p-3 outline-none focus:border-[#0B63E5] bg-white font-semibold">
                        <option value="">-- Pilih Bank --</option>
                        @foreach(App\Helpers\MasterDataHelper::getBanks() as $key => $label)
                            <option value="{{ $key }}" {{ old('bank_name') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Nomor Rekening</label>
                    <input type="text" name="bank_account_number" value="{{ old('bank_account_number') }}" placeholder="Contoh: 1400012345678" class="w-full text-xs rounded-xl border border-slate-200 p-3 outline-none focus:border-[#0B63E5]">
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Atas Nama Rekening</label>
                    <input type="text" name="bank_account_holder" value="{{ old('bank_account_holder') }}" placeholder="Nama Pemilik Rekening" class="w-full text-xs rounded-xl border border-slate-200 p-3 outline-none focus:border-[#0B63E5]">
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
            <a href="{{ route('admin.interns.index') }}" class="px-5 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">Batal</a>
            <button type="submit" class="px-6 py-3 rounded-2xl bg-[#0B63E5] hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/20 transition flex items-center gap-2">
                <i class="fa-solid fa-save"></i>
                <span>Simpan Peserta Magang</span>
            </button>
        </div>
    </form>
</div>
@endsection
