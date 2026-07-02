<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Set the application locale from session or user preference.
     * Must run AFTER StartSession and auth middleware.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (session()->has('locale')) {
            App::setLocale(session()->get('locale'));
        } elseif (auth()->check() && auth()->user()->language) {
            App::setLocale(auth()->user()->language);
            session()->put('locale', auth()->user()->language);
        }

        return $next($request);
    }
}
