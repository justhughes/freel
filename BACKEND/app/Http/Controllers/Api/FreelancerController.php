<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FreelancerSkill;
use App\Models\Order;
use App\Services\OrderWorkflowService;
use Illuminate\Http\Request;

class FreelancerController extends Controller
{
    private function jobBoardQuery($user)
    {
        $categoryIds = FreelancerSkill::query()
            ->where('freelancer_id', $user->id)
            ->where('status', FreelancerSkill::STATUS_APPROVED)
            ->pluck('service_category_id');

        return Order::query()
            ->jobBoard()
            ->whereHas(
                'package',
                fn ($query) => $query->whereIn('service_category_id', $categoryIds)
            );
    }

    public function jobs(Request $request)
    {
        $orders = $this->jobBoardQuery($request->user())
            ->with(['client', 'package.category', 'assets'])
            ->orderBy('deadline_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    public function take(
        Request $request,
        Order $order,
        OrderWorkflowService $workflow
    ) {
        $order = $workflow->takeJob($order, $request->user());

        return response()->json([
            'success' => true,
            'message' => 'Job berhasil diambil.',
            'data' => $order,
        ]);
    }

    public function tasks(Request $request)
    {
        $orders = Order::query()
            ->forFreelancer($request->user()->id)
            ->with([
                'client',
                'package.category',
                'assets',
                'currentSubmission.files',
                'submissions.files',
                'activeRevision',
                'freelancerRevisions.submission.files',
                'freelancerRevisions.resultSubmission.files',
            ])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    public function showTask(Request $request, Order $order)
    {
        abort_unless($order->freelancer_id === $request->user()->id, 403);

        $order->load([
            'client',
            'package.category',
            'assets',
            'currentSubmission.files',
            'submissions.files',
            'activeRevision',
            'freelancerRevisions.submission.files',
            'freelancerRevisions.resultSubmission.files',
        ]);

        return response()->json([
            'success' => true,
            'data' => $order,
        ]);
    }

    public function submit(
        Request $request,
        Order $order,
        OrderWorkflowService $workflow
    ) {
        abort_unless($order->freelancer_id === $request->user()->id, 403);

        $data = $request->validate([
            'result_file' => [
                'required',
                'file',
                'max:51200',
                'mimes:jpg,jpeg,png,pdf,zip,mp4,mov,doc,docx',
            ],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $submission = $workflow->submitWork(
            $order,
            $request->user(),
            $request->file('result_file'),
            $data['notes'] ?? null
        );

        return response()->json([
            'success' => true,
            'message' => 'Hasil pekerjaan berhasil diupload dan menunggu review klien.',
            'data' => $submission,
        ], 201);
    }
}
