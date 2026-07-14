<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FreelancerProfile;
use App\Models\Order;
use App\Models\Payment;
use App\Models\OrderRevision;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $stats = [
            'clients' => User::query()->where('role', User::ROLE_CLIENT)->count(),
            'active_freelancers' => User::query()->where('role', User::ROLE_FREELANCER)->where('account_status', User::STATUS_ACTIVE)->count(),
            'pending_freelancers' => FreelancerProfile::query()->whereIn('application_status', [FreelancerProfile::STATUS_PENDING, FreelancerProfile::STATUS_PARTIAL])->count(),
            'queue_orders' => Order::query()->where('status', Order::STATUS_QUEUE)->count(),
            'active_orders' => Order::query()->whereIn('status', [
                Order::STATUS_PROCESS,
                Order::STATUS_REVISION_REQUESTED,
                Order::STATUS_REVISION,
                Order::STATUS_REVIEW,
            ])->count(),
            'pending_revisions' => OrderRevision::query()
                ->where('status', OrderRevision::STATUS_PENDING_ADMIN)
                ->count(),
            'paid_revenue' => Payment::query()->where('status', Payment::STATUS_PAID)->sum('amount'),
            'pending_withdrawals' => Withdrawal::query()->whereIn('status', [Withdrawal::STATUS_PENDING, Withdrawal::STATUS_APPROVED])->count(),
        ];

        $latestOrders = Order::query()->with(['client', 'package.category', 'freelancer'])->latest()->limit(8)->get();

        return view('admin.dashboard', compact('stats', 'latestOrders'));
    }
}
