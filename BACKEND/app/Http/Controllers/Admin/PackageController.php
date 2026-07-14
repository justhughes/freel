<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Models\ServicePackage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PackageController extends Controller
{
    public function index(): View
    {
        $packages = ServicePackage::query()->with('category')->withCount('orders')->latest()->get();
        $categories = ServiceCategory::query()->orderBy('name')->get();

        return view('admin.packages.index', compact('packages', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        ServicePackage::query()->create($this->prepared($data));

        return back()->with('success', 'Paket berhasil ditambahkan.');
    }

    public function update(Request $request, ServicePackage $package): RedirectResponse
    {
        $data = $this->validated($request, $package);
        $package->update($this->prepared($data, $package));

        return back()->with('success', 'Paket berhasil diperbarui.');
    }

    public function toggle(ServicePackage $package): RedirectResponse
    {
        $package->update(['is_active' => ! $package->is_active]);

        return back()->with('success', 'Status paket berhasil diperbarui.');
    }

    private function validated(Request $request, ?ServicePackage $package = null): array
    {
        return $request->validate([
            'service_category_id' => ['required', 'integer', 'exists:service_categories,id'],
            'code' => ['required', 'alpha_num', 'size:8', Rule::unique('service_packages', 'code')->ignore($package?->id)],
            'name' => [
                'required',
                'string',
                'max:120',
                Rule::unique('service_packages', 'name')
                    ->where(fn ($query) => $query->where('service_category_id', $request->integer('service_category_id')))
                    ->ignore($package?->id),
            ],
            'description' => ['required', 'string', 'max:5000'],
            'includes_text' => ['nullable', 'string', 'max:5000'],
            'base_price' => ['required', 'integer', 'min:0'],
            'regular_days' => ['required', 'integer', 'min:1', 'max:90'],
            'fast_days' => ['nullable', 'integer', 'min:1', 'max:90'],
            'express_days' => ['nullable', 'integer', 'min:1', 'max:90'],
            'fast_fee_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'express_fee_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'revision_limit' => ['required', 'integer', 'min:0', 'max:20'],
            'total_slot' => ['required', 'integer', 'min:0', 'max:10000'],
            'freelancer_fee_percent' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);
    }

    private function prepared(array $data, ?ServicePackage $package = null): array
    {
        return [
            'service_category_id' => $data['service_category_id'],
            'code' => strtoupper($data['code']),
            'name' => trim($data['name']),
            'slug' => Str::slug($data['name']).'-'.strtolower($data['code']),
            'description' => trim($data['description']),
            'includes' => collect(preg_split('/\r\n|\r|\n/', $data['includes_text'] ?? ''))
                ->map(fn ($item) => trim($item))->filter()->unique()->values()->all(),
            'base_price' => $data['base_price'],
            'regular_days' => $data['regular_days'],
            'fast_days' => $data['fast_days'] ?? null,
            'express_days' => $data['express_days'] ?? null,
            'fast_fee_percent' => $data['fast_fee_percent'],
            'express_fee_percent' => $data['express_fee_percent'],
            'revision_limit' => $data['revision_limit'],
            'total_slot' => $data['total_slot'],
            'freelancer_fee_percent' => $data['freelancer_fee_percent'],
            'is_active' => $package?->is_active ?? true,
        ];
    }
}
