<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (
            $user &&
            $user->account_status === User::ACCOUNT_SUSPENDED
        ) {
            $message = 'Akun Anda sedang disuspend oleh administrator SI-RENT. '
                .'Anda tidak dapat melakukan pemesanan atau menggunakan fitur akun.';

            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Untuk request AJAX/fetch
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'code' => 'ACCOUNT_SUSPENDED',
                    'message' => $message,
                ], 403);
            }

            // Untuk request halaman/form biasa
            return redirect()
                ->route('home')
                ->with('account_suspended', $message);
        }

        return $next($request);
    }
}