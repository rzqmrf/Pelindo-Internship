@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    <!-- Centralized Page Header -->
    <x-page-header 
        title="Edit Profil Peserta Magang" 
        subtitle="Perbarui data biodata, penempatan divisi, pembimbing, dan rekening bank peserta magang." 
        icon="fa-solid fa-user-pen"
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

    <form action="{{ route('admin.interns.update', $intern->id) }}" method="POST" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm space-y-8">
        @csrf
        @method('PUT')

        <!-- Section 1: Informasi Akun & Biodata -->
        <div class="space-y-4">
            <h2 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3">
                <i class="fa-solid fa-id-card text-[#0B63E5]"></i>
                <span>1. Biodata & Akun Login</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Nama Lengkap Peserta <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $intern->user?->name) }}" required class="w-full text-xs rounded-xl border border-slate-200 p-3 outline-none focus:border-[#0B63E5]">
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Email Pelindo / Pribadi <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $intern->user?->email) }}" required class="w-full text-xs rounded-xl border border-slate-200 p-3 outline-none focus:border-[#0B63E5]">
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Nomor WhatsApp / HP</label>
                    <input type="text" name="phone" value="{{ old('phone', $intern->user?->phone) }}" class="w-full text-xs rounded-xl border border-slate-200 p-3 outline-none focus:border-[#0B63E5]">
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Nomor Induk Kependudukan (NIK)</label>
                    <input type="text" name="nik_number" value="{{ old('nik_number', $intern->nik_number) }}" class="w-full text-xs rounded-xl border border-slate-200 p-3 outline-none focus:border-[#0B63E5]">
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Agama <span class="text-slate-400 font-normal">(Master Data)</span></label>
                    <select name="religion" class="w-full text-xs rounded-xl border border-slate-200 p-3 outline-none focus:border-[#0B63E5] bg-white font-semibold">
                        <option value="">-- Pilih Agama --</option>
                        @foreach(App\Helpers\MasterDataHelper::getReligions() as $key => $label)
                            <option value="{{ $key }}" {{ old('religion', $intern->religion) == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Jenis Kelamin</label>
                    <select name="gender" class="w-full text-xs rounded-xl border border-slate-200 p-3 outline-none focus:border-[#0B63E5] bg-white font-semibold">
                        <option value="">-- Pilih Jenis Kelamin --</option>
                        @foreach(App\Helpers\MasterDataHelper::getGenders() as $key => $label)
                            <option value="{{ $key }}" {{ old('gender', $intern->gender) == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="text-xs font-bold text-slate-700 block mb-1">Alamat Tinggal / Domisili</label>
                <textarea name="address" rows="2" class="w-full text-xs rounded-xl border border-slate-200 p-3 outline-none focus:border-[#0B63E5]">{{ old('address', $intern->address) }}</textarea>
            </div>
        </div>

        <!-- Section 2: Institusi & Penempatan -->
        <div class="space-y-4">
            <h2 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3">
                <i class="fa-solid fa-graduation-cap text-[#0B63E5]"></i>
                <span>2. Akademik & Divisi Pelindo</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">NIM / NISM <span class="text-rose-500">*</span></label>
                    <input type="text" name="student_id_number" value="{{ old('student_id_number', $intern->student_id_number) }}" required class="w-full text-xs rounded-xl border border-slate-200 p-3 outline-none focus:border-[#0B63E5]">
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Perguruan Tinggi / Sekolah <span class="text-rose-500">*</span></label>
                    <input type="text" name="institution" list="institutionList" value="{{ old('institution', $intern->institution) }}" required class="w-full text-xs rounded-xl border border-slate-200 p-3 outline-none focus:border-[#0B63E5]">
                    <datalist id="institutionList">
                        @foreach(App\Helpers\MasterDataHelper::getInstitutions() as $inst)
                            <option value="{{ $inst }}">
                        @endforeach
                    </datalist>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Program Studi / Jurusan <span class="text-rose-500">*</span></label>
                    <input type="text" name="major" value="{{ old('major', $intern->major) }}" required class="w-full text-xs rounded-xl border border-slate-200 p-3 outline-none focus:border-[#0B63E5]">
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Divisi Penempatan <span class="text-rose-500">*</span></label>
                    <select name="division_id" required class="w-full text-xs rounded-xl border border-slate-200 p-3 outline-none focus:border-[#0B63E5] bg-white font-semibold">
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}" {{ old('division_id', $intern->division_id) == $div->id ? 'selected' : '' }}>
                                {{ $div->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Pembimbing Lapangan <span class="text-rose-500">*</span></label>
                    <select name="supervisor_id" required class="w-full text-xs rounded-xl border border-slate-200 p-3 outline-none focus:border-[#0B63E5] bg-white font-semibold">
                        @foreach($supervisors as $spv)
                            <option value="{{ $spv->id }}" {{ old('supervisor_id', $intern->supervisor_id) == $spv->id ? 'selected' : '' }}>
                                {{ $spv->name }} ({{ $spv->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Status Keaktifan <span class="text-rose-500">*</span></label>
                    <select name="status" required class="w-full text-xs rounded-xl border border-slate-200 p-3 outline-none focus:border-[#0B63E5] bg-white font-semibold">
                        <option value="active" {{ old('status', $intern->status) == 'active' ? 'selected' : '' }}>ACTIVE (Aktif Magang)</option>
                        <option value="completed" {{ old('status', $intern->status) == 'completed' ? 'selected' : '' }}>COMPLETED (Selesai Magang)</option>
                        <option value="extended" {{ old('status', $intern->status) == 'extended' ? 'selected' : '' }}>EXTENDED (Perpanjangan)</option>
                        <option value="terminated" {{ old('status', $intern->status) == 'terminated' ? 'selected' : '' }}>TERMINATED (Berhenti)</option>
                    </select>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Tanggal Mulai <span class="text-rose-500">*</span></label>
                    <input type="date" name="start_date" value="{{ old('start_date', $intern->start_date?->format('Y-m-d')) }}" required class="w-full text-xs rounded-xl border border-slate-200 p-3 outline-none focus:border-[#0B63E5]">
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Tanggal Selesai <span class="text-rose-500">*</span></label>
                    <input type="date" name="end_date" value="{{ old('end_date', $intern->end_date?->format('Y-m-d')) }}" required class="w-full text-xs rounded-xl border border-slate-200 p-3 outline-none focus:border-[#0B63E5]">
                </div>
            </div>
        </div>

        <!-- Section 3: Rekening Perbankan -->
        <div class="space-y-4">
            <h2 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3">
                <i class="fa-solid fa-building-columns text-[#0B63E5]"></i>
                <span>3. Rekening Pencairan Uang Saku</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Nama Bank <span class="text-slate-400 font-normal">(Master Bank)</span></label>
                    <select name="bank_name" class="w-full text-xs rounded-xl border border-slate-200 p-3 outline-none focus:border-[#0B63E5] bg-white font-semibold">
                        <option value="">-- Pilih Bank --</option>
                        @foreach(App\Helpers\MasterDataHelper::getBanks() as $key => $label)
                            <option value="{{ $key }}" {{ old('bank_name', $intern->bank_name) == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Nomor Rekening</label>
                    <input type="text" name="bank_account_number" value="{{ old('bank_account_number', $intern->bank_account_number) }}" class="w-full text-xs rounded-xl border border-slate-200 p-3 outline-none focus:border-[#0B63E5]">
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Atas Nama Rekening</label>
                    <input type="text" name="bank_account_holder" value="{{ old('bank_account_holder', $intern->bank_account_holder) }}" class="w-full text-xs rounded-xl border border-slate-200 p-3 outline-none focus:border-[#0B63E5]">
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
            <a href="{{ route('admin.interns.index') }}" class="px-5 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">Batal</a>
            <button type="submit" class="px-6 py-3 rounded-2xl bg-[#0B63E5] hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/20 transition flex items-center gap-2">
                <i class="fa-solid fa-save"></i>
                <span>Simpan Perubahan</span>
            </button>
        </div>
    </form>
</div>
@endsection
