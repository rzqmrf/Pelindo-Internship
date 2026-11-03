# Coding Rules & Development Guidelines

## 1. Clean Corporate UI Guidelines
- **Warna & Palet**:
  - Warna Utama: Biru Pelindo (`#005B9C`) dan Biru Gelap (`#002B49`).
  - Warna Latar: Abu-abu Terang (`#F8FAFC`).
  - Warna Kartu: Putih Murni (`#FFFFFF`) dengan border abu-abu transparan (`border-slate-100` atau `border-slate-200/60`).
- **Gaya Elemen**:
  - Semua kartu utama wajib menggunakan sudut melengkung berukuran besar: **`rounded-3xl`** (kecil `rounded-2xl`).
  - Gunakan bayangan super halus: **`shadow-xs`** atau **`shadow-2xs`** agar tampilan terasa premium dan melayang.
- **Tipografi**:
  - Menggunakan font Sans-Serif bersih (Inter / Outfit / Roboto).
  - Judul menggunakan ketebalan ekstrim: **`font-black`** atau **`font-extrabold`** dengan pelacak huruf rapat (`tracking-tight`).

---

## 2. Code Architecture Guidelines
- **Keamanan Data**:
  - Selalu validasi data masukan dari pengguna di tingkat Controller sebelum menyimpannya ke database.
  - Terapkan middleware `auth` dan middleware kustom pembatas peran (`role:superadmin`, `role:admin`, `role:supervisor`, `role:intern`) pada setiap rute.
- **Blade & JavaScript**:
  - Gunakan Alpine.js untuk interaksi dinamis ringan (seperti toggle sidebar, visualisasi berkas terpilih pada form, modal detail). Jangan membebani halaman dengan pustaka eksternal yang besar.
  - Untuk diagram dan visualisasi grafik, gunakan pustaka **Chart.js** via CDN resmi yang dimuat pada tumpukan `@push('scripts')`.

---

## 3. Database & Seeding Rules
- **Seeding Konsisten**:
  - Pastikan database selalu bisa direkonstruksi bersih menggunakan perintah: `php artisan migrate:fresh --seed`.
  - Pertahankan user demo bawaan agar penguji dapat berpindah role dengan cepat tanpa registrasi manual:
    - **Superadmin IT**: `superadmin@pelindo.co.id` (Password: `password123`)
    - **Admin HR**: `admin@pelindo.co.id` (Password: `password123`)
    - **Supervisor**: `pembimbing.it@pelindo.co.id` (Password: `password123`)
    - **Intern**: `intern1@pelindo.co.id` (Password: `password123`)
