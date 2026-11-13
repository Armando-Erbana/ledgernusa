<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'count' => $user->unreadNotifications->count(),
            'items' => $user->unreadNotifications->take(10)->map(function($notif) {
                return [
                    'id' => $notif->id,
                    'message' => $notif->data['message'],
                    'url' => $notif->data['url'],
                ];
            })
        ]);
    }

    public function markAsRead(Request $request, $id)
    {
        $request->user()->unreadNotifications->where('id', $id)->markAsRead();
        return response()->json(['success' => true]);
    }
}