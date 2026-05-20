<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get all notifications for the authenticated user.
     */
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->paginate(15);
        return response()->json($notifications);
    }

    /**
     * Get only unread notifications for the authenticated user.
     */
    public function unread(Request $request)
    {
        $notifications = $request->user()->unreadNotifications()->get();
        return response()->json($notifications);
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()->notifications()->find($id);

        if ($notification) {
            $notification->markAsRead();
            return response()->json(['message' => 'Notification marked as read']);
        }

        return response()->json(['message' => 'Notification not found'], 404);
    }

    /**
     * Mark all unread notifications as read.
     */
    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return response()->json(['message' => 'All notifications marked as read']);
    }
}
