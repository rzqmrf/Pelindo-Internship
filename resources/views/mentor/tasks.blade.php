@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900">Tugas &amp; Milestone Magang</h1>
        <p class="text-sm text-slate-500">Berikan target kerja/proyek kepada peserta bimbingan dan tinjau hasil pengumpulannya.</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
        <h2 class="text-base font-extrabold text-slate-900 mb-4">Tambah Tugas / Milestone Baru</h2>
        <form action="{{ route('mentor.tasks.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                <label class="block text-xs font-bold text-slate-600 mb-1">Tenggat Waktu (Opsional)</label>
                <input type="date" name="due_date" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-600 mb-1">Judul Tugas / Milestone</label>
                <input type="text" name="title" required placeholder="Contoh: Membuat Modul API Integrasi" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-600 mb-1">Deskripsi &amp; Kriteria Selesai</label>
                <textarea name="description" rows="3" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm" placeholder="Jelaskan detail tugas..."></textarea>
            </div>
            <div class="md:col-span-2">
                <button type="submit" class="px-5 py-2.5 bg-[#005B9C] text-white rounded-xl text-sm font-bold">Simpan Tugas</button>
            </div>
        </form>
    </div>

    <div class="space-y-4">
        @forelse($tasks as $task)
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <h3 class="font-extrabold text-slate-900">{{ $task->title }}</h3>
                        <p class="text-xs text-slate-500">Peserta: {{ $task->intern?->name ?? '-' }} &bull; Tenggat: {{ $task->due_date?->format('d M Y') ?? 'Fleksibel' }}</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase bg-slate-100 text-slate-700">{{ $task->status }}</span>
                </div>
                @if($task->description)
                    <p class="text-sm text-slate-600 mt-2">{{ $task->description }}</p>
                @endif
                @if($task->submission_note || $task->submission_file)
                    <div class="mt-3 p-3 rounded-xl bg-blue-50 border border-blue-100 text-xs text-slate-700">
                        <p class="font-bold text-[#005B9C]">Pengumpulan Peserta:</p>
                        @if($task->submission_note)<p class="mt-1">{{ $task->submission_note }}</p>@endif
                        @if($task->submission_file)
                            <a href="{{ asset('storage/' . $task->submission_file) }}" target="_blank" class="inline-block mt-1 font-bold text-[#005B9C] underline">Lihat Lampiran</a>
                        @endif
                    </div>
                @endif
                @if(in_array($task->status, ['submitted', 'in_progress']))
                    <form action="{{ route('mentor.tasks.review', $task->id) }}" method="POST" class="mt-4 flex flex-col sm:flex-row gap-2 pt-3 border-t border-slate-100">
                        @csrf
                        <input type="text" name="reviewer_note" placeholder="Catatan review mentor..." class="flex-1 px-3 py-2 rounded-xl border border-slate-300 text-sm">
                        <button type="submit" name="status" value="approved" class="px-4 py-2 bg-emerald-600 text-white rounded-xl text-xs font-bold">Approve Selesai</button>
                        <button type="submit" name="status" value="revision" class="px-4 py-2 bg-amber-500 text-white rounded-xl text-xs font-bold">Minta Revisi</button>
                    </form>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-200 p-8 text-center text-slate-500">Belum ada tugas yang dibuat.</div>
        @endforelse
        <div>{{ $tasks->links() }}</div>
    </div>
</div>
@endsection
