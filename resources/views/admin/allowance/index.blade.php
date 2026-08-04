@extends('layouts.app')

@section('page_title', 'Verifikasi & Pencairan Uang Saku HR')

@section('content')
<div class="space-y-6" x-data="{ hrModalOpen: false, selectedClaim: null, hrNote: '' }">

    <!-- Header Card -->
    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center space-x-3.5">
            <div class="w-12 h-12 rounded-full bg-blue-50 text-[#0B63E5] flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-money-check-dollar text-sm"></i>
            </div>
            <div>
                <h1 class="text-xl font-black text-slate-850 tracking-tight">Verifikasi & Pencairan Uang Saku</h1>
                <p class="text-xs font-semibold text-slate-400 mt-1">Kelola pencairan uang saku mahasiswa magang bulanan yang telah disetujui oleh pembimbing lapangan.</p>
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

    <!-- Filters Bar -->
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
                    <span>Filter</span>
                </button>
                <a href="{{ route('admin.allowance.index') }}" class="px-4 py-2.5 bg-white border border-slate-200 text-slate-650 rounded-2xl text-xs font-extrabold hover:bg-slate-50 transition flex items-center justify-center space-x-1.5 shadow-2xs">
                    <i class="fa-solid fa-rotate text-xs"></i>
                    <span>Reset</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Table/Grid List -->
    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-xs space-y-4">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-wider">
                        <th class="py-3.5 px-5">Periode</th>
                        <th class="py-3.5 px-4">Nama & Divisi</th>
                        <th class="py-3.5 px-4">Presensi</th>
                        <th class="py-3.5 px-4">Uang Saku</th>
                        <th class="py-3.5 px-4">Tujuan Rekening</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-xs font-bold text-slate-700">
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
                        <tr class="hover:bg-slate-50/40 transition">
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
                                <span class="px-2.5 py-1 text-[9px] font-black uppercase tracking-wider rounded-md border {{ $badgeStyle }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="py-4 px-5 text-right">
                                @if($claim->status === 'approved_by_supervisor')
                                    <button @click="selectedClaim = {{ json_encode($claim) }}; hrNote = ''; hrModalOpen = true" 
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
                            <td colspan="7" class="py-12 text-center text-slate-400">
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
    </div>

    <!-- HR Process Modal -->
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
