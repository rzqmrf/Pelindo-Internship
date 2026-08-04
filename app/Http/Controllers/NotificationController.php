<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Mark a specific notification as read.
     */
    public function read($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read.
     */
    public function readAll()
    {
        Auth::user()->notifications()->where('is_read', false)->update(['is_read' => true]);

        return redirect()->back()->with('success', 'Semua notifikasi ditandai sebagai telah dibaca.');
    }
}
