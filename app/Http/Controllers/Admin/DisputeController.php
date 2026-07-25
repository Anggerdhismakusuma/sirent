<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dispute;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DisputeController extends Controller
{
    public function index(Request $request): View
    {
        $disputes = Dispute::query()
            ->with([
                'reporter',
                'handler',
                'rentalRequest.borrower',
                'rentalRequest.owner',
                'rentalRequest.product',
            ])
            ->when(
                $request->filled('status'),
                fn ($query) => $query->where(
                    'status',
                    $request->status
                )
            )
            ->oldest('created_at')
            ->paginate(15)
            ->withQueryString();

        return view(
            'admin.disputes.index',
            compact('disputes')
        );
    }

    public function approve(
        Request $request,
        Dispute $dispute
    ): RedirectResponse {
        $validated = $request->validate([
            'resolution' => [
                'required',
                'string',
                'max:3000',
            ],
        ]);

        abort_if(
            in_array(
                $dispute->status,
                [
                    Dispute::STATUS_RESOLVED,
                    Dispute::STATUS_REJECTED,
                ],
                true
            ),
            422,
            'This dispute has already been processed.'
        );

        $dispute->update([
            'status' => Dispute::STATUS_RESOLVED,
            'resolution' => $validated['resolution'],
            'handled_by' => $request->user()->id,
            'resolved_at' => now(),
        ]);

        return back()->with(
            'success',
            'The dispute claim has been approved.'
        );
    }

    public function reject(
        Request $request,
        Dispute $dispute
    ): RedirectResponse {
        $validated = $request->validate([
            'resolution' => [
                'required',
                'string',
                'max:3000',
            ],
        ]);

        abort_if(
            in_array(
                $dispute->status,
                [
                    Dispute::STATUS_RESOLVED,
                    Dispute::STATUS_REJECTED,
                ],
                true
            ),
            422,
            'This dispute has already been processed.'
        );

        $dispute->update([
            'status' => Dispute::STATUS_REJECTED,
            'resolution' => $validated['resolution'],
            'handled_by' => $request->user()->id,
            'resolved_at' => now(),
        ]);

        return back()->with(
            'success',
            'The dispute claim has been rejected.'
        );
    }
}