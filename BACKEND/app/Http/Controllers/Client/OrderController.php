<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderAsset;
use App\Models\Payment;
use App\Models\PaymentChannel;
use App\Models\ServicePackage;
use App\Models\SubmissionFile;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use App\Services\CodeGenerator;
use App\Services\OrderPricingService;
use App\Services\OrderWorkflowService;
use App\Services\PaymentService;
use App\Services\ProductionSlotService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    public function home(): View
    {
        $packages = ServicePackage::query()
            ->active()
            ->with('category')
            ->orderBy('base_price')
            ->get();

        return view('client.home', compact('packages'));
    }

    public function index(Request $request): View
    {
        $orders = Order::query()
            ->forClient($request->user()->id)
            ->with(['package.category', 'freelancer', 'latestPayment', 'currentSubmission.files', 'activeRevision'])
            ->latest()
            ->get();

        return view('client.orders.index', compact('orders'));
    }

    public function create(Request $request): View
    {
        $packages = ServicePackage::query()->active()->with('category')->orderBy('name')->get();
        $selectedPackage = $request->filled('package')
            ? $packages->firstWhere('id', (int) $request->integer('package'))
            : null;
        $channels = PaymentChannel::query()
            ->where('is_active', true)
            ->whereHas('method', fn ($query) => $query->where('is_active', true))
            ->with('method')
            ->orderBy('payment_method_id')
            ->orderBy('name')
            ->get();

        return view('client.orders.create', compact('packages', 'selectedPackage', 'channels'));
    }

    public function store(
        Request $request,
        CodeGenerator $codes,
        OrderPricingService $pricing,
        ProductionSlotService $slots,
        PaymentService $payments
    ): RedirectResponse {
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
            'assets.*' => ['file', 'max:51200', 'mimes:jpg,jpeg,png,pdf,zip,mp4,mov,doc,docx'],
        ]);

        $package = ServicePackage::query()->active()->findOrFail($data['service_package_id']);
        $voucher = null;

        if (! empty($data['voucher_code'])) {
            $voucher = Voucher::query()
                ->whereRaw('UPPER(code) = ?', [strtoupper(trim($data['voucher_code']))])
                ->first();

            if (! $voucher) {
                throw ValidationException::withMessages(['voucher_code' => 'Kode voucher tidak ditemukan.']);
            }
        }

        $bookingDate = Carbon::parse($data['booking_date'], config('app.timezone'));

        $baseQuote = $pricing->calculate(
            $package,
            $data['quantity'],
            $data['speed_type']
        );

        if ($voucher && ! $voucher->isUsableFor($baseQuote['subtotal'])) {
            throw ValidationException::withMessages([
                'voucher_code' => 'Voucher tidak aktif, sudah habis, belum berlaku, sudah berakhir, atau minimum belanja belum terpenuhi.',
            ]);
        }

        $price = $pricing->calculate(
            $package,
            $data['quantity'],
            $data['speed_type'],
            $voucher
        );
        $slot = $slots->reserve($package, $bookingDate);

        try {
            $order = DB::transaction(function () use (
                $request,
                $data,
                $package,
                $voucher,
                $bookingDate,
                $price,
                $slot,
                $codes,
                $pricing,
                $payments
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
                    'deadline_at' => $pricing->deadline($package, $data['speed_type'], $bookingDate),
                    ...$price,
                    'status' => Order::STATUS_PENDING_PAYMENT,
                ]);

                foreach ($request->file('assets', []) as $file) {
                    $path = $file->store("orders/{$order->order_code}/assets", 'public');
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

                if ($voucher && $price['discount_amount'] > 0) {
                    VoucherUsage::query()->create([
                        'voucher_id' => $voucher->id,
                        'order_id' => $order->id,
                        'user_id' => $request->user()->id,
                        'discount_amount' => $price['discount_amount'],
                        'used_at' => now(),
                    ]);
                    $voucher->increment('used_count');
                }

                $channel = PaymentChannel::query()->findOrFail($data['payment_channel_id']);
                $payments->createPendingPayment($order, $channel);

                return $order;
            });
        } catch (\Throwable $exception) {
            $slots->release($slot);
            throw $exception;
        }

        return redirect()->route('client.orders.show', $order)
            ->with('success', 'Pesanan dibuat. Gunakan tombol simulasi pembayaran untuk menguji alur backend.');
    }

    public function show(Request $request, Order $order): View
    {
        $this->ensureOwner($request, $order);
        $order->load([
            'package.category',
            'freelancer',
            'assets',
            'payments.method',
            'payments.channel',
            'currentSubmission.files',
            'submissions.files',
            'revisions.submission.files',
            'revisions.resultSubmission.files',
            'revisions.forwarder',
            'activeRevision',
        ]);

        return view('client.orders.show', compact('order'));
    }

    public function simulatePayment(
        Request $request,
        Order $order,
        PaymentService $payments
    ): RedirectResponse {
        $this->ensureOwner($request, $order);
        $payment = $order->payments()->where('status', Payment::STATUS_PENDING)->latest()->firstOrFail();
        $payments->markAsPaid($payment, 'SIM-'.now()->format('YmdHis'));

        return back()->with('success', 'Pembayaran simulasi berhasil. Pesanan masuk ke Job Board.');
    }

    public function requestRevision(
        Request $request,
        Order $order,
        OrderWorkflowService $workflow
    ): RedirectResponse {
        $this->ensureOwner($request, $order);
        $data = $request->validate(['notes' => ['required', 'string', 'max:5000']]);
        $workflow->requestRevision($order, $request->user(), $data['notes']);

        return back()->with('success', 'Pengajuan revisi sudah dikirim ke admin untuk diperiksa dan diteruskan kepada freelancer.');
    }

    public function approve(
        Request $request,
        Order $order,
        OrderWorkflowService $workflow
    ): RedirectResponse {
        $this->ensureOwner($request, $order);
        $workflow->approveResult($order, $request->user());

        return back()->with('success', 'Hasil diterima. Pesanan selesai dan pendapatan masuk ke saldo freelancer.');
    }

    public function downloadAsset(Request $request, Order $order, OrderAsset $asset): StreamedResponse
    {
        $this->ensureOwner($request, $order);
        abort_unless($asset->order_id === $order->id, 404);

        return Storage::disk('public')->download($asset->file_path, $asset->original_name);
    }

    public function downloadResult(Request $request, Order $order, SubmissionFile $file): StreamedResponse
    {
        $this->ensureOwner($request, $order);
        abort_unless($file->submission?->order_id === $order->id, 404);

        return Storage::disk('public')->download($file->file_path, $file->original_name);
    }

    private function ensureOwner(Request $request, Order $order): void
    {
        abort_unless($order->client_id === $request->user()->id, 403);
    }
}
