# System Architecture & Technical Specifications

## 1. Tech Stack
- **Framework**: Laravel 12 (PHP 8.2+)
- **Frontend Logic & Interactions**: Alpine.js (State management, modal modals, file-change reactive text)
- **Styling**: Tailwind CSS (Tailwind UI component utilities, custom gradients, corporate colors)
- **Database**: MySQL / MariaDB (Relational schema for divisions, users, profiles, and daily logs)
- **Icons**: FontAwesome v6 (Solid, Regular, Brands font sets)

---

## 2. Database Schema Design

### A. Users Table
Menampung data otentikasi dasar untuk semua role.
- `id` (Primary Key)
- `name` (Nama lengkap)
- `email` (Email unik perorangan / institusi)
- `password` (Hash sandi)
- `role` (Enum: `admin`, `supervisor`, `intern`)

### B. Divisions Table
Mengelola divisi/unit kerja Pelindo beserta kuota maksimal penampungan anak magang.
- `id` (Primary Key)
- `name` (Nama divisi)
- `quota` (Kuota tampung maksimal)

### C. Intern Profiles Table
Profil detail khusus peserta magang, memuat data NIK & perbankan untuk pencairan uang saku.
- `id` (Primary Key)
- `user_id` (Foreign Key -> `users.id`)
- `division_id` (Foreign Key -> `divisions.id`)
- `supervisor_id` (Foreign Key -> `users.id` - pembimbing lapangan yang ditugaskan)
- `student_id_number` (NIM / NIS)
- `nik_number` (Nomor Induk Kependudukan / KTP)
- `institution` (Asal sekolah / perguruan tinggi)
- `major` (Jurusan)
- `bank_name` (Nama Bank untuk pencairan)
- `bank_account_number` (Nomor Rekening)
- `bank_account_holder` (Nama Pemilik Rekening)
- `start_date` (Tanggal mulai magang)
- `end_date` (Tanggal selesai magang)

### D. Daily Activities Table (Logbook)
Menampung catatan aktivitas harian yang dikirim oleh peserta magang.
- `id` (Primary Key)
- `intern_id` (Foreign Key -> `users.id`)
- `supervisor_id` (Foreign Key -> `users.id`)
- `date` (Tanggal aktivitas)
- `work_location` (Enum: `WFO`, `WFH`, `Leave`)
- `activity_description` (Rincian teks minimal 10 karakter)
- `notes` (Catatan opsional)
- `attachment_path` (Lokasi file bukti unggahan)
- `status` (Enum: `draft`, `pending`, `approved`, `revision`, `rejected`)
- `supervisor_note` (Umpan balik revisi/penolakan dari pembimbing)

---

## 3. UI Component Structure

### A. Base Layout (`layouts/app.blade.php`)
- **Left Sidebar**: `fixed` untuk mobile (toggleable) dan `sticky top-0 h-screen` pada desktop agar tidak ikut tergulung saat halaman utama digulir. Memuat logo transparan resmi Pelindo Solusi Digital, info kartu pengguna dengan chevron, navigasi berdesain pill dinamis, ilustrasi *Catat kegiatanmu*, dan tombol keluar.
- **Top Header**: Menampilkan tanggal real-time dinamis dan dropdown mini akun pengguna.
- **Main Area**: Responsif dengan lebar maksimum `max-w-7xl` untuk visualisasi optimal di berbagai resolusi layar desktop.
