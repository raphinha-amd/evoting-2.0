<?php

namespace App\Http\Controllers;

use App\Models\Kandidat;
use App\Models\Notification;
use App\Models\Pemilu;
use App\Models\Prodi;
use App\Models\Ukm;
use App\Models\User;
use App\Models\Voting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

class PemiluController extends Controller
{
    public function managePemilu()
    {
        $pemilu = Pemilu::with(['prodis', 'ukm', 'user'])->orderBy('created_at', 'DESC')->get();
        $prodis = Prodi::where('is_active', true)->orderBy('name', 'asc')->get();
        $ukms = Ukm::where('is_active', true)->orderBy('name', 'asc')->get();
        $notificationCount = Notification::count();
        $notification = Notification::latest()->limit(5)->get();

        confirmDelete('Hapus Pemilu', 'Apakah kamu yakin ingin menghapus pemilu?');
        return view('manage.pemilu', compact([
            'pemilu',
            'prodis',
            'ukms',
            'notification',
            'notificationCount'
        ]), ['menu_type' => 'manage-pemilu']);
    }

    public function addPemilu(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'is_private' => 'required|in:0,1,2',
            'password' => 'nullable|string|max:255',
            'status' => 'required|in:0,1',
            'prodi_ids' => 'required_if:is_private,1|array',
            'prodi_ids.*' => 'exists:prodis,id',
            'ukm_id' => 'required_if:is_private,2|nullable|exists:ukms,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput($request->all());
        }

        $slug = Str::slug($request->name);
        $originalSlug = $slug;
        $counter = 1;
        while (Pemilu::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $isPrivate = (int)$request->is_private;
        $prodiIds = ($isPrivate === 1) ? ($request->prodi_ids ?? []) : [];
        $ukmId = ($isPrivate === 2) ? $request->ukm_id : null;

        $pemilu = new Pemilu();
        $pemilu->name = $request->name;
        $pemilu->slug = $slug;
        $pemilu->description = $request->description;
        $pemilu->is_private = $isPrivate;
        $pemilu->password = ($isPrivate > 0 && !empty($request->password)) ? $request->password : null;
        $pemilu->ukm_id = $ukmId;
        $pemilu->status = (int)$request->status;
        $pemilu->user_id = Auth::id();
        $pemilu->total_participants = $this->calculateTotalParticipants($isPrivate, $prodiIds, $ukmId);
        $pemilu->save();

        if ($isPrivate === 1 && !empty($prodiIds)) {
            $pemilu->prodis()->sync($prodiIds);
        }

        return redirect()->back()->with('success', 'Pemilu berhasil ditambahkan');
    }

    public function updatePemilu(Request $request, $slug)
    {
        $pemilu = Pemilu::where('slug', $slug)->first();

        if (!$pemilu) {
            return redirect()->back()->with('error', 'Pemilu yang dicari tidak ditemukan');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'is_private' => 'required|in:0,1,2',
            'password' => 'nullable|string|max:255',
            'status' => 'required|in:0,1',
            'prodi_ids' => 'required_if:is_private,1|array',
            'prodi_ids.*' => 'exists:prodis,id',
            'ukm_id' => 'required_if:is_private,2|nullable|exists:ukms,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput($request->all());
        }

        $newSlug = Str::slug($request->name);
        if ($newSlug !== $pemilu->slug) {
            $originalSlug = $newSlug;
            $counter = 1;
            while (Pemilu::where('slug', $newSlug)->where('id', '!=', $pemilu->id)->exists()) {
                $newSlug = $originalSlug . '-' . $counter;
                $counter++;
            }
            $pemilu->slug = $newSlug;
        }

        $isPrivate = (int)$request->is_private;
        $prodiIds = ($isPrivate === 1) ? ($request->prodi_ids ?? []) : [];
        $ukmId = ($isPrivate === 2) ? $request->ukm_id : null;

        $pemilu->name = $request->name;
        $pemilu->description = $request->description;
        $pemilu->is_private = $isPrivate;
        $pemilu->password = ($isPrivate > 0 && !empty($request->password)) ? $request->password : null;
        $pemilu->ukm_id = $ukmId;
        $pemilu->status = (int)$request->status;
        $pemilu->total_participants = $this->calculateTotalParticipants($isPrivate, $prodiIds, $ukmId);
        $pemilu->save();

        if ($isPrivate === 1) {
            $pemilu->prodis()->sync($prodiIds);
        } else {
            $pemilu->prodis()->detach();
        }

        return redirect()->back()->with('success', 'Pemilu berhasil diubah');
    }

