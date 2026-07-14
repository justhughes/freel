<?php

namespace App\Services;

use App\Models\Order;
use App\Models\ServicePackage;
use App\Models\Voucher;
use Carbon\CarbonInterface;

class OrderPricingService
{
    public function calculate(
        ServicePackage $package,
        int $quantity,
        string $speedType,
        ?Voucher $voucher = null
    ): array {
        $basePrice = $package->base_price * $quantity;
        $speedPercent = $package->feePercentForSpeed($speedType);
        $speedFee = (int) round($basePrice * ($speedPercent / 100));
        $subtotal = $basePrice + $speedFee;
        $discount = $voucher?->calculateDiscount($subtotal) ?? 0;
        $total = max(0, $subtotal - $discount);
        $freelancerEarning = (int) round(
            $total * ((float) $package->freelancer_fee_percent / 100)
        );

        return [
            'base_price' => $basePrice,
            'speed_fee' => $speedFee,
            'subtotal' => $subtotal,
            'discount_amount' => $discount,
            'total_amount' => $total,
            'freelancer_earning' => $freelancerEarning,
            'platform_margin' => $total - $freelancerEarning,
            'revision_limit' => $package->revision_limit,
        ];
    }

    public function deadline(
        ServicePackage $package,
        string $speedType,
        CarbonInterface $startDate
    ): CarbonInterface {
        $days = max(1, $package->daysForSpeed($speedType));
        $currentTime = now();

        return $startDate->copy()
            ->setTime(
                $currentTime->hour,
                $currentTime->minute,
                $currentTime->second
            )
            ->addDays($days);
    }

    public function validSpeedTypes(): array
    {
        return [
            Order::SPEED_REGULAR,
            Order::SPEED_FAST,
            Order::SPEED_EXPRESS,
        ];
    }
}
