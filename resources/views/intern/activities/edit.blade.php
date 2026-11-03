@extends('layouts.app')

@section('page_title', 'Edit / Revisi Aktivitas Harian')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Header Card -->
    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Edit / Perbarui Aktivitas Harian</h1>
            <p class="text-xs text-slate-500">Perbarui rincian kegiatan atau perbaiki laporan yang diminta revisi oleh pembimbing.</p>
        </div>
        <a href="{{ route('intern.activities.index') }}" class="px-3.5 py-2 bg-slate-100 text-slate-600 hover:bg-slate-200 font-bold rounded-xl text-xs transition">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    @if($activity->supervisor_note)
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 text-amber-900 text-xs shadow-sm space-y-1">
        <div class="flex items-center space-x-2 font-bold text-amber-800 text-sm">
            <i class="fa-solid fa-comment-dots text-amber-600"></i>
            <span>Catatan Revisi Pembimbing:</span>
        </div>
        <p class="font-medium leading-relaxed bg-white/60 p-3 rounded-xl border border-amber-200/60">{{ $activity->supervisor_note }}</p>
    </div>
    @endif

    <!-- Activity Form -->
    <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm">
        <form action="{{ route('intern.activities.update', $activity->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6 text-xs">
            @csrf
            @method('PUT')

            <!-- Date & Work Location -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5 text-xs">Tanggal Kegiatan *</label>
                    <input type="date" name="date" value="{{ old('date', $activity->date ? $activity->date->format('Y-m-d') : '') }}" required
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-[#005B9C] focus:bg-white outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1.5 text-xs">Lokasi Kerja *</label>
                    <select name="work_location" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-[#005B9C] focus:bg-white outline-none">
                        <option value="WFO" {{ $activity->work_location === 'WFO' ? 'selected' : '' }}>WFO (Work From Office)</option>
                        <option value="WFH" {{ $activity->work_location === 'WFH' ? 'selected' : '' }}>WFH (Work From Home)</option>
                        <option value="Leave" {{ $activity->work_location === 'Leave' ? 'selected' : '' }}>Izin / Sakit</option>
                    </select>
                </div>
            </div>

            <!-- Activity Description -->
            <div>
                <label class="block font-bold text-slate-700 mb-1.5 text-xs">Deskripsi Rincian Kegiatan *</label>
                <textarea name="activity_description" rows="5" required
                    class="w-full p-4 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-[#005B9C] focus:bg-white outline-none leading-relaxed">{{ old('activity_description', $activity->activity_description) }}</textarea>
            </div>

            <!-- Notes / Learnings -->
            <div>
                <label class="block font-bold text-slate-700 mb-1.5 text-xs">Catatan / Pembelajaran (Opsional)</label>
                <textarea name="notes" rows="3"
                    class="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-[#005B9C] focus:bg-white outline-none leading-relaxed">{{ old('notes', $activity->notes) }}</textarea>
            </div>

            <!-- Attachment File -->
            <div>
                <label class="block font-bold text-slate-700 mb-1.5 text-xs">Ganti Berkas / Lampiran (Opsional)</label>
                @if($activity->attachment_path)
                <p class="text-[11px] text-blue-600 font-semibold mb-2">Lampiran saat ini tersimpan.</p>
                @endif
                <input type="file" name="attachment" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-[#005B9C] file:text-white hover:file:bg-[#002B49]">
            </div>

            <!-- Buttons: Save Draft vs Submit -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                <button type="submit" name="action" value="draft"
                    class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition">
                    <i class="fa-solid fa-floppy-disk mr-1"></i> Simpan Draft
                </button>

                <button type="submit" name="action" value="submit"
                    class="px-6 py-2.5 bg-[#005B9C] hover:bg-[#002B49] text-white font-bold rounded-xl text-xs transition shadow-lg shadow-blue-900/20 active:scale-95">
                    <i class="fa-solid fa-paper-plane mr-1"></i> Submit Ulang ke Pembimbing
                </button>
            </div>

        </form>
    </div>

</div>
@endsection