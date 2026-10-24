<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProdiController extends Controller
{
    public function manageProdi()
    {
        $prodis = Prodi::withCount('kelas')->orderBy('name', 'asc')->get();
        $notification = Notification::latest()->limit(5)->get();
        $notificationCount = Notification::count();

        confirmDelete('Hapus Program Studi', 'Apakah kamu yakin ingin menghapus program studi ini?');

        return view('manage.prodi', compact([
            'prodis',
            'notification',
            'notificationCount'
        ]), ['menu_type' => 'manage-prodi']);
    }

    public function addProdi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:20',
            'degree' => 'nullable|string|max:10',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput($request->all());
        }

        $slug = Str::slug($request->name);
        // Pastikan slug unik
        $originalSlug = $slug;
        $counter = 1;
        while (Prodi::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        Prodi::create([
            'name' => $request->name,
            'slug' => $slug,
            'code' => $request->code,
            'degree' => $request->degree,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Program Studi berhasil ditambahkan');
    }

    public function updateProdi(Request $request, $id)
    {
        $prodi = Prodi::where('id', $id)->first();

        if (!$prodi) {
            return redirect()->back()->with('error', 'Program Studi tidak ditemukan');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:20',
            'degree' => 'nullable|string|max:10',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput($request->all());
        }

        $slug = Str::slug($request->name);
        if ($slug !== $prodi->slug) {
            $originalSlug = $slug;
            $counter = 1;
            while (Prodi::where('slug', $slug)->where('id', '!=', $id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
            $prodi->slug = $slug;
        }

        $prodi->name = $request->name;
        $prodi->code = $request->code;
        $prodi->degree = $request->degree;
        $prodi->save();

        return redirect()->back()->with('success', 'Program Studi berhasil diubah');
    }

    public function deleteProdi($id)
    {
        $prodi = Prodi::where('id', $id)->first();

        if (!$prodi) {
            return redirect()->back()->with('error', 'Program Studi tidak ditemukan');
        }

        $prodi->delete();

        return redirect()->back()->with('success', 'Program Studi berhasil dihapus');
    }

    public function data(Request $request)
    {
        $length = intval($request->input('length', 15));
        $start = intval($request->input('start', 0));
        $search = $request->input('search');
        $columns = $request->input('columns');
        $order = $request->input('order');

        $query = Prodi::query()->withCount('kelas')->where('is_active', true);

        if (!empty($search['value'])) {
            $searchValue = $search['value'];
            $query->where(function ($q) use ($searchValue) {
                $q->where('name', 'LIKE', "%{$searchValue}%")
                  ->orWhere('code', 'LIKE', "%{$searchValue}%")
                  ->orWhere('degree', 'LIKE', "%{$searchValue}%");
            });
        }

        $countFiltered = $query->count();

        if (!empty($order)) {
            $order = $order[0];
            $orderBy = $order['column'];
            $orderDir = $order['dir'];

            if (isset($columns[$orderBy]['data']) && in_array($columns[$orderBy]['data'], ['name', 'code', 'degree', 'kelas_count', 'created_at'])) {
                $query->orderBy($columns[$orderBy]['data'], $orderDir);
            } else {
                $query->orderBy('name', 'asc');
            }
        } else {
            $query->orderBy('name', 'asc');
        }

        $data = $query->skip($start)->take($length)->get();
        $totalRecords = Prodi::where('is_active', true)->count();

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
        $prodi = Prodi::where('id', $id)->first();

        if (!$prodi) {
            return response()->json(['message' => 'Prodi not found'], 404);
        }

        return response()->json($prodi);
    }
}
