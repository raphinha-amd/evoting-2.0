<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $notificationCount = Notification::count();
        $notification = Notification::latest()->limit(5)->get();
        $activeUserCount = User::where('role', '!=', 'admin')->where('is_active', true)->count();
        $activeClassCount = Kelas::where('is_active', true)->count();

        confirmDelete('Reset Pengguna', 'Apakah kamu yakin ingin menonaktifkan seluruh akun pengguna non-admin?');
        confirmDelete('Reset Kelas', 'Apakah kamu yakin ingin menonaktifkan semua data kelas?');

        return view('settings', compact([
            'notification',
            'notificationCount',
            'activeUserCount',
            'activeClassCount',
        ]), ['menu_type' => 'settings']);
    }

    public function nonactiveUser()
    {
        User::where('role', '!=', 'admin')->where('is_active', true)->update(['is_active' => false]);
        return redirect()->back()->with('success', 'Semua data pengguna telah dinonaktifkan');
    }

    public function nonactiveClass()
    {
        Kelas::where('is_active', true)->update(['is_active' => false]);
        return redirect()->back()->with('success', 'Semua data kelas telah dinonaktifkan');
    }
}
