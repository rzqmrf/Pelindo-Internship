@extends('layouts.app')

@section('page_title', 'Manajemen Pengumuman HR')

@section('content')
<div class="space-y-6">

    <!-- Header Card -->
    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center space-x-3.5">
            <div class="w-12 h-12 rounded-full bg-blue-50 text-[#0B63E5] flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-bullhorn text-sm"></i>
            </div>
            <div>
                <h1 class="text-xl font-black text-slate-850 tracking-tight">Manajemen Pengumuman HR</h1>
                <p class="text-xs font-semibold text-slate-400 mt-1">Buat, kelola, dan sebarluaskan pengumuman atau papan informasi penting ke seluruh peserta magang dan pembimbing.</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left Side: Create Form (5 Cols) -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-xs lg:col-span-5 space-y-4">
            <div class="border-b border-slate-100 pb-3">
                <h3 class="font-black text-slate-800 text-sm flex items-center gap-1.5">
                    <i class="fa-solid fa-plus-circle text-blue-600"></i>
                    <span>Buat Pengumuman Baru</span>
                </h3>
                <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Isi detail untuk menyebarkan informasi baru.</p>
            </div>

            <!-- Error Messages -->
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

            <form action="{{ route('admin.announcements.store') }}" method="POST" class="space-y-4 text-xs font-semibold">
                @csrf

                <!-- Judul -->
                <div>
                    <label class="block font-extrabold text-slate-800 mb-2">Judul Pengumuman *</label>
                    <input type="text" name="title" value="{{ old('title') }}" placeholder="Contoh: Pengumuman Hari Libur Nasional" required
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl font-bold text-slate-800 focus:ring-2 focus:ring-blue-600 focus:bg-white outline-none transition">
                </div>

                <!-- Kategori -->
                <div>
                    <label class="block font-extrabold text-slate-800 mb-2">Kategori *</label>
                    <select name="category" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl font-bold text-slate-800 focus:ring-2 focus:ring-blue-600 focus:bg-white outline-none appearance-none transition">
                        <option value="info" {{ old('category') == 'info' ? 'selected' : '' }}>Informasi Umum</option>
                        <option value="important" {{ old('category') == 'important' ? 'selected' : '' }}>Penting (Urgent)</option>
                        <option value="event" {{ old('category') == 'event' ? 'selected' : '' }}>Acara / Event</option>
                    </select>
                </div>

                <!-- Isi Pengumuman -->
                <div class="space-y-1.5">
                    <label class="font-extrabold text-slate-800">Isi Pengumuman *</label>
                    <textarea name="content" rows="6" placeholder="Tuliskan isi pengumuman secara rinci di sini..." required
                              class="w-full p-4 bg-slate-50 border border-slate-200 rounded-2xl font-medium focus:ring-2 focus:ring-blue-600 focus:bg-white outline-none leading-relaxed transition">{{ old('content') }}</textarea>
                </div>

                <button type="submit" class="w-full py-3 bg-[#0B63E5] hover:bg-blue-700 text-white rounded-2xl font-extrabold transition shadow-md flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-paper-plane text-xs"></i>
                    <span>Terbitkan & Siarkan</span>
                </button>
            </form>
        </div>

        <!-- Right Side: Announcements List (7 Cols) -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-xs lg:col-span-7 space-y-4">
            <div class="border-b border-slate-100 pb-3.5 flex justify-between items-center">
                <div>
                    <h3 class="font-black text-slate-800 text-sm flex items-center gap-1.5">
                        <i class="fa-solid fa-list-ul text-blue-600"></i>
                        <span>Daftar Pengumuman Aktif</span>
                    </h3>
                    <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Seluruh informasi yang telah dipublikasikan ke portal.</p>
                </div>
            </div>

            <!-- List -->
            <div class="space-y-3.5 overflow-y-auto max-h-[580px] pr-1">
                @forelse($announcements as $ann)
                    <div class="p-4 bg-slate-50/50 border border-slate-200/80 rounded-2xl flex flex-col gap-3.5 hover:bg-slate-50 transition relative">
                        <div class="space-y-2 flex-1">
                            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200/60 pb-2">
                                <div class="flex items-center gap-2">
                                    @php
                                        $badgeStyle = match($ann->category) {
                                            'important' => 'bg-rose-50 text-rose-650 border-rose-100',
                                            'event' => 'bg-purple-50 text-purple-650 border-purple-100',
                                            default => 'bg-blue-50 text-blue-650 border-blue-100',
                                        };
                                        $categoryLabel = match($ann->category) {
                                            'important' => 'Penting',
                                            'event' => 'Event',
                                            default => 'Info',
                                        };
                                    @endphp
                                    <span class="px-2.5 py-0.5 text-[9px] font-black uppercase tracking-wider rounded-md border {{ $badgeStyle }}">
                                        {{ $categoryLabel }}
                                    </span>
                                    <span class="text-xs font-black text-slate-800">
                                        {{ $ann->title }}
                                    </span>
                                </div>
                                <span class="text-[9px] text-slate-400 font-extrabold">
                                    {{ $ann->created_at ? $ann->created_at->isoFormat('D MMMM YYYY, HH:mm') : '-' }} WIB
                                </span>
                            </div>
                            
                            <p class="text-xs font-medium text-slate-650 leading-relaxed whitespace-pre-line">{{ $ann->content }}</p>
                        </div>

                        <!-- Actions footer -->
                        <div class="flex items-center justify-between border-t border-slate-100 pt-2 text-[10px] text-slate-400">
                            <span class="font-extrabold flex items-center gap-1">
                                <i class="fa-regular fa-user text-xs"></i>
                                <span>Dibuat oleh: {{ $ann->creator?->name ?? 'HR Admin' }}</span>
                            </span>

                            <form action="{{ route('admin.announcements.destroy', $ann->id) }}" method="POST" 
                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengumuman ini? Tindakan ini tidak dapat dibatalkan.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-600 hover:text-rose-700 hover:underline font-extrabold flex items-center gap-1 bg-transparent border-none p-0 cursor-pointer transition">
                                    <i class="fa-regular fa-trash-can text-xs"></i>
                                    <span>Hapus Pengumuman</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-slate-400 space-y-2">
                        <div class="w-12 h-12 bg-slate-100 text-slate-450 rounded-full flex items-center justify-center text-lg mx-auto">
                            <i class="fa-regular fa-bell-slash"></i>
                        </div>
                        <p class="text-xs font-bold">Belum ada pengumuman</p>
                        <p class="text-[10px] text-slate-400">Gunakan form di sebelah kiri untuk menyiarkan informasi baru.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="pt-3">
                {{ $announcements->links() }}
            </div>
        </div>
    </div>

</div>
@endsection
