<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Division;
use App\Models\SupervisorProfile;
use App\Models\InternProfile;
use App\Models\DailyActivity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Divisions
        $divIT = Division::create([
            'code' => 'IT-DIGITAL',
            'name' => 'Divisi Teknologi Informasi & Digitalisasi',
            'description' => 'Mengelola infrastruktur IT, pengembangan aplikasi port, dan transformasi digital Pelindo.',
            'quota' => 15,
            'head_name' => 'Ir. Budi Santoso, M.T.',
        ]);

        $divHR = Division::create([
            'code' => 'HR-GA',
            'name' => 'Divisi SDM & Umum',
            'description' => 'Mengelola manajemen talenta, rekrutmen, pelatihan, dan layanan umum perusahaan.',
            'quota' => 10,
            'head_name' => 'Dra. Ratna Sari, M.M.',
        ]);

        $divOps = Division::create([
            'code' => 'OPS-LOG',
            'name' => 'Divisi Operasional & Logistik',
            'description' => 'Mengawasi alur barang, manajemen terminal kargo, dan fasilitas kepelabuhanan.',
            'quota' => 20,
            'head_name' => 'Bambang Wijaya, S.E.',
        ]);

        $divFin = Division::create([
            'code' => 'FIN-TREASURY',
            'name' => 'Divisi Keuangan & Perbendaharaan',
            'description' => 'Perencanaan anggaran, akuntansi, arus kas, dan manajemen keuangan pelabuhan.',
            'quota' => 12,
            'head_name' => 'Maya Putri, Ak., M.Ak.',
        ]);

        $divLegal = Division::create([
            'code' => 'LEGAL-CORP',
            'name' => 'Divisi Hukum & Sekretariat Perusahaan',
            'description' => 'Kepatuhan regulasi, tata kelola perusahaan (GCG), dan hubungan publik.',
            'quota' => 8,
            'head_name' => 'Hendra Gunawan, S.H., LL.M.',
        ]);

        $divComm = Division::create([
            'code' => 'COMM-DEV',
            'name' => 'Divisi Komersial & Pengembangan Bisnis',
            'description' => 'Pemasaran jasa pelabuhan, kemitraan strategis, dan investasi bisnis baru.',
            'quota' => 10,
            'head_name' => 'Dewi Lestari, S.S., M.B.A.',
        ]);

        $password = Hash::make('password123');

        // 2. Admin HR
        $admin = User::create([
            'name' => 'Admin HR Pelindo',
            'email' => 'admin@pelindo.co.id',
            'role' => 'admin',
            'phone' => '081299887766',
            'password' => $password,
            'is_active' => true,
        ]);

        // 3. Supervisors
        $spv1 = User::create([
            'name' => 'Ahmad Rizky, S.T.',
            'email' => 'pembimbing.it@pelindo.co.id',
            'role' => 'supervisor',
            'phone' => '081311223344',
            'password' => $password,
            'is_active' => true,
        ]);
        SupervisorProfile::create([
            'user_id' => $spv1->id,
            'division_id' => $divIT->id,
            'employee_id_number' => 'NIP-198504122010121003',
            'position_title' => 'Senior IT Systems Specialist',
            'phone' => '081311223344',
        ]);

        $spv2 = User::create([
            'name' => 'Siti Rahmawati, M.Psi.',
            'email' => 'pembimbing.hr@pelindo.co.id',
            'role' => 'supervisor',
            'phone' => '081322334455',
            'password' => $password,
            'is_active' => true,
        ]);
        SupervisorProfile::create([
            'user_id' => $spv2->id,
            'division_id' => $divHR->id,
            'employee_id_number' => 'NIP-198809152012042001',
            'position_title' => 'HR Business Partner Specialist',
            'phone' => '081322334455',
        ]);

        $spv3 = User::create([
            'name' => 'Dedi Kurniawan, S.E.',
            'email' => 'pembimbing.ops@pelindo.co.id',
            'role' => 'supervisor',
            'phone' => '081333445566',
            'password' => $password,
            'is_active' => true,
        ]);
        SupervisorProfile::create([
            'user_id' => $spv3->id,
            'division_id' => $divOps->id,
            'employee_id_number' => 'NIP-198207102008011002',
            'position_title' => 'Manager Terminal & Logistik',
            'phone' => '081333445566',
        ]);

        // 4. Interns
        $intern1 = User::create([
            'name' => 'Bagas Pratama',
            'email' => 'intern1@pelindo.co.id',
            'role' => 'intern',
            'phone' => '085712345678',
            'password' => $password,
            'is_active' => true,
        ]);
        InternProfile::create([
            'user_id' => $intern1->id,
            'division_id' => $divIT->id,
            'supervisor_id' => $spv1->id,
            'student_id_number' => '5025211045',
            'nik_number' => '3578011204010003',
            'institution' => 'Institut Teknologi Sepuluh Nopember (ITS)',
            'major' => 'Teknik Informatika',
            'bank_name' => 'Bank Mandiri',
            'bank_account_number' => '1420018899221',
            'bank_account_holder' => 'Bagas Pratama',
            'start_date' => Carbon::now()->subMonths(1)->startOfMonth(),
            'end_date' => Carbon::now()->addMonths(2)->endOfMonth(),
            'status' => 'active',
        ]);

        $intern2 = User::create([
            'name' => 'Anisa Nurhaliza',
            'email' => 'intern2@pelindo.co.id',
            'role' => 'intern',
            'phone' => '085787654321',
            'password' => $password,
            'is_active' => true,
        ]);
        InternProfile::create([
            'user_id' => $intern2->id,
            'division_id' => $divHR->id,
            'supervisor_id' => $spv2->id,
            'student_id_number' => '042111233089',
            'nik_number' => '3578025508020005',
            'institution' => 'Universitas Airlangga (UNAIR)',
            'major' => 'Psikologi / Manajemen SDM',
            'bank_name' => 'Bank Central Asia (BCA)',
            'bank_account_number' => '8290334411',
            'bank_account_holder' => 'Anisa Nurhaliza',
            'start_date' => Carbon::now()->subMonths(1)->startOfMonth(),
            'end_date' => Carbon::now()->addMonths(1)->endOfMonth(),
            'status' => 'active',
        ]);

        $intern3 = User::create([
            'name' => 'Dimas Anggara',
            'email' => 'intern3@pelindo.co.id',
            'role' => 'intern',
            'phone' => '085744556677',
            'password' => $password,
            'is_active' => true,
        ]);
        InternProfile::create([
            'user_id' => $intern3->id,
            'division_id' => $divIT->id,
            'supervisor_id' => $spv1->id,
            'student_id_number' => '21/478912/SV/19821',
            'nik_number' => '3404071509010002',
            'institution' => 'Universitas Gadjah Mada (UGM)',
            'major' => 'Sistem Informasi',
            'bank_name' => 'Bank Negara Indonesia (BNI)',
            'bank_account_number' => '0891234567',
            'bank_account_holder' => 'Dimas Anggara',
            'start_date' => Carbon::now()->subMonths(2)->startOfMonth(),
            'end_date' => Carbon::now()->addDays(5),
            'status' => 'active',
        ]);

        $intern4 = User::create([
            'name' => 'Farah Diba',
            'email' => 'intern4@pelindo.co.id',
            'role' => 'intern',
            'phone' => '085799001122',
            'password' => $password,
            'is_active' => true,
        ]);
        InternProfile::create([
            'user_id' => $intern4->id,
            'division_id' => $divOps->id,
            'supervisor_id' => $spv3->id,
            'student_id_number' => '3121600045',
            'institution' => 'Politeknik Elektronika Negeri Surabaya (PENS)',
            'major' => 'Teknik Komputer & Logistik Digital',
            'start_date' => Carbon::now()->subMonths(1)->startOfMonth(),
            'end_date' => Carbon::now()->addMonths(2)->endOfMonth(),
            'status' => 'active',
        ]);

        // 5. Seed Daily Activities
        $today = Carbon::today();

        // Seed activities for Intern 1 in Period 1 (July 1 to July 28, 2026) to make it claimable!
        $periodStart = Carbon::now()->subMonths(1)->startOfMonth();
        $seededWeekdays = 0;
        $seedDate = $periodStart->copy();
        
        while ($seededWeekdays < 20) {
            if (!$seedDate->isWeekend()) {
                DailyActivity::create([
                    'intern_id' => $intern1->id,
                    'supervisor_id' => $spv1->id,
                    'date' => $seedDate->format('Y-m-d'),
                    'work_location' => $seededWeekdays % 5 === 0 ? 'WFH' : 'WFO',
                    'activity_description' => 'Melakukan pengembangan modul internal dan sinkronisasi data logbook magang ke server Pelindo untuk tanggal ' . $seedDate->format('Y-m-d') . '.',
                    'notes' => 'Pekerjaan berjalan lancar dan selesai sesuai target.',
                    'status' => 'approved',
                    'supervisor_note' => 'Disetujui. Lanjutkan progresnya.',
                    'reviewed_at' => $seedDate->copy()->addHours(8),
                ]);
                $seededWeekdays++;
            }
            $seedDate->addDay();
        }

        $sampleActivities = [
            [
                'offset' => 0,
                'location' => 'WFO',
                'desc' => 'Melakukan integrasi API webhook untuk tracking kontainer kapal dengan Laravel 12 dan Redis queue.',
                'notes' => 'API berhasil di-benchmark dengan latency < 150ms pada 500 request per detik.',
                'status' => 'pending',
                'spv_note' => null,
            ],
            [
                'offset' => 1,
                'location' => 'WFO',
                'desc' => 'Mengembangkan tampilan dashboard monitoring aktivitas harian magang dengan Chart.js & Alpine.js.',
                'notes' => 'Menyesuaikan skema warna dengan Pelindo Navy #002B49 dan Cyan #00A3E0.',
                'status' => 'approved',
                'spv_note' => 'Kerja yang sangat baik. Visualisasi sangat jelas dan responsif.',
            ],
            [
                'offset' => 2,
                'location' => 'WFH',
                'desc' => 'Analisis arsitektur database MySQL untuk modul otomatisasi laporan PDF magang Pelindo.',
                'notes' => 'Menyusun relasi antar tabel user_profiles, daily_activities, dan divisions.',
                'status' => 'approved',
                'spv_note' => 'Sesuai dengan standar ERD perusahaan.',
            ],
            [
                'offset' => 3,
                'location' => 'WFO',
                'desc' => 'Rapat koordinasi mingguan dengan tim IT Digitalization mengenai migrasi mikroservis.',
                'notes' => 'Mendapatkan tugas tambahan untuk optimasi query SQL pada tabel transaksi kargo.',
                'status' => 'revision',
                'spv_note' => 'Mohon lengkapi catatan hasil riset indeks tabel MySQL pada deskripsi kegiatan.',
            ],
            [
                'offset' => 4,
                'location' => 'WFO',
                'desc' => 'Pengujian performa server staging dan penanganan exception error pada upload dokumen lampiran.',
                'notes' => 'Menggunakan Laravel File Validation untuk membatasi tipe PDF, PNG, JPG maks 5MB.',
                'status' => 'approved',
                'spv_note' => 'Bagus, pastikan enkripsi file tersimpan aman.',
            ],
            [
                'offset' => 5,
                'location' => 'Leave',
                'desc' => 'Izin sakit dengan surat keterangan dokter terlampir.',
                'notes' => 'Kondisi flu berat.',
                'status' => 'approved',
                'spv_note' => 'Semoga lekas sembuh.',
            ],
            [
                'offset' => 6,
                'location' => 'WFO',
                'desc' => 'Studi literatur tentang keamanan REST API, JWT authentication, dan CSRF protection.',
                'notes' => 'Membuat rangkuman dokumen modul keamanan software.',
                'status' => 'rejected',
                'spv_note' => 'Deskripsi kegiatan terlalu umum. Harap cantumkan implementasi konkret pada aplikasi Pelindo.',
            ],
            [
                'offset' => 7,
                'location' => 'WFH',
                'desc' => 'Refactoring controller dan pemisahan service layer untuk logika ekspor data Excel.',
                'notes' => 'Menggunakan Maatwebsite Excel / CSV exporter.',
                'status' => 'approved',
                'spv_note' => 'Struktur kode menjadi lebih bersih.',
            ],
        ];

        foreach ($sampleActivities as $act) {
            $activityDate = (clone $today)->subDays($act['offset']);
            if ($activityDate->isWeekend()) {
                continue;
            }

            DailyActivity::create([
                'intern_id' => $intern1->id,
                'supervisor_id' => $spv1->id,
                'date' => $activityDate,
                'work_location' => $act['location'],
                'activity_description' => $act['desc'],
                'notes' => $act['notes'],
                'status' => $act['status'],
                'supervisor_note' => $act['spv_note'],
                'reviewed_at' => $act['status'] !== 'pending' && $act['status'] !== 'draft' ? $activityDate->copy()->addHours(4) : null,
            ]);
        }

        // Add activities for Anisa (Intern 2)
        foreach (array_slice($sampleActivities, 0, 5) as $act) {
            $activityDate = (clone $today)->subDays($act['offset'] + 1);
            if ($activityDate->isWeekend()) {
                continue;
            }

            DailyActivity::create([
                'intern_id' => $intern2->id,
                'supervisor_id' => $spv2->id,
                'date' => $activityDate,
                'work_location' => $act['location'] === 'Leave' ? 'WFO' : $act['location'],
                'activity_description' => 'Rekapitulasi absensi & koordinasi onboarding magang peserta baru batch III Pelindo.',
                'notes' => 'Merekap 45 berkas administrasi calon magang.',
                'status' => 'pending',
                'supervisor_note' => null,
            ]);
        }

        // Add activities for Dimas (Intern 3)
        DailyActivity::create([
            'intern_id' => $intern3->id,
            'supervisor_id' => $spv1->id,
            'date' => $today,
            'work_location' => 'WFO',
            'activity_description' => 'Penyesuaian konfigurasi server Nginx dan SSL certificate untuk portal magang.',
            'notes' => 'Port HTTPS 443 berjalan lancar.',
            'status' => 'pending',
        ]);
    }
}
