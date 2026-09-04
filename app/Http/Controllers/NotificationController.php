<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $notifications = $request->user()
            ->notifications()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(Request $request, string $id): \Illuminate\Http\RedirectResponse
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return back()->with('success', 'Notifikasi ditandai sudah dibaca.');
    }

    public function markAllAsRead(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }

    public function destroy(Request $request, string $id): \Illuminate\Http\RedirectResponse
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->delete();

        return back()->with('success', 'Notifikasi dihapus.');
    }

    public function clearAll(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->user()->notifications()->delete();

        return back()->with('success', 'Semua notifikasi dihapus.');
    }
}
