<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function manageRole()
    {
        $roles = Role::withCount('users')->orderBy('name', 'asc')->get();
        $notification = Notification::latest()->limit(5)->get();
        $notificationCount = Notification::count();

        confirmDelete('Hapus Role', 'Apakah kamu yakin ingin menghapus role ini?');

        return view('manage.roles', compact([
            'roles',
            'notification',
            'notificationCount'
        ]), ['menu_type' => 'manage-roles']);
    }

    public function addRole(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'has_class' => 'nullable|boolean',
            'can_vote' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput($request->all());
        }

        $slug = Str::slug($request->name);
        $originalSlug = $slug;
        $counter = 1;
        while (Role::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        Role::create([
            'name' => $request->name,
            'slug' => $slug,
            'has_class' => $request->boolean('has_class'),
            'can_vote' => $request->boolean('can_vote'),
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Role berhasil ditambahkan');
    }

    public function updateRole(Request $request, $id)
    {
        $role = Role::where('id', $id)->first();

        if (!$role) {
            return redirect()->back()->with('error', 'Role tidak ditemukan');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'has_class' => 'nullable|boolean',
            'can_vote' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput($request->all());
        }

        // Jangan izinkan ubah slug admin bawaan
        if ($role->slug !== 'admin') {
            $slug = Str::slug($request->name);
            if ($slug !== $role->slug) {
                $originalSlug = $slug;
                $counter = 1;
                while (Role::where('slug', $slug)->where('id', '!=', $id)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
                $role->slug = $slug;
            }
        }

        $role->name = $request->name;
        $role->has_class = $request->boolean('has_class');
        $role->can_vote = $request->boolean('can_vote');
        $role->save();

        return redirect()->back()->with('success', 'Role berhasil diubah');
    }

    public function deleteRole($id)
    {
        $role = Role::where('id', $id)->first();

        if (!$role) {
            return redirect()->back()->with('error', 'Role tidak ditemukan');
        }

        // Proteksi role sistem
        if (in_array($role->slug, ['admin', 'mahasiswa'])) {
            return redirect()->back()->with('error', 'Role bawaan sistem tidak boleh dihapus');
        }

        if ($role->users()->count() > 0) {
            return redirect()->back()->with('error', 'Role tidak dapat dihapus karena masih digunakan oleh user');
        }

        $role->delete();

        return redirect()->back()->with('success', 'Role berhasil dihapus');
    }

    public function data(Request $request)
    {
        $length = intval($request->input('length', 15));
        $start = intval($request->input('start', 0));
        $search = $request->input('search');
        $columns = $request->input('columns');
        $order = $request->input('order');

        $query = Role::query()->withCount('users')->where('is_active', true);

        if (!empty($search['value'])) {
            $searchValue = $search['value'];
            $query->where(function ($q) use ($searchValue) {
                $q->where('name', 'LIKE', "%{$searchValue}%")
                  ->orWhere('slug', 'LIKE', "%{$searchValue}%");
            });
        }

        $countFiltered = $query->count();

        if (!empty($order)) {
            $order = $order[0];
            $orderBy = $order['column'];
            $orderDir = $order['dir'];

            if (isset($columns[$orderBy]['data']) && in_array($columns[$orderBy]['data'], ['name', 'slug', 'has_class', 'can_vote', 'users_count'])) {
                $query->orderBy($columns[$orderBy]['data'], $orderDir);
            } else {
                $query->orderBy('name', 'asc');
            }
        } else {
            $query->orderBy('name', 'asc');
        }

        $data = $query->skip($start)->take($length)->get();
        $totalRecords = Role::where('is_active', true)->count();

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
        $role = Role::where('id', $id)->first();

        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }

        return response()->json($role);
    }
}
