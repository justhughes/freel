<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceCategory extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'freelancer_quota',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'freelancer_quota' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function packages(): HasMany
    {
        return $this->hasMany(ServicePackage::class);
    }

    public function freelancerSkills(): HasMany
    {
        return $this->hasMany(FreelancerSkill::class);
    }

    public function approvedFreelancerSkills(): HasMany
    {
        return $this->freelancerSkills()->where('status', FreelancerSkill::STATUS_APPROVED);
    }

    public function hasAvailableFreelancerQuota(): bool
    {
        if ($this->freelancer_quota === 0) {
            return true;
        }

        return $this->approvedFreelancerSkills()->count() < $this->freelancer_quota;
    }
}
