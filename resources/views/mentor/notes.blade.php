@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900">Catatan Sesi Mentoring</h1>
        <p class="text-sm text-slate-500">Dokumentasikan sesi 1-on-1, umpan balik berkala, dan rencana tindak lanjut peserta magang.</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
        <h2 class="text-base font-extrabold text-slate-900 mb-4">Catat Sesi Mentoring Baru</h2>
        <form action="{{ route('mentor.notes.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">Peserta Magang</label>
                <select name="intern_id" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                    <option value="">-- Pilih Peserta --</option>
                    @foreach($myInterns as $intern)
                        <option value="{{ $intern->id }}">{{ $intern->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">Tanggal Sesi</label>
                <input type="date" name="session_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-600 mb-1">Topik Pembahasan</label>
                <input type="text" name="topic" required placeholder="Contoh: Evaluasi Sprint Minggu ke-2" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-600 mb-1">Catatan &amp; Umpan Balik</label>
                <textarea name="notes" rows="3" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm"></textarea>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-600 mb-1">Action Items / Tindak Lanjut (Opsional)</label>
                <textarea name="action_items" rows="2" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm"></textarea>
            </div>
            <div class="md:col-span-2">
                <button type="submit" class="px-5 py-2.5 bg-[#005B9C] text-white rounded-xl text-sm font-bold">Simpan Catatan</button>
            </div>
        </form>
    </div>

    <div class="space-y-4">
        @forelse($notes as $note)
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-extrabold text-slate-900">{{ $note->topic }}</h3>
                    <span class="text-xs font-semibold text-slate-500">{{ $note->session_date?->format('d M Y') }}</span>
                </div>
                <p class="text-xs font-bold text-[#005B9C] mb-2">Peserta: {{ $note->intern?->name ?? '-' }}</p>
                <p class="text-sm text-slate-700 whitespace-pre-line">{{ $note->notes }}</p>
                @if($note->action_items)
                    <div class="mt-3 p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-700">
                        <span class="font-bold">Tindak Lanjut:</span> {{ $note->action_items }}
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-200 p-8 text-center text-slate-500">Belum ada catatan sesi mentoring.</div>
        @endforelse
        <div>{{ $notes->links() }}</div>
    </div>
</div>
@endsection
