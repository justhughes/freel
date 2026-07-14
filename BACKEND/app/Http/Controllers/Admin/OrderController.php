<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderAsset;
use App\Models\SubmissionFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = Order::query()->with([
            'client',
            'package.category',
            'freelancer',
            'latestPayment',
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        $orders = $query->latest()->get();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $order->load([
            'client',
            'package.category',
            'freelancer',
            'assets',
            'payments.method',
            'payments.channel',
            'assignments.freelancer',
            'submissions.files',
            'revisions.requester',
            'revisions.forwarder',
            'revisions.submission.files',
            'revisions.resultSubmission.files',
        ]);

        return view('admin.orders.show', compact('order'));
    }

    public function downloadAsset(Order $order, OrderAsset $asset): StreamedResponse
    {
        abort_unless($asset->order_id === $order->id, 404);

        return Storage::disk('public')->download(
            $asset->file_path,
            $asset->original_name
        );
    }

    public function downloadResult(Order $order, SubmissionFile $file): StreamedResponse
    {
        abort_unless($file->submission?->order_id === $order->id, 404);

        return Storage::disk('public')->download(
            $file->file_path,
            $file->original_name
        );
    }
}
