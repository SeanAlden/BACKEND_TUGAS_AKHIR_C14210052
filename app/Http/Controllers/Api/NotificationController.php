<?php

namespace App\Http\Controllers\Api;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class NotificationController extends Controller
{

    // Menampilkan semua notifikasi
    public function index()
    {
        $notifications = Notification::orderBy('notification_time', 'desc')->where('condition', 'unread')->get();
        return response()->json($notifications);
    }

    // Menandai notifikasi telah dibaca
    public function read($id)
    {
        $notification = Notification::find($id);

        if (!$notification) {
            return response()->json(['message' => 'Notifikasi tidak ditemukan'], 404);
        }

        $notification->condition = 'read';
        $notification->save();

        return response()->json(['message' => 'Notifikasi berhasil ditandai sebagai telah dibaca']);
    }
}
