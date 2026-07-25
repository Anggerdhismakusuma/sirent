<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rental_requests', function (Blueprint $table) {
            // ── Canonical order reference for Midtrans lookups ──
            $table->string('order_ref', 50)
                  ->unique()
                  ->nullable()
                  ->after('id')
                  ->comment('Unique Midtrans order_id. Format: SIRENT-{uniqid}-{ts}');

            // ── Quantity (fixes the quantity bug — was sent but never stored) ──
            $table->integer('quantity')
                  ->default(1)
                  ->after('total_days');

            // ── Payment tracking ──
            $table->string('payment_status', 20)
                  ->default('pending')
                  ->after('status')
                  ->comment('pending|paid|expired|failed|refunded');

            $table->string('payment_method', 50)
                  ->nullable()
                  ->after('payment_status');

            $table->text('snap_token')
                  ->nullable()
                  ->after('payment_method');

            $table->string('transaction_id', 100)
                  ->nullable()
                  ->after('snap_token')
                  ->comment('Midtrans transaction_id from webhook');

            $table->timestamp('paid_at')
                  ->nullable()
                  ->after('completed_at');

            // ── Index for scheduled cleanup of expired payments ──
            $table->index(['payment_status', 'created_at'], 'idx_payment_expired_cleanup');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rental_requests', function (Blueprint $table) {
            $table->dropIndex('idx_payment_expired_cleanup');
            $table->dropColumn([
                'order_ref',
                'quantity',
                'payment_status',
                'payment_method',
                'snap_token',
                'transaction_id',
                'paid_at',
            ]);
        });
    }
};
