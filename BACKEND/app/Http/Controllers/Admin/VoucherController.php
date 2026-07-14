<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class VoucherController extends Controller
{
    public function index(): View
    {
        $vouchers = Voucher::query()->latest()->get();

        return view('admin.vouchers', compact('vouchers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:30', 'unique:vouchers,code'],
            'description' => ['nullable', 'string', 'max:255'],
            'discount_type' => ['required', Rule::in([Voucher::TYPE_PERCENT, Voucher::TYPE_FIXED])],
            'discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'discount_amount' => ['nullable', 'integer', 'min:0'],
            'minimum_order_amount' => ['required', 'integer', 'min:0'],
            'maximum_discount_amount' => ['nullable', 'integer', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ]);

        if ($data['discount_type'] === Voucher::TYPE_PERCENT && empty($data['discount_percent'])) {
            return back()->withInput()->withErrors(['discount_percent' => 'Persentase diskon wajib diisi.']);
        }

        if ($data['discount_type'] === Voucher::TYPE_FIXED && empty($data['discount_amount'])) {
            return back()->withInput()->withErrors(['discount_amount' => 'Nominal diskon wajib diisi.']);
        }

        Voucher::query()->create([
            ...$data,
            'code' => strtoupper(trim($data['code'])),
            'used_count' => 0,
            'is_active' => true,
        ]);

        return back()->with('success', 'Voucher berhasil ditambahkan.');
    }

    public function toggle(Voucher $voucher): RedirectResponse
    {
        $voucher->update(['is_active' => ! $voucher->is_active]);

        return back()->with('success', 'Status voucher berhasil diperbarui.');
    }
}
