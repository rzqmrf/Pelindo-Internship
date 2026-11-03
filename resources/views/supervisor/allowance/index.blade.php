@extends('layouts.app')

@section('page_title', 'Persetujuan Klaim Uang Saku')

@section('content')
<div class="space-y-6" x-data="{ reviewModalOpen: false, selectedClaim: null, supervisorNote: '' }">

    <!-- Header Card -->
    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center space-x-3.5">
            <div class="w-12 h-12 rounded-full bg-blue-50 text-[#0B63E5] flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-file-invoice-dollar text-sm"></i>
            </div>
            <div>
                <h1 class="text-xl font-black text-slate-850 tracking-tight">Persetujuan Klaim Uang Saku Bulanan</h1>
                <p class="text-xs font-semibold text-slate-400 mt-1">Verifikasi rekap presensi dan setujui klaim uang saku bulanan dari mahasiswa bimbingan Anda.</p>
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

    <!-- Claims List -->
    <div class="grid grid-cols-1 md:grid-span-2 gap-6">
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-xs space-y-4">
            <div class="border-b border-slate-100 pb-3.5 flex justify-between items-center">
                <div>
                    <h3 class="font-black text-slate-800 text-sm flex items-center gap-1.5">
                        <i class="fa-regular fa-clock text-amber-500"></i>
                        <span>Antrean Klaim Uang Saku</span>
                    </h3>
                    <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Daftar pengajuan klaim bulanan yang membutuhkan verifikasi Anda.</p>
                </div>
                <span class="px-2.5 py-1 bg-amber-50 border border-amber-100 text-amber-600 rounded-full text-[10px] font-black">
                    {{ count($claims) }} Tertunda
                </span>
            </div>

            <div class="space-y-4">
                @forelse($claims as $claim)
                <div class="p-5 bg-slate-50/50 border border-slate-200/80 rounded-2xl flex flex-col lg:flex-row gap-5 hover:bg-slate-50 transition items-start">

                    <!-- Intern Bio -->
                    <div class="w-full lg:w-1/4 space-y-1.5">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider block">Peserta Magang</span>
                        <h4 class="font-extrabold text-slate-800 text-xs">{{ $claim->intern?->name }}</h4>
                        <p class="text-[10px] text-slate-500 font-semibold leading-relaxed">
                            {{ $claim->intern?->internProfile?->institution ?? '-' }}<br>
                            <span class="text-slate-400 font-medium">{{ $claim->intern?->internProfile?->major ?? '-' }}</span>
                        </p>
                    </div>

                    <!-- Claim Details -->
                    <div class="w-full lg:w-2/4 space-y-3">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 text-[9px] font-black uppercase tracking-wider bg-blue-50 text-blue-650 border border-blue-150/50 rounded-md">
                                Periode {{ $claim->period_number }} ({{ \Carbon\Carbon::parse($claim->start_date)->isoFormat('D MMMM') }} - {{ \Carbon\Carbon::parse($claim->end_date)->isoFormat('D MMMM YYYY') }})
                            </span>
                            <span class="text-xs font-black text-blue-600">
                                Total Klaim: Rp {{ number_format($claim->total_allowance, 0, ',', '.') }}
                            </span>
                        </div>

                        <!-- Attendance summary grid -->
                        <div class="grid grid-cols-3 gap-2 text-center text-[10px] max-w-xs">
                            <div class="bg-white p-2 rounded-xl border border-slate-200/50">
                                <span class="font-black text-slate-800 block text-xs">{{ $claim->wfo_count }}</span>
                                <span class="text-[8px] font-bold text-slate-450 uppercase">Hadir WFO</span>
                            </div>
                            <div class="bg-white p-2 rounded-xl border border-slate-200/50">
                                <span class="font-black text-slate-800 block text-xs">{{ $claim->wfh_count }}</span>
                                <span class="text-[8px] font-bold text-slate-450 uppercase">Hadir WFH</span>
                            </div>
                            <div class="bg-white p-2 rounded-xl border border-slate-200/50">
                                <span class="font-black text-slate-800 block text-xs">{{ $claim->leave_count }}</span>
                                <span class="text-[8px] font-bold text-slate-450 uppercase">Izin/Sakit</span>
                            </div>
                        </div>

                        <!-- Bank Info -->
                        <div class="bg-white/80 border border-slate-100 rounded-xl p-2.5 text-[10px] max-w-sm flex items-center justify-between">
                            <div class="flex items-center space-x-1.5">
                                <i class="fa-solid fa-credit-card text-blue-600"></i>
                                <span class="font-extrabold text-slate-700">{{ $claim->bank_name }} - {{ $claim->bank_account_number }}</span>
                            </div>
                            <span class="text-slate-450 font-semibold italic">{{ $claim->bank_account_holder }}</span>
                        </div>

                        <!-- Attachment Info -->
                        @if($claim->attachment_path)
                        <div class="bg-white/80 border border-slate-100 rounded-xl p-2.5 text-[10px] max-w-sm flex items-center justify-between">
                            <span class="text-slate-550 font-semibold">Berkas Logbook:</span>
                            <a href="{{ asset($claim->attachment_path) }}" target="_blank"
                                class="font-black text-[#0B63E5] hover:underline flex items-center gap-1.5">
                                <i class="fa-solid fa-paperclip text-blue-600"></i>
                                <span>Lihat Berkas Logbook</span>
                            </a>
                        </div>
                        @endif
                    </div>

                    <!-- Action Area -->
                    <div class="w-full lg:w-1/4 space-y-3.5 lg:self-center">
                        <form action="{{ route('supervisor.allowance.approve', $claim->id) }}" method="POST" id="approve-form-{{ $claim->id }}" class="hidden">
                            @csrf
                            <input type="hidden" name="supervisor_note" :value="selectedClaim && selectedClaim.id === {{ $claim->id }} ? supervisorNote : ''">
                        </form>

                        <form action="{{ route('supervisor.allowance.reject', $claim->id) }}" method="POST" id="reject-form-{{ $claim->id }}" class="hidden">
                            @csrf
                            <input type="hidden" name="supervisor_note" :value="selectedClaim && selectedClaim.id === {{ $claim->id }} ? supervisorNote : ''">
                        </form>

                        <button @click="selectedClaim = {{ json_encode($claim) }}; supervisorNote = ''; reviewModalOpen = true"
                            class="w-full py-2.5 bg-[#0B63E5] hover:bg-blue-700 text-white rounded-xl font-extrabold text-xs transition shadow-2xs flex items-center justify-center space-x-1.5">
                            <i class="fa-solid fa-clipboard-check text-[11px]"></i>
                            <span>Tinjau & Setujui</span>
                        </button>
                    </div>
                </div>
                @empty
                <div class="py-12 text-center text-slate-400 space-y-2">
                    <div class="w-12 h-12 bg-slate-100 text-slate-450 rounded-full flex items-center justify-center text-lg mx-auto">
                        <i class="fa-solid fa-circle-check text-emerald-500"></i>
                    </div>
                    <p class="text-xs font-bold">Antrean klaim kosong</p>
                    <p class="text-[10px] text-slate-400">Tidak ada pengajuan klaim uang saku bulanan bimbingan yang tertunda.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Review Modal -->
    <div x-show="reviewModalOpen"
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
        x-transition.opacity
        x-cloak>
        <div class="bg-white rounded-3xl max-w-md w-full p-6 space-y-4 border border-slate-100 shadow-xl" @click.away="reviewModalOpen = false">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                <h3 class="font-black text-slate-800 text-sm">Review Klaim Uang Saku</h3>
                <button @click="reviewModalOpen = false" class="text-slate-400 hover:text-slate-650 transition">
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
                    <span class="font-bold text-slate-400 uppercase text-[9px] tracking-wider block">Total Uang Saku</span>
                    <span class="font-black text-blue-600 block text-sm" x-text="selectedClaim ? 'Rp ' + Number(selectedClaim.total_allowance).toLocaleString('id-ID') : ''"></span>
                </div>

                <div class="p-3 bg-amber-50 border border-amber-100/50 rounded-xl text-[10px] text-amber-900 leading-normal flex items-start space-x-2">
                    <i class="fa-solid fa-info-circle text-xs shrink-0 mt-0.5"></i>
                    <span><strong>PENTING:</strong> Menyetujui klaim uang saku ini akan <strong>secara otomatis menyetujui seluruh logbook harian pending/revisi</strong> milik peserta magang tersebut pada periode ini.</span>
                </div>

                <div class="space-y-1.5">
                    <label class="block font-extrabold text-slate-800">Catatan / Umpan Balik Pembimbing (Opsional)</label>
                    <textarea x-model="supervisorNote" rows="3"
                        placeholder="Masukkan catatan ulasan atau alasan penolakan di sini..."
                        class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl font-medium focus:ring-2 focus:ring-blue-600 focus:bg-white outline-none leading-relaxed transition"></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-100 text-xs">
                <button @click="reviewModalOpen = false"
                    class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-650 rounded-xl font-extrabold transition">
                    Batal
                </button>
                <button @click="if(confirm('Tolak klaim uang saku bulanan ini?')) { document.getElementById('reject-form-' + selectedClaim.id).submit(); }"
                    class="px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-150/50 rounded-xl font-extrabold transition">
                    Tolak Klaim
                </button>
                <button @click="document.getElementById('approve-form-' + selectedClaim.id).submit();"
                    class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-extrabold transition shadow-2xs">
                    Setujui & Approve Logbook
                </button>
            </div>
        </div>
    </div>

</div>
@endsection