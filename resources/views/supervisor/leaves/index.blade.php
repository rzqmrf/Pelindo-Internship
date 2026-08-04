@extends('layouts.app')

@section('page_title', 'Persetujuan Izin & Sakit')

@section('content')
<div class="space-y-6" x-data="{ reviewModalOpen: false, selectedLeave: null, supervisorNote: '' }">

    <!-- Header Card -->
    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center space-x-3.5">
            <div class="w-12 h-12 rounded-full bg-blue-50 text-[#0B63E5] flex items-center justify-center text-xl shrink-0">
                <i class="fa-regular fa-calendar-check text-sm"></i>
            </div>
            <div>
                <h1 class="text-xl font-black text-slate-850 tracking-tight">Persetujuan Izin & Sakit</h1>
                <p class="text-xs font-semibold text-slate-400 mt-1">Tinjau, setujui, atau tolak permohonan izin dan sakit dari mahasiswa bimbingan Anda.</p>
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

    <!-- Leaves list -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
        <!-- Pending Leave Requests -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-xs md:col-span-2 space-y-4">
            <div class="border-b border-slate-100 pb-3.5 flex justify-between items-center">
                <div>
                    <h3 class="font-black text-slate-800 text-sm flex items-center gap-1.5">
                        <i class="fa-regular fa-clock text-amber-500"></i>
                        <span>Antrean Pengajuan Izin</span>
                    </h3>
                    <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Daftar pengajuan izin sakit yang memerlukan keputusan Anda.</p>
                </div>
                <span class="px-2.5 py-1 bg-amber-50 border border-amber-100 text-amber-600 rounded-full text-[10px] font-black">
                    {{ count($leaves) }} Tertunda
                </span>
            </div>

            <div class="space-y-4">
                @forelse($leaves as $leave)
                    <div class="p-5 bg-slate-50/50 border border-slate-200/80 rounded-2xl flex flex-col lg:flex-row gap-5 hover:bg-slate-50 transition items-start">
                        
                        <!-- Intern Bio -->
                        <div class="w-full lg:w-1/4 space-y-1.5">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider block">Peserta Magang</span>
                            <h4 class="font-extrabold text-slate-800 text-xs">{{ $leave->intern?->name }}</h4>
                            <p class="text-[10px] text-slate-500 font-semibold leading-relaxed">
                                {{ $leave->intern?->internProfile?->institution ?? '-' }}<br>
                                <span class="text-slate-400 font-medium">{{ $leave->intern?->internProfile?->major ?? '-' }}</span>
                            </p>
                        </div>

                        <!-- Leave Details -->
                        <div class="w-full lg:w-2/4 space-y-3">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 text-[9px] font-black uppercase tracking-wider rounded-md border
                                      {{ $leave->type === 'sakit' ? 'bg-red-50 text-red-650 border-red-150/50' : 'bg-blue-50 text-blue-650 border-blue-150/50' }}">
                                    {{ $leave->type }}
                                </span>
                                <span class="text-xs font-black text-slate-800">
                                    @if(\Carbon\Carbon::parse($leave->start_date)->format('Y-m-d') === \Carbon\Carbon::parse($leave->end_date)->format('Y-m-d'))
                                        {{ \Carbon\Carbon::parse($leave->start_date)->isoFormat('dddd, D MMMM YYYY') }}
                                    @else
                                        {{ \Carbon\Carbon::parse($leave->start_date)->isoFormat('D MMMM YYYY') }} s.d. {{ \Carbon\Carbon::parse($leave->end_date)->isoFormat('D MMMM YYYY') }}
                                    @endif
                                </span>
                            </div>
                            <div class="bg-white/80 border border-slate-100 rounded-xl p-3 text-[11px] leading-relaxed">
                                <span class="font-extrabold text-slate-700 block">Alasan Pengajuan:</span>
                                <p class="text-slate-500 font-medium italic mt-0.5">"{{ $leave->reason }}"</p>
                            </div>

                            @if($leave->attachment_path)
                                <a href="{{ asset('storage/' . $leave->attachment_path) }}" target="_blank"
                                   class="inline-flex items-center space-x-1.5 px-3 py-2 bg-white border border-slate-200 text-slate-600 rounded-xl text-[10px] font-bold hover:bg-slate-50 transition shadow-3xs">
                                    <i class="fa-solid fa-file-lines text-rose-500 text-xs"></i>
                                    <span>Buka Dokumen Bukti Lampiran</span>
                                </a>
                            @endif
                        </div>

                        <!-- Action Area -->
                        <div class="w-full lg:w-1/4 space-y-3.5 lg:self-center">
                            <form action="{{ route('supervisor.leaves.approve', $leave->id) }}" method="POST" id="approve-form-{{ $leave->id }}" class="hidden">
                                @csrf
                                <input type="hidden" name="supervisor_note" :value="selectedLeave && selectedLeave.id === {{ $leave->id }} ? supervisorNote : ''">
                            </form>

                            <form action="{{ route('supervisor.leaves.reject', $leave->id) }}" method="POST" id="reject-form-{{ $leave->id }}" class="hidden">
                                @csrf
                                <input type="hidden" name="supervisor_note" :value="selectedLeave && selectedLeave.id === {{ $leave->id }} ? supervisorNote : ''">
                            </form>

                            <button @click="selectedLeave = {{ json_encode($leave) }}; supervisorNote = ''; reviewModalOpen = true" 
                                    class="w-full py-2.5 bg-[#0B63E5] hover:bg-blue-700 text-white rounded-xl font-extrabold text-xs transition shadow-2xs flex items-center justify-center space-x-1.5">
                                <i class="fa-solid fa-clipboard-check text-[11px]"></i>
                                <span>Tinjau & Putuskan</span>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-slate-400 space-y-2">
                        <div class="w-12 h-12 bg-slate-100 text-slate-450 rounded-full flex items-center justify-center text-lg mx-auto">
                            <i class="fa-solid fa-circle-check text-emerald-500"></i>
                        </div>
                        <p class="text-xs font-bold">Antrean pengajuan kosong</p>
                        <p class="text-[10px] text-slate-400">Tidak ada pengajuan izin sakit baru yang tertunda.</p>
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
                <h3 class="font-black text-slate-800 text-sm">Review Pengajuan Izin</h3>
                <button @click="reviewModalOpen = false" class="text-slate-400 hover:text-slate-650 transition">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="text-xs space-y-3.5">
                <div>
                    <span class="font-bold text-slate-400 uppercase text-[9px] tracking-wider block">Peserta</span>
                    <span class="font-extrabold text-slate-800 block text-xs" x-text="selectedLeave ? selectedLeave.intern.name : ''"></span>
                </div>
                <div>
                    <span class="font-bold text-slate-400 uppercase text-[9px] tracking-wider block">Tanggal & Tipe</span>
                    <span class="font-extrabold text-slate-800 block text-xs" x-text="selectedLeave ? (selectedLeave.type.toUpperCase() + ' - ' + (selectedLeave.start_date === selectedLeave.end_date ? new Date(selectedLeave.start_date).toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) : (new Date(selectedLeave.start_date).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' }) + ' s.d. ' + new Date(selectedLeave.end_date).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' })))) : ''"></span>
                </div>

                <div class="space-y-1.5">
                    <label class="block font-extrabold text-slate-800">Catatan / Umpan Balik Pembimbing (Opsional)</label>
                    <textarea x-model="supervisorNote" rows="3" 
                              placeholder="Masukkan catatan persetujuan atau alasan penolakan di sini..."
                              class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl font-medium focus:ring-2 focus:ring-blue-600 focus:bg-white outline-none leading-relaxed transition"></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-100 text-xs">
                <button @click="reviewModalOpen = false" 
                        class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl font-extrabold transition">
                    Batal
                </button>
                <button @click="if(confirm('Tolak pengajuan izin ini?')) { document.getElementById('reject-form-' + selectedLeave.id).submit(); }" 
                        class="px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-150/50 rounded-xl font-extrabold transition">
                    Tolak Izin
                </button>
                <button @click="document.getElementById('approve-form-' + selectedLeave.id).submit();" 
                        class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-extrabold transition shadow-2xs">
                    Setujui Izin
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
