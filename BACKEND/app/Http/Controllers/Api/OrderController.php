<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderAsset;
use App\Models\Payment;
use App\Models\PaymentChannel;
use App\Models\ServicePackage;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use App\Services\CodeGenerator;
use App\Services\OrderPricingService;
use App\Services\PaymentService;
use App\Services\ProductionSlotService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::query()
            ->with([
                'client',
                'package.category',
                'freelancer',
                'latestPayment.method',
                'latestPayment.channel',
                'assets',
                'currentSubmission.files',
                'submissions.files',
                'activeRevision',
                'revisions.resultSubmission.files',
            ])
            ->latest();

        if ($request->user()->isClient()) {
            $query->forClient($request->user()->id);
        } elseif (! $request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Kamu tidak memiliki akses ke daftar pesanan ini.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Daftar order berhasil diambil.',
            'data' => $query->get(),
        ]);
    }

    public function show(Request $request, Order $order)
    {
        $user = $request->user();

        if (! $user->isAdmin() && $order->client_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Order tidak ditemukan.',
            ], 404);
        }

        $order->load([
            'client',
            'package.category',
            'freelancer',
            'latestPayment.method',
            'latestPayment.channel',
            'assets',
            'currentSubmission.files',
            'submissions.files',
            'activeRevision',
            'revisions.submission.files',
            'revisions.resultSubmission.files',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Detail order berhasil diambil.',
            'data' => $order,
        ]);
    }

    public function store(
        Request $request,
        CodeGenerator $codes,
        OrderPricingService $pricing,
        ProductionSlotService $slots,
        PaymentService $payments
    ) {
        $data = $request->validate([
            'service_package_id' => ['required', 'integer', 'exists:service_packages,id'],
            'title' => ['required', 'string', 'max:180'],
            'business_name' => ['required', 'string', 'max:120'],
            'product_description' => ['required', 'string', 'max:15000'],
            'target_audience' => ['nullable', 'string', 'max:3000'],
            'visual_reference' => ['nullable', 'string', 'max:255'],
            'brief' => ['nullable', 'string', 'max:15000'],
            'platform' => ['nullable', 'string', 'max:50'],
            'content_size' => ['nullable', 'string', 'max:50'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
            'speed_type' => ['required', Rule::in($pricing->validSpeedTypes())],
            'booking_date' => ['required', 'date', 'after_or_equal:today'],
            'voucher_code' => ['nullable', 'string', 'max:30'],
            'payment_channel_id' => ['required', 'integer', 'exists:payment_channels,id'],
            'assets' => ['nullable', 'array', 'max:10'],
            'assets.*' => [
                'file',
                'max:51200',
                'mimes:jpg,jpeg,png,pdf,zip,mp4,mov,doc,docx',
            ],
        ]);

        $package = ServicePackage::query()
            ->where('is_active', true)
            ->findOrFail($data['service_package_id']);

        $voucher = null;

        if (! empty($data['voucher_code'])) {
            $voucher = Voucher::query()
                ->whereRaw('UPPER(code) = ?', [strtoupper(trim($data['voucher_code']))])
                ->first();

            if (! $voucher) {
                throw ValidationException::withMessages([
                    'voucher_code' => 'Voucher tidak ditemukan.',
                ]);
            }
        }

        $bookingDate = Carbon::parse($data['booking_date']);
        $quote = $pricing->calculate(
            $package,
            (int) $data['quantity'],
            $data['speed_type'],
            $voucher
        );

        if ($voucher && ! $voucher->isUsableFor($quote['subtotal'])) {
            throw ValidationException::withMessages([
                'voucher_code' => 'Voucher tidak aktif, sudah habis, atau belum memenuhi minimum transaksi.',
            ]);
        }

        $slot = $slots->reserve($package, $bookingDate);
        $deadline = $pricing->deadline($package, $data['speed_type'], $bookingDate);
        $channel = PaymentChannel::query()->findOrFail($data['payment_channel_id']);
        $storedPaths = [];

        try {
            [$order, $payment] = DB::transaction(function () use (
                $request,
                $data,
                $codes,
                $package,
                $voucher,
                $quote,
                $slot,
                $deadline,
                $bookingDate,
                $channel,
                $payments,
                &$storedPaths
            ) {
                $order = Order::query()->create([
                    'order_code' => $codes->order(),
                    'client_id' => $request->user()->id,
                    'service_package_id' => $package->id,
                    'production_slot_id' => $slot->id,
                    'voucher_id' => $voucher?->id,
                    'title' => $data['title'],
                    'business_name' => $data['business_name'],
                    'product_description' => $data['product_description'],
                    'target_audience' => $data['target_audience'] ?? null,
                    'visual_reference' => $data['visual_reference'] ?? null,
                    'brief' => $data['brief'] ?? null,
                    'platform' => $data['platform'] ?? null,
                    'content_size' => $data['content_size'] ?? null,
                    'quantity' => $data['quantity'],
                    'speed_type' => $data['speed_type'],
                    'booking_date' => $bookingDate->toDateString(),
                    'start_date' => $bookingDate->toDateString(),
                    'deadline_at' => $deadline,
                    'base_price' => $quote['base_price'],
                    'speed_fee' => $quote['speed_fee'],
                    'subtotal' => $quote['subtotal'],
                    'discount_amount' => $quote['discount_amount'],
                    'total_amount' => $quote['total_amount'],
                    'freelancer_earning' => $quote['freelancer_earning'],
                    'platform_margin' => $quote['platform_margin'],
                    'revision_limit' => $quote['revision_limit'],
                    'revision_used' => 0,
                    'status' => Order::STATUS_PENDING_PAYMENT,
                ]);

                foreach ($request->file('assets', []) as $file) {
                    $path = $file->store("orders/{$order->order_code}/assets", 'public');
                    $storedPaths[] = $path;

                    OrderAsset::query()->create([
                        'order_id' => $order->id,
                        'uploaded_by' => $request->user()->id,
                        'asset_type' => 'raw',
                        'original_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'mime_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize() ?: 0,
                    ]);
                }

                if ($voucher && $quote['discount_amount'] > 0) {
                    VoucherUsage::query()->create([
                        'voucher_id' => $voucher->id,
                        'order_id' => $order->id,
                        'user_id' => $request->user()->id,
                        'discount_amount' => $quote['discount_amount'],
                        'used_at' => now(),
                    ]);
                    $voucher->increment('used_count');
                }

                $payment = $payments->createPendingPayment($order, $channel);

                return [$order, $payment];
            });

            return response()->json([
                'success' => true,
                'message' => 'Order dan transaksi pembayaran berhasil dibuat.',
                'data' => $order->fresh()->load([
                    'package.category',
                    'assets',
                    'latestPayment.method',
                    'latestPayment.channel',
                ]),
                'payment' => $payment->fresh(['method', 'channel']),
            ], 201);
        } catch (\Throwable $e) {
            $slots->release($slot);

            foreach ($storedPaths as $path) {
                Storage::disk('public')->delete($path);
            }

            throw $e;
        }
    }

    public function update(
        Request $request,
        Order $order,
        OrderPricingService $pricing
    ) {
        if ($order->client_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Order tidak ditemukan.',
            ], 404);
        }

        if ($order->status !== Order::STATUS_PENDING_PAYMENT) {
            return response()->json([
                'success' => false,
                'message' => 'Order tidak dapat diubah karena sudah diproses.',
            ], 422);
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'business_name' => ['required', 'string', 'max:120'],
            'product_description' => ['required', 'string', 'max:15000'],
            'target_audience' => ['nullable', 'string', 'max:3000'],
            'visual_reference' => ['nullable', 'string', 'max:255'],
            'brief' => ['nullable', 'string', 'max:15000'],
            'platform' => ['nullable', 'string', 'max:50'],
            'content_size' => ['nullable', 'string', 'max:50'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
            'speed_type' => ['required', Rule::in($pricing->validSpeedTypes())],
        ]);

        $package = ServicePackage::query()->findOrFail($order->service_package_id);
        $quote = $pricing->calculate($package, (int) $data['quantity'], $data['speed_type']);

        $order->update([
            'title' => $data['title'],
            'business_name' => $data['business_name'],
            'product_description' => $data['product_description'],
            'target_audience' => $data['target_audience'] ?? null,
            'visual_reference' => $data['visual_reference'] ?? null,
            'brief' => $data['brief'] ?? null,
            'platform' => $data['platform'] ?? null,
            'content_size' => $data['content_size'] ?? null,
            'quantity' => $data['quantity'],
            'speed_type' => $data['speed_type'],
            'base_price' => $quote['base_price'],
            'speed_fee' => $quote['speed_fee'],
            'subtotal' => $quote['subtotal'],
            'discount_amount' => $quote['discount_amount'],
            'total_amount' => $quote['total_amount'],
            'freelancer_earning' => $quote['freelancer_earning'],
            'platform_margin' => $quote['platform_margin'],
            'revision_limit' => $quote['revision_limit'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order berhasil diperbarui.',
            'data' => $order->fresh()->load('package.category'),
        ]);
    }

    public function destroy(Request $request, Order $order)
    {
        if ($order->client_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Order tidak ditemukan.',
            ], 404);
        }

        if ($order->status !== Order::STATUS_PENDING_PAYMENT) {
            return response()->json([
                'success' => false,
                'message' => 'Order yang sudah diproses tidak dapat dihapus.',
            ], 400);
        }

        DB::transaction(function () use ($order) {
            $slot = $order->productionSlot;

            if ($slot) {
                app(ProductionSlotService::class)->release($slot);
            }

            foreach ($order->assets as $asset) {
                Storage::disk('public')->delete($asset->file_path);
                $asset->delete();
            }

            Payment::query()->where('order_id', $order->id)->delete();
            VoucherUsage::query()->where('order_id', $order->id)->delete();
            $order->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Order berhasil dihapus.',
        ]);
    }
}
