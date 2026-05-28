<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AutoLogout
{
    /**
     * Logout otomatis jika admin tidak aktif selama N menit.
     * Waktu timeout diambil dari config desa.auto_logout_menit.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $timeoutMenit = intval(\App\Models\Setting::get('auto_logout_menit', 30));
        $lastActivity = session('last_activity_at');

        if ($lastActivity && now()->diffInMinutes($lastActivity) >= $timeoutMenit) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                return response()->json([
                    'message'      => 'Sesi Anda telah berakhir karena tidak aktif.',
                    'redirect_url' => route('admin.login'),
                ], 401);
            }

            return redirect()->route('admin.login')
                ->withErrors(['timeout' => 'Sesi Anda telah berakhir karena tidak aktif selama ' . $timeoutMenit . ' menit.']);
        }

        // Perbarui timestamp aktivitas terakhir
        session(['last_activity_at' => now()]);

        return $next($request);
    }
}
