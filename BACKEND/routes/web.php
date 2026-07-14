<?php

use App\Http\Controllers\Admin\ClientController as AdminClientController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\FreelancerController as AdminFreelancerController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\OrderRevisionController as AdminOrderRevisionController;
use App\Http\Controllers\Admin\PackageController as AdminPackageController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\VoucherController as AdminVoucherController;
use App\Http\Controllers\Admin\WithdrawalController as AdminWithdrawalController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Client\OrderController as ClientOrderController;
use App\Http\Controllers\Freelancer\FreelancerController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    return match (auth()->user()->role) {
        User::ROLE_ADMIN => redirect()->route('admin.dashboard'),
        User::ROLE_FREELANCER => redirect()->route('freelancer.dashboard'),
        default => redirect()->route('client.home'),
    };
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'loginPage'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');

    Route::get('/register/client', [AuthController::class, 'clientRegisterPage'])
        ->name('register.client');
    Route::post('/register/client', [AuthController::class, 'registerClient'])
        ->name('register.client.process');

    Route::get('/register/freelancer', [AuthController::class, 'freelancerRegisterPage'])
        ->name('register.freelancer');
    Route::post('/register/freelancer', [AuthController::class, 'registerFreelancer'])
        ->name('register.freelancer.process');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'role:client'])
    ->prefix('client')
    ->name('client.')
    ->group(function () {
        Route::get('/home', [ClientOrderController::class, 'home'])->name('home');
        Route::get('/orders', [ClientOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/create', [ClientOrderController::class, 'create'])->name('orders.create');
        Route::post('/orders', [ClientOrderController::class, 'store'])->name('orders.store');
        Route::get('/orders/{order}', [ClientOrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/simulate-payment', [ClientOrderController::class, 'simulatePayment'])
            ->name('orders.simulate-payment');
        Route::post('/orders/{order}/revision', [ClientOrderController::class, 'requestRevision'])
            ->name('orders.revision');
        Route::post('/orders/{order}/approve', [ClientOrderController::class, 'approve'])
            ->name('orders.approve');
        Route::get('/orders/{order}/assets/{asset}/download', [ClientOrderController::class, 'downloadAsset'])
            ->name('orders.assets.download');
        Route::get('/orders/{order}/results/{file}/download', [ClientOrderController::class, 'downloadResult'])
            ->name('orders.results.download');
    });

Route::middleware(['auth', 'role:freelancer'])
    ->prefix('freelancer')
    ->name('freelancer.')
    ->group(function () {
        Route::get('/dashboard', [FreelancerController::class, 'dashboard'])->name('dashboard');
        Route::get('/jobs', [FreelancerController::class, 'jobBoard'])->name('jobs.index');
        Route::post('/jobs/{order}/take', [FreelancerController::class, 'take'])->name('jobs.take');
        Route::get('/tasks', [FreelancerController::class, 'tasks'])->name('tasks.index');
        Route::get('/tasks/{order}', [FreelancerController::class, 'showTask'])->name('tasks.show');
        Route::post('/tasks/{order}/submit', [FreelancerController::class, 'submit'])->name('tasks.submit');
        Route::get('/tasks/{order}/assets/{asset}/download', [FreelancerController::class, 'downloadAsset'])
            ->name('tasks.assets.download');
        Route::get('/tasks/{order}/results/{file}/download', [FreelancerController::class, 'downloadResult'])
            ->name('tasks.results.download');
        Route::get('/wallet', [FreelancerController::class, 'wallet'])->name('wallet');
        Route::post('/wallet/withdraw', [FreelancerController::class, 'withdraw'])->name('wallet.withdraw');
    });

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');

        Route::get('/clients', [AdminClientController::class, 'index'])->name('clients.index');
        Route::get('/clients/{client}', [AdminClientController::class, 'show'])->name('clients.show');
        Route::patch('/clients/{client}/status', [AdminClientController::class, 'toggleStatus'])
            ->name('clients.status');

        Route::get('/freelancers', [AdminFreelancerController::class, 'index'])->name('freelancers.index');
        Route::get('/freelancers/{freelancer}', [AdminFreelancerController::class, 'show'])
            ->name('freelancers.show');
        Route::post('/freelancer-skills/{skill}/approve', [AdminFreelancerController::class, 'approveSkill'])
            ->name('freelancer-skills.approve');
        Route::post('/freelancer-skills/{skill}/reject', [AdminFreelancerController::class, 'rejectSkill'])
            ->name('freelancer-skills.reject');
        Route::patch('/freelancers/{freelancer}/status', [AdminFreelancerController::class, 'toggleStatus'])
            ->name('freelancers.status');
        Route::patch('/categories/{category}/quota', [AdminFreelancerController::class, 'updateQuota'])
            ->name('categories.quota');

        Route::get('/packages', [AdminPackageController::class, 'index'])->name('packages.index');
        Route::post('/packages', [AdminPackageController::class, 'store'])->name('packages.store');
        Route::patch('/packages/{package}', [AdminPackageController::class, 'update'])->name('packages.update');
        Route::patch('/packages/{package}/toggle', [AdminPackageController::class, 'toggle'])->name('packages.toggle');

        Route::get('/revisions', [AdminOrderRevisionController::class, 'index'])
            ->name('revisions.index');
        Route::post('/revisions/{revision}/forward', [AdminOrderRevisionController::class, 'forward'])
            ->name('revisions.forward');
        Route::post('/revisions/{revision}/reject', [AdminOrderRevisionController::class, 'reject'])
            ->name('revisions.reject');

        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::get('/orders/{order}/assets/{asset}/download', [AdminOrderController::class, 'downloadAsset'])
            ->name('orders.assets.download');
        Route::get('/orders/{order}/results/{file}/download', [AdminOrderController::class, 'downloadResult'])
            ->name('orders.results.download');

        Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
        Route::post('/payments/{payment}/problem', [AdminPaymentController::class, 'markProblem'])
            ->name('payments.problem');

        Route::get('/withdrawals', [AdminWithdrawalController::class, 'index'])->name('withdrawals.index');
        Route::post('/withdrawals/{withdrawal}/approve', [AdminWithdrawalController::class, 'approve'])
            ->name('withdrawals.approve');
        Route::post('/withdrawals/{withdrawal}/paid', [AdminWithdrawalController::class, 'paid'])
            ->name('withdrawals.paid');
        Route::post('/withdrawals/{withdrawal}/reject', [AdminWithdrawalController::class, 'reject'])
            ->name('withdrawals.reject');

        Route::get('/vouchers', [AdminVoucherController::class, 'index'])->name('vouchers.index');
        Route::post('/vouchers', [AdminVoucherController::class, 'store'])->name('vouchers.store');
        Route::patch('/vouchers/{voucher}/toggle', [AdminVoucherController::class, 'toggle'])
            ->name('vouchers.toggle');
    });
