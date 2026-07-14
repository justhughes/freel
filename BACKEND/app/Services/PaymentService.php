<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentChannel;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    public function __construct(private readonly CodeGenerator $codes)
    {
    }

    public function createPendingPayment(Order $order, PaymentChannel $channel): Payment
    {
        $channel->loadMissing('method');

        if (! $channel->is_active || ! $channel->method?->is_active) {
            throw ValidationException::withMessages([
                'payment_channel_id' => 'Metode pembayaran sedang tidak tersedia.',
            ]);
        }

        $attempt = ((int) $order->payments()->max('attempt_number')) + 1;

        return $order->payments()->create([
            'payment_code' => $this->codes->payment(),
            'payment_method_id' => $channel->payment_method_id,
            'payment_channel_id' => $channel->id,
            'attempt_number' => $attempt,
            'amount' => $order->total_amount,
            'status' => Payment::STATUS_PENDING,
            'expires_at' => now()->addDay(),
            'metadata' => [
                'source' => 'web-backend-simulation',
                'note' => 'API payment gateway akan dilengkapi oleh bagian integrasi.',
            ],
        ]);
    }

    public function markAsPaid(Payment $payment, ?string $gatewayReference = null): Payment
    {
        return DB::transaction(function () use ($payment, $gatewayReference) {
            $locked = Payment::query()->lockForUpdate()->findOrFail($payment->id);

            if ($locked->status === Payment::STATUS_PAID) {
                return $locked;
            }

            if ($locked->status !== Payment::STATUS_PENDING) {
                throw ValidationException::withMessages([
                    'payment' => 'Pembayaran yang bukan pending tidak dapat ditandai lunas.',
                ]);
            }

            if ($locked->expires_at && now()->isAfter($locked->expires_at)) {
                $locked->update([
                    'status' => Payment::STATUS_EXPIRED,
                    'failure_reason' => 'Masa pembayaran sudah berakhir.',
                ]);

                throw ValidationException::withMessages([
                    'payment' => 'Masa pembayaran sudah berakhir. Buat transaksi pembayaran baru.',
                ]);
            }

            $order = Order::query()->lockForUpdate()->findOrFail($locked->order_id);

            $locked->update([
                'status' => Payment::STATUS_PAID,
                'gateway_reference' => $gatewayReference,
                'paid_at' => now(),
            ]);

            if ($order->status === Order::STATUS_PENDING_PAYMENT) {
                $order->update([
                    'status' => Order::STATUS_QUEUE,
                    'paid_at' => now(),
                ]);
            }

            return $locked->fresh(['order', 'method', 'channel']);
        });
    }

    public function markProblem(Payment $payment, string $reason): Payment
    {
        $payment->update([
            'status' => Payment::STATUS_PROBLEM,
            'failure_reason' => $reason,
        ]);

        $payment->order()->update(['status' => Order::STATUS_PROBLEM]);

        return $payment->fresh();
    }
}
