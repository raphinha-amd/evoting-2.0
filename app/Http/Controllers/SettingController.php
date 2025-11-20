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

        confirmDelete('Reset Pengguna', 'Apakah kamu yakin ingin mereset semua data pengguna?');
        return view('settings', compact([
            'notification',
            'notificationCount',
        ]), ['menu_type' => 'settings']);
    }

    public function nonactiveUser()
    {
        User::where('role', '!=', 'admin')->get()->each(function ($user) {;
           $user->where('is_active', true)->update(['is_active' => false]);
        });
        return redirect()->back()->with('success', 'Semua data pengguna telah dinonaktifkan');
    }

    public function nonactiveClass()
    {
        Kelas::all()->each(function ($kelas) {
           $kelas->where('is_active', true)->update(['is_active' => false]);
        });
        return redirect()->back()->with('success', 'Semua data kelas telah dinonaktifkan');
    }
}
