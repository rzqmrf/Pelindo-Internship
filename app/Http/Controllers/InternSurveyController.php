<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\InternSurvey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InternSurveyController extends Controller
{
    public function showForm()
    {
        $user = Auth::user();
        $existingSurvey = InternSurvey::where('intern_id', $user->id)->first();

        return view('intern.survey.index', compact('existingSurvey'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'satisfaction_rating' => 'required|integer|min:1|max:5',
            'supervisor_rating' => 'required|integer|min:1|max:5',
            'facility_rating' => 'required|integer|min:1|max:5',
            'recommend_score' => 'required|integer|min:1|max:10',
            'feedback_notes' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();

        InternSurvey::updateOrCreate(
            ['intern_id' => $user->id],
            [
                'satisfaction_rating' => $request->satisfaction_rating,
                'supervisor_rating' => $request->supervisor_rating,
                'facility_rating' => $request->facility_rating,
                'recommend_score' => $request->recommend_score,
                'feedback_notes' => $request->feedback_notes,
            ]
        );

        return redirect()->back()->with('success', 'Terima kasih! Survei kepuasan magang Anda telah berhasil tersimpan.');
    }

    public function adminIndex(Request $request)
    {
        $query = InternSurvey::with(['intern.internProfile.division', 'intern.internProfile.supervisor']);

        if ($request->filled('division_id')) {
            $query->whereHas('intern.internProfile', function ($q) use ($request) {
                $q->where('division_id', $request->division_id);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('feedback_notes', 'like', "%{$search}%")
                    ->orWhereHas('intern', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $surveys = $query->latest()->paginate(12)->withQueryString();
        $divisions = Division::orderBy('name')->get();

        $totalResponses = InternSurvey::count();
        $avgSatisfaction = $totalResponses > 0 ? round((float) InternSurvey::avg('satisfaction_rating'), 1) : 0;
        $avgSupervisor = $totalResponses > 0 ? round((float) InternSurvey::avg('supervisor_rating'), 1) : 0;
        $avgFacility = $totalResponses > 0 ? round((float) InternSurvey::avg('facility_rating'), 1) : 0;
        $avgRecommend = $totalResponses > 0 ? round((float) InternSurvey::avg('recommend_score'), 1) : 0;

        return view('admin.surveys.index', compact(
            'surveys',
            'divisions',
            'totalResponses',
            'avgSatisfaction',
            'avgSupervisor',
            'avgFacility',
            'avgRecommend'
        ));
    }
}
