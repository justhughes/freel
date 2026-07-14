<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Payment::query()->with(['order.client', 'method', 'channel']);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        $payments = $query->latest()->get();

        return view('admin.payments.index', compact('payments'));
    }

    public function markProblem(Request $request, Payment $payment, PaymentService $service): RedirectResponse
    {
        $data = $request->validate(['reason' => ['required', 'string', 'max:255']]);
        $service->markProblem($payment, $data['reason']);

        return back()->with('success', 'Transaksi ditandai bermasalah.');
    }
}
