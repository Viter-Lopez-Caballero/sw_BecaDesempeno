<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of the notifications.
     */
    public function index(Request $request)
    {
        $notifications = Notification::query()
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->through(fn ($notification) => [
                'id' => $notification->id,
                'title' => $notification->title,
                'data' => $notification->data,
                'type' => $notification->type,
                'read_at' => $notification->read_at,
                'created_at' => $notification->created_at,
                'is_read' => !is_null($notification->read_at),
            ]);

        return response()->json([
            'notifications' => $notifications,
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->markAsRead();

        return back()->with('success', 'Notificación marcada como leída');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        Notification::whereNull('read_at')->update(['read_at' => now()]);

        return back()->with('success', 'Todas las notificaciones marcadas como leídas');
    }

    /**
     * Delete a notification
     */
    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->delete();

        return back()->with('success', 'Notificación eliminada correctamente');
    }
}
