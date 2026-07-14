<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Silakan login terlebih dahulu.',
                ], 401);
            }

            return redirect()->route('login');
        }

        if (! in_array($user->role, $roles, true)) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kamu tidak memiliki akses untuk fitur ini.',
                ], 403);
            }

            abort(403, 'Kamu tidak memiliki akses ke halaman ini.');
        }

        if (! $user->isActive()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun belum aktif atau sedang dinonaktifkan.',
                ], 403);
            }

            auth()->logout();

            return redirect()->route('login')->withErrors([
                'login' => 'Akun belum aktif atau sedang dinonaktifkan.',
            ]);
        }

        return $next($request);
    }
}
