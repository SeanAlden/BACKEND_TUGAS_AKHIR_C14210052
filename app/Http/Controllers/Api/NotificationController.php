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
        $notifications = Notification::orderBy('notification_time', 'desc')->get();
        return response()->json($notifications);
    }

    // Menghapus notifikasi berdasarkan ID
    public function destroy($id)
    {
        $notification = Notification::find($id);

        if (!$notification) {
            return response()->json(['message' => 'Notifikasi tidak ditemukan'], 404);
        }

        $notification->delete();
        return response()->json(['message' => 'Notifikasi berhasil dihapus']);
    }
}
