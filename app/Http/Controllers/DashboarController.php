<?php

namespace App\Http\Controllers;

use App\Models\Kandidat;
use App\Models\Notification;
use App\Models\Pemilu;
use App\Models\Voting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

class DashboarController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            // Admin KPUM melihat semua pemilu yang sedang aktif
            $pemilu = Pemilu::with(['prodis', 'ukm', 'kandidats'])->where('status', 1)->orderBy('created_at', 'DESC')->get();
        } else {
            // Voter hanya melihat pemilu public, pemilu prodi yang sesuai, atau pemilu UKM yang dia ikuti
            $userProdiId = $user->kelas?->prodi_id;
            $userUkmIds = $user->ukms->pluck('id')->toArray();

            $pemilu = Pemilu::with(['prodis', 'ukm', 'kandidats'])
                ->where('status', 1)
                ->where(function ($query) use ($userProdiId, $userUkmIds) {
                    $query->where('is_private', 0);

                    if ($userProdiId) {
                        $query->orWhere(function ($subQuery) use ($userProdiId) {
                            $subQuery->where('is_private', 1)
                                ->whereHas('prodis', function ($p) use ($userProdiId) {
                                    $p->where('prodis.id', $userProdiId);
                                });
                        });
                    }

                    if (!empty($userUkmIds)) {
                        $query->orWhere(function ($subQuery) use ($userUkmIds) {
                            $subQuery->where('is_private', 2)
                                ->whereIn('ukm_id', $userUkmIds);
                        });
                    }
                })
                ->orderBy('created_at', 'DESC')
                ->get();
        }

        $notificationCount = Notification::count();
        $notification = Notification::latest()->limit(5)->get();

        return view('dashboard', compact([
            'pemilu',
            'notification',
            'notificationCount',
        ]), ['menu_type' => 'dashboard']);
    }

    public function joinPemilu($slug)
    {
        $pemilu = Pemilu::with(['prodis', 'ukm'])->where('slug', $slug)->first();

        if (!$pemilu) {
            return redirect()->back()->with('error', 'Pemilu yang dicari tidak ditemukan');
        }

        $user = Auth::user();

        // Validasi hak pilih prodi / UKM jika pemilu private
        $eligibilityError = $this->checkVoterEligibility($pemilu, $user);
        if ($eligibilityError) {
            Alert::error('Akses Ditolak', $eligibilityError);
            return redirect()->route('user.dashboard');
        }

        // Cek apakah user sudah voting
        $userVoted = Voting::where('pemilu_id', $pemilu->id)->where('user_id', $user->id)->exists();
        if ($userVoted) {
            Alert::warning('Peringatan', 'Anda Sudah Melakukan Voting');
            return redirect()->back();
        }

        $kandidat = Kandidat::where('pemilu_id', $pemilu->id)->get();

        return view('join-pemilu', compact([
            'pemilu',
            'kandidat'
        ]), ['menu_type' => 'dashboard']);
    }

    public function verifyPasswordJoin(Request $request, $slug)
    {
        $pemilu = Pemilu::with(['prodis', 'ukm'])->where('slug', $slug)->first();

        if (!$pemilu) {
            return redirect()->back()->with('error', 'Pemilu yang dicari tidak ditemukan');
        }

        $user = Auth::user();

        // Validasi kesesuaian prodi / UKM terlebih dahulu
        $eligibilityError = $this->checkVoterEligibility($pemilu, $user);
        if ($eligibilityError) {
            Alert::error('Akses Ditolak', $eligibilityError);
            return redirect()->route('user.dashboard');
        }

        $validator = Validator::make($request->all(), [
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput($request->all());
        }

        if ($request->password === $pemilu->password) {
            Session::put('pemilu_' . $pemilu->slug . '_verified', true);
            return redirect()->route('user.pemilu.join', $slug);
        }

        Alert::error('Error', 'Password Salah');
        return redirect()->back();
    }

    public function votePemilu($slug, $id)
    {
        $pemilu = Pemilu::with(['prodis', 'ukm'])->where('slug', $slug)->first();
        if (!$pemilu) {
            return redirect()->back()->with('error', 'Pemilu yang dicari tidak ditemukan');
        }

        $user = Auth::user();

        // Validasi hak pilih prodi / UKM
        $eligibilityError = $this->checkVoterEligibility($pemilu, $user);
        if ($eligibilityError) {
            Alert::error('Akses Ditolak', $eligibilityError);
            return redirect()->route('user.dashboard');
        }

        // Cek double vote
        $alreadyVoted = Voting::where('pemilu_id', $pemilu->id)->where('user_id', $user->id)->exists();
        if ($alreadyVoted) {
            Alert::warning('Peringatan', 'Anda Sudah Melakukan Voting');
            return redirect()->route('user.dashboard');
        }

        $kandidat = Kandidat::where('id', $id)->where('pemilu_id', $pemilu->id)->first();
        if (!$kandidat) {
            return redirect()->back()->with('error', 'Kandidat yang dicari tidak ditemukan');
        }

        $voting = new Voting();
        $voting->pemilu_id = $pemilu->id;
        $voting->kandidat_id = $kandidat->id;
        $voting->user_id = $user->id;
        $voting->user_fullname = $user->fullname;
        $voting->class_name = $user->kelas ? $user->kelas->name : 'N/A';
        $voting->save();

        Session::remove('pemilu_' . $pemilu->slug . '_verified');

        $notification = new Notification();
        $notification->user_id = $user->id;
        $notification->pemilu_id = $pemilu->id;
        $notification->save();

        Alert::success('Berhasil', 'Terima kasih, suara Anda telah berhasil disimpan!');
        return redirect()->route('user.dashboard');
    }

    /**
     * Check whether voter is eligible to join/vote in private prodi or private ukm elections
     */
    protected function checkVoterEligibility($pemilu, $user): ?string
    {
        if ($user->role === 'admin') {
            return null;
        }

        if ($pemilu->is_private == 1) {
            $userProdiId = $user->kelas?->prodi_id;
            $allowedProdiIds = $pemilu->prodis->pluck('id')->toArray();

            if (!$userProdiId || !in_array($userProdiId, $allowedProdiIds)) {
                return 'Anda bukan mahasiswa dari Program Studi yang terdaftar dalam pemilihan ini.';
            }
        } elseif ($pemilu->is_private == 2) {
            $isUkmMember = $pemilu->ukm_id && $user->ukms()->where('ukms.id', $pemilu->ukm_id)->exists();

            if (!$isUkmMember) {
                return 'Anda bukan anggota dari ' . ($pemilu->ukm?->name ?? 'UKM ini') . '.';
            }
        }

        return null;
    }
}
