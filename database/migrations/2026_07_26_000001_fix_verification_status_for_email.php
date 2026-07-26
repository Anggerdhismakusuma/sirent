<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Users with verified email → set verification_status = 'verified'
        DB::table('users')
            ->whereNotNull('email_verified_at')
            ->where('verification_status', '!=', 'verified')
            ->update(['verification_status' => 'verified']);

        // 2. Users who were 'verified' via WhatsApp magic link but have no email_verified_at
        //    → preserve whatsapp_verified_at, revert verification_status
        DB::table('users')
            ->where('verification_status', 'verified')
            ->whereNull('email_verified_at')
            ->update([
                'whatsapp_verified_at' => DB::raw('COALESCE(whatsapp_verified_at, NOW())'),
                'verification_status' => 'unverified',
            ]);

        // 3. Users who uploaded KTP and are 'pending' but have verified email → promote to 'verified'
        DB::table('users')
            ->where('verification_status', 'pending')
            ->whereNotNull('email_verified_at')
            ->update(['verification_status' => 'verified']);
    }

    public function down(): void
    {
        // Revert is not meaningful for this migration — verification_status
        // was derived from a combination of email/WhatsApp/KTP states.
    }
};
