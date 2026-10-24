<?php

namespace App\Http\Controllers;

use App\Imports\ClassImport;
use App\Models\Kelas;
use App\Models\Notification;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ClassController extends Controller
{
    public function manageClass()
    {
        $kelas = Kelas::with('prodi')->orderBy('name', 'asc')->get();
        $prodis = Prodi::where('is_active', true)->orderBy('name', 'asc')->get();
        $notification = Notification::latest()->limit(5)->get();
        $notificationCount = Notification::count();

        confirmDelete('Hapus Kelas', 'Apakah kamu yakin ingin menghapus kelas?');
        return view('manage.class', compact([
            'kelas',
            'prodis',
            'notification',
            'notificationCount'
        ]), ['menu_type' => 'manage-class']);
    }

    public function addClass(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'prodi_id' => 'required|exists:prodis,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput($request->all());
        }

        $slug = Str::slug($request->name);
        $originalSlug = $slug;
        $counter = 1;
        while (Kelas::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        Kelas::create([
            'name' => $request->name,
            'slug' => $slug,
            'prodi_id' => $request->prodi_id,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Kelas berhasil ditambahkan');
    }

    public function updateClass(Request $request, $id)
    {
        $kelas = Kelas::where('id', $id)->first();

        if (!$kelas) {
            return redirect()->back()->with('error', 'Kelas yang ingin diubah tidak ditemukan');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'prodi_id' => 'required|exists:prodis,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput($request->all());
        }

        $slug = Str::slug($request->name);
        if ($slug !== $kelas->slug) {
            $originalSlug = $slug;
            $counter = 1;
            while (Kelas::where('slug', $slug)->where('id', '!=', $id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
            $kelas->slug = $slug;
        }

        $kelas->name = $request->name;
        $kelas->prodi_id = $request->prodi_id;
        $kelas->save();

        return redirect()->back()->with('success', 'Kelas berhasil diubah');
    }

    public function importClass(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,xlsx,xls',
            'prodi_id' => 'required|exists:prodis,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput($request->all());
        }

        Excel::import(new ClassImport($request->prodi_id), $request->file('file'));

        return redirect()->back()->with('success', 'Data kelas berhasil diimport');
    }

    public function deleteClass($id)
    {
        $kelas = Kelas::where('id', $id)->first();

        if (!$kelas) {
            return redirect()->back()->with('error', 'Kelas yang ingin dihapus tidak ditemukan');
        }

        $kelas->delete();

        return redirect()->back()->with('success', 'Kelas berhasil dihapus');
    }

    public function data(Request $request)
    {
        $length = intval($request->input('length', 15));
        $start = intval($request->input('start', 0));
        $search = $request->input('search');
        $columns = $request->input('columns');
        $order = $request->input('order');

        $query = Kelas::query()->with('prodi')->where('is_active', true);

        if (!empty($search['value'])) {
            $searchValue = $search['value'];
            $query->where(function ($q) use ($searchValue) {
                $q->where('name', 'LIKE', "%{$searchValue}%")
                  ->orWhereHas('prodi', function ($p) use ($searchValue) {
                      $p->where('name', 'LIKE', "%{$searchValue}%");
                  });
            });
        }

        $countFiltered = $query->count();

        if (!empty($order)) {
            $order = $order[0];
            $orderBy = $order['column'];
            $orderDir = $order['dir'];

            if (isset($columns[$orderBy]['data']) && in_array($columns[$orderBy]['data'], ['name', 'slug'])) {
                $query->orderBy($columns[$orderBy]['data'], $orderDir);
            } else {
                $query->orderBy('name', 'asc');
            }
        } else {
            $query->orderBy('name', 'asc');
        }

        $data = $query->skip($start)->take($length)->get();
        $totalRecords = Kelas::where('is_active', true)->count();

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
        $class = Kelas::with('prodi')->where('id', $id)->first();

        if (!$class) {
            return response()->json(['message' => 'Kelas not found'], 404);
        }

        return response()->json($class);
    }
}
