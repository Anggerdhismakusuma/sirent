<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfGuest
{
    /**
     * Instead of redirecting to a /login page, redirect back
     * with a query param that JS reads to open the auth modal.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('ui.please_login'),
                    'require_auth' => true,
                ], 401);
            }

            // Redirect to the intended URL with auth=login trigger
            $currentUrl = $request->fullUrl();
            $separator = str_contains($currentUrl, '?') ? '&' : '?';
            return redirect($currentUrl . $separator . 'auth=login');
        }

        return $next($request);
    }
}
