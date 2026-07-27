<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── Scheduled Tasks ──
Schedule::command('payments:expire-pending')
    ->hourly()
    ->withoutOverlapping()
    ->runInBackground();

Schedule::command('rentals:auto-reject-expired')
    ->dailyAt('23:59')
    ->withoutOverlapping()
    ->runInBackground();
