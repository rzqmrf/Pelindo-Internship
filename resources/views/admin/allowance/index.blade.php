@extends('layouts.app')

@section('page_title', 'Verifikasi & Pencairan Uang Saku HR')

@section('content')
<div class="space-y-6" x-data="{ 
    hrModalOpen: false, 
    periodModalOpen: false, 
    selectedClaim: null, 
    hrNote: '',
    title: '',
    startDate: '',
    endDate: '',
    subStartDate: '',
    subEndDate: '',
    get logbookDays() {
        if (!this.startDate || !this.endDate) return 0;
        const diff = new Date(this.endDate) - new Date(this.startDate);
        const days = Math.ceil(diff / (1000 * 60 * 60 * 24)) + 1;
        return days > 0 ? days : 0;
    },
    get submissionDays() {
        if (!this.subStartDate || !this.subEndDate) return 0;
        const diff = new Date(this.subEndDate) - new Date(this.subStartDate);
        const days = Math.ceil(diff / (1000 * 60 * 60 * 24)) + 1;
        return days > 0 ? days : 0;
    }
}">

    <!-- Centralized Page Header -->
    <x-page-header 
        title="Verifikasi & Pencairan Uang Saku" 
        subtitle="Kelola jadwal buka periode pengajuan klaim dan verifikasi pencairan uang saku mahasiswa." 
        icon="fa-solid fa-money-check-dollar"
    >
        <x-slot:action>
            <div class="flex items-center gap-2">
                <button @click="periodModalOpen = true" class="px-4 py-2.5 bg-[#005B9C] hover:bg-blue-800 text-white rounded-2xl text-xs font-extrabold transition flex items-center space-x-2 shadow-2xs">
                    <i class="fa-solid fa-calendar-plus"></i>
                    <span>Buka Periode Submit Baru</span>
                </button>
                <a href="{{ route('admin.allowance.export_payroll', request()->all()) }}" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-xs font-extrabold transition flex items-center space-x-2 shadow-2xs">
                    <i class="fa-solid fa-file-excel"></i>
                    <span>Ekspor Payroll Excel/CSV</span>
                </a>
            </div>
        </x-slot:action>
    </x-page-header>

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

    <!-- Section 1: Daftar Periode Submit Klaim (Diatur Manual oleh Admin HR) -->
    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
            <div>
                <h3 class="font-black text-slate-800 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-calendar-days text-[#005B9C]"></i>
                    <span>Jadwal & Periode Pengajuan Klaim Uang Saku (Aktif)</span>
                </h3>
                <p class="text-xs text-slate-400 font-semibold mt-0.5">Admin HR menentukan secara manual rentang logbook dan durasi berapa hari jendela pengajuan dibuka.</p>
            </div>
            <button @click="periodModalOpen = true" class="px-3.5 py-2 bg-blue-50 hover:bg-blue-100 text-[#005B9C] border border-blue-200/60 rounded-xl text-xs font-extrabold transition inline-flex items-center gap-1.5 self-start sm:self-auto">
                <i class="fa-solid fa-plus"></i>
                <span>Tambah Periode Buka</span>
            </button>
        </div>

        @if($periods->isEmpty())
        <div class="p-6 bg-slate-50 rounded-2xl border border-dashed border-slate-200 text-center space-y-2">
            <i class="fa-solid fa-calendar-xmark text-slate-400 text-2xl"></i>
            <p class="text-xs font-bold text-slate-700">Belum ada periode pengajuan klaim yang dibuat oleh HR Admin.</p>
            <p class="text-[11px] text-slate-500">Klik "Buka Periode Submit Baru" di atas untuk membuka jadwal submit bagi peserta magang.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-wider">
                        <th class="py-3 px-4">Judul Periode Klaim</th>
                        <th class="py-3 px-4">Rentang Logbook (Presensi)</th>
                        <th class="py-3 px-4">Jadwal Submit (Buka HR)</th>
                        <th class="py-3 px-4 text-center">Durasi Buka Submit</th>
                        <th class="py-3 px-4 text-center">Status Jendela Submit</th>
                        <th class="py-3 px-4 text-right">Aksi HR</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-xs font-bold text-slate-700">
                    @foreach($periods as $p)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="py-3.5 px-4 font-extrabold text-slate-900">
                            {{ $p->title }}
                        </td>
                        <td class="py-3.5 px-4 font-mono text-slate-650">
                            {{ $p->start_date->format('d/m/Y') }} s.d {{ $p->end_date->format('d/m/Y') }}
                            <span class="text-[10px] text-blue-600 font-extrabold block">({{ $p->logbook_days }} Hari Presensi)</span>
                        </td>
                        <td class="py-3.5 px-4 font-mono text-slate-650">
                            {{ $p->submission_start_date->format('d/m/Y') }} s.d {{ $p->submission_end_date->format('d/m/Y') }}
                        </td>
                        <td class="py-3.5 px-4 text-center font-extrabold text-slate-800">
                            <span class="px-2.5 py-1 bg-blue-50 text-[#005B9C] rounded-xl border border-blue-200">
                                {{ $p->submission_days }} Hari
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            @if($p->is_open)
                                <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-full text-[10px] font-black border border-emerald-200 inline-flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    DIBUKA (Tersisa {{ $p->remaining_days }} Hari)
                                </span>
                            @elseif(!$p->is_active)
                                <span class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-full text-[10px] font-bold border border-slate-200">
                                    NONAKTIF (HR CLOSED)
                                </span>
                            @else
                                <span class="px-2.5 py-1 bg-amber-50 text-amber-700 rounded-full text-[10px] font-bold border border-amber-200">
                                    DITUTUP / EXPIRED
                                </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-right space-x-1.5">
                            <form action="{{ route('admin.allowance_periods.toggle', $p->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 {{ $p->is_active ? 'bg-amber-50 hover:bg-amber-100 text-amber-700 border-amber-200' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border-emerald-200' }} border rounded-xl text-[11px] font-bold transition">
                                    {{ $p->is_active ? 'Tutup' : 'Buka' }}
                                </button>
                            </form>
                            <form action="{{ route('admin.allowance_periods.destroy', $p->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus periode pengajuan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-xl transition text-xs">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    <!-- Section 2: Filters Bar -->
    <div class="bg-white rounded-3xl p-4 border border-slate-100 shadow-sm">
        <form method="GET" action="{{ route('admin.allowance.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-center">
            
            <!-- Division Filter -->
            <div class="relative">
                <select name="division_id" class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold text-slate-650 outline-none appearance-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
                    <option value="">Semua Divisi</option>
                    @foreach($divisions as $div)
                        <option value="{{ $div->id }}" {{ request('division_id') == $div->id ? 'selected' : '' }}>{{ $div->name }}</option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-400">
                    <i class="fa-solid fa-chevron-down text-[10px]"></i>
                </div>
            </div>

            <!-- Status Filter -->
            <div class="relative">
                <select name="status" class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold text-slate-650 outline-none appearance-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
                    <option value="">Semua Status</option>
                    <option value="pending_supervisor" {{ request('status') == 'pending_supervisor' ? 'selected' : '' }}>Menunggu Pembimbing</option>
                    <option value="approved_by_supervisor" {{ request('status') == 'approved_by_supervisor' ? 'selected' : '' }}>Disetujui Pembimbing</option>
                    <option value="processed_by_hr" {{ request('status') == 'processed_by_hr' ? 'selected' : '' }}>Telah Dicairkan</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-400">
                    <i class="fa-solid fa-chevron-down text-[10px]"></i>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex items-center gap-3 col-span-1 sm:col-span-2">
                <button type="submit" class="flex-1 py-2.5 bg-[#0B63E5] hover:bg-blue-750 text-white rounded-2xl text-xs font-extrabold transition flex items-center justify-center space-x-1.5 shadow-2xs">
                    <i class="fa-solid fa-sliders"></i>
                    <span>Filter Klaim</span>
                </button>
                <a href="{{ route('admin.allowance.index') }}" class="px-4 py-2.5 bg-white border border-slate-200 text-slate-650 rounded-2xl text-xs font-extrabold hover:bg-slate-50 transition flex items-center justify-center space-x-1.5 shadow-2xs">
                    <i class="fa-solid fa-rotate text-xs"></i>
                    <span>Reset</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Section 3: Table List Klaim Masuk -->
    <form action="{{ route('admin.allowance.bulk_process') }}" method="POST" id="bulkForm" class="bg-white rounded-3xl p-6 border border-slate-100 shadow-xs space-y-4">
        @csrf
        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
            <span class="text-xs font-bold text-slate-500">Pilih klaim yang disetujui pembimbing untuk pencairan massal:</span>
            <button type="submit" onclick="return confirm('Apakah Anda yakin ingin mencairkan seluruh klaim tercentang?')" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-xs">
                <i class="fa-solid fa-check-double mr-1"></i> Cairkan Massal Tercentang
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-wider">
                        <th class="py-3.5 px-3 text-center">
                            <input type="checkbox" onclick="document.querySelectorAll('.claim-checkbox').forEach(cb => cb.checked = this.checked)" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                        </th>
                        <th class="py-3.5 px-5">Periode Logbook</th>
                        <th class="py-3.5 px-4">Nama & Divisi</th>
                        <th class="py-3.5 px-4">Presensi</th>
                        <th class="py-3.5 px-4">Uang Saku</th>
                        <th class="py-3.5 px-4">Tujuan Rekening</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-5 text-right">Aksi HR</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-xs font-bold text-slate-700">
                    @forelse($claims as $claim)
                        @php
                            $badge = App\Helpers\StatusHelper::claimBadge($claim->status);
                        @endphp
                        <tr class="hover:bg-slate-50/40 transition">
                            <td class="py-4 px-3 text-center">
                                @if($claim->status === 'approved_by_supervisor')
                                    <input type="checkbox" name="claim_ids[]" value="{{ $claim->id }}" class="claim-checkbox rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                                @else
                                    <span class="text-slate-300">&bull;</span>
                                @endif
                            </td>
                            <td class="py-4 px-5 font-black text-slate-800 leading-normal">
                                <span class="block">Periode {{ $claim->period_number }}</span>
                                <span class="text-[10px] text-slate-400 font-semibold block mt-0.5">
                                    {{ \Carbon\Carbon::parse($claim->start_date)->isoFormat('D MMMM') }} - {{ \Carbon\Carbon::parse($claim->end_date)->isoFormat('D MMMM YYYY') }}
                                </span>
                            </td>
                            <td class="py-4 px-4 leading-relaxed">
                                <span class="font-extrabold text-slate-850 block">{{ $claim->intern?->name }}</span>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="text-[10px] text-slate-400 font-semibold uppercase">{{ $claim->intern?->internProfile?->division?->name ?? '-' }}</span>
                                    @if($claim->attachment_path)
                                        <span class="text-slate-300 text-[9px]">|</span>
                                        <a href="{{ asset($claim->attachment_path) }}" target="_blank"
                                           class="text-[10px] text-blue-600 hover:text-blue-750 hover:underline inline-flex items-center gap-0.5 font-bold">
                                            <i class="fa-solid fa-paperclip text-[9px]"></i>
                                            <span>PDF Logbook</span>
                                        </a>
                                    @endif
                                </div>
                            </td>
                            <td class="py-4 px-4 font-semibold text-slate-650">
                                {{ $claim->wfo_count }} WFO / {{ $claim->wfh_count }} WFH / {{ $claim->leave_count }} Izin
                            </td>
                            <td class="py-4 px-4 font-black text-blue-650">
                                Rp {{ number_format($claim->total_allowance, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-4 leading-normal">
                                <span class="font-bold text-slate-700 block">{{ $claim->bank_name }} - {{ $claim->bank_account_number }}</span>
                                <span class="text-[10px] text-slate-450 font-semibold italic">{{ $claim->bank_account_holder }}</span>
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-2.5 py-1 text-[9px] font-black uppercase tracking-wider rounded-md border {{ $badge['class'] }} inline-block">
                                    <i class="fa-solid {{ $badge['icon'] }} mr-1"></i> {{ $badge['label'] }}
                                </span>
                                <div class="flex items-center gap-1 mt-1 text-[9px] text-slate-400 font-bold">
                                    {{ $badge['step'] }}
                                </div>
                            </td>
                            <td class="py-4 px-5 text-right">
                                @if($claim->status === 'approved_by_supervisor')
                                    <button @click="selectedClaim = {{ json_encode($claim) }}; hrNote = ''; hrModalOpen = true" 
                                            type="button"
                                            class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-extrabold transition shadow-2xs inline-flex items-center space-x-1">
                                        <i class="fa-solid fa-check-double"></i>
                                        <span>Proses Cair</span>
                                    </button>
                                @else
                                    <span class="text-[10px] text-slate-400 font-semibold italic">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <div class="w-12 h-12 bg-slate-100 text-slate-450 rounded-full flex items-center justify-center text-lg mx-auto mb-2">
                                    <i class="fa-solid fa-receipt"></i>
                                </div>
                                <p class="text-xs font-bold">Tidak ada pengajuan klaim</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pt-3">
            {{ $claims->links() }}
        </div>
    </form>

    <!-- Modal 1: Tambah Periode Submit Klaim Baru -->
    <div x-show="periodModalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" 
         x-transition.opacity 
         x-cloak>
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 space-y-5 border border-slate-100 shadow-xl" @click.away="periodModalOpen = false">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-blue-50 text-[#005B9C] flex items-center justify-center font-bold">
                        <i class="fa-solid fa-calendar-plus"></i>
                    </div>
                    <h3 class="font-black text-slate-800 text-sm">Buka Periode Submit Klaim Baru</h3>
                </div>
                <button @click="periodModalOpen = false" class="text-slate-400 hover:text-slate-650 transition">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form action="{{ route('admin.allowance_periods.store') }}" method="POST" class="space-y-4 text-xs font-semibold">
                @csrf
                
                <div>
                    <label class="block font-extrabold text-slate-800 mb-1">Judul Periode Klaim *</label>
                    <input type="text" name="title" x-model="title" required placeholder="Contoh: Klaim Uang Saku September 2026"
                        class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-900 focus:ring-2 focus:ring-blue-600 focus:bg-white outline-none">
                </div>

                <div class="p-3 bg-slate-50 border border-slate-200 rounded-2xl space-y-3">
                    <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider block">1. Rentang Presensi Logbook Yang Diklaim</span>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Tanggal Mulai Logbook</label>
                            <input type="date" name="start_date" x-model="startDate" required
                                class="w-full p-2.5 bg-white border border-slate-300 rounded-xl font-bold text-slate-900 outline-none">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Tanggal Selesai Logbook</label>
                            <input type="date" name="end_date" x-model="endDate" required
                                class="w-full p-2.5 bg-white border border-slate-300 rounded-xl font-bold text-slate-900 outline-none">
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-[11px] font-extrabold text-[#005B9C]" x-text="'Total Presensi: ' + logbookDays + ' Hari'"></span>
                    </div>
                </div>

                <div class="p-3 bg-blue-50/70 border border-blue-200/80 rounded-2xl space-y-3">
                    <span class="text-[10px] font-extrabold text-[#005B9C] uppercase tracking-wider block">2. Tanggal HR Membuka Submit Pengajuan</span>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Tanggal Buka Submit</label>
                            <input type="date" name="submission_start_date" x-model="subStartDate" required
                                class="w-full p-2.5 bg-white border border-slate-300 rounded-xl font-bold text-slate-900 outline-none">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Tanggal Tutup Submit</label>
                            <input type="date" name="submission_end_date" x-model="subEndDate" required
                                class="w-full p-2.5 bg-white border border-slate-300 rounded-xl font-bold text-slate-900 outline-none">
                        </div>
                    </div>
                    <div class="p-2 bg-white rounded-xl border border-blue-100 flex items-center justify-between">
                        <span class="text-[10px] text-slate-500 font-bold">Lama Jendela Pengajuan:</span>
                        <span class="text-sm font-black text-[#005B9C]" x-text="submissionDays + ' Hari Buka'"></span>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="periodModalOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold transition">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-[#005B9C] hover:bg-blue-800 text-white rounded-xl font-extrabold transition">
                        Buka Periode Submit
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 2: HR Process Modal (Cairkan) -->
    <div x-show="hrModalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" 
         x-transition.opacity 
         x-cloak>
        <div class="bg-white rounded-3xl max-w-md w-full p-6 space-y-4 border border-slate-100 shadow-xl" @click.away="hrModalOpen = false">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                <h3 class="font-black text-slate-800 text-sm">Proses Pencairan Uang Saku</h3>
                <button @click="hrModalOpen = false" class="text-slate-400 hover:text-slate-650 transition">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="text-xs space-y-3">
                <div>
                    <span class="font-bold text-slate-400 uppercase text-[9px] tracking-wider block">Peserta Magang</span>
                    <span class="font-extrabold text-slate-800 block text-xs" x-text="selectedClaim ? selectedClaim.intern.name : ''"></span>
                </div>
                <div>
                    <span class="font-bold text-slate-400 uppercase text-[9px] tracking-wider block">Periode Klaim</span>
                    <span class="font-extrabold text-slate-800 block text-xs" x-text="selectedClaim ? 'Periode ' + selectedClaim.period_number + ' (' + new Date(selectedClaim.start_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long' }) + ' - ' + new Date(selectedClaim.end_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) + ')' : ''"></span>
                </div>
                <div>
                    <span class="font-bold text-slate-400 uppercase text-[9px] tracking-wider block">Tujuan Transfer</span>
                    <span class="font-extrabold text-slate-800 block text-xs" x-text="selectedClaim ? (selectedClaim.bank_name + ' - ' + selectedClaim.bank_account_number + ' a.n. ' + selectedClaim.bank_account_holder) : ''"></span>
                </div>
                <div>
                    <span class="font-bold text-slate-400 uppercase text-[9px] tracking-wider block">Nominal Pencairan</span>
                    <span class="font-black text-[#0B63E5] block text-sm" x-text="selectedClaim ? 'Rp ' + Number(selectedClaim.total_allowance).toLocaleString('id-ID') : ''"></span>
                </div>

                <div class="space-y-1.5">
                    <label class="block font-extrabold text-slate-800">Catatan Pencairan / Umpan Balik (Opsional)</label>
                    <textarea x-model="hrNote" rows="3" 
                              placeholder="Masukkan nomor referensi transfer atau catatan pencairan lainnya..."
                              class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl font-medium focus:ring-2 focus:ring-blue-600 focus:bg-white outline-none leading-relaxed transition"></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-100 text-xs">
                <button @click="hrModalOpen = false" 
                        class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-650 rounded-xl font-extrabold transition">
                    Batal
                </button>
                <button @click="
                            let form = document.createElement('form');
                            form.action = '{{ url('admin/allowance') }}/' + selectedClaim.id + '/process';
                            form.method = 'POST';
                            
                            let csrf = document.createElement('input');
                            csrf.type = 'hidden';
                            csrf.name = '_token';
                            csrf.value = '{{ csrf_token() }}';
                            form.appendChild(csrf);

                            let note = document.createElement('input');
                            note.type = 'hidden';
                            note.name = 'hr_note';
                            note.value = hrNote;
                            form.appendChild(note);

                            document.body.appendChild(form);
                            form.submit();
                        " 
                        class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-extrabold transition shadow-2xs">
                    Konfirmasi & Tandai Cair
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