    public function deletePemilu($slug)
    {
        $pemilu = Pemilu::where('slug', $slug)->first();

        if (!$pemilu) {
            return redirect()->back()->with('error', 'Pemilu yang ingin dihapus tidak ditemukan');
        }

        $pemilu->delete();
        return redirect()->back()->with('success', 'Pemilu berhasil dihapus');
    }

    public function data(Request $request)
    {
        $length = intval($request->input('length', 15));
        $start = intval($request->input('start', 0));
        $search = $request->input('search');
        $columns = $request->input('columns');
        $order = $request->input('order');

        $query = Pemilu::query()->with(['prodis', 'ukm', 'user']);

        if (!empty($search['value'])) {
            $searchValue = $search['value'];
            $query->where(function ($q) use ($searchValue) {
                $q->where('name', 'LIKE', "%{$searchValue}%")
                  ->orWhere('description', 'LIKE', "%{$searchValue}%");
            });
        }

        $countFiltered = $query->count();

        if (!empty($order)) {
            $order = $order[0];
            $orderBy = $order['column'];
            $orderDir = $order['dir'];

            if (isset($columns[$orderBy]['data']) && in_array($columns[$orderBy]['data'], ['name', 'total_participants', 'status', 'created_at'])) {
                $query->orderBy($columns[$orderBy]['data'], $orderDir);
            } else {
                $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $data = $query->skip($start)->take($length)->get();
        $totalRecords = Pemilu::count();

        return response()->json([
            "draw" => intval($request->input('draw', 1)),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $countFiltered,
            "limit" => $length,
            "data" => $data
        ]);
    }

    public function dataBySlug($slug)
    {
        $pemilu = Pemilu::with(['prodis', 'ukm'])->where('slug', $slug)->first();

        if (!$pemilu) {
            return response()->json(['message' => 'Pemilu not found'], 404);
        }

        return response()->json($pemilu);
    }

    public function dataResultVoting($slug)
    {
        $pemilu = Pemilu::where('slug', $slug)->first();

        if (!$pemilu) {
            return response()->json(['message' => 'Pemilu not found'], 404);
        }

        $kandidat = Kandidat::where('pemilu_id', $pemilu->id)->withCount('voting')->get();

        $labels = [];
        $data = [];

        foreach ($kandidat as $item) {
            $labels[] = $item->name;
            $data[] = $item->voting_count;
        }

        $totalUser = $pemilu->total_participants;
        $votedUsers = $pemilu->voting()->count();
        $notVotedUsers = max(0, $totalUser - $votedUsers);

        $votesPerClass = Voting::where('pemilu_id', $pemilu->id)
            ->select('class_name', DB::raw('COUNT(*) as votes_count'))
            ->groupBy('class_name')
            ->get();

        return response()->json([
            'total_users' => $totalUser,
            'pie_charts' => [
                'voted' => $votedUsers,
                'not_voted' => $notVotedUsers,
            ],
            'bar_charts' => [
                'labels' => $labels,
                'data' => $data
            ],
            'votes_per_class' => $votesPerClass,
        ]);
    }

    public function syncTotalParticipants($slug)
    {
        $pemilu = Pemilu::with(['prodis', 'ukm'])->where('slug', $slug)->first();
        if (!$pemilu) {
            return redirect()->back()->with('error', 'Pemilu tidak ditemukan');
        }

        $isPrivate = (int)$pemilu->is_private;
        $prodiIds = ($isPrivate === 1) ? $pemilu->prodis->pluck('id')->toArray() : [];

        $pemilu->total_participants = $this->calculateTotalParticipants($isPrivate, $prodiIds, $pemilu->ukm_id);
        $pemilu->save();

        return redirect()->back()->with('success', 'Berhasil menyinkronkan total peserta pemilu');
    }

    /**
     * Helper to calculate total active participants dynamically based on election scope
     */
    protected function calculateTotalParticipants(int $isPrivate, array $prodiIds = [], ?string $ukmId = null): int
    {
        $query = User::where('role', '!=', 'admin')->where('is_active', true);

        if ($isPrivate === 2 && !empty($ukmId)) {
            $query->whereHas('ukms', function ($q) use ($ukmId) {
                $q->where('ukms.id', $ukmId);
            });
        } elseif ($isPrivate === 1 && !empty($prodiIds)) {
            $query->whereHas('kelas', function ($q) use ($prodiIds) {
                $q->whereIn('prodi_id', $prodiIds);
            });
        }

        return $query->count();
    }
}
