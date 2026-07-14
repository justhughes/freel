<?php

namespace App\Services;

use App\Models\FreelancerProfile;
use App\Models\FreelancerSkill;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FreelancerApprovalService
{
    public function approveSkill(FreelancerSkill $skill, User $admin): FreelancerSkill
    {
        return DB::transaction(function () use ($skill, $admin) {
            $locked = FreelancerSkill::query()
                ->with('category')
                ->lockForUpdate()
                ->findOrFail($skill->id);

            if (! $locked->category->hasAvailableFreelancerQuota()
                && $locked->status !== FreelancerSkill::STATUS_APPROVED) {
                throw ValidationException::withMessages([
                    'skill' => "Kuota freelancer bidang {$locked->category->name} sudah penuh.",
                ]);
            }

            $locked->update([
                'status' => FreelancerSkill::STATUS_APPROVED,
                'approved_by' => $admin->id,
                'approved_at' => now(),
                'rejection_reason' => null,
            ]);

            $this->syncApplicationStatus($locked->freelancer_id, $admin);

            return $locked->fresh(['freelancer', 'category']);
        });
    }

    public function rejectSkill(FreelancerSkill $skill, User $admin, string $reason): FreelancerSkill
    {
        return DB::transaction(function () use ($skill, $admin, $reason) {
            $locked = FreelancerSkill::query()->lockForUpdate()->findOrFail($skill->id);

            $locked->update([
                'status' => FreelancerSkill::STATUS_REJECTED,
                'approved_by' => $admin->id,
                'approved_at' => now(),
                'rejection_reason' => $reason,
            ]);

            $this->syncApplicationStatus($locked->freelancer_id, $admin);

            return $locked->fresh(['freelancer', 'category']);
        });
    }

    private function syncApplicationStatus(int $freelancerId, User $admin): void
    {
        $freelancer = User::query()->findOrFail($freelancerId);
        $skills = FreelancerSkill::query()->where('freelancer_id', $freelancerId)->get();
        $approved = $skills->where('status', FreelancerSkill::STATUS_APPROVED)->count();
        $pending = $skills->where('status', FreelancerSkill::STATUS_PENDING)->count();

        if ($approved > 0 && $pending === 0) {
            $profileStatus = FreelancerProfile::STATUS_APPROVED;
            $accountStatus = User::STATUS_ACTIVE;
        } elseif ($approved > 0) {
            $profileStatus = FreelancerProfile::STATUS_PARTIAL;
            $accountStatus = User::STATUS_ACTIVE;
        } elseif ($pending > 0) {
            $profileStatus = FreelancerProfile::STATUS_PENDING;
            $accountStatus = User::STATUS_PENDING;
        } else {
            $profileStatus = FreelancerProfile::STATUS_REJECTED;
            $accountStatus = User::STATUS_REJECTED;
        }

        $freelancer->update(['account_status' => $accountStatus]);
        $freelancer->freelancerProfile()->update([
            'application_status' => $profileStatus,
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
        ]);
    }
}
