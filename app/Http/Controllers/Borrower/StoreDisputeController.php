<?php

namespace App\Http\Controllers\Borrower;

use App\Http\Controllers\Controller;
use App\Models\Dispute;
use App\Models\RentalRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Notifications\DisputeStatusChanged;
use App\Notifications\DisputeStatusChanged;
use App\Notifications\DisputeStatusChanged;
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

        if (
            $rentalRequest->status !==
            RentalRequest::STATUS_COMPLETED
        ) {
            return back()->withErrors([
                'dispute' => __('ui.dispute_only_after_completed'),
            ]);
        }

        $hasExistingDispute = Dispute::query()
            ->where('rental_request_id', $rentalRequest->id)
            ->exists();

        if ($hasExistingDispute) {
            return back()->withErrors([
                'dispute' =>
                    'Transaksi ini sudah pernah memiliki dispute dan tidak dapat diajukan kembali.',
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

        $dispute = Dispute::create([
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

        $productName = $rentalRequest->product?->title ?? 'Unknown Product';

        // Notify the reporter (store owner) that the dispute has been submitted
        $user->notify(new DisputeStatusChanged(
            disputeId: $dispute->id,
            productName: $productName,
            status: 'submitted',
        ));

        // Notify the borrower that they received a dispute
        $borrower = $rentalRequest->borrower;
        if ($borrower) {
            $borrower->notify(new DisputeStatusChanged(
                disputeId: $dispute->id,
                productName: $productName,
                status: 'received',
            ));
        }

        return back()->with(
            'success',
            'Dispute berhasil diajukan dan menunggu pemeriksaan admin.'
        );
    }

    public function destroy(
            Request $request,
            Dispute $dispute
        ): RedirectResponse {
            $user = $request->user();

            abort_unless(
                $user->is_owner_active,
                403,
                'Store kamu belum aktif.'
            );

            $evidencePath = null;

            DB::transaction(function () use (
                $user,
                $dispute,
                &$evidencePath
            ): void {
                /*
                * Ambil ulang dan lock row untuk mencegah race condition
                * dengan proses approve/reject oleh admin.
                */
                $lockedDispute = Dispute::query()
                    ->with('rentalRequest')
                    ->lockForUpdate()
                    ->findOrFail($dispute->id);

                /*
                * Harus memenuhi dua kondisi:
                * 1. User merupakan reporter dispute.
                * 2. User merupakan owner dari transaksi tersebut.
                *
                * Ini mencegah dispute milik borrower dibatalkan melalui
                * endpoint khusus store.
                */
                abort_unless(
                    (int) $lockedDispute->reporter_id === (int) $user->id
                    && (int) $lockedDispute->rentalRequest?->owner_id
                        === (int) $user->id,
                    403,
                    'Kamu tidak berhak membatalkan dispute ini.'
                );

                if (! in_array(
                    $lockedDispute->status,
                    [
                        Dispute::STATUS_OPEN,
                        Dispute::STATUS_IN_REVIEW,
                    ],
                    true
                )) {
                    throw ValidationException::withMessages([
                        'dispute' =>
                            'Dispute yang sudah selesai diproses tidak dapat dibatalkan.',
                    ]);
                }

                $evidencePath = $lockedDispute->evidence;

                /*
                * Delete row, bukan mengubah status.
                * Setelah row hilang, transaksi dapat mengajukan dispute lagi.
                */
                $lockedDispute->delete();
            });

            /*
            * Hapus evidence setelah transaksi database berhasil.
            */
            if ($evidencePath) {
                Storage::disk('public')->delete($evidencePath);
            }

            return back()->with(
                'success',
                'Dispute berhasil dibatalkan.'
            );
        }
}