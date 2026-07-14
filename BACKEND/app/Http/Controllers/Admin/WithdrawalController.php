<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use App\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WithdrawalController extends Controller
{
    public function index(): View
    {
        $withdrawals = Withdrawal::query()->with(['freelancer', 'reviewer'])->latest()->get();

        return view('admin.withdrawals.index', compact('withdrawals'));
    }

    public function approve(Request $request, Withdrawal $withdrawal, WalletService $wallets): RedirectResponse
    {
        $wallets->approveWithdrawal($withdrawal, $request->user());

        return back()->with('success', 'Withdraw disetujui.');
    }

    public function paid(Request $request, Withdrawal $withdrawal, WalletService $wallets): RedirectResponse
    {
        $data = $request->validate([
            'proof' => ['nullable', 'file', 'max:5120', 'mimes:jpg,jpeg,png,pdf'],
        ]);
        $proofPath = $request->file('proof')
            ? $request->file('proof')->store('withdrawal-proofs', 'public')
            : null;
        $wallets->markWithdrawalPaid($withdrawal, $request->user(), $proofPath);

        return back()->with('success', 'Withdraw ditandai sudah dibayar.');
    }

    public function reject(Request $request, Withdrawal $withdrawal, WalletService $wallets): RedirectResponse
    {
        $data = $request->validate(['reason' => ['required', 'string', 'max:2000']]);
        $wallets->rejectWithdrawal($withdrawal, $request->user(), $data['reason']);

        return back()->with('success', 'Withdraw ditolak dan saldo dikembalikan.');
    }
}
