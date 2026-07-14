<?php

namespace App\Services;

use App\Models\ProductionSlot;
use App\Models\ServicePackage;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductionSlotService
{
    public function reserve(ServicePackage $package, CarbonInterface $date): ProductionSlot
    {
        return DB::transaction(function () use ($package, $date) {
            $slot = ProductionSlot::query()
                ->where('service_package_id', $package->id)
                ->whereDate('production_date', $date->toDateString())
                ->lockForUpdate()
                ->first();

            if (! $slot) {
                $slot = ProductionSlot::query()->create([
                    'service_package_id' => $package->id,
                    'production_date' => $date->toDateString(),
                    'total_slots' => $package->total_slot,
                    'reserved_slots' => 0,
                    'status' => $package->total_slot > 0
                        ? ProductionSlot::STATUS_OPEN
                        : ProductionSlot::STATUS_CLOSED,
                ]);
            }

            if (! $slot->hasAvailability()) {
                throw ValidationException::withMessages([
                    'booking_date' => 'Slot produksi pada tanggal tersebut sudah penuh atau ditutup.',
                ]);
            }

            $slot->reserved_slots++;
            $slot->status = $slot->reserved_slots >= $slot->total_slots
                ? ProductionSlot::STATUS_FULL
                : ProductionSlot::STATUS_OPEN;
            $slot->save();

            return $slot;
        });
    }

    public function release(?ProductionSlot $slot): void
    {
        if (! $slot) {
            return;
        }

        DB::transaction(function () use ($slot) {
            $locked = ProductionSlot::query()->lockForUpdate()->find($slot->id);

            if (! $locked) {
                return;
            }

            $locked->reserved_slots = max(0, $locked->reserved_slots - 1);
            $locked->status = $locked->total_slots > 0
                ? ProductionSlot::STATUS_OPEN
                : ProductionSlot::STATUS_CLOSED;
            $locked->save();
        });
    }
}
