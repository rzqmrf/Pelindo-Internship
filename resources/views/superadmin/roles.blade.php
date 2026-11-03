@extends('layouts.app')

@section('content')
@php
    $allPermKeys = array_keys($permissionCatalog);
@endphp
<div class="space-y-6"
     x-data="{
         createOpen: false,
         editOpen: false,
         slugTouched: false,
         allPermissions: @js($allPermKeys),
         createForm: {
             name: '',
             slug: '',
             description: '',
             base_portal: 'admin',
             badge_color: 'emerald',
             permissions: ['admin.dashboard']
         },
         editForm: {
             id: null,
             name: '',
             slug: '',
             description: '',
             base_portal: 'admin',
             badge_color: 'blue',
             is_system: false,
             permissions: []
         },
         syncSlug() {
             if (!this.slugTouched) {
                 this.createForm.slug = this.createForm.name
                     .toLowerCase()
                     .replace(/[^a-z0-9]+/g, '_')
                     .replace(/^_+|_+$/g, '');
             }
         },
         applyPreset(preset) {
             this.slugTouched = true;
             if (preset === 'finance') {
                 this.createForm = {
                     name: 'Staff Finance',
                     slug: 'finance',
                     description: 'Verifikasi pencairan klaim uang saku peserta magang dan ekspor CSV Payroll Bank.',
                     base_portal: 'admin',
                     badge_color: 'emerald',
                     permissions: ['admin.dashboard', 'allowance.manage', 'attendance.view']
                 };
             } else if (preset === 'auditor') {
                 this.createForm = {
                     name: 'Auditor Internal',
                     slug: 'auditor',
                     description: 'Pemantauan kepatuhan presensi harian, aktivitas logbook, dan hasil evaluasi survei magang.',
                     base_portal: 'admin',
                     badge_color: 'amber',
                     permissions: ['admin.dashboard', 'attendance.view', 'monitoring.view', 'surveys.view']
                 };
             } else if (preset === 'kadiv') {
                 this.createForm = {
                     name: 'Kepala Divisi',
                     slug: 'kepala_divisi',
                     description: 'Pemantauan kuota unit kerja, rekap logbook divisi, penilaian akhir, dan survei peserta.',
                     base_portal: 'admin',
                     badge_color: 'sky',
                     permissions: ['admin.dashboard', 'divisions.manage', 'monitoring.view', 'assessments.manage', 'surveys.view']
                 };
             }
         },
         openEdit(role) {
             this.editForm = {
                 id: role.id,
                 name: role.name,
                 slug: role.slug,
                 description: role.description || '',
                 base_portal: role.base_portal,
                 badge_color: role.badge_color,
                 is_system: !!role.is_system,
                 permissions: Array.isArray(role.permissions) ? [...role.permissions] : []
             };
             this.editOpen = true;
         }
     }"
     @keydown.escape.window="createOpen = false; editOpen = false">

    <!-- Header Halaman -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/70 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="space-y-1.5">
            <h1 class="text-2xl font-black text-[#002B49] tracking-tight">Manajemen Role &amp; Matriks Hak Akses</h1>
            <p class="text-xs sm:text-sm font-semibold text-slate-600">
                Tambahkan role organisasi baru (seperti Staff Finance, Auditor, Kepala Divisi) dan atur izin akses modul secara dinamis tanpa ubah kode.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 shrink-0">
            <a href="{{ route('superadmin.users') }}"
               class="px-4 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-extrabold text-xs transition flex items-center gap-2">
                <i class="fa-solid fa-users-gear text-[#005B9C]"></i>
                <span>Kelola Akun Pengguna</span>
            </a>
            <button type="button" @click="createOpen = true"
                    class="px-5 py-3 rounded-xl bg-[#005B9C] hover:bg-[#002B49] text-white font-extrabold text-xs transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i>
                <span>Tambah Role Baru</span>
            </button>
        </div>
    </div>

    @if ($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-900 p-5 rounded-2xl text-xs font-semibold space-y-1">
            <p class="font-extrabold">Periksa kembali konfigurasi role:</p>
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Tabel Daftar Role & Hak Akses -->
    <div class="bg-white rounded-3xl border border-slate-200/70 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-extrabold uppercase tracking-wider text-slate-500">
                        <th class="py-4 px-6">Role &amp; Kode Sistem</th>
                        <th class="py-4 px-4">Portal Dasar</th>
                        <th class="py-4 px-4">Cakupan Hak Akses Modul</th>
                        <th class="py-4 px-4 text-center">Akun</th>
                        <th class="py-4 px-6 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs align-top">
                    @foreach($roles as $role)
                        @php
                            $accountCount = (int) ($userCountsByRole[$role->slug] ?? 0);
                            $perms = is_array($role->permissions) ? $role->permissions : [];
                        @endphp
                        <tr class="hover:bg-slate-50/70">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-2.5 flex-wrap">
                                    <span class="px-2.5 py-1 rounded-lg text-[11px] font-extrabold {{ $role->badgeClasses() }}">
                                        {{ $role->name }}
                                    </span>
                                    <code class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-mono text-[11px] font-bold">{{ $role->slug }}</code>
                                    @if($role->is_system)
                                        <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-500 text-[10px] font-extrabold uppercase">Sistem</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-extrabold uppercase">Kustom</span>
                                    @endif
                                </div>
                                @if($role->description)
                                    <p class="text-slate-600 font-medium mt-1.5 max-w-md leading-relaxed">{{ $role->description }}</p>
                                @endif
                            </td>

                            <td class="py-4 px-4 whitespace-nowrap font-bold text-slate-700">
                                {{ $portalOptions[$role->base_portal] ?? $role->base_portal }}
                            </td>

                            <td class="py-4 px-4">
                                @if($role->base_portal === 'superadmin' || in_array('*', $perms, true))
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-900 text-white text-[11px] font-extrabold">
                                        <i class="fa-solid fa-key text-[10px]"></i>
                                        <span>Akses Penuh Seluruh Sistem &amp; Infrastruktur</span>
                                    </span>
                                @elseif($role->base_portal === 'supervisor')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-sky-50 text-sky-800 border border-sky-200 text-[11px] font-bold">
                                        Modul Pembimbing (Validasi Logbook, Izin, Klaim, &amp; Penilaian)
                                    </span>
                                @elseif($role->base_portal === 'intern')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-[11px] font-bold">
                                        Modul Peserta Magang (Presensi, Logbook, Klaim, &amp; Sertifikat)
                                    </span>
                                @else
                                    <div class="flex flex-wrap gap-1.5 max-w-xl">
                                        @forelse($perms as $permKey)
                                            @if(isset($permissionCatalog[$permKey]))
                                                <span class="px-2 py-0.5 rounded-md bg-blue-50 text-[#005B9C] border border-blue-100 text-[11px] font-bold">
                                                    {{ $permissionCatalog[$permKey]['label'] }}
                                                </span>
                                            @endif
                                        @empty
                                            <span class="text-slate-400 font-semibold">Belum ada modul dipilih</span>
                                        @endforelse
                                    </div>
                                @endif
                            </td>

                            <td class="py-4 px-4 text-center whitespace-nowrap">
                                <a href="{{ route('superadmin.users', ['role' => $role->slug]) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1 rounded-xl bg-slate-100 hover:bg-blue-50 text-slate-800 hover:text-[#005B9C] font-extrabold text-xs transition"
                                   title="Lihat daftar pengguna dengan role {{ $role->name }}">
                                    <span>{{ $accountCount }}</span>
                                    <span class="text-[10px] font-semibold text-slate-500">akun</span>
                                </a>
                            </td>

                            <td class="py-4 px-6 whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button"
                                            @click="openEdit(@js([
                                                'id' => $role->id,
                                                'name' => $role->name,
                                                'slug' => $role->slug,
                                                'description' => $role->description,
                                                'base_portal' => $role->base_portal,
                                                'badge_color' => $role->badge_color,
                                                'is_system' => $role->is_system,
                                                'permissions' => $perms,
                                            ]))"
                                            class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-extrabold text-[11px] transition flex items-center gap-1.5">
                                        <i class="fa-solid fa-sliders"></i>
                                        <span>Atur Akses</span>
                                    </button>

                                    @if(!$role->is_system)
                                        <form action="{{ route('superadmin.roles.delete', $role->id) }}" method="POST"
                                              onsubmit="return confirm('Hapus role {{ addslashes($role->name) }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="p-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 transition"
                                                    title="Hapus Role Kustom">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah Role Baru -->
    <div x-show="createOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 overflow-y-auto">
        <div @click.outside="createOpen = false" class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 border border-slate-200 shadow-xl space-y-5 my-8 max-h-[90vh] overflow-y-auto">
            <div class="flex items-start justify-between border-b border-slate-100 pb-4">
                <div>
                    <h3 class="text-lg font-black text-[#002B49]">Tambah Role Pengguna Baru</h3>
                    <p class="text-xs font-semibold text-slate-500 mt-0.5">Buat role baru beserta daftar izin modul yang dapat diakses.</p>
                </div>
                <button type="button" @click="createOpen = false" class="text-slate-400 hover:text-slate-700 p-1.5" aria-label="Tutup dialog">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Preset Template Cepat -->
            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 flex flex-wrap items-center gap-2">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mr-1">Template Cepat:</span>
                <button type="button" @click="applyPreset('finance')" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 hover:border-[#005B9C] text-xs font-bold text-slate-700 transition">
                    Staff Finance
                </button>
                <button type="button" @click="applyPreset('auditor')" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 hover:border-[#005B9C] text-xs font-bold text-slate-700 transition">
                    Auditor Internal
                </button>
                <button type="button" @click="applyPreset('kadiv')" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 hover:border-[#005B9C] text-xs font-bold text-slate-700 transition">
                    Kepala Divisi
                </button>
            </div>

            <form action="{{ route('superadmin.roles.store') }}" method="POST" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Tampilan Role</label>
                        <input type="text" name="name" x-model="createForm.name" @input="syncSlug()" required
                               placeholder="Contoh: Staff Finance"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-none focus:border-[#005B9C]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kode Unik Sistem (Slug)</label>
                        <input type="text" name="slug" x-model="createForm.slug" @input="slugTouched = true" required
                               placeholder="Contoh: finance"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 font-mono text-xs font-bold text-slate-800 focus:outline-none focus:border-[#005B9C]">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Portal Dasar</label>
                        <select name="base_portal" x-model="createForm.base_portal"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-800 focus:outline-none focus:border-[#005B9C]">
                            @foreach($portalOptions as $pKey => $pLabel)
                                <option value="{{ $pKey }}">{{ $pLabel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Warna Badge Identitas</label>
                        <select name="badge_color" x-model="createForm.badge_color"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-800 focus:outline-none focus:border-[#005B9C]">
                            @foreach($badgeOptions as $bKey => $bLabel)
                                <option value="{{ $bKey }}">{{ $bLabel }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi Tugas / Wewenang</label>
                    <input type="text" name="description" x-model="createForm.description"
                           placeholder="Penjelasan singkat fungsi role ini di organisasi"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-none focus:border-[#005B9C]">
                </div>

                <!-- Matriks Permission Modul (Tampil untuk Portal Manajemen / Admin) -->
                <div x-show="createForm.base_portal === 'admin'" class="space-y-3 pt-2">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-extrabold text-[#002B49] uppercase tracking-wider">Pilih Hak Akses Modul</label>
                        <div class="flex items-center gap-2 text-[11px] font-bold">
                            <button type="button" @click="createForm.permissions = [...allPermissions]" class="text-[#005B9C] hover:underline">Pilih Semua</button>
                            <span class="text-slate-300">|</span>
                            <button type="button" @click="createForm.permissions = []" class="text-slate-500 hover:underline">Kosongkan</button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        @foreach($permissionCatalog as $permKey => $meta)
                            <label class="flex items-start gap-2.5 p-3 rounded-2xl border border-slate-200 hover:border-[#005B9C] bg-slate-50/60 cursor-pointer transition">
                                <input type="checkbox" name="permissions[]" value="{{ $permKey }}" x-model="createForm.permissions"
                                       class="mt-0.5 rounded border-slate-300 text-[#005B9C] focus:ring-[#005B9C] w-4 h-4">
                                <span class="text-xs leading-snug">
                                    <strong class="font-extrabold text-slate-900 block">{{ $meta['label'] }}</strong>
                                    <span class="text-[11px] font-medium text-slate-500">{{ $meta['description'] }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div x-show="createForm.base_portal !== 'admin'" class="p-3.5 rounded-2xl bg-blue-50 border border-blue-200 text-xs font-semibold text-slate-800">
                    Role dengan portal dasar <strong x-text="createForm.base_portal"></strong> otomatis mewarisi alur kerja dan menu standar portal tersebut.
                </div>

                <div class="pt-3 flex justify-end gap-2.5 border-t border-slate-100">
                    <button type="button" @click="createOpen = false" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#005B9C] hover:bg-[#002B49] text-white font-extrabold text-xs">
                        Simpan Role Baru
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Role & Matriks Hak Akses -->
    <div x-show="editOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 overflow-y-auto">
        <div @click.outside="editOpen = false" class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 border border-slate-200 shadow-xl space-y-5 my-8 max-h-[90vh] overflow-y-auto">
            <div class="flex items-start justify-between border-b border-slate-100 pb-4">
                <div>
                    <h3 class="text-lg font-black text-[#002B49]">Atur Konfigurasi &amp; Hak Akses Role</h3>
                    <p class="text-xs font-semibold text-slate-500 mt-0.5">
                        Kode sistem: <code class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-800 font-bold" x-text="editForm.slug"></code>
                    </p>
                </div>
                <button type="button" @click="editOpen = false" class="text-slate-400 hover:text-slate-700 p-1.5" aria-label="Tutup dialog">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form :action="'{{ url('/superadmin/roles') }}/' + editForm.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Tampilan Role</label>
                        <input type="text" name="name" x-model="editForm.name" required
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-none focus:border-[#005B9C]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Warna Badge Identitas</label>
                        <select name="badge_color" x-model="editForm.badge_color"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-800 focus:outline-none focus:border-[#005B9C]">
                            @foreach($badgeOptions as $bKey => $bLabel)
                                <option value="{{ $bKey }}">{{ $bLabel }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div x-show="!editForm.is_system">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Portal Dasar</label>
                    <select name="base_portal" x-model="editForm.base_portal"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-800 focus:outline-none focus:border-[#005B9C]">
                        @foreach($portalOptions as $pKey => $pLabel)
                            <option value="{{ $pKey }}">{{ $pLabel }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi Tugas / Wewenang</label>
                    <input type="text" name="description" x-model="editForm.description"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-none focus:border-[#005B9C]">
                </div>

                <div x-show="editForm.base_portal === 'admin' && editForm.slug !== 'superadmin'" class="space-y-3 pt-2">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-extrabold text-[#002B49] uppercase tracking-wider">Matriks Hak Akses Modul</label>
                        <div class="flex items-center gap-2 text-[11px] font-bold">
                            <button type="button" @click="editForm.permissions = [...allPermissions]" class="text-[#005B9C] hover:underline">Pilih Semua</button>
                            <span class="text-slate-300">|</span>
                            <button type="button" @click="editForm.permissions = []" class="text-slate-500 hover:underline">Kosongkan</button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        @foreach($permissionCatalog as $permKey => $meta)
                            <label class="flex items-start gap-2.5 p-3 rounded-2xl border border-slate-200 hover:border-[#005B9C] bg-slate-50/60 cursor-pointer transition">
                                <input type="checkbox" name="permissions[]" value="{{ $permKey }}" x-model="editForm.permissions"
                                       class="mt-0.5 rounded border-slate-300 text-[#005B9C] focus:ring-[#005B9C] w-4 h-4">
                                <span class="text-xs leading-snug">
                                    <strong class="font-extrabold text-slate-900 block">{{ $meta['label'] }}</strong>
                                    <span class="text-[11px] font-medium text-slate-500">{{ $meta['description'] }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div x-show="editForm.slug === 'superadmin'" class="p-3.5 rounded-2xl bg-slate-100 border border-slate-200 text-xs font-semibold text-slate-700">
                    Role <strong>Superadmin IT</strong> selalu memegang hak akses penuh ke seluruh kontrol sistem agar pengembang tidak terkunci dari portal.
                </div>

                <div class="pt-3 flex justify-end gap-2.5 border-t border-slate-100">
                    <button type="button" @click="editOpen = false" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#005B9C] hover:bg-[#002B49] text-white font-extrabold text-xs">
                        Simpan Perubahan Role
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
