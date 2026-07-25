<?php

namespace App\Http\Controllers\Borrower;

use App\Http\Controllers\Controller;
use App\Models\RentalRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StoreRentalRequestController extends Controller
{
    public function approve(
        Request $request,
        RentalRequest $rentalRequest
    ): RedirectResponse {
        $owner = $request->user();

        abort_unless(
            $owner->is_owner_active,
            403,
            'Store kamu belum aktif.'
        );

        DB::transaction(function () use ($owner, $rentalRequest): void {
            $lockedRental = RentalRequest::query()
                ->lockForUpdate()
                ->findOrFail($rentalRequest->id);

            abort_unless(
                (int) $lockedRental->owner_id === (int) $owner->id,
                403,
                'Rental request ini bukan milik store kamu.'
            );

            if ($lockedRental->status !== RentalRequest::STATUS_PENDING) {
                throw ValidationException::withMessages([
                    'rental_request' =>
                        'Rental request ini sudah diproses sebelumnya.',
                ]);
            }

            /*
             * Validasi ulang saat approval.
             *
             * Request dapat dibuat ketika slot masih tersedia, tetapi owner
             * mungkin sudah menyetujui request lain untuk periode yang sama
             * sebelum request ini diproses.
             */
            $conflictingRental = RentalRequest::query()
                ->where('product_id', $lockedRental->product_id)
                ->where('id', '!=', $lockedRental->id)
                ->whereIn('status', [
                    RentalRequest::STATUS_APPROVED,
                    RentalRequest::STATUS_ONGOING,
                ])
                ->whereDate('start_date', '<=', $lockedRental->end_date)
                ->whereDate('end_date', '>=', $lockedRental->start_date)
                ->lockForUpdate()
                ->first();

            if ($conflictingRental) {
                throw ValidationException::withMessages([
                    'rental_request' =>
                        'Tidak dapat disetujui karena periode ini bentrok '
                        . 'dengan peminjaman lain yang sudah aktif.',
                ]);
            }

            $lockedRental->update([
                'status' => RentalRequest::STATUS_APPROVED,
                'approved_at' => now(),
                'rejection_reason' => null,
            ]);
        });

        return redirect()
            ->route('borrower.dashboard', ['tab' => 'store'])
            ->with(
                'success',
                'Permintaan peminjaman berhasil disetujui.'
            );
    }

    public function reject(
        Request $request,
        RentalRequest $rentalRequest
    ): RedirectResponse {
        $validated = $request->validate([
            'rejection_reason' => [
                'required',
                'string',
                'max:500',
            ],
        ]);

        $owner = $request->user();

        abort_unless(
            $owner->is_owner_active,
            403,
            'Store kamu belum aktif.'
        );

        DB::transaction(function () use (
            $owner,
            $rentalRequest,
            $validated
        ): void {
            $lockedRental = RentalRequest::query()
                ->lockForUpdate()
                ->findOrFail($rentalRequest->id);

            abort_unless(
                (int) $lockedRental->owner_id === (int) $owner->id,
                403,
                'Rental request ini bukan milik store kamu.'
            );

            if ($lockedRental->status !== RentalRequest::STATUS_PENDING) {
                throw ValidationException::withMessages([
                    'rental_request' =>
                        'Rental request ini sudah diproses sebelumnya.',
                ]);
            }

            $lockedRental->update([
                'status' => RentalRequest::STATUS_REJECTED,
                'rejection_reason' => $validated['rejection_reason'],
                'approved_at' => null,
            ]);
        });

        return redirect()
            ->route('borrower.dashboard', ['tab' => 'store'])
            ->with(
                'success',
                'Permintaan peminjaman berhasil ditolak.'
            );
    }

    public function history(Request $request)
    {
        $user = $request->user();

        abort_unless($user->is_owner_active, 403);

        $status = $request->input('status', 'all');
        $month = $request->input('month');

        $transactions = RentalRequest::query()
            ->with([
                'product.primaryImage',
                'borrower',
                'activeDispute',
            ])
            ->whereHas('product', function ($query) use ($user) {
                /*
                * Sesuaikan dengan kolom owner pada tabel products.
                * Kalau products menggunakan user_id, ganti owner_id menjadi user_id.
                */
                $query->where('owner_id', $user->id);
            })
            ->whereIn('status', [
                'approved',
                'ongoing',
                'completed',
                'cancelled',
            ])
            ->when(
                $status !== 'all',
                fn ($query) => $query->where('status', $status)
            )
            ->when($month, function ($query, $month) {
                [$year, $monthNumber] = array_pad(
                    explode('-', $month),
                    2,
                    null
                );

                if ($year && $monthNumber) {
                    $query
                        ->whereYear('start_date', $year)
                        ->whereMonth('start_date', $monthNumber);
                }
            })
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString();

        return view(
            'borrower.store.transactions',
            compact('transactions', 'status', 'month')
        );
    }
}
