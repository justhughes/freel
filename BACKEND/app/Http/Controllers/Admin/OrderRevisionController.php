<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderRevision;
use App\Services\OrderWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderRevisionController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();

        $query = OrderRevision::query()
            ->with([
                'order.client',
                'order.freelancer',
                'order.package.category',
                'submission.files',
                'resultSubmission.files',
                'requester',
                'forwarder',
            ])
            ->latest('requested_at');

        if ($status !== '') {
            $query->where('status', $status);
        }

        $revisions = $query->get();

        return view('admin.revisions.index', compact('revisions', 'status'));
    }

    public function forward(
        Request $request,
        OrderRevision $revision,
        OrderWorkflowService $workflow
    ): RedirectResponse {
        $data = $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $workflow->forwardRevision(
            $revision,
            $request->user(),
            $data['admin_notes'] ?? null
        );

        return back()->with(
            'success',
            'Catatan revisi sudah diteruskan kepada freelancer.'
        );
    }

    public function reject(
        Request $request,
        OrderRevision $revision,
        OrderWorkflowService $workflow
    ): RedirectResponse {
        $data = $request->validate([
            'admin_notes' => ['required', 'string', 'max:5000'],
        ]);

        $workflow->rejectRevision(
            $revision,
            $request->user(),
            $data['admin_notes']
        );

        return back()->with(
            'success',
            'Pengajuan revisi ditolak dan pesanan dikembalikan ke tahap review.'
        );
    }
}
