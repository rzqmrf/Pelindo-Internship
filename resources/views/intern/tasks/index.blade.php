@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900">Tugas &amp; Catatan Mentoring</h1>
        <p class="text-sm text-slate-500">Pantau target/milestone dari mentor Anda, kumpulkan hasil tugas, dan lihat catatan bimbingan.</p>
    </div>

    <div class="space-y-4">
        <h2 class="text-lg font-extrabold text-slate-900">Daftar Tugas / Milestone</h2>
        @forelse($tasks as $task)
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <h3 class="font-extrabold text-slate-900">{{ $task->title }}</h3>
                        <p class="text-xs text-slate-500">Dari: {{ $task->assignedBy?->name ?? 'Mentor' }} &bull; Tenggat: {{ $task->due_date?->format('d M Y') ?? 'Fleksibel' }}</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase {{ $task->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($task->status === 'revision' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-[#005B9C]') }}">{{ $task->status }}</span>
                </div>
                @if($task->description)
                    <p class="text-sm text-slate-600 mt-2">{{ $task->description }}</p>
                @endif
                @if($task->reviewer_note)
                    <div class="mt-3 p-3 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-800">
                        <span class="font-bold">Catatan Mentor:</span> {{ $task->reviewer_note }}
                    </div>
                @endif

                @if($task->status !== 'approved')
                    <form action="{{ route('intern.tasks.submit', $task->id) }}" method="POST" enctype="multipart/form-data" class="mt-4 pt-4 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                        @csrf
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-600 mb-1">Catatan Pengumpulan</label>
                            <input type="text" name="submission_note" value="{{ $task->submission_note }}" placeholder="Ringkasan progres atau tautan hasil..." class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1">Lampiran (Opsional)</label>
                            <input type="file" name="submission_file" class="w-full text-xs text-slate-600">
                        </div>
                        <div class="sm:col-span-3 flex gap-2">
                            <button type="submit" name="status" value="in_progress" class="px-4 py-2 bg-slate-200 text-slate-800 rounded-xl text-xs font-bold">Simpan Progres</button>
                            <button type="submit" name="status" value="submitted" class="px-4 py-2 bg-[#005B9C] text-white rounded-xl text-xs font-bold">Kumpulkan Tugas</button>
                        </div>
                    </form>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-200 p-8 text-center text-slate-500">Belum ada tugas yang diberikan mentor Anda.</div>
        @endforelse
    </div>

    <div class="space-y-4 pt-4">
        <h2 class="text-lg font-extrabold text-slate-900">Riwayat Catatan Sesi Mentoring</h2>
        @forelse($mentoringNotes as $note)
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
                <div class="flex items-center justify-between mb-1">
                    <h3 class="font-extrabold text-slate-900">{{ $note->topic }}</h3>
                    <span class="text-xs text-slate-500">{{ $note->session_date?->format('d M Y') }}</span>
                </div>
                <p class="text-xs font-semibold text-[#005B9C] mb-2">Mentor: {{ $note->mentor?->name ?? '-' }}</p>
                <p class="text-sm text-slate-700 whitespace-pre-line">{{ $note->notes }}</p>
                @if($note->action_items)
                    <p class="text-xs text-slate-600 mt-2"><span class="font-bold">Action Items:</span> {{ $note->action_items }}</p>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-200 p-8 text-center text-slate-500">Belum ada catatan sesi mentoring.</div>
        @endforelse
    </div>
</div>
@endsection
