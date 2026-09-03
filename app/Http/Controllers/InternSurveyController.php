<?php

namespace App\Http\Controllers;

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
}
