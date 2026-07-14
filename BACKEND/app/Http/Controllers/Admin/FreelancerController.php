<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FreelancerSkill;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Services\FreelancerApprovalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FreelancerController extends Controller
{
    public function index(): View
    {
        $freelancers = User::query()
            ->where('role', User::ROLE_FREELANCER)
            ->with(['freelancerProfile', 'freelancerSkills.category'])
            ->latest()
            ->get();
        $categories = ServiceCategory::query()->withCount('approvedFreelancerSkills')->orderBy('name')->get();

        return view('admin.freelancers.index', compact('freelancers', 'categories'));
    }

    public function show(User $freelancer): View
    {
        abort_unless($freelancer->isFreelancer(), 404);
        $freelancer->load(['freelancerProfile.reviewer', 'freelancerSkills.category', 'freelanceOrders.package']);

        return view('admin.freelancers.show', compact('freelancer'));
    }

    public function approveSkill(
        Request $request,
        FreelancerSkill $skill,
        FreelancerApprovalService $approvals
    ): RedirectResponse {
        $approvals->approveSkill($skill, $request->user());

        return back()->with('success', 'Keahlian freelancer berhasil disetujui.');
    }

    public function rejectSkill(
        Request $request,
        FreelancerSkill $skill,
        FreelancerApprovalService $approvals
    ): RedirectResponse {
        $data = $request->validate(['reason' => ['required', 'string', 'max:2000']]);
        $approvals->rejectSkill($skill, $request->user(), $data['reason']);

        return back()->with('success', 'Keahlian freelancer ditolak.');
    }

    public function updateQuota(Request $request, ServiceCategory $category): RedirectResponse
    {
        $data = $request->validate(['freelancer_quota' => ['required', 'integer', 'min:0', 'max:10000']]);
        $category->update($data);

        return back()->with('success', 'Kuota freelancer bidang berhasil diperbarui.');
    }

    public function toggleStatus(User $freelancer): RedirectResponse
    {
        abort_unless($freelancer->isFreelancer(), 404);
        $freelancer->update([
            'account_status' => $freelancer->account_status === User::STATUS_ACTIVE
                ? User::STATUS_INACTIVE
                : User::STATUS_ACTIVE,
        ]);

        return back()->with('success', 'Status akun freelancer berhasil diperbarui.');
    }
}
