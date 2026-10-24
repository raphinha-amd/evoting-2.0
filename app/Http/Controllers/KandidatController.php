<?php

namespace App\Http\Controllers;

use App\Models\Kandidat;
use App\Models\Notification;
use App\Models\Pemilu;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class KandidatController extends Controller
{
    public function kandidatPemilu($slug)
    {
        $pemilu = Pemilu::where('slug', $slug)->first();
        $notificationCount = Notification::count();
        $notification = Notification::latest()->limit(5)->get();

        if (!$pemilu) {
            return redirect()->back()->with('error', 'Pemilu yang dicari tidak ditemukan');
        }

        $kandidat = Kandidat::where('pemilu_id', $pemilu->id)->get();

        confirmDelete('Hapus Kandidat', 'Apakah kamu yakin ingin menghapus kandidat?');
        return view('manage.pemilu-kandidat', compact([
            'pemilu',
            'kandidat',
            'notification',
            'notificationCount'
        ]), ['menu_type' => 'manage-pemilu']);
    }

    public function addKandidatPemilu($slug, Request $request)
    {
        $pemilu = Pemilu::where('slug', $slug)->first();

        if (!$pemilu) {
            return redirect()->back()->with('error', 'Pemilu yang dicari tidak ditemukan');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'vision_mission' => 'required|string',
            'image' => 'required|file|image|mimes:png,jpg,jpeg|max:2048'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput($request->all());
        }

        $fileName = Str::random(8) . '.' . $request->file('image')->getClientOriginalExtension();

        $kandidat = new Kandidat();
        $kandidat->name = $request->name;
        $kandidat->description = $request->description;
        $kandidat->vision_mission = $request->vision_mission;
        $kandidat->image = $request->file('image')->storeAs('pemilu/' . $pemilu->slug, $fileName);
        $kandidat->pemilu_id = $pemilu->id;
        $kandidat->save();

        return redirect()->back()->with('success', 'Kandidat berhasil ditambahkan');
    }

    public function updateKandidatPemilu(Request $request, $slug, $id)
    {
        $pemilu = Pemilu::where('slug', $slug)->first();

        if (!$pemilu) {
            return redirect()->back()->with('error', 'Pemilu yang dicari tidak ditemukan');
        }

        $kandidat = Kandidat::where('id', $id)->where('pemilu_id', $pemilu->id)->first();

        if (!$kandidat) {
            return redirect()->back()->with('error', 'Kandidat yang dicari tidak ditemukan');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'vision_mission' => 'required|string',
            'image' => 'nullable|file|image|mimes:png,jpg,jpeg|max:2048'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput($request->all());
        }

        $kandidat->name = $request->name;
        $kandidat->description = $request->description;
        $kandidat->vision_mission = $request->vision_mission;

        if ($request->hasFile('image')) {
            $fileName = Str::random(8) . '.' . $request->file('image')->getClientOriginalExtension();
            $kandidat->image = $request->file('image')->storeAs('pemilu/' . $pemilu->slug, $fileName);
        }

        $kandidat->save();

        return redirect()->back()->with('success', 'Kandidat berhasil diubah');
    }

    public function deleteKandidatPemilu($slug, $id)
    {
        $pemilu = Pemilu::where('slug', $slug)->first();

        if (!$pemilu) {
            return redirect()->back()->with('error', 'Pemilu yang dicari tidak ditemukan');
        }

        $kandidat = Kandidat::where('id', $id)->where('pemilu_id', $pemilu->id)->first();

        if (!$kandidat) {
            return redirect()->back()->with('error', 'Kandidat yang dicari tidak ditemukan');
        }

        $kandidat->delete();
        return redirect()->back()->with('success', 'Kandidat berhasil dihapus');
    }

    public function exportResultPdf($slug)
    {
        $pemilu = Pemilu::where('slug', $slug)->first();

        if (!$pemilu) {
            return redirect()->back()->with('error', 'Pemilu tidak ditemukan');
        }

        $kandidat = Kandidat::where('pemilu_id', $pemilu->id)->get();

        if ($kandidat->isEmpty()) {
            return redirect()->back()->with('error', 'Kandidat tidak ditemukan');
        }

        $votedUsers = DB::table('votings')
            ->where('pemilu_id', $pemilu->id)
            ->distinct('user_id')
            ->count('user_id');

        $votesPerClass = DB::table('votings')
            ->select('class_name', DB::raw('COUNT(DISTINCT user_id) as votes_count'))
            ->where('pemilu_id', $pemilu->id)
            ->groupBy('class_name')
            ->orderBy('class_name')
            ->get();

        $pdf = Pdf::loadView('export.export-result', compact(
            'pemilu',
            'kandidat',
            'votedUsers',
            'votesPerClass'
        ));

        $filename = 'hasil-' . Str::slug($pemilu->name) . '.pdf';

        return $pdf->download($filename);
    }

    public function data(Request $request, $slug)
    {
        $length = intval($request->input('length', 15));
        $start = intval($request->input('start', 0));
        $search = $request->input('search');
        $columns = $request->input('columns');
        $order = $request->input('order');

        $pemilu = Pemilu::where('slug', $slug)->first();

        if (!$pemilu) {
            return response()->json(['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0]);
        }

        $query = Kandidat::with('pemilu')->where('pemilu_id', $pemilu->id);

        if (!empty($search['value'])) {
            $searchValue = $search['value'];
            $query->where('name', 'LIKE', "%{$searchValue}%");
        }

        $countFiltered = $query->count();

        if (!empty($order)) {
            $order = $order[0];
            $orderBy = $order['column'];
            $orderDir = $order['dir'];

            if (isset($columns[$orderBy]['data']) && in_array($columns[$orderBy]['data'], ['name'])) {
                $query->orderBy($columns[$orderBy]['data'], $orderDir);
            } else {
                $query->orderBy('name', 'asc');
            }
        } else {
            $query->orderBy('name', 'asc');
        }

        $data = $query->skip($start)->take($length)->get();
        $totalRecords = Kandidat::where('pemilu_id', $pemilu->id)->count();

        return response()->json([
            "draw" => intval($request->input('draw', 1)),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $countFiltered,
            "limit" => $length,
            "data" => $data
        ]);
    }

    public function dataById($slug, $id)
    {
        $pemilu = Pemilu::where('slug', $slug)->first();

        if (!$pemilu) {
            return response()->json(['message' => 'Pemilu not found'], 404);
        }

        $kandidat = Kandidat::where('id', $id)->where('pemilu_id', $pemilu->id)->first();
        if (!$kandidat) {
            return response()->json(['message' => 'Kandidat not found'], 404);
        }

        return response()->json($kandidat);
    }
}
