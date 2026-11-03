<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of announcements for HR Admin.
     */
    public function index()
    {
        $announcements = Announcement::with('creator')->latest()->paginate(10);
        return view('admin.announcements.index', compact('announcements'));
    }

    /**
     * Store a newly created announcement in database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string|in:info,important,event',
        ]);

        $announcement = Announcement::create([
            'created_by' => Auth::id(),
            'title' => $request->title,
            'content' => $request->content,
            'category' => $request->category,
        ]);

        AuditLogger::record('announcement.create', 'announcements', "Admin HR menerbitkan pengumuman: {$announcement->title}.", $announcement);

        // Send a notification to all interns and supervisors!
        $recipients = \App\Models\User::whereIn('role', ['intern', 'supervisor'])->get();
        foreach ($recipients as $recipient) {
            \App\Models\Notification::create([
                'user_id' => $recipient->id,
                'title' => 'Pengumuman Baru: ' . $request->title,
                'message' => 'HR Admin memposting pengumuman baru dalam kategori ' . strtoupper($request->category) . '.',
            ]);
        }

        return redirect()->route('admin.announcements.index')->with('success', 'Pengumuman berhasil diterbitkan dan disebarkan ke seluruh peserta & pembimbing.');
    }

    /**
     * Remove the specified announcement from database.
     */
    public function destroy($id)
    {
        $announcement = Announcement::findOrFail($id);
        $title = $announcement->title;
        $announcement->delete();

        AuditLogger::record('announcement.delete', 'announcements', "Admin HR menghapus pengumuman: {$title}.");

        return redirect()->route('admin.announcements.index')->with('success', 'Pengumuman berhasil dihapus.');
    }
}
