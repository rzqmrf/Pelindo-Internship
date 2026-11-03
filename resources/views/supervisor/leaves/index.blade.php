@extends('layouts.app')

@section('page_title', 'Persetujuan Izin & Sakit')

@section('content')
<div class="space-y-6" x-data="{ reviewModalOpen: false, selectedLeave: null, supervisorNote: '' }">

    <!-- Centralized Header -->
    <x-page-header
        title="Persetujuan Izin & Sakit"
        subtitle="Tinjau, setujui, atau tolak permohonan izin dan sakit dari mahasiswa bimbingan Anda."
        icon="fa-regular fa-calendar-check" />

    <!-- Alert Messages -->
    @if(session('success'))
    <x-alert type="success" title="Berhasil!">
        {{ session('success') }}
    </x-alert>
    @endif

    <!-- Leaves list -->
    <x-card>
        <x-slot name="title">Antrean Pengajuan Izin</x-slot>
        <x-slot name="subtitle">Daftar pengajuan izin sakit yang memerlukan keputusan Anda.</x-slot>
        <x-slot name="icon">fa-regular fa-clock text-amber-500</x-slot>
        <x-slot name="headerAction">
            <span class="px-3 py-1 bg-amber-50 border border-amber-200 text-amber-700 rounded-full text-xs font-extrabold">
                {{ count($leaves) }} Tertunda
            </span>
        </x-slot>

        <div class="space-y-4">
            @forelse($leaves as $leave)
            <div class="p-6 bg-slate-50/70 border border-slate-200/80 rounded-2xl flex flex-col lg:flex-row gap-6 hover:bg-slate-50 transition items-start">

                <!-- Intern Bio -->
                <div class="w-full lg:w-1/4 space-y-1.5">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Peserta Magang</span>
                    <h4 class="font-extrabold text-slate-900 text-sm leading-snug">{{ $leave->intern?->name }}</h4>
                    <p class="text-xs text-slate-600 font-medium leading-relaxed mt-1">
                        {{ $leave->intern?->internProfile?->institution ?? '-' }}<br>
                        <span class="text-slate-400 font-normal">{{ $leave->intern?->internProfile?->major ?? '-' }}</span>
                    </p>
                </div>

                <!-- Leave Details -->
                <div class="w-full lg:w-2/4 space-y-3">
                    <div class="flex items-center gap-3">
                        <x-badge :status="$leave->type" />
                        <span class="text-sm font-bold text-slate-800">
                            @if(\Carbon\Carbon::parse($leave->start_date)->format('Y-m-d') === \Carbon\Carbon::parse($leave->end_date)->format('Y-m-d'))
                            {{ \Carbon\Carbon::parse($leave->start_date)->isoFormat('dddd, D MMMM YYYY') }}
                            @else
                            {{ \Carbon\Carbon::parse($leave->start_date)->isoFormat('D MMMM YYYY') }} s.d. {{ \Carbon\Carbon::parse($leave->end_date)->isoFormat('D MMMM YYYY') }}
                            @endif
                        </span>
                    </div>
                    <div class="bg-white border border-slate-100 rounded-xl p-4 text-xs leading-relaxed shadow-2xs">
                        <span class="font-bold text-slate-700 block mb-1">Alasan Pengajuan:</span>
                        <p class="text-slate-600 font-medium italic">"{{ $leave->reason }}"</p>
                    </div>

                    @if($leave->attachment_path)
                    <a href="{{ asset('storage/' . $leave->attachment_path) }}" target="_blank"
                        class="inline-flex items-center space-x-2 px-3.5 py-2 bg-white border border-slate-200 text-slate-700 rounded-xl text-xs font-bold hover:bg-slate-50 transition shadow-2xs">
                        <i class="fa-solid fa-file-lines text-rose-500"></i>
                        <span>Buka Dokumen Bukti Lampiran</span>
                    </a>
                    @endif
                </div>

                <!-- Action Area -->
                <div class="w-full lg:w-1/4 space-y-3 lg:self-center">
                    <form action="{{ route('supervisor.leaves.approve', $leave->id) }}" method="POST" id="approve-form-{{ $leave->id }}" class="hidden">
                        @csrf
                        <input type="hidden" name="supervisor_note" :value="selectedLeave && selectedLeave.id === {{ $leave->id }} ? supervisorNote : ''">
                    </form>
                    <form action="{{ route('supervisor.leaves.reject', $leave->id) }}" method="POST" id="reject-form-{{ $leave->id }}" class="hidden">
                        @csrf
                        <input type="hidden" name="supervisor_note" :value="selectedLeave && selectedLeave.id === {{ $leave->id }} ? supervisorNote : ''">
                    </form>

                    <x-button
                        type="button"
                        @click="selectedLeave = {{ json_encode($leave) }}; supervisorNote = ''; reviewModalOpen = true"
                        variant="primary"
                        class="w-full justify-center"
                        icon="fa-solid fa-clipboard-check">
                        Tinjau & Putuskan
                    </x-button>
                </div>
            </div>
            @empty
            <x-empty-state
                title="Antrean pengajuan kosong"
                subtitle="Tidak ada pengajuan izin sakit baru yang memerlukan persetujuan."
                icon="fa-solid fa-circle-check text-emerald-500" />
            @endforelse
        </div>
    </x-card>

    <!-- Review Modal -->
    <div x-show="reviewModalOpen"
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
        x-transition.opacity
        x-cloak>
        <div class="bg-white rounded-3xl max-w-md w-full p-6 space-y-5 border border-slate-100 shadow-2xl" @click.away="reviewModalOpen = false">
            <div class="flex justify-between items-center border-b border-slate-100 pb-4">
                <h3 class="font-extrabold text-slate-900 text-base">Review Pengajuan Izin</h3>
                <button @click="reviewModalOpen = false" class="text-slate-400 hover:text-slate-600 transition">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <div class="text-sm space-y-4">
                <div>
                    <span class="font-bold text-slate-400 uppercase text-xs tracking-wider block mb-1">Peserta</span>
                    <span class="font-extrabold text-slate-900 block text-sm" x-text="selectedLeave ? selectedLeave.intern.name : ''"></span>
                </div>
                <div>
                    <span class="font-bold text-slate-400 uppercase text-xs tracking-wider block mb-1">Tanggal & Tipe</span>
                    <span class="font-extrabold text-slate-800 block text-sm" x-text="selectedLeave ? (selectedLeave.type.toUpperCase() + ' - ' + (selectedLeave.start_date === selectedLeave.end_date ? new Date(selectedLeave.start_date).toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) : (new Date(selectedLeave.start_date).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' }) + ' s.d. ' + new Date(selectedLeave.end_date).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' })))) : ''"></span>
                </div>

                <div class="space-y-1.5">
                    <label class="block font-bold text-slate-800 text-sm">Catatan / Umpan Balik Pembimbing (Opsional)</label>
                    <textarea x-model="supervisorNote" rows="3"
                        placeholder="Masukkan catatan persetujuan atau alasan penolakan di sini..."
                        class="w-full p-3.5 text-sm bg-slate-50 border border-slate-300 rounded-xl font-medium focus:ring-2 focus:ring-[#005B9C]/20 focus:border-[#005B9C] focus:bg-white outline-none leading-relaxed transition"></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                <x-button type="button" @click="reviewModalOpen = false" variant="ghost" size="sm">
                    Batal
                </x-button>
                <x-button type="button" @click="if(confirm('Tolak pengajuan izin ini?')) { document.getElementById('reject-form-' + selectedLeave.id).submit(); }" variant="danger" size="sm">
                    Tolak Izin
                </x-button>
                <x-button type="button" @click="document.getElementById('approve-form-' + selectedLeave.id).submit();" variant="primary" size="sm">
                    Setujui Izin
                </x-button>
            </div>
        </div>
    </div>

</div>
@endsection