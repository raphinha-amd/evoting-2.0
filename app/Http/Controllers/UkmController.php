<?php

namespace App\Http\Controllers;

use App\Imports\UkmMemberImport;
use App\Models\Notification;
use App\Models\Ukm;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class UkmController extends Controller
{
    public function manageUkm()
    {
        $ukms = Ukm::withCount('users')->orderBy('name', 'asc')->get();
        $notification = Notification::latest()->limit(5)->get();
        $notificationCount = Notification::count();

        confirmDelete('Hapus UKM', 'Apakah kamu yakin ingin menghapus UKM ini? Data keanggotaan pemilih UKM ini akan terhapus.');

        return view('manage.ukm', compact([
            'ukms',
            'notification',
            'notificationCount'
        ]), ['menu_type' => 'manage-ukm']);
    }

    public function addUkm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'is_active' => 'nullable|in:0,1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput($request->all());
        }

        $slug = Str::slug($request->name);
        $originalSlug = $slug;
        $counter = 1;
        while (Ukm::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        Ukm::create([
            'name' => $request->name,
            'slug' => $slug,
            'code' => $request->code,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : true,
        ]);

        return redirect()->back()->with('success', 'UKM berhasil ditambahkan');
    }

    public function updateUkm(Request $request, $id)
    {
        $ukm = Ukm::find($id);

        if (!$ukm) {
            return redirect()->back()->with('error', 'Data UKM tidak ditemukan');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'is_active' => 'nullable|in:0,1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput($request->all());
        }

        $slug = Str::slug($request->name);
        if ($slug !== $ukm->slug) {
            $originalSlug = $slug;
            $counter = 1;
            while (Ukm::where('slug', $slug)->where('id', '!=', $id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
            $ukm->slug = $slug;
        }

        $ukm->name = $request->name;
        $ukm->code = $request->code;
        $ukm->description = $request->description;
        $ukm->is_active = $request->has('is_active') ? (bool)$request->is_active : true;
        $ukm->save();

        return redirect()->back()->with('success', 'Data UKM berhasil diubah');
    }

    public function deleteUkm($id)
    {
        $ukm = Ukm::find($id);

        if (!$ukm) {
            return redirect()->back()->with('error', 'Data UKM tidak ditemukan');
        }

        $ukm->delete();

        return redirect()->back()->with('success', 'Data UKM berhasil dihapus');
    }

    public function data(Request $request)
    {
        $length = intval($request->input('length', 15));
        $start = intval($request->input('start', 0));
        $search = $request->input('search');
        $columns = $request->input('columns');
        $order = $request->input('order');

        $query = Ukm::query()->withCount('users');

        if (!empty($search['value'])) {
            $searchValue = $search['value'];
            $query->where(function ($q) use ($searchValue) {
                $q->where('name', 'LIKE', "%{$searchValue}%")
                  ->orWhere('code', 'LIKE', "%{$searchValue}%")
                  ->orWhere('description', 'LIKE', "%{$searchValue}%");
            });
        }

        $countFiltered = $query->count();

        if (!empty($order)) {
            $order = $order[0];
            $orderBy = $order['column'];
            $orderDir = $order['dir'];

            if (isset($columns[$orderBy]['data']) && in_array($columns[$orderBy]['data'], ['name', 'code', 'users_count', 'is_active', 'created_at'])) {
                $query->orderBy($columns[$orderBy]['data'], $orderDir);
            } else {
                $query->orderBy('name', 'asc');
            }
        } else {
            $query->orderBy('name', 'asc');
        }

        $data = $query->skip($start)->take($length)->get();
        $totalRecords = Ukm::count();

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
        $ukm = Ukm::withCount('users')->find($id);

        if (!$ukm) {
            return response()->json(['message' => 'UKM not found'], 404);
        }

        return response()->json($ukm);
    }

    public function members(Request $request, $id)
    {
        $ukm = Ukm::find($id);

        if (!$ukm) {
            return response()->json(['message' => 'UKM not found'], 404);
        }

        $length = intval($request->input('length', 10));
        $start = intval($request->input('start', 0));
        $search = $request->input('search');

        $query = $ukm->users()->with(['kelas.prodi']);

        if (!empty($search['value'])) {
            $searchValue = $search['value'];
            $query->where(function ($q) use ($searchValue) {
                $q->where('users.username', 'LIKE', "%{$searchValue}%")
                  ->orWhere('users.fullname', 'LIKE', "%{$searchValue}%")
                  ->orWhereHas('kelas', function ($kq) use ($searchValue) {
                      $kq->where('kelas.name', 'LIKE', "%{$searchValue}%")
                         ->orWhereHas('prodi', function ($pq) use ($searchValue) {
                             $pq->where('name', 'LIKE', "%{$searchValue}%");
                         });
                  });
            });
        }

        $countFiltered = $query->count();
        $data = $query->skip($start)->take($length)->get();
        $totalRecords = $ukm->users()->count();

        return response()->json([
            "draw" => intval($request->input('draw', 1)),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $countFiltered,
            "limit" => $length,
            "data" => $data
        ]);
    }

    public function importMembers(Request $request, $id)
    {
        $ukm = Ukm::find($id);

        if (!$ukm) {
            return redirect()->back()->with('error', 'UKM tidak ditemukan');
        }

        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        $import = new UkmMemberImport($ukm);
        Excel::import($import, $request->file('file'));

        $imported = $import->getImportedCount();
        $already = $import->getAlreadyMemberCount();
        $notFound = $import->getNotFoundNims();

        $message = "Berhasil mengimpor {$imported} anggota baru ke {$ukm->name}.";

        if ($already > 0) {
            $message .= " {$already} mahasiswa sudah terdaftar sebelumnya.";
        }

        if (!empty($notFound)) {
            $missingPreview = implode(', ', array_slice($notFound, 0, 5));
            if (count($notFound) > 5) {
                $missingPreview .= ' dan ' . (count($notFound) - 5) . ' lainnya';
            }
            $message .= " [Perhatian: " . count($notFound) . " NIM tidak ditemukan di data mahasiswa: {$missingPreview}]";
            return redirect()->back()
                ->with('warning', $message)
                ->with('open_ukm_id', $ukm->id)
                ->with('open_ukm_name', $ukm->name);
        }

        return redirect()->back()
            ->with('success', $message)
            ->with('open_ukm_id', $ukm->id)
            ->with('open_ukm_name', $ukm->name);
    }

    public function addMemberManual(Request $request, $id)
    {
        $ukm = Ukm::find($id);

        if (!$ukm) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'UKM tidak ditemukan'], 404);
            }
            return redirect()->back()->with('error', 'UKM tidak ditemukan');
        }

        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
            }
            return redirect()->back()->withErrors($validator->errors());
        }

        $user = User::where('username', trim($request->username))->first();

        if (!$user) {
            $msg = "Mahasiswa dengan NIM/Username '{$request->username}' tidak ditemukan.";
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 404);
            }
            return redirect()->back()->with('error', $msg);
        }

        if ($ukm->users()->where('users.id', $user->id)->exists()) {
            $msg = "Mahasiswa {$user->fullname} ({$user->username}) sudah terdaftar sebagai anggota {$ukm->name}.";
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->back()->with('error', $msg);
        }

        $ukm->users()->attach($user->id);

        $successMsg = "Mahasiswa {$user->fullname} ({$user->username}) berhasil ditambahkan ke {$ukm->name}.";
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $successMsg]);
        }

        return redirect()->back()->with('success', $successMsg);
    }

    public function removeMember($ukmId, $userId)
    {
        $ukm = Ukm::find($ukmId);

        if (!$ukm) {
            if (request()->ajax()) {
                return response()->json(['message' => 'UKM not found'], 404);
            }
            return redirect()->back()->with('error', 'UKM tidak ditemukan');
        }

        $ukm->users()->detach($userId);

        if (request()->ajax()) {
            return response()->json(['message' => 'Anggota berhasil dihapus dari UKM']);
        }

        return redirect()->back()->with('success', 'Anggota berhasil dihapus dari UKM');
    }
}
