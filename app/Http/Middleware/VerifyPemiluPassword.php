<?php

namespace App\Http\Middleware;

use App\Models\Pemilu;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyPemiluPassword
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $slug = $request->route('slug');
        $pemilu = Pemilu::with('prodis')->where('slug', $slug)->first();

        if ($pemilu && $pemilu->is_private && auth()->user()->role != 'admin') {
            $user = auth()->user();

            if ($pemilu->is_private == 1) {
                $userProdiId = $user->kelas?->prodi_id;
                $allowedProdiIds = $pemilu->prodis->pluck('id')->toArray();

                if (!$userProdiId || !in_array($userProdiId, $allowedProdiIds)) {
                    return redirect()->route('user.dashboard')->with('error', 'Pemilihan ini khusus untuk Program Studi tertentu.');
                }
            } elseif ($pemilu->is_private == 2) {
                $isUkmMember = $pemilu->ukm_id && $user->ukms()->where('ukms.id', $pemilu->ukm_id)->exists();

                if (!$isUkmMember) {
                    return redirect()->route('user.dashboard')->with('error', 'Pemilihan ini khusus untuk anggota UKM terkait.');
                }
            }

            if ($pemilu->password && !$request->session()->has('pemilu_' . $pemilu->slug . '_verified')) {
                return redirect()->route('user.dashboard')->with('error', 'Password diperlukan untuk mengakses pemilu ini');
            }
        }

        return $next($request);
    }
}
