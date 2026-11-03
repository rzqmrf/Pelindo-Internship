@extends('layouts.app')

@section('page_title', 'Edit Profil & Periode Magang')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    <!-- Centralized Page Header -->
    <x-page-header
        title="Edit Data & Periode Magang"
        subtitle="Perbarui data biodata, penempatan divisi, serta durasi periode magang."
        icon="fa-solid fa-user-pen">
        <x-slot:action>
            <x-button href="{{ route('admin.interns.index') }}" variant="outline" icon="fa-solid fa-arrow-left">
                Kembali ke Daftar
            </x-button>
        </x-slot:action>
    </x-page-header>

    @if ($errors->any())
    <x-alert type="error" title="Terdapat beberapa kesalahan input:">
        <ul class="list-disc list-inside space-y-1 mt-1">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </x-alert>
    @endif

    <form action="{{ route('admin.interns.update', $intern->id) }}" method="POST"
        x-data="{
            startDate: '{{ old('start_date', $intern->start_date?->format('Y-m-d')) }}',
            endDate: '{{ old('end_date', $intern->end_date?->format('Y-m-d')) }}',
            get totalDays() {
                if (!this.startDate || !this.endDate) return 0;
                const start = new Date(this.startDate);
                const end = new Date(this.endDate);
                const diffTime = end - start;
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                return diffDays > 0 ? diffDays : 0;
            }
        }"
        class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-xs space-y-8">
        @csrf
        @method('PUT')

        <!-- Section 1: Informasi Akun & Biodata -->
        <div class="space-y-4">
            <h2 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2.5 border-b border-slate-100 pb-3">
                <i class="fa-solid fa-id-card text-[#005B9C]"></i>
                <span>1. Biodata & Akun Login</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-form-input
                    type="text"
                    name="name"
                    label="Nama Lengkap Peserta *"
                    value="{{ old('name', $intern->user?->name) }}"
                    required />

                <x-form-input
                    type="email"
                    name="email"
                    label="Email Pelindo / Pribadi *"
                    value="{{ old('email', $intern->user?->email) }}"
                    required />

                <x-form-input
                    type="text"
                    name="phone"
                    label="Nomor WhatsApp / HP"
                    value="{{ old('phone', $intern->user?->phone) }}" />

                <x-form-input
                    type="text"
                    name="nik_number"
                    label="Nomor Induk Kependudukan (NIK)"
                    value="{{ old('nik_number', $intern->nik_number) }}" />

                <x-form-select name="religion" label="Agama">
                    <option value="">-- Pilih Agama --</option>
                    @foreach(App\Helpers\MasterDataHelper::getReligions() as $key => $label)
                    <option value="{{ $key }}" {{ old('religion', $intern->religion) == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </x-form-select>

                <x-form-select name="gender" label="Jenis Kelamin">
                    <option value="">-- Pilih Jenis Kelamin --</option>
                    @foreach(App\Helpers\MasterDataHelper::getGenders() as $key => $label)
                    <option value="{{ $key }}" {{ old('gender', $intern->gender) == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </x-form-select>
            </div>

            <x-form-textarea
                name="address"
                label="Alamat Tinggal / Domisili"
                rows="2">
                {{ old('address', $intern->address) }}
            </x-form-textarea>
        </div>

        <!-- Section 2: Penempatan & Periode Magang -->
        <div class="space-y-4">
            <h2 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2.5 border-b border-slate-100 pb-3">
                <i class="fa-solid fa-graduation-cap text-[#005B9C]"></i>
                <span>2. Penempatan & Pengaturan Periode Magang</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-form-input
                    type="text"
                    name="student_id_number"
                    label="NIM / NISM *"
                    value="{{ old('student_id_number', $intern->student_id_number) }}"
                    required />

                <div>
                    <x-form-input
                        type="text"
                        name="institution"
                        label="Perguruan Tinggi / Sekolah *"
                        value="{{ old('institution', $intern->institution) }}"
                        list="institutionList"
                        required />
                    <datalist id="institutionList">
                        @foreach(App\Helpers\MasterDataHelper::getInstitutions() as $inst)
                        <option value="{{ $inst }}">
                        @endforeach
                    </datalist>
                </div>

                <x-form-input
                    type="text"
                    name="major"
                    label="Program Studi / Jurusan *"
                    value="{{ old('major', $intern->major) }}"
                    required />

                <x-form-select name="division_id" label="Divisi Penempatan *" required>
                    @foreach($divisions as $div)
                    <option value="{{ $div->id }}" {{ old('division_id', $intern->division_id) == $div->id ? 'selected' : '' }}>
                        {{ $div->name }}
                    </option>
                    @endforeach
                </x-form-select>

                <x-form-select name="supervisor_id" label="Pembimbing Lapangan *" required>
                    @foreach($supervisors as $spv)
                    <option value="{{ $spv->id }}" {{ old('supervisor_id', $intern->supervisor_id) == $spv->id ? 'selected' : '' }}>
                        {{ $spv->name }} ({{ $spv->email }})
                    </option>
                    @endforeach
                </x-form-select>

                <x-form-select name="status" label="Status Keaktifan *" required>
                    <option value="active" {{ old('status', $intern->status) == 'active' ? 'selected' : '' }}>ACTIVE (Aktif Magang)</option>
                    <option value="completed" {{ old('status', $intern->status) == 'completed' ? 'selected' : '' }}>COMPLETED (Selesai Magang)</option>
                    <option value="extended" {{ old('status', $intern->status) == 'extended' ? 'selected' : '' }}>EXTENDED (Perpanjangan)</option>
                    <option value="terminated" {{ old('status', $intern->status) == 'terminated' ? 'selected' : '' }}>TERMINATED (Berhenti)</option>
                </x-form-select>
            </div>

            <!-- Manually Set Internship Period (Start Date & End Date) + Live Days Calculation -->
            <div class="p-5 bg-blue-50/70 border border-blue-200/80 rounded-2xl space-y-4">
                <div class="flex items-center space-x-3 border-b border-blue-100 pb-3">
                    <div class="w-10 h-10 rounded-xl bg-[#005B9C] text-white flex items-center justify-center text-lg font-bold shrink-0">
                        <i class="fa-regular fa-calendar-days"></i>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-slate-900 text-sm">Pengaturan Manual Periode Magang</h4>
                        <p class="text-xs text-slate-600 font-medium">Ubah secara manual tanggal mulai dan selesai magang. Durasi total hari magang dihitung secara langsung.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-800 text-sm mb-1.5">Tanggal Mulai Magang *</label>
                        <input
                            type="date"
                            name="start_date"
                            x-model="startDate"
                            required
                            class="w-full text-sm font-semibold text-slate-900 bg-white border border-slate-300 rounded-xl p-3.5 outline-none transition focus:ring-2 focus:ring-[#005B9C]/20 focus:border-[#005B9C]">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-800 text-sm mb-1.5">Tanggal Selesai Magang *</label>
                        <input
                            type="date"
                            name="end_date"
                            x-model="endDate"
                            required
                            class="w-full text-sm font-semibold text-slate-900 bg-white border border-slate-300 rounded-xl p-3.5 outline-none transition focus:ring-2 focus:ring-[#005B9C]/20 focus:border-[#005B9C]">
                    </div>
                </div>

                <!-- Live Calculated Period Summary -->
                <div class="p-4 bg-white rounded-xl border border-blue-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="space-y-0.5">
                        <span class="text-xs text-slate-500 font-medium block">Rentang Tanggal Terpilih:</span>
                        <span class="text-sm font-black text-slate-900 block" x-text="startDate && endDate ? (startDate + ' s.d. ' + endDate) : 'Belum diatur'"></span>
                    </div>
                    <div class="bg-blue-50 px-4 py-2 rounded-xl border border-blue-200 text-right">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Total Durasi Magang</span>
                        <span class="text-lg font-black text-[#005B9C]" x-text="totalDays + ' Hari'"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: Rekening Perbankan -->
        <div class="space-y-4">
            <h2 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2.5 border-b border-slate-100 pb-3">
                <i class="fa-solid fa-building-columns text-[#005B9C]"></i>
                <span>3. Rekening Pencairan Uang Saku</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <x-form-select name="bank_name" label="Nama Bank">
                    <option value="">-- Pilih Bank --</option>
                    @foreach(App\Helpers\MasterDataHelper::getBanks() as $key => $label)
                    <option value="{{ $key }}" {{ old('bank_name', $intern->bank_name) == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </x-form-select>

                <x-form-input
                    type="text"
                    name="bank_account_number"
                    label="Nomor Rekening"
                    value="{{ old('bank_account_number', $intern->bank_account_number) }}" />

                <x-form-input
                    type="text"
                    name="bank_account_holder"
                    label="Atas Nama Rekening"
                    value="{{ old('bank_account_holder', $intern->bank_account_holder) }}" />
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
            <x-button href="{{ route('admin.interns.index') }}" variant="ghost">Batal</x-button>
            <x-button type="submit" variant="primary" icon="fa-solid fa-save">
                Update Peserta & Periode Magang
            </x-button>
        </div>
    </form>
</div>
@endsection