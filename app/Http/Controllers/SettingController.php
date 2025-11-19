<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $notificationCount = Notification::count();
        $notification = Notification::latest()->limit(5)->get();

        confirmDelete('Reset Pengguna', 'Apakah kamu yakin ingin mereset semua data pengguna?');
        return view('settings', compact([
            'notification',
            'notificationCount',
        ]), ['menu_type' => 'settings']);
    }

    public function resetUser()
    {
        User::where('role', '!=', 'admin')->delete();
        return redirect()->back()->with('success', 'Semua data pengguna telah direset');
    }
}
