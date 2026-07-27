<?php

namespace App\Console\Commands;

use App\Models\RentalRequest;
use App\Notifications\RentalRequestStatusChanged;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoRejectExpiredRentals extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'rentals:auto-reject-expired';

    /**
     * The console command description.
     */
    protected $description = 'Auto-reject pending rental requests whose start date has passed without owner approval.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $today = now()->startOfDay();

        // Find all pending rental requests where the start date is today or earlier
        // (i.e. the rental was supposed to start but was never approved by the owner)
        $expired = RentalRequest::with(['product', 'borrower'])
            ->where('status', RentalRequest::STATUS_PENDING)
            ->where('start_date', '<=', $today->toDateString())
            ->get();

        if ($expired->isEmpty()) {
            $this->info('No expired pending rental requests found.');
            Log::info('rentals:auto-reject-expired — none found');

            return self::SUCCESS;
        }

        $count = 0;

        foreach ($expired as $rental) {
            $productName = $rental->product?->title ?? 'Unknown Product';

            // Auto-reject the rental
            $rental->update([
                'status'           => RentalRequest::STATUS_REJECTED,
                'rejection_reason' => 'Permintaan sewa otomatis ditolak karena pemilik tidak merespons tepat waktu.',
            ]);

            // Notify the borrower
            if ($rental->borrower) {
                $rental->borrower->notify(new RentalRequestStatusChanged(
                    rentalId: $rental->id,
                    productName: $productName,
                    status: 'rejected',
                    reason: __('ui.notif_rental_auto_rejected', ['product' => $productName]),
                ));
            }

            $this->line("Auto-rejected rental #{$rental->id} — \"{$productName}\" (borrower: {$rental->borrower?->name})");
            $count++;
        }

        $this->info("Auto-rejected {$count} expired pending rental request(s).");

        Log::info('rentals:auto-reject-expired executed', [
            'rejected_count' => $count,
            'cutoff_date'    => $today->toDateString(),
        ]);

        return self::SUCCESS;
    }
}
