<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServicePackage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PackageController extends Controller
{
    /**
     * Klien dan freelancer hanya menerima paket aktif.
     * Admin menerima seluruh paket agar CMS tetap dapat mengelola paket nonaktif.
     */
    public function index(Request $request)
    {
        $query = ServicePackage::query()
            ->with('category')
            ->orderBy('service_category_id')
            ->orderBy('base_price');

        if (! $request->user()->isAdmin()) {
            $query->where('is_active', true);
        }

        return response()->json([
            'success' => true,
            'message' => 'Daftar paket berhasil diambil.',
            'data' => $query->get(),
        ]);
    }

    public function show(ServicePackage $package)
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail paket berhasil diambil.',
            'data' => $package->load('category'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validatePackage($request);
        $validated['is_active'] = $validated['is_active'] ?? true;

        $package = ServicePackage::query()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Paket berhasil ditambahkan.',
            'data' => $package->load('category'),
        ], 201);
    }

    public function update(Request $request, ServicePackage $package)
    {
        $validated = $this->validatePackage($request, $package);
        $package->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Paket berhasil diperbarui.',
            'data' => $package->fresh('category'),
        ]);
    }

    public function destroy(ServicePackage $package)
    {
        if ($package->orders()->exists()) {
            $package->update(['is_active' => false]);

            return response()->json([
                'success' => true,
                'message' => 'Paket sudah digunakan dalam pesanan sehingga dinonaktifkan, bukan dihapus.',
                'data' => $package->fresh('category'),
            ]);
        }

        $package->delete();

        return response()->json([
            'success' => true,
            'message' => 'Paket berhasil dihapus.',
        ]);
    }

    private function validatePackage(
        Request $request,
        ?ServicePackage $package = null
    ): array {
        return $request->validate([
            'service_category_id' => ['required', 'integer', 'exists:service_categories,id'],
            'code' => [
                'required',
                'string',
                'max:8',
                Rule::unique('service_packages', 'code')->ignore($package?->id),
            ],
            'name' => ['required', 'string', 'max:120'],
            'slug' => [
                'required',
                'string',
                'max:140',
                Rule::unique('service_packages', 'slug')->ignore($package?->id),
            ],
            'description' => ['required', 'string', 'max:10000'],
            'includes' => ['nullable', 'array', 'max:30'],
            'includes.*' => ['required', 'string', 'max:255'],
            'base_price' => ['required', 'integer', 'min:0'],
            'regular_days' => ['required', 'integer', 'min:1', 'max:255'],
            'fast_days' => ['nullable', 'integer', 'min:1', 'max:255'],
            'express_days' => ['nullable', 'integer', 'min:1', 'max:255'],
            'fast_fee_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'express_fee_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'revision_limit' => ['required', 'integer', 'min:0', 'max:255'],
            'total_slot' => ['required', 'integer', 'min:1', 'max:65535'],
            'freelancer_fee_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
    }
}
