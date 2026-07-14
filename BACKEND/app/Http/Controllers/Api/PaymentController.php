<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentChannel;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(private readonly PaymentService $paymentService)
    {
    }

    private function ensureOwner(Request $request, Order $order): void
    {
        abort_unless($order->client_id === $request->user()->id, 403);
    }

    public function show(Request $request, Order $order)
    {
        $this->ensureOwner($request, $order);
        $payment = $order->latestPayment;

        if (! $payment) {
            return response()->json([
                'success' => false,
                'message' => 'Belum ada pembayaran.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $payment->load('method', 'channel'),
        ]);
    }

    public function store(Request $request, Order $order)
    {
        $this->ensureOwner($request, $order);

        $data = $request->validate([
            'payment_channel_id' => ['required', 'integer', 'exists:payment_channels,id'],
        ]);

        $channel = PaymentChannel::query()->findOrFail($data['payment_channel_id']);
        $payment = $this->paymentService->createPendingPayment($order, $channel);

        return response()->json([
            'success' => true,
            'message' => 'Payment berhasil dibuat.',
            'data' => $payment->load('method', 'channel'),
        ], 201);
    }

    public function uploadProof(Request $request, Payment $payment)
    {
        abort_unless($payment->order?->client_id === $request->user()->id, 403);

        $request->validate([
            'proof' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        $path = $request->file('proof')->store('payment-proofs', 'public');
        $meta = $payment->metadata ?? [];
        $meta['proof'] = $path;

        $payment->update([
            'proof_file' => $path,
            'metadata' => $meta,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bukti pembayaran berhasil diupload.',
            'data' => [
                'payment_id' => $payment->id,
                'proof' => $path,
            ],
        ]);
    }

    public function verify(Request $request, Payment $payment)
    {
        abort_unless($payment->order?->client_id === $request->user()->id, 403);

        $payment = $this->paymentService->markAsPaid(
            $payment,
            'SIM-'.now()->format('YmdHis').'-'.$payment->id
        );

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil diverifikasi.',
            'data' => $payment,
        ]);
    }
}
