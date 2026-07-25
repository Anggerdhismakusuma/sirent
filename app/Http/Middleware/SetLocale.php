<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Set the application locale from session or user preference.
     * Must run AFTER StartSession and auth middleware.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = null;

        if (session()->has('locale')) {
            $locale = session()->get('locale');
        } elseif (auth()->check() && auth()->user()->language) {
            $locale = auth()->user()->language;
            session()->put('locale', $locale);
        }

        if ($locale) {
            App::setLocale($locale);
            Carbon::setLocale($locale);
        }

        return $next($request);
    }
}
