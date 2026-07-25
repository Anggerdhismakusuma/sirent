<?php

namespace App\Http\Controllers\Borrower;

use App\Http\Controllers\Controller;
use App\Models\Dispute;
use App\Models\RentalRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StoreDisputeController extends Controller
{
    public function store(
        Request $request,
        RentalRequest $rentalRequest
    ): RedirectResponse {
        $user = $request->user();

        abort_unless($user->is_owner_active, 403);

        $rentalRequest->load('product');

        /*
         * Sesuaikan owner_id dengan foreign key pada tabel products.
         * Kalau produk menggunakan user_id, ganti menjadi:
         * $rentalRequest->product?->user_id
         */
        abort_unless(
            (int) $rentalRequest->product?->owner_id === (int) $user->id,
            403
        );

        $status = strtoupper((string) $rentalRequest->status);

        if (!in_array(
            $status,
            ['APPROVED', 'ONGOING', 'COMPLETED'],
            true
        )) {
            return back()->withErrors([
                'dispute' =>
                    'Transaksi ini tidak memenuhi syarat untuk mengajukan dispute.',
            ]);
        }

        $hasActiveDispute = Dispute::query()
            ->where('rental_request_id', $rentalRequest->id)
            ->whereIn('status', ['open', 'in_review'])
            ->exists();

        if ($hasActiveDispute) {
            return back()->withErrors([
                'dispute' =>
                    'Transaksi ini sudah memiliki dispute yang sedang diproses.',
            ]);
        }

        $validated = $request->validate([
            'reason' => [
                'required',
                'string',
                'min:20',
                'max:1000',
            ],

            'evidence' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,webp,pdf',
                'max:4096',
            ],
        ]);

        $evidencePath = null;

        if ($request->hasFile('evidence')) {
            $evidencePath = $request
                ->file('evidence')
                ->store('dispute-evidence', 'public');
        }

        Dispute::create([
            'rental_request_id' => $rentalRequest->id,
            'reporter_id' => $user->id,
            'reason' => $validated['reason'],
            'evidence' => $evidencePath,
            'status' => 'open',

            // Akan diisi admin
            'resolution' => null,
            'handled_by' => null,
            'resolved_at' => null,
        ]);

        return back()->with(
            'success',
            'Dispute berhasil diajukan dan menunggu pemeriksaan admin.'
        );
    }
}