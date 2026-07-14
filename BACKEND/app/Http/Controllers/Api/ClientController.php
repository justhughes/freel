<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderWorkflowService;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function show(Request $request, Order $order)
    {
        abort_unless($order->client_id === $request->user()->id, 403);

        $order->load([
            'package.category',
            'freelancer',
            'assets',
            'latestPayment.method',
            'latestPayment.channel',
            'currentSubmission.files',
            'submissions.files',
            'revisions.submission.files',
            'revisions.resultSubmission.files',
            'revisions.requester',
            'revisions.forwarder',
        ]);

        return response()->json([
            'success' => true,
            'data' => $order,
        ]);
    }

    public function requestRevision(
        Request $request,
        Order $order,
        OrderWorkflowService $workflow
    ) {
        abort_unless($order->client_id === $request->user()->id, 403);

        $data = $request->validate([
            'notes' => ['required', 'string', 'max:5000'],
        ]);

        $revision = $workflow->requestRevision(
            $order,
            $request->user(),
            $data['notes']
        );

        return response()->json([
            'success' => true,
            'message' => 'Permintaan revisi berhasil dikirim.',
            'data' => $revision,
        ]);
    }

    public function approve(
        Request $request,
        Order $order,
        OrderWorkflowService $workflow
    ) {
        abort_unless($order->client_id === $request->user()->id, 403);

        $order = $workflow->approveResult($order, $request->user());

        return response()->json([
            'success' => true,
            'message' => 'Pesanan selesai.',
            'data' => $order,
        ]);
    }
}
