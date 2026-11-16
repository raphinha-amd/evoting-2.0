<?php

namespace App\Http\Controllers;

use App\Imports\UserImport;
use App\Models\Kelas;
use App\Models\Notification;
use App\Models\Role;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    public function manageUser(Request $request)
    {
        $roles = Role::where('is_active', true)->orderBy('name', 'asc')->get();
        $kelas = Kelas::with('prodi')->orderBy('name', 'asc')->where('is_active', true)->get();
        $notification = Notification::latest()->limit(5)->get();
        $notificationCount = Notification::count();

        confirmDelete('Hapus User', 'Apakah kamu yakin ingin menghapus user?');

        if ($request->has('admin')) {
            $user = User::with('roleData')->where('role', 'admin')->orderBy('fullname', 'asc')->get();
            return view('manage.users-admin', compact([
                'user',
                'roles',
                'kelas',
                'notification',
                'notificationCount'
            ]), ['menu_type' => 'manage-user']);
        } else {
            $user = User::with(['kelas.prodi', 'roleData'])->where('role', '!=', 'admin')->orderBy('fullname', 'asc')->get();
            return view('manage.users', compact([
                'user',
                'roles',
                'kelas',
                'notification',
                'notificationCount'
            ]), ['menu_type' => 'manage-user']);
        }
    }

    public function addUser(Request $request)
    {
        $role = Role::where('slug', $request->role)->first();
        $hasClass = $role ? $role->has_class : ($request->role === 'mahasiswa');

        $rules = [
            'fullname' => 'required|string|max:255',
            'username' => 'required|min:3|max:100',
            'password' => 'required|min:4',
            'role' => 'required|string|max:50',
            'kelas_id' => $hasClass ? 'required|exists:kelas,id' : 'nullable',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        $username = $request->username;
        while (User::where('username', $username)->exists()) {
            $randomString = Str::random(4);
            $username = $request->username . $randomString;
        }

        User::create([
            'fullname' => $request->fullname,
            'username' => $username,
            'password' => bcrypt($request->password),
            'unencrypted_password' => $request->password,
            'role' => $request->role,
            'kelas_id' => $hasClass ? $request->kelas_id : null,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'User berhasil ditambahkan');
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::where('id', $id)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'User yang ingin diubah tidak ditemukan');
        }

        $role = Role::where('slug', $request->role)->first();
        $hasClass = $role ? $role->has_class : ($request->role === 'mahasiswa');

        $rules = [
            'fullname' => 'required|string|max:255',
            'username' => 'required|min:3|max:100',
            'password' => 'required|min:4',
            'role' => 'required|string|max:50',
            'kelas_id' => $hasClass ? 'required|exists:kelas,id' : 'nullable',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        $user->fullname = $request->fullname;
        $user->username = $request->username;
        $user->password = bcrypt($request->password);
        $user->unencrypted_password = $request->password;
        $user->role = $request->role;
        $user->kelas_id = $hasClass ? $request->kelas_id : null;
        $user->save();

        return redirect()->back()->with('success', 'User berhasil diubah');
    }

    public function importUser(Request $request)
    {
        $role = Role::where('slug', $request->role)->first();
        $hasClass = $role ? $role->has_class : ($request->role === 'mahasiswa');

        $rules = [
            'file' => 'required|file|mimes:csv,xlsx,xls',
            'role' => 'required|string|max:50',
            'kelas_id' => $hasClass ? 'required|exists:kelas,id' : 'nullable',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput($request->all());
        }

        Excel::import(new UserImport($hasClass ? $request->kelas_id : null, $request->role), $request->file('file'));

        return redirect()->back()->with('success', 'Data user berhasil diimport');
    }

    public function exportUser(Request $request)
    {
        $role = Role::where('slug', $request->role)->first();
        $hasClass = $role ? $role->has_class : ($request->role === 'mahasiswa');

        $rules = [
            'role' => 'required',
            'kelas_id' => $hasClass ? 'required|exists:kelas,id' : 'nullable',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput($request->all());
        }

        $kelas = null;
        if ($hasClass && $request->kelas_id) {
            $kelas = Kelas::with('prodi')->where('id', $request->kelas_id)->first();
            $users = User::with(['kelas.prodi', 'roleData'])->where('kelas_id', $request->kelas_id)->get();
            $fileName = 'Data User RuangSuara ' . ($kelas ? $kelas->name : 'Kelas') . '.pdf';
        } else {
            $users = User::with('roleData')->where('role', $request->role)->get();
            $roleName = $role ? $role->name : ucfirst($request->role);
            $fileName = 'Data User RuangSuara ' . $roleName . '.pdf';
        }

        $pdf = Pdf::loadView('export.export-users', compact('users', 'kelas'));
        return $pdf->download($fileName);
    }

    public function deleteUser($username)
    {
        $user = User::where('username', $username)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'User yang ingin dihapus tidak ditemukan');
        }

        $user->delete();

        return redirect()->back()->with('success', 'User berhasil dihapus');
    }

    public function data(Request $request)
    {
        $length = intval($request->input('length', 15));
        $start = intval($request->input('start', 0));
        $search = $request->input('search');
        $columns = $request->input('columns');
        $order = $request->input('order');

        $query = User::query()
            ->with(['kelas.prodi', 'roleData'])
            ->where('role', '!=', 'admin')
            ->where('is_active', true);

        if (!empty($search['value'])) {
            $searchValue = $search['value'];
            $query->where(function ($q) use ($searchValue) {
                $q->where('fullname', 'LIKE', "%{$searchValue}%")
                  ->orWhere('username', 'LIKE', "%{$searchValue}%")
                  ->orWhere('role', 'LIKE', "%{$searchValue}%")
                  ->orWhereHas('kelas', function ($k) use ($searchValue) {
                      $k->where('name', 'LIKE', "%{$searchValue}%");
                  });
            });
        }

        $countFiltered = $query->count();

        if (!empty($order)) {
            $order = $order[0];
            $orderBy = $order['column'];
            $orderDir = $order['dir'];

            if (isset($columns[$orderBy]['data']) && in_array($columns[$orderBy]['data'], ['fullname', 'username', 'role'])) {
                $query->orderBy($columns[$orderBy]['data'], $orderDir);
            } else {
                $query->orderBy('fullname', 'asc');
            }
        } else {
            $query->orderBy('fullname', 'asc');
        }

        $data = $query->skip($start)->take($length)->get();
        $totalRecords = User::where('role', '!=', 'admin')->where('is_active', true)->count();

        return response()->json([
            "draw" => intval($request->input('draw', 1)),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $countFiltered,
            "limit" => $length,
            "data" => $data
        ]);
    }

    public function dataAdmin(Request $request)
    {
        $length = intval($request->input('length', 15));
        $start = intval($request->input('start', 0));
        $search = $request->input('search');
        $columns = $request->input('columns');
        $order = $request->input('order');

        $query = User::query()->with('roleData')->where('role', 'admin');

        if (!empty($search['value'])) {
            $searchValue = $search['value'];
            $query->where(function ($q) use ($searchValue) {
                $q->where('fullname', 'LIKE', "%{$searchValue}%")
                  ->orWhere('username', 'LIKE', "%{$searchValue}%");
            });
        }

        $countFiltered = $query->count();

        if (!empty($order)) {
            $order = $order[0];
            $orderBy = $order['column'];
            $orderDir = $order['dir'];

            if (isset($columns[$orderBy]['data']) && in_array($columns[$orderBy]['data'], ['fullname', 'username'])) {
                $query->orderBy($columns[$orderBy]['data'], $orderDir);
            } else {
                $query->orderBy('fullname', 'asc');
            }
        } else {
            $query->orderBy('fullname', 'asc');
        }

        $data = $query->skip($start)->take($length)->get();
        $totalRecords = User::where('role', 'admin')->count();

        return response()->json([
            "draw" => intval($request->input('draw', 1)),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $countFiltered,
            "limit" => $length,
            "data" => $data
        ]);
    }

    public function dataById($id)
    {
        $user = User::with(['kelas.prodi', 'roleData'])->where('id', $id)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user);
    }
}
