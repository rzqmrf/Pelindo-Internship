# Product Requirement Document (PRD) - Pelindo Internship Portal (PRO)

## 1. Overview
Pelindo Internship Portal (PRO) adalah platform internal PT Pelabuhan Indonesia (Persero) - Solusi Digital yang dirancang untuk mendigitalisasi proses pemantauan magang, pengisian logbook harian, dan proses persetujuan aktivitas secara daring.

Platform ini memfasilitasi kolaborasi tiga peran utama:
- **Intern (Mahasiswa/Siswa Magang)**: Mengisi kehadiran, melapor tugas harian, memantau kemajuan magang.
- **Supervisor (Pembimbing Lapangan)**: Memantau interns bimbingannya, melakukan approval/reject/request revision atas logbook harian.
- **HR Admin**: Mengelola data divisi, kuota divisi, pendaftaran pembimbing, pendaftaran anak magang, dan memantau aktivitas keseluruhan di tingkat perusahaan.

---

## 2. User Roles & Permissions

### A. Intern (Mahasiswa Magang)
- **Login & Dashboard**: Melihat ringkasan progres magang (contoh: 6 / 20 hari kerja - 30%), status hari ini (Belum diisi / Terisi), riwayat logbook, dan motivasi harian.
- **Form Logbook Harian**: 
  - Input tanggal kegiatan.
  - Memilih status kerja (`WFO` / `WFH` / `Izin`).
  - Mengisi deskripsi rincian kegiatan (minimal 10 karakter).
  - Mengisi catatan/pembelajaran tambahan (opsional).
  - Mengunggah berkas bukti kegiatan (PDF/DOCX/PNG/JPG maks. 5MB) (opsional).
  - Menyimpan sebagai Draft atau Submit langsung ke pembimbing.
- **Riwayat Logbook**: Memantau daftar riwayat kegiatan harian beserta status persetujuan pembimbing (`Approved`, `Pending`, `Revision`, `Rejected`) beserta catatan feedback.
- **Sistem Pengajuan Izin & Sakit**: Mengisi pengajuan izin atau sakit mandiri (tanggal, jenis, alasan, serta bukti pendukung berkas/surat keterangan dokter) ke pembimbing lapangan.
- **Klaim Uang Saku Bulanan**: Memilih bulan pengajuan, sistem secara otomatis merincikan statistik jumlah hari WFO/WFH/Izin pada bulan tersebut beserta perhitungan besaran uang saku secara dinamis (rate WFO Rp 100k, WFH Rp 80k).

### B. Supervisor (Pembimbing Lapangan)
- **Dashboard**: Melihat jumlah peserta bimbingan, antrean approval pending, ringkasan aktivitas hari ini, timeline aktivitas bimbingan, serta bagan tren logbook.
- **Persetujuan Logbook**: Meninjau rincian logbook dari peserta bimbingan, memberikan umpan balik, serta menetapkan keputusan (`Setujui`, `Revisi`, `Tolak`).
- **Kelola Peserta**: Melihat data profil lengkap peserta bimbingan beserta kontak WhatsApp.
- **Persetujuan Izin & Sakit**: Tinjauan antrean izin/sakit dari peserta bimbingan. Ketika disetujui, sistem otomatis mencatatkan record kehadiran `Izin` (status `Approved` dengan deskripsi izin) pada hari/tanggal logbook bersangkutan.
- **Persetujuan Uang Saku Periodik**: Meninjau klaim bulanan anak bimbingan. Menyetujui klaim bulanan otomatis menyetujui (*bulk-approve*) seluruh logbook harian tertunda milik anak magang tersebut pada bulan terpilih.

### C. HR Admin
- **Dashboard**: Analisis metrik statistik magang perusahaan, kuota divisi, dan tren aktivitas logbook.
- **Kelola Data**: Manajemen data peserta magang (dilengkapi input NIK & Informasi Rekening pencairan uang saku), pembimbing lapangan, dan kuota divisi/unit kerja.
- **Monitoring**: Pemantauan real-time status logbook harian semua peserta dari seluruh divisi (tanpa hak akses ekspor laporan).
- **Pusat Notifikasi (In-App Notification Center)**:
  - Notifikasi di header bar (bell dropdown icon) yang memperbarui jumlah pesan belum dibaca secara real-time dan interaktif.
  - Pemicu otomatis (triggers):
    - Pembimbing (Supervisor) mendapatkan notifikasi instan saat peserta mengirim/mengubah logbook harian atau mengajukan izin baru.
    - Peserta (Intern) mendapatkan notifikasi instan saat logbook disetujui/ditolak/diminta revisi, serta saat pengajuan izin/sakit disetujui atau ditolak oleh pembimbing.
- **Verifikasi & Pencairan Uang Saku**: Memantau daftar pengajuan uang saku yang disetujui pembimbing lapangan, meninjau rekening bank peserta magang, dan mencairkan uang saku ke rekening peserta magang (mengubah status menjadi 'Dicairkan').

---

## 3. Key Non-Functional Requirements
- **Desain & Estetika**: Bertema putih minimalis modern korporat (clean white corporate theme), menggunakan logo resmi transparan Pelindo Solusi Digital, layout sidebar tetap (`sticky top-0 h-screen`), dan komponen visual bermutu tinggi (rounded-3xl, shadow-xs, dll).
- **Keamanan**: Otentikasi berbasis session, proteksi CSRF pada formulir, pembatasan akses data berdasarkan peran pengguna (Role Middleware).
- **Fleksibilitas & Akses Laporan**: 
  - Hak ekspor laporan PDF/Excel dibatasi secara ketat **hanya untuk peran Intern (Mahasiswa Magang)**. Supervisor dan HR Admin tidak diperbolehkan mencetak atau mengekspor dokumen laporan.
  - Laporan PDF yang dihasilkan dibatasi tepat **20 hari kerja** (Senin - Jumat) mulai dari tanggal yang dipilih, dengan pengisian kehadiran otomatis (`Hadir (WFO)` / `Hadir (WFH)`) untuk hari kerja yang kosong.
  - Bagian tanda tangan bawah dokumen (Peserta Magang & Manajer Divisi) disiapkan dengan area kosong untuk tanda tangan basah/manual (tanpa tanda tangan digital/coretan pra-cetak).
