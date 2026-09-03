@extends('layouts.app')

@section('page_title', 'Klaim Uang Saku Bulanan')

@section('content')
<div class="space-y-6" x-data="{ 
    selectedPeriod: '',
    periodStartDate: '',
    periodEndDate: '',
    loading: false,
    calculated: false,
    wfoCount: 0,
    wfhCount: 0,
    leaveCount: 0,
    wfoRate: 0,
    wfhRate: 0,
    wfoRateFormatted: '',
    wfhRateFormatted: '',
    totalAllowance: 0,
    totalAllowanceFormatted: '',
    calculate() {
        if (!this.selectedPeriod) {
            this.calculated = false;
            return;
        }
        const parts = this.selectedPeriod.split('|');
        this.periodStartDate = parts[1];
        this.periodEndDate = parts[2];
        this.loading = true;
        fetch('{{ route('intern.allowance.calculate') }}?start_date=' + this.periodStartDate + '&end_date=' + this.periodEndDate)
            .then(res => res.json())
            .then(data => {
                this.wfoCount = data.wfo_count;
                this.wfhCount = data.wfh_count;
                this.leaveCount = data.leave_count;
                this.wfoRate = data.wfo_rate;
                this.wfhRate = data.wfh_rate;
                this.wfoRateFormatted = data.wfo_rate_formatted;
                this.wfhRateFormatted = data.wfh_rate_formatted;
                this.totalAllowance = data.total_allowance;
                this.totalAllowanceFormatted = data.total_allowance_formatted;
                this.calculated = true;
                this.loading = false;
            })
            .catch(err => {
                console.error(err);
                this.loading = false;
            });
    }
}">

    <!-- Header Card -->
    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center space-x-3.5">
            <div class="w-12 h-12 rounded-full bg-blue-50 text-[#0B63E5] flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-coins text-sm"></i>
            </div>
            <div>
                <h1 class="text-xl font-black text-slate-850 tracking-tight">Klaim Uang Saku Bulanan</h1>
                <p class="text-xs font-semibold text-slate-400 mt-1">Ajukan klaim uang saku bulanan berdasarkan rekapitulasi kehadiran logbook Anda.</p>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-250/50 rounded-2xl flex items-start space-x-3">
            <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs font-bold shrink-0">
                <i class="fa-solid fa-check"></i>
            </div>
            <div>
                <h4 class="font-extrabold text-emerald-800 text-xs">Berhasil!</h4>
                <p class="text-[11px] text-emerald-650 font-semibold mt-0.5">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-250/50 rounded-2xl flex items-start space-x-3">
            <div class="w-6 h-6 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center text-xs font-bold shrink-0">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <h4 class="font-extrabold text-rose-800 text-xs">Ada Kesalahan!</h4>
                <ul class="list-disc list-inside text-[11px] text-rose-600 font-medium mt-0.5 leading-relaxed space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left Side: Claim Form (5 Cols) -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-xs lg:col-span-5 space-y-4">
            <div class="border-b border-slate-100 pb-3">
                <h3 class="font-black text-slate-800 text-sm flex items-center gap-1.5">
                    <i class="fa-solid fa-file-invoice-dollar text-blue-600"></i>
                    <span>Form Pengajuan Klaim</span>
                </h3>
                <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Kehadiran dan uang saku dihitung otomatis berdasarkan logbook.</p>
            </div>

            @php
                $bankMissing = empty($profile->bank_name) || empty($profile->bank_account_number) || empty($profile->bank_account_holder);
            @endphp

            @if($bankMissing)
                <div class="p-4 bg-amber-50 border border-amber-200/50 rounded-2xl flex items-start space-x-3">
                    <div class="w-6 h-6 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center text-xs font-bold shrink-0">
                        <i class="fa-solid fa-info"></i>
                    </div>
                    <div class="text-[11px] leading-relaxed text-amber-800">
                        <h4 class="font-extrabold text-xs">Rekening Belum Lengkap</h4>
                        <p class="font-semibold mt-0.5">Anda belum mengisi informasi rekening bank di profil Anda. Silakan isi terlebih dahulu untuk mengajukan klaim.</p>
                        <a href="{{ route('profile.show') }}" class="inline-block mt-2 px-3 py-1 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-[9px] font-black transition">Lengkapi Profil</a>
                    </div>
                </div>
            @else
                <form action="{{ route('intern.allowance.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs font-semibold">
                    @csrf

                    <!-- Pilih Periode -->
                    <div>
                        <label class="block font-extrabold text-slate-800 mb-2">Pilih Periode Pengajuan *</label>
                        <select name="period_value" x-model="selectedPeriod" @change="calculate()" required
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl font-bold text-slate-850 focus:ring-2 focus:ring-blue-600 focus:bg-white outline-none appearance-none transition">
                            <option value="">-- Pilih Periode --</option>
                            @foreach($availablePeriods as $p)
                                <option value="{{ $p['number'] }}|{{ $p['start_date'] }}|{{ $p['end_date'] }}">{{ $p['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Loader -->
                    <div x-show="loading" class="py-6 text-center text-slate-400 space-y-2">
                        <i class="fa-solid fa-circle-notch animate-spin text-lg text-blue-600"></i>
                        <p class="text-[10px] font-bold">Menghitung logbook & uang saku...</p>
                    </div>

                    <!-- Calculation Results -->
                    <div x-show="calculated && !loading" class="space-y-4 animate-fade-in">
                        
                        <!-- Attendance Stats -->
                        <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl space-y-2.5">
                            <h4 class="font-extrabold text-slate-700 text-[10px] uppercase tracking-wider">Ringkasan Kehadiran</h4>
                            <div class="grid grid-cols-3 gap-2 text-center">
                                <div class="p-2 bg-white rounded-xl border border-slate-100">
                                    <span class="text-sm font-black text-slate-800 block" x-text="wfoCount"></span>
                                    <span class="text-[8px] font-bold text-slate-400 uppercase">WFO</span>
                                </div>
                                <div class="p-2 bg-white rounded-xl border border-slate-100">
                                    <span class="text-sm font-black text-slate-800 block" x-text="wfhCount"></span>
                                    <span class="text-[8px] font-bold text-slate-400 uppercase">WFH</span>
                                </div>
                                <div class="p-2 bg-white rounded-xl border border-slate-100">
                                    <span class="text-sm font-black text-slate-800 block" x-text="leaveCount"></span>
                                    <span class="text-[8px] font-bold text-slate-400 uppercase">Izin</span>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Details -->
                        <div class="p-4 bg-blue-50/40 border border-blue-100 rounded-2xl space-y-2.5">
                            <h4 class="font-extrabold text-blue-800 text-[10px] uppercase tracking-wider">Perhitungan Uang Saku</h4>
                            <div class="space-y-1.5 text-[11px] leading-relaxed text-slate-650">
                                <div class="flex justify-between">
                                    <span>Hadir WFO (<span x-text="wfoCount"></span> hari &times; <span x-text="wfoRateFormatted"></span>)</span>
                                    <span class="font-bold text-slate-800" x-text="'Rp ' + (wfoCount * wfoRate).toLocaleString('id-ID')"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Hadir WFH (<span x-text="wfhCount"></span> hari &times; <span x-text="wfhRateFormatted"></span>)</span>
                                    <span class="font-bold text-slate-800" x-text="'Rp ' + (wfhCount * wfhRate).toLocaleString('id-ID')"></span>
                                </div>
                                <div class="border-t border-blue-200/50 my-2 pt-2 flex justify-between text-xs font-black text-blue-900">
                                    <span>Total Uang Saku:</span>
                                    <span x-text="totalAllowanceFormatted"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Bank Account Confirmation -->
                        <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl space-y-2 text-[11px]">
                            <h4 class="font-extrabold text-slate-700 text-[10px] uppercase tracking-wider">Tujuan Transfer</h4>
                            <div class="space-y-1 font-semibold text-slate-600">
                                <p>Bank: <strong class="text-slate-850 font-extrabold">{{ $profile->bank_name }}</strong></p>
                                <p>No. Rekening: <strong class="text-slate-850 font-extrabold">{{ $profile->bank_account_number }}</strong></p>
                                <p>Atas Nama: <strong class="text-slate-850 font-extrabold">{{ $profile->bank_account_holder }}</strong></p>
                            </div>
                        </div>

                         <!-- Logbook PDF Download & Upload -->
                        <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl space-y-3.5">
                            <h4 class="font-extrabold text-slate-700 text-[10px] uppercase tracking-wider block">Lampiran Bukti Logbook</h4>
                            
                            <!-- 1. Download Helper Button -->
                            <div class="space-y-1">
                                <span class="text-[9px] text-[#0B63E5] block font-black flex items-center gap-1">
                                    <i class="fa-solid fa-1"></i>
                                    <span>Unduh rekap logbook bulanan untuk meminta tanda tangan (TTD) Pembimbing Lapangan</span>
                                </span>
                                <a :href="'{{ route('reports.pdf') }}?start_date=' + periodStartDate" 
                                   target="_blank"
                                   class="w-full py-2 px-3 bg-blue-50 hover:bg-blue-100 text-[#0B63E5] rounded-xl text-[10px] font-black transition flex items-center justify-center space-x-1.5 border border-blue-100/50">
                                    <i class="fa-solid fa-file-pdf"></i>
                                    <span>Unduh PDF Logbook Periode Ini</span>
                                </a>
                            </div>

                            <!-- 2. File Upload Input -->
                            <div class="space-y-1.5 pt-2 border-t border-slate-200/50">
                                <span class="text-[9px] text-emerald-600 block font-black flex items-center gap-1">
                                    <i class="fa-solid fa-2"></i>
                                    <span>Unggah berkas logbook yang SUDAH DITANDATANGANI *</span>
                                </span>
                                <input type="file" name="attachment" required
                                       class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl font-semibold text-[10px] text-slate-800 outline-none transition file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[9px] file:font-black file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100">
                                <p class="text-[8px] text-slate-400 font-semibold">Mendukung PDF, PNG, JPG, JPEG (Max. 5 MB)</p>
                            </div>
                        </div>

                        <button type="submit" class="w-full py-3 bg-[#0B63E5] hover:bg-blue-700 text-white rounded-2xl font-extrabold transition shadow-md flex items-center justify-center space-x-2">
                            <i class="fa-solid fa-paper-plane text-xs"></i>
                            <span>Kirim Pengajuan</span>
                        </button>

                    </div>
                </form>
            @endif
        </div>

        <!-- Right Side: Claim History List (7 Cols) -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-xs lg:col-span-7 space-y-4">
            <div class="border-b border-slate-100 pb-3.5">
                <h3 class="font-black text-slate-800 text-sm flex items-center gap-1.5">
                    <i class="fa-solid fa-history text-blue-600"></i>
                    <span>Riwayat Klaim Uang Saku</span>
                </h3>
                <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Status dan pencairan klaim bulanan Anda.</p>
            </div>

            <!-- List -->
            <div class="space-y-3.5 overflow-y-auto max-h-[580px] pr-1">
                @forelse($claims as $claim)
                    @php
                        $badgeStyle = match($claim->status) {
                            'processed_by_hr' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                            'approved_by_supervisor' => 'bg-blue-50 text-blue-600 border-blue-100',
                            'rejected' => 'bg-rose-50 text-rose-600 border-rose-100',
                            default => 'bg-amber-50 text-amber-600 border-amber-100',
                        };
                        $statusLabel = match($claim->status) {
                            'processed_by_hr' => 'Dicairkan',
                            'approved_by_supervisor' => 'Disetujui Pembimbing',
                            'rejected' => 'Ditolak',
                            default => 'Menunggu Pembimbing',
                        };
                    @endphp
                    <div class="p-4 bg-slate-50/50 border border-slate-200/80 rounded-2xl flex flex-col gap-3.5 hover:bg-slate-50 transition">
                        
                        <div class="flex items-center justify-between border-b border-slate-200/60 pb-2 flex-wrap gap-2">
                            <span class="text-xs font-black text-slate-800">
                                Periode {{ $claim->period_number }} ({{ \Carbon\Carbon::parse($claim->start_date)->isoFormat('D MMMM') }} - {{ \Carbon\Carbon::parse($claim->end_date)->isoFormat('D MMMM YYYY') }})
                            </span>
                            <span class="px-2.5 py-0.5 text-[9px] font-black uppercase tracking-wider rounded-md border {{ $badgeStyle }}">
                                {{ $statusLabel }}
                            </span>
                        </div>

                        <!-- KIP-K Style Detailed Disbursement Tracking Stepper -->
                        @if($claim->status === 'rejected')
                            <div class="p-3.5 bg-rose-50 border border-rose-200/80 rounded-2xl text-xs text-rose-800 flex items-start space-x-3">
                                <div class="w-7 h-7 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center font-bold text-sm shrink-0 mt-0.5">
                                    <i class="fa-solid fa-circle-xmark"></i>
                                </div>
                                <div>
                                    <strong class="font-extrabold block text-rose-900">Klaim Ditolak Pembimbing Lapangan</strong>
                                    <span class="text-[11px] text-rose-700 font-semibold block mt-0.5">Catatan: "{{ $claim->supervisor_note ?? 'Berkas/rekapitulasi belum memenuhi ketentuan.' }}"</span>
                                </div>
                            </div>
                        @else
                            @php
                                $s1 = true;
                                $s2 = in_array($claim->status, ['approved_by_supervisor', 'processed_by_hr']);
                                $s3 = in_array($claim->status, ['approved_by_supervisor', 'processed_by_hr']);
                                $s4 = $claim->status === 'processed_by_hr';
                            @endphp

                            <div class="p-4 bg-gradient-to-br from-slate-900 via-slate-850 to-blue-950 text-white rounded-2xl space-y-3.5 shadow-sm border border-slate-800">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-2.5">
                                    <div class="flex items-center space-x-2">
                                        <span class="w-2 h-2 rounded-full {{ $s4 ? 'bg-emerald-400 animate-ping' : 'bg-amber-400 animate-pulse' }}"></span>
                                        <span class="text-[11px] font-extrabold text-blue-200 uppercase tracking-wider">Lacak Status Pencairan Uang Saku</span>
                                    </div>
                                    <span class="text-[10px] font-bold text-slate-400">
                                        {{ $s4 ? 'Status: SP2D / Cair' : ($s2 ? 'Status: Verifikasi HR' : 'Status: Review Pembimbing') }}
                                    </span>
                                </div>

                                <!-- 4 Nodes Grid -->
                                <div class="grid grid-cols-4 gap-2 relative">
                                    <!-- Progress Line Background -->
                                    <div class="absolute top-3.5 left-4 right-4 h-0.5 bg-slate-700 -z-0"></div>
                                    <!-- Active Progress Line -->
                                    <div class="absolute top-3.5 left-4 h-0.5 bg-gradient-to-r from-blue-500 via-indigo-500 to-emerald-400 transition-all duration-500 -z-0"
                                         style="width: {{ $s4 ? '88%' : ($s2 ? '60%' : '25%') }};"></div>

                                    <!-- Node 1: Pengajuan -->
                                    <div class="flex flex-col items-center text-center relative z-10 space-y-1">
                                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-[10px] font-bold bg-blue-600 text-white ring-4 ring-slate-900 shadow-sm">
                                            <i class="fa-solid fa-check text-[9px]"></i>
                                        </div>
                                        <span class="text-[10px] font-extrabold text-white">1. Pengajuan</span>
                                        <span class="text-[8px] font-medium text-slate-400">Logbook OK</span>
                                    </div>

                                    <!-- Node 2: Verifikasi Pembimbing -->
                                    <div class="flex flex-col items-center text-center relative z-10 space-y-1">
                                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-[10px] font-bold {{ $s2 ? 'bg-blue-500 text-white ring-4 ring-slate-900 shadow-sm' : 'bg-slate-700 text-slate-400 ring-4 ring-slate-900' }}">
                                            @if($s2) <i class="fa-solid fa-check text-[9px]"></i> @else <i class="fa-solid fa-spinner animate-spin text-[9px]"></i> @endif
                                        </div>
                                        <span class="text-[10px] font-extrabold {{ $s2 ? 'text-white' : 'text-slate-400' }}">2. Pembimbing</span>
                                        <span class="text-[8px] font-medium text-slate-400">{{ $s2 ? 'Verified' : 'Review' }}</span>
                                    </div>

                                    <!-- Node 3: Verifikasi HR Admin -->
                                    <div class="flex flex-col items-center text-center relative z-10 space-y-1">
                                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-[10px] font-bold {{ $s3 ? ($s4 ? 'bg-blue-500 text-white ring-4 ring-slate-900' : 'bg-amber-500 text-white ring-4 ring-slate-900 animate-pulse') : 'bg-slate-700 text-slate-400 ring-4 ring-slate-900' }}">
                                            @if($s4) <i class="fa-solid fa-check text-[9px]"></i> @elseif($s3) 3 @else 3 @endif
                                        </div>
                                        <span class="text-[10px] font-extrabold {{ $s3 ? 'text-white' : 'text-slate-400' }}">3. Rekap HR</span>
                                        <span class="text-[8px] font-medium text-slate-400">{{ $s4 ? 'Disetujui' : ($s3 ? 'Verifikasi' : 'Menunggu') }}</span>
                                    </div>

                                    <!-- Node 4: Transfer / Cair -->
                                    <div class="flex flex-col items-center text-center relative z-10 space-y-1">
                                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-[10px] font-bold {{ $s4 ? 'bg-emerald-500 text-white ring-4 ring-slate-900 shadow-md shadow-emerald-500/30' : 'bg-slate-700 text-slate-400 ring-4 ring-slate-900' }}">
                                            @if($s4) <i class="fa-solid fa-coins text-[10px]"></i> @else 4 @endif
                                        </div>
                                        <span class="text-[10px] font-extrabold {{ $s4 ? 'text-emerald-300' : 'text-slate-400' }}">4. Pencairan</span>
                                        <span class="text-[8px] font-medium text-slate-400">{{ $s4 ? 'Transfer Cair' : 'Proses Bank' }}</span>
                                    </div>
                                </div>

                                <!-- Current Status Description Card -->
                                <div class="bg-white/10 backdrop-blur border border-white/10 rounded-xl p-2.5 text-[10px] flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <i class="fa-solid {{ $s4 ? 'fa-circle-check text-emerald-400' : ($s2 ? 'fa-clock-rotate-left text-amber-300' : 'fa-hourglass-start text-blue-300') }}"></i>
                                        <span class="font-semibold text-slate-200">
                                            @if($s4)
                                                Uang saku sebesar <strong>Rp {{ number_format($claim->total_allowance, 0, ',', '.') }}</strong> telah dicairkan ke rekening {{ $claim->bank_name }}.
                                            @elseif($s2)
                                                Klaim disetujui Pembimbing. Menunggu penetapan dan verifikasi rekening oleh HR Admin Pelindo.
                                            @else
                                                Pengajuan klaim berhasil dikirim. Menunggu persetujuan Pembimbing Lapangan.
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Stats and amount -->
                        <div class="grid grid-cols-2 gap-4 text-xs">
                            <div class="space-y-1">
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider block">Rekap Presensi</span>
                                <p class="font-extrabold text-slate-700">
                                    {{ $claim->wfo_count }} WFO &bull; {{ $claim->wfh_count }} WFH &bull; {{ $claim->leave_count }} Izin
                                </p>
                            </div>
                            <div class="space-y-1 text-right">
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider block">Total Klaim</span>
                                <p class="font-black text-blue-600 text-sm">
                                    Rp {{ number_format($claim->total_allowance, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>

                        <!-- Bank account info -->
                        <div class="bg-white/80 border border-slate-100 rounded-xl p-2.5 text-[10px] leading-relaxed flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <div class="w-6 h-6 rounded-lg bg-blue-50 text-[#0B63E5] flex items-center justify-center text-xs"><i class="fa-solid fa-credit-card"></i></div>
                                <span class="font-extrabold text-slate-700">{{ $claim->bank_name }} - {{ $claim->bank_account_number }}</span>
                            </div>
                            <span class="text-slate-450 font-semibold italic">{{ $claim->bank_account_holder }}</span>
                        </div>

                        <!-- Attachment link -->
                        @if($claim->attachment_path)
                            <div class="px-2.5 py-2 bg-white border border-slate-100 rounded-xl text-[10px] flex items-center justify-between">
                                <span class="text-slate-500 font-semibold">Berkas Terlampir:</span>
                                <a href="{{ asset($claim->attachment_path) }}" target="_blank"
                                   class="font-black text-[#0B63E5] hover:underline flex items-center gap-1">
                                    <i class="fa-solid fa-paperclip"></i>
                                    <span>Lihat Berkas</span>
                                </a>
                            </div>
                        @endif

                        <!-- Supervisor/HR Notes -->
                        @if($claim->supervisor_note)
                            <div class="p-2.5 bg-amber-50/40 border border-amber-100/60 rounded-xl text-[10px] leading-relaxed">
                                <span class="font-extrabold text-slate-700 block">Catatan Pembimbing:</span>
                                <p class="text-slate-500 font-medium italic mt-0.5">"{{ $claim->supervisor_note }}"</p>
                            </div>
                        @endif

                        @if($claim->hr_note)
                            <div class="p-2.5 bg-emerald-50/40 border border-emerald-100/60 rounded-xl text-[10px] leading-relaxed">
                                <span class="font-extrabold text-slate-700 block">Catatan HR Admin:</span>
                                <p class="text-slate-500 font-medium italic mt-0.5">"{{ $claim->hr_note }}"</p>
                            </div>
                        @endif

                        <span class="text-[9px] text-slate-400 font-bold self-end leading-none">
                            Diajukan {{ $claim->created_at ? $claim->created_at->diffForHumans() : '-' }}
                        </span>

                    </div>
                @empty
                    <div class="py-12 text-center text-slate-400 space-y-2">
                        <div class="w-12 h-12 bg-slate-100 text-slate-450 rounded-full flex items-center justify-center text-lg mx-auto">
                            <i class="fa-solid fa-folder-open"></i>
                        </div>
                        <p class="text-xs font-bold">Belum ada riwayat klaim</p>
                        <p class="text-[10px] text-slate-400">Silakan gunakan form di sebelah kiri untuk mengajukan klaim uang saku.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection
