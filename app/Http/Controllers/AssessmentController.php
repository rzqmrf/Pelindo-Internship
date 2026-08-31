<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\InternAssessment;
use App\Models\Certificate;
use App\Models\InternProfile;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class AssessmentController extends Controller
{
    // ==========================================
    // SUPERVISOR ENDPOINTS
    // ==========================================

    public function supervisorIndex(Request $request)
    {
        $supervisorId = Auth::id();

        $interns = User::where('role', 'intern')
            ->whereHas('internProfile', function ($q) use ($supervisorId) {
                $q->where('supervisor_id', $supervisorId);
            })
            ->with(['internProfile.division', 'assessments' => function ($q) use ($supervisorId) {
                $q->where('supervisor_id', $supervisorId);
            }, 'certificates'])
            ->get();

        return view('supervisor.assessments.index', compact('interns'));
    }

    public function evaluate($internId)
    {
        $supervisorId = Auth::id();
        $intern = User::with(['internProfile.division'])->findOrFail($internId);

        // Security check
        if ($intern->internProfile?->supervisor_id !== $supervisorId && !Auth::user()->isAdmin()) {
            abort(403, 'Anda tidak memiliki akses menilai peserta magang ini.');
        }

        $assessment = InternAssessment::where('intern_id', $internId)
            ->where('supervisor_id', $supervisorId)
            ->first();

        return view('supervisor.assessments.evaluate', compact('intern', 'assessment'));
    }

    public function storeOrUpdate(Request $request, $internId)
    {
        $supervisorId = Auth::id();
        $intern = User::with('internProfile')->findOrFail($internId);

        if ($intern->internProfile?->supervisor_id !== $supervisorId && !Auth::user()->isAdmin()) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'technical_skill' => 'required|integer|min:0|max:100',
            'discipline_score' => 'required|integer|min:0|max:100',
            'initiative_score' => 'required|integer|min:0|max:100',
            'teamwork_score' => 'required|integer|min:0|max:100',
            'attitude_score' => 'required|integer|min:0|max:100',
            'notes_strengths' => 'nullable|string|max:2000',
            'notes_improvements' => 'nullable|string|max:2000',
            'supervisor_recommendation' => 'nullable|string|max:2000',
            'action' => 'required|in:draft,submit',
        ]);

        $calculated = InternAssessment::calculateGrade($request->only([
            'technical_skill',
            'discipline_score',
            'initiative_score',
            'teamwork_score',
            'attitude_score',
        ]));

        $status = $request->action === 'submit' ? 'submitted' : 'draft';

        $assessment = InternAssessment::updateOrCreate(
            [
                'intern_id' => $internId,
                'supervisor_id' => $supervisorId,
            ],
            [
                'technical_skill' => $request->technical_skill,
                'discipline_score' => $request->discipline_score,
                'initiative_score' => $request->initiative_score,
                'teamwork_score' => $request->teamwork_score,
                'attitude_score' => $request->attitude_score,
                'final_score' => $calculated['final_score'],
                'predicate' => $calculated['predicate'],
                'notes_strengths' => $request->notes_strengths,
                'notes_improvements' => $request->notes_improvements,
                'supervisor_recommendation' => $request->supervisor_recommendation,
                'status' => $status,
                'reviewed_at' => Carbon::now(),
            ]
        );

        if ($status === 'submitted') {
            // Beritahu Admin HR untuk persetujuan sertifikat
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $adm) {
                Notification::create([
                    'user_id' => $adm->id,
                    'title' => '🎓 Evaluasi Magang Siap Terbit',
                    'message' => "Pembimbing {$intern->name} telah menyelesaikan evaluasi nilai akhir ({$calculated['predicate']}). Silakan verifikasi dan terbitkan sertifikat.",
                    'is_read' => false,
                ]);
            }

            // Beritahu Intern bahwa evaluasi telah selesai dinilai
            Notification::create([
                'user_id' => $intern->id,
                'title' => '🎓 Penilaian Akhir Magang Selesai',
                'message' => "Pembimbing Anda telah menyelesaikan evaluasi magang dengan predikat {$calculated['predicate']}. Menunggu verifikasi HR untuk penerbitan e-Sertifikat resmi.",
                'is_read' => false,
            ]);
        }

        $msg = $status === 'submitted'
            ? 'Penilaian berhasil disimpan dan diteruskan ke HR untuk penerbitan sertifikat.'
            : 'Draft penilaian berhasil disimpan.';

        return redirect()->route('supervisor.assessments.index')->with('success', $msg);
    }

    // ==========================================
    // ADMIN / HR ENDPOINTS
    // ==========================================

    public function adminIndex(Request $request)
    {
        $query = InternAssessment::with(['intern.internProfile.division', 'supervisor', 'certificate']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('intern', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $assessments = $query->latest()->paginate(15);

        return view('admin.assessments.index', compact('assessments'));
    }

    public function publishCertificate(Request $request, $assessmentId)
    {
        $assessment = InternAssessment::with('intern.internProfile.division')->findOrFail($assessmentId);

        $certificate = Certificate::firstOrCreate(
            ['assessment_id' => $assessment->id],
            [
                'intern_id' => $assessment->intern_id,
                'certificate_number' => Certificate::generateCertificateNumber($assessment->intern),
                'verification_token' => Certificate::generateUniqueToken(),
                'issue_date' => Carbon::today(),
                'status' => 'published',
            ]
        );

        $assessment->update(['status' => 'approved']);

        // Notifikasi ke Intern
        Notification::create([
            'user_id' => $assessment->intern_id,
            'title' => '📜 E-Sertifikat Magang Resmi Telah Terbit!',
            'message' => "Selamat! E-Sertifikat Magang resmi Pelindo Anda telah diterbitkan dengan nomor {$certificate->certificate_number}. Anda dapat mengunduhnya sekarang.",
            'is_read' => false,
        ]);

        return back()->with('success', "E-Sertifikat resmi berhasil diterbitkan dengan nomor {$certificate->certificate_number}.");
    }

    // ==========================================
    // INTERN ENDPOINTS
    // ==========================================

    public function internView()
    {
        $user = Auth::user();
        $assessment = InternAssessment::where('intern_id', $user->id)->with('certificate', 'supervisor')->first();
        $certificate = $assessment?->certificate;

        return view('intern.certificate.index', compact('user', 'assessment', 'certificate'));
    }

    public function downloadPdf($token)
    {
        Carbon::setLocale('id');

        $certificate = Certificate::where('verification_token', $token)
            ->with(['intern.internProfile.division', 'assessment.supervisor.supervisorProfile'])
            ->firstOrFail();

        $intern = $certificate->intern;
        $profile = $intern->internProfile;
        $assessment = $certificate->assessment;
        $supervisor = $assessment?->supervisor;

        $startDateFormatted = $profile?->start_date ? $profile->start_date->isoFormat('D MMMM YYYY') : '1 Juli 2026';
        $endDateFormatted = $profile?->end_date ? $profile->end_date->isoFormat('D MMMM YYYY') : Carbon::today()->isoFormat('D MMMM YYYY');
        $issueDateFormatted = $certificate->issue_date->isoFormat('D MMMM YYYY');

        $verifyUrl = route('certificate.verify', ['token' => $certificate->verification_token]);

        $data = [
            'certificate' => $certificate,
            'intern' => $intern,
            'profile' => $profile,
            'assessment' => $assessment,
            'supervisor' => $supervisor,
            'startDateFormatted' => $startDateFormatted,
            'endDateFormatted' => $endDateFormatted,
            'issueDateFormatted' => $issueDateFormatted,
            'verifyUrl' => $verifyUrl,
            'companyName' => 'PT PELABUHAN INDONESIA (PERSERO)',
        ];

        $pdf = Pdf::loadView('reports.certificate_pdf', $data);
        $pdf->setPaper('A4', 'landscape');

        $fileName = 'Sertifikat_Magang_Pelindo_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $intern->name) . '.pdf';

        return $pdf->download($fileName);
    }

    // ==========================================
    // PUBLIC VERIFICATION ENDPOINT
    // ==========================================

    public function publicVerify($token)
    {
        Carbon::setLocale('id');

        $certificate = Certificate::where('verification_token', $token)
            ->with(['intern.internProfile.division', 'assessment.supervisor'])
            ->first();

        return view('public.verify_certificate', compact('certificate', 'token'));
    }
}
