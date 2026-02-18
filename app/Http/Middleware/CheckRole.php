<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckRole
{
    /**
     * Handle middleware untuk check role user
     * 
     * @param Request $request
     * @param Closure $next
     * @param string ...$roles Role yang diizinkan (pisahkan dengan koma)
     * @return Response
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. CEK USER SUDAH LOGIN
        if (!Auth::check()) {
            Log::warning('Unauthorized access attempt - user not authenticated', [
                'path' => $request->path(),
                'ip' => $request->ip(),
                'timestamp' => now(),
            ]);
            return redirect('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = Auth::user();

        // 2. JIKA TIDAK ADA ROLE REQUIREMENT, IZINKAN AKSES
        if (empty($roles)) {
            return $next($request);
        }

        // 3. CEK APAKAH ROLE USER ADA DI DAFTAR ROLES YANG DIIZINKAN
        if (in_array($user->role, $roles, true)) {
            return $next($request);
        }

        // 4. LOG UNAUTHORIZED ACCESS
        Log::warning('Unauthorized access attempt - insufficient role', [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'required_roles' => $roles,
            'path' => $request->path(),
            'ip' => $request->ip(),
            'timestamp' => now(),
        ]);

        // 5. RETURN ERROR 403
        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }
}
