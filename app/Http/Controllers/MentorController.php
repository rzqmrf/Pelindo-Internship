<?php

namespace App\Http\Controllers;

use App\Models\DailyActivity;
use App\Models\InternProfile;
use App\Models\InternTask;
use App\Models\MentoringNote;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MentorController extends Controller
{
    public function dashboard()
    {
        $mentorId = Auth::id();

        $mentoredInterns = InternProfile::with(['user', 'division', 'supervisor'])
            ->where('mentor_id', $mentorId)
            ->where('status', 'active')
            ->get();

        $internIds = $mentoredInterns->pluck('user_id')->all();

        $pendingActivitiesCount = DailyActivity::whereIn('intern_id', $internIds)
            ->where('status', 'pending')
            ->count();

        $activeTasksCount = InternTask::where('assigned_by', $mentorId)
            ->whereIn('status', ['todo', 'in_progress', 'submitted', 'revision'])
            ->count();

        $submittedTasksCount = InternTask::where('assigned_by', $mentorId)
            ->where('status', 'submitted')
            ->count();

        $recentActivities = DailyActivity::with(['intern.internProfile.division'])
            ->whereIn('intern_id', $internIds)
            ->where('status', 'pending')
            ->latest('date')
            ->take(6)
            ->get();

        $recentNotes = MentoringNote::with('intern')
            ->where('mentor_id', $mentorId)
            ->latest('session_date')
            ->take(5)
            ->get();

        return view('mentor.dashboard', compact(
            'mentoredInterns',
            'pendingActivitiesCount',
            'activeTasksCount',
            'submittedTasksCount',
            'recentActivities',
            'recentNotes'
        ));
    }

    public function myInterns(Request $request)
    {
        $mentorId = Auth::id();
        $query = InternProfile::with(['user.dailyActivities', 'division', 'supervisor'])
            ->where('mentor_id', $mentorId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%");
                })->orWhere('student_id_number', 'like', "%{$search}%")
                  ->orWhere('institution', 'like', "%{$search}%");
            });
        }

        $interns = $query->latest()->paginate(10)->withQueryString();

        return view('mentor.interns', compact('interns'));
    }

    public function activities(Request $request)
    {
        $mentorId = Auth::id();
        $internIds = InternProfile::where('mentor_id', $mentorId)->pluck('user_id')->all();

        $query = DailyActivity::with(['intern.internProfile.division'])
            ->whereIn('intern_id', $internIds);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('intern_id')) {
            $query->where('intern_id', $request->intern_id);
        }

        $activities = $query->latest('date')->paginate(15)->withQueryString();
        $myInterns = User::whereIn('id', $internIds)->orderBy('name')->get();

        return view('mentor.activities', compact('activities', 'myInterns'));
    }

    public function reviewActivity(Request $request, $id)
    {
        $mentorId = Auth::id();
        $internIds = InternProfile::where('mentor_id', $mentorId)->pluck('user_id')->all();

        $activity = DailyActivity::where('id', $id)
            ->whereIn('intern_id', $internIds)
            ->firstOrFail();

        $request->validate([
            'status' => 'required|in:approved,revision,rejected',
            'supervisor_note' => 'nullable|string|max:1000',
        ]);

        if (in_array($request->status, ['revision', 'rejected'], true) && empty($request->supervisor_note)) {
            return back()->with('error', 'Catatan mentor wajib diisi jika status Revisi atau Ditolak.');
        }

        $activity->update([
            'status' => $request->status,
            'supervisor_note' => $request->supervisor_note,
            'reviewed_at' => Carbon::now(),
        ]);

        Notification::create([
            'user_id' => $activity->intern_id,
            'title' => 'Review Logbook oleh Mentor',
            'message' => 'Logbook tanggal ' . Carbon::parse($activity->date)->isoFormat('D MMMM YYYY') . " telah diperbarui menjadi status {$request->status} oleh mentor Anda.",
        ]);

        return back()->with('success', 'Review logbook berhasil disimpan.');
    }

    public function tasks(Request $request)
    {
        $mentorId = Auth::id();
        $internIds = InternProfile::where('mentor_id', $mentorId)->pluck('user_id')->all();
        $myInterns = User::whereIn('id', $internIds)->orderBy('name')->get();

        $query = InternTask::with('intern')
            ->where('assigned_by', $mentorId);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('intern_id')) {
            $query->where('intern_id', $request->intern_id);
        }

        $tasks = $query->latest()->paginate(12)->withQueryString();

        return view('mentor.tasks', compact('tasks', 'myInterns'));
    }

    public function storeTask(Request $request)
    {
        $mentorId = Auth::id();
        $internIds = InternProfile::where('mentor_id', $mentorId)->pluck('user_id')->all();

        $request->validate([
            'intern_id' => 'required|integer|in:' . implode(',', $internIds ?: [0]),
            'title' => 'required|string|max:200',
            'description' => 'nullable|string|max:2000',
            'due_date' => 'nullable|date',
        ]);

        $task = InternTask::create([
            'intern_id' => $request->intern_id,
            'assigned_by' => $mentorId,
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'status' => 'todo',
        ]);

        Notification::create([
            'user_id' => $task->intern_id,
            'title' => 'Tugas / Milestone Baru dari Mentor',
            'message' => "Anda mendapatkan tugas baru: \"{$task->title}\".",
        ]);

        return back()->with('success', 'Tugas baru berhasil diberikan kepada peserta.');
    }

    public function reviewTask(Request $request, $id)
    {
        $task = InternTask::where('id', $id)
            ->where('assigned_by', Auth::id())
            ->firstOrFail();

        $request->validate([
            'status' => 'required|in:approved,revision',
            'reviewer_note' => 'nullable|string|max:1000',
        ]);

        if ($request->status === 'revision' && empty($request->reviewer_note)) {
            return back()->with('error', 'Catatan revisi wajib diisi.');
        }

        $task->update([
            'status' => $request->status,
            'reviewer_note' => $request->reviewer_note,
            'reviewed_at' => Carbon::now(),
        ]);

        Notification::create([
            'user_id' => $task->intern_id,
            'title' => 'Hasil Tugas Direview Mentor',
            'message' => "Tugas \"{$task->title}\" berstatus {$request->status}.",
        ]);

        return back()->with('success', 'Review tugas berhasil disimpan.');
    }

    public function mentoringNotes(Request $request)
    {
        $mentorId = Auth::id();
        $internIds = InternProfile::where('mentor_id', $mentorId)->pluck('user_id')->all();
        $myInterns = User::whereIn('id', $internIds)->orderBy('name')->get();

        $notes = MentoringNote::with('intern')
            ->where('mentor_id', $mentorId)
            ->when($request->filled('intern_id'), fn ($q) => $q->where('intern_id', $request->intern_id))
            ->latest('session_date')
            ->paginate(12)
            ->withQueryString();

        return view('mentor.notes', compact('notes', 'myInterns'));
    }

    public function storeMentoringNote(Request $request)
    {
        $mentorId = Auth::id();
        $internIds = InternProfile::where('mentor_id', $mentorId)->pluck('user_id')->all();

        $request->validate([
            'intern_id' => 'required|integer|in:' . implode(',', $internIds ?: [0]),
            'session_date' => 'required|date',
            'topic' => 'required|string|max:200',
            'notes' => 'required|string|max:2000',
            'action_items' => 'nullable|string|max:1000',
        ]);

        $note = MentoringNote::create([
            'intern_id' => $request->intern_id,
            'mentor_id' => $mentorId,
            'session_date' => $request->session_date,
            'topic' => $request->topic,
            'notes' => $request->notes,
            'action_items' => $request->action_items,
        ]);

        Notification::create([
            'user_id' => $note->intern_id,
            'title' => 'Catatan Sesi Mentoring Baru',
            'message' => "Mentor mencatat sesi bimbingan baru: \"{$note->topic}\".",
        ]);

        return back()->with('success', 'Catatan sesi mentoring berhasil disimpan.');
    }
}
