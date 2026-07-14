<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrderRevision;
use App\Services\OrderWorkflowService;
use Illuminate\Http\Request;

class AdminRevisionController extends Controller
{
    /**
     * Menampilkan seluruh permintaan revisi.
     */
    public function index()
    {
        $revisions = OrderRevision::with([
            'order',
            'requester',
            'forwarder',
            'submission.files'
        ])
        ->latest()
        ->get();

        return response()->json([
            'success' => true,
            'data' => $revisions
        ]);
    }

    /**
     * Admin meneruskan revisi ke freelancer.
     */
    public function forward(
        Request $request,
        OrderRevision $revision,
        OrderWorkflowService $workflow
    ) {
        $data = $request->validate([
            'admin_notes' => [
                'nullable',
                'string',
                'max:5000'
            ]
        ]);

        $workflow->forwardRevision(
            $revision,
            $request->user(),
            $data['admin_notes'] ?? null
        );

        return response()->json([
            'success' => true,
            'message' => 'Revisi berhasil diteruskan ke freelancer.'
        ]);
    }

    /**
     * Admin menolak revisi.
     */
    public function reject(
        Request $request,
        OrderRevision $revision,
        OrderWorkflowService $workflow
    ) {
        $data = $request->validate([
            'admin_notes' => [
                'required',
                'string',
                'max:5000'
            ]
        ]);

        $workflow->rejectRevision(
            $revision,
            $request->user(),
            $data['admin_notes']
        );

        return response()->json([
            'success' => true,
            'message' => 'Permintaan revisi ditolak.'
        ]);
    }
}