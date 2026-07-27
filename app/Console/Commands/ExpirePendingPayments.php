<?php

namespace App\Console\Commands;

use App\Models\RentalRequest;
use Illuminate\Console\Command;

class ExpirePendingPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:expire-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel rental requests with pending payments older than 24 hours, freeing product availability.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $cutoff = now()->subHours(24);

        $expired = RentalRequest::where('payment_status', RentalRequest::PAYMENT_PENDING)
            ->whereNotNull('snap_token') // must have initiated Snap payment
            ->where('created_at', '<', $cutoff)
            ->update([
                'payment_status' => RentalRequest::PAYMENT_EXPIRED,
                'status'         => RentalRequest::STATUS_CANCELLED,
            ]);

        $this->info("Expired {$expired} pending payment(s) older than 24 hours.");

        \Illuminate\Support\Facades\Log::info('payments:expire-pending executed', [
            'expired_count' => $expired,
            'cutoff'        => $cutoff->toDateTimeString(),
        ]);

        return self::SUCCESS;
    }
}
