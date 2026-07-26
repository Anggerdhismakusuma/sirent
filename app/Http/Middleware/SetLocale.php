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
        $supportedLocales = ['en', 'id'];

        $locale = session('locale');

        if (! $locale && auth()->check()) {
            $locale = auth()->user()->language;
        }

        if (! in_array($locale, $supportedLocales, true)) {
            $locale = config('app.locale', 'en');
        }

        App::setLocale($locale);

        session()->put('locale', $locale);

        return $next($request);
    }
}
