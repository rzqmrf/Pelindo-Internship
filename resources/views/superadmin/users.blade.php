@extends('layouts.app')

@section('content')
<div class="space-y-6"
     x-data="{
         createOpen: false,
         editOpen: false,
         createRole: 'admin',
         rolesPortalMap: @js($rolesPortalMap ?? []),
         editUser: { id: null, name: '', email: '', role: 'admin', phone: '' },
         needsDivision(roleSlug) {
             const portal = this.rolesPortalMap[roleSlug] || roleSlug;
             return portal === 'supervisor' || portal === 'intern';
         },
         openEdit(u) {
             this.editUser = { id: u.id, name: u.name, email: u.email, role: u.role, phone: u.phone || '' };
             this.editOpen = true;
         }
     }"
     @keydown.escape.window="createOpen = false; editOpen = false">

    <!-- Header & Tombol Tambah Akun -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/70 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="space-y-1">
            <h1 class="text-2xl font-black text-[#002B49] tracking-tight">Manajemen Akun &amp; Impersonasi</h1>
            <p class="text-xs sm:text-sm font-semibold text-slate-600">
                Kelola akun lintas role (termasuk role kustom baru) atau masuk sementara sebagai pengguna tertentu untuk penelusuran kendala.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 shrink-0">
            <a href="{{ route('superadmin.roles') }}"
               class="px-4 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-extrabold text-xs transition flex items-center gap-2">
                <i class="fa-solid fa-user-lock text-[#005B9C]"></i>
                <span>Kelola Role &amp; Akses</span>
            </a>
            <button type="button" @click="createOpen = true"
                    class="px-5 py-3 rounded-xl bg-[#005B9C] hover:bg-[#002B49] text-white font-extrabold text-xs transition flex items-center gap-2">
                <i class="fa-solid fa-user-plus"></i>
                <span>Buat Akun Sistem</span>
            </button>
        </div>
    </div>

    @if ($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-900 p-4 rounded-2xl text-xs font-semibold">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Filter Role & Pencarian -->
    <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/70 shadow-xs space-y-4">
        <div class="flex flex-wrap gap-2">
            @php
                $currentRole = request('role', '');
                $tabs = $roleTabs ?? [
                    '' => ['label' => 'Semua Akun', 'count' => $counts['all']],
                    'superadmin' => ['label' => 'Superadmin IT', 'count' => $counts['superadmin']],
                    'admin' => ['label' => 'Admin HR', 'count' => $counts['admin']],
                    'supervisor' => ['label' => 'Pembimbing', 'count' => $counts['supervisor']],
                    'intern' => ['label' => 'Peserta Magang', 'count' => $counts['intern']],
                ];
            @endphp
            @foreach($tabs as $roleKey => $tab)
                <a href="{{ route('superadmin.users', array_filter(['role' => $roleKey, 'search' => request('search'), 'status' => request('status')])) }}"
                   class="px-3.5 py-2 rounded-xl text-xs font-extrabold transition flex items-center gap-2 {{ $currentRole === $roleKey ? 'bg-[#002B49] text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    <span>{{ $tab['label'] }}</span>
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $currentRole === $roleKey ? 'bg-white/20 text-white' : 'bg-white text-slate-700' }}">{{ $tab['count'] }}</span>
                </a>
            @endforeach
        </div>

        <form action="{{ route('superadmin.users') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
            @if(request('role'))
                <input type="hidden" name="role" value="{{ request('role') }}">
            @endif
            <div class="sm:col-span-7 relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3.5 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nama, alamat email, atau nomor telepon..."
                       class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#005B9C]">
            </div>
            <div class="sm:col-span-3">
                <select name="status" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-700 focus:outline-none focus:border-[#005B9C]">
                    <option value="">Semua Status Akun</option>
                    <option value="active" @selected(request('status') === 'active')>Aktif</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
                </select>
            </div>
            <div class="sm:col-span-2 flex gap-2">
                <button type="submit" class="flex-1 py-2.5 px-4 rounded-xl bg-[#005B9C] hover:bg-[#002B49] text-white font-extrabold text-xs transition">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'role', 'status']))
                    <a href="{{ route('superadmin.users') }}" class="py-2.5 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center" title="Reset Filter">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Daftar Pengguna -->
    <div class="bg-white rounded-3xl border border-slate-200/70 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-extrabold uppercase tracking-wider text-slate-500">
                        <th class="py-4 px-6">Pengguna</th>
                        <th class="py-4 px-4">Role &amp; Unit</th>
                        <th class="py-4 px-4">Status</th>
                        <th class="py-4 px-6 text-right">Tindakan &amp; Impersonasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($users as $u)
                        @php
                            $roleBadge = $u->roleBadgeClass();
                            $divisionName = $u->internProfile?->division?->name ?? $u->supervisorProfile?->division?->name;
                        @endphp
                        <tr class="hover:bg-slate-50/70">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-[#002B49] text-white font-extrabold flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-extrabold text-slate-900 flex items-center gap-1.5">
                                            <span>{{ $u->name }}</span>
                                            @if($u->id === Auth::id())
                                                <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[10px] font-extrabold">Anda</span>
                                            @endif
                                        </p>
                                        <p class="text-slate-500 font-medium mt-0.5">{{ $u->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-lg text-[11px] font-extrabold {{ $roleBadge }}">
                                    {{ $u->roleLabel() }}
                                </span>
                                @if($divisionName)
                                    <p class="text-[11px] font-semibold text-slate-500 mt-1">{{ $divisionName }}</p>
                                @endif
                            </td>
                            <td class="py-4 px-4 whitespace-nowrap">
                                @if($u->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 text-[11px] font-extrabold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-rose-50 text-rose-800 border border-rose-200 text-[11px] font-extrabold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center justify-end gap-2">
                                    @if($u->id !== Auth::id() && $u->is_active)
                                        <form action="{{ route('superadmin.users.impersonate', $u->id) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                    class="px-3 py-2 rounded-xl bg-[#005B9C] hover:bg-[#002B49] text-white font-extrabold text-[11px] transition flex items-center gap-1.5 whitespace-nowrap"
                                                    title="Masuk sementara sebagai {{ $u->name }}">
                                                <i class="fa-solid fa-right-to-bracket"></i>
                                                <span>Masuk Sebagai</span>
                                            </button>
                                        </form>
                                    @endif

                                    <button type="button"
                                            @click="openEdit(@js(['id' => $u->id, 'name' => $u->name, 'email' => $u->email, 'role' => $u->role, 'phone' => $u->phone]))"
                                            class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition"
                                            title="Ubah Role / Reset Sandi">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    @if($u->id !== Auth::id())
                                        <form action="{{ route('superadmin.users.toggle_status', $u->id) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                    class="p-2 rounded-xl {{ $u->is_active ? 'bg-amber-50 hover:bg-amber-100 text-amber-800' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-800' }} transition"
                                                    title="{{ $u->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}">
                                                <i class="fa-solid {{ $u->is_active ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                                            </button>
                                        </form>

                                        <form action="{{ route('superadmin.users.delete', $u->id) }}" method="POST"
                                              onsubmit="return confirm('Hapus permanen akun {{ addslashes($u->name) }}? Tindakan ini tidak dapat dibatalkan.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 transition" title="Hapus Akun">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 px-6 text-center space-y-2">
                                <p class="text-sm font-extrabold text-slate-700">Tidak ada akun yang cocok dengan kriteria pencarian.</p>
                                <p class="text-xs font-medium text-slate-500">Ubah kata kunci pencarian atau tekan tombol Reset Filter untuk menampilkan seluruh pengguna.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Buat Akun Baru -->
    <div x-show="createOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50">
        <div @click.outside="createOpen = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 border border-slate-200 shadow-xl space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <h3 class="text-lg font-black text-[#002B49]">Buat Akun Pengguna Baru</h3>
                    <p class="text-xs font-semibold text-slate-500">Tambahkan akun Superadmin IT, Admin HR, Pembimbing, atau Peserta.</p>
                </div>
                <button type="button" @click="createOpen = false" class="text-slate-400 hover:text-slate-700 p-1.5" aria-label="Tutup dialog">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form action="{{ route('superadmin.users.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" required placeholder="Nama lengkap pengguna"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-none focus:border-[#005B9C]">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Alamat Email</label>
                        <input type="email" name="email" required placeholder="nama@pelindo.co.id"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-none focus:border-[#005B9C]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Telepon</label>
                        <input type="text" name="phone" placeholder="0812xxxxxxxx"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-none focus:border-[#005B9C]">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Role Hak Akses</label>
                        <select name="role" x-model="createRole" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold focus:outline-none focus:border-[#005B9C]">
                            @foreach($roles as $roleOption)
                                <option value="{{ $roleOption->slug }}">{{ $roleOption->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kata Sandi Awal</label>
                        <input type="password" name="password" required minlength="6" placeholder="Minimal 6 karakter"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-none focus:border-[#005B9C]">
                    </div>
                </div>

                <div x-show="needsDivision(createRole)">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Divisi Penempatan Awal</label>
                    <select name="division_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-semibold focus:outline-none focus:border-[#005B9C]">
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}">{{ $div->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="pt-3 flex justify-end gap-2.5">
                    <button type="button" @click="createOpen = false" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#005B9C] hover:bg-[#002B49] text-white font-extrabold text-xs">
                        Simpan Akun Baru
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Akun / Reset Password -->
    <div x-show="editOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50">
        <div @click.outside="editOpen = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 border border-slate-200 shadow-xl space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <h3 class="text-lg font-black text-[#002B49]">Perbarui Akun &amp; Hak Akses</h3>
                    <p class="text-xs font-semibold text-slate-500">Ubah profil dasar, level role, atau atur ulang kata sandi pengguna.</p>
                </div>
                <button type="button" @click="editOpen = false" class="text-slate-400 hover:text-slate-700 p-1.5" aria-label="Tutup dialog">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form :action="'{{ url('/superadmin/users') }}/' + editUser.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" x-model="editUser.name" required
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-none focus:border-[#005B9C]">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Alamat Email</label>
                        <input type="email" name="email" x-model="editUser.email" required
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-none focus:border-[#005B9C]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Telepon</label>
                        <input type="text" name="phone" x-model="editUser.phone"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-none focus:border-[#005B9C]">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Role Hak Akses</label>
                        <select name="role" x-model="editUser.role" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold focus:outline-none focus:border-[#005B9C]">
                            @foreach($roles as $roleOption)
                                <option value="{{ $roleOption->slug }}">{{ $roleOption->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Reset Kata Sandi (Opsional)</label>
                        <input type="password" name="password" minlength="6" placeholder="Kosongkan jika tetap"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-none focus:border-[#005B9C]">
                    </div>
                </div>

                <div class="pt-3 flex justify-end gap-2.5">
                    <button type="button" @click="editOpen = false" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#005B9C] hover:bg-[#002B49] text-white font-extrabold text-xs">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
