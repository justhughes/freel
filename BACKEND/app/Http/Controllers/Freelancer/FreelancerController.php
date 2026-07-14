<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Models\FreelancerSkill;
use App\Models\Order;
use App\Models\OrderAsset;
use App\Models\SubmissionFile;
use App\Services\OrderWorkflowService;
use App\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FreelancerController extends Controller
{
    public function dashboard(Request $request, WalletService $wallets): View
    {
        $user = $request->user();
        $wallet = $wallets->walletFor($user);
        $stats = [
            'available_jobs' => $this->jobBoardQuery($user)->count(),
            'active_tasks' => Order::query()
                ->forFreelancer($user->id)
                ->whereIn('status', [
                    Order::STATUS_PROCESS,
                    Order::STATUS_REVISION_REQUESTED,
                    Order::STATUS_REVISION,
                    Order::STATUS_REVIEW,
                ])
                ->count(),
            'completed_tasks' => Order::query()
                ->forFreelancer($user->id)
                ->where('status', Order::STATUS_DONE)
                ->count(),
        ];
        $tasks = Order::query()
            ->forFreelancer($user->id)
            ->with('package.category')
            ->latest()
            ->limit(5)
            ->get();

        return view('freelancer.dashboard', compact('wallet', 'stats', 'tasks'));
    }

    public function jobBoard(Request $request): View
    {
        $orders = $this->jobBoardQuery($request->user())
            ->with(['client', 'package.category'])
            ->orderBy('deadline_at')
            ->get();

        return view('freelancer.job-board', compact('orders'));
    }

    public function take(Request $request, Order $order, OrderWorkflowService $workflow): RedirectResponse
    {
        $workflow->takeJob($order, $request->user());

        return redirect()
            ->route('freelancer.tasks.show', $order)
            ->with('success', 'Pekerjaan berhasil diambil dan masuk ke My Tasks.');
    }

    public function tasks(Request $request): View
    {
        $orders = Order::query()
            ->forFreelancer($request->user()->id)
            ->with([
                'client',
                'package.category',
                'currentSubmission.files',
                'activeRevision',
            ])
            ->latest()
            ->get();

        return view('freelancer.tasks.index', compact('orders'));
    }

    public function showTask(Request $request, Order $order): View
    {
        $this->ensureAssignee($request, $order);
        $order->load([
            'client',
            'package.category',
            'assets',
            'currentSubmission.files',
            'submissions.files',
            'revisions.submission.files',
            'revisions.resultSubmission.files',
            'revisions.forwarder',
            'activeRevision',
        ]);

        return view('freelancer.tasks.show', compact('order'));
    }

    public function submit(
        Request $request,
        Order $order,
        OrderWorkflowService $workflow
    ): RedirectResponse {
        $this->ensureAssignee($request, $order);
        $data = $request->validate([
            'result_file' => [
                'required',
                'file',
                'max:51200',
                'mimes:jpg,jpeg,png,pdf,zip,mp4,mov,doc,docx',
            ],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $workflow->submitWork(
            $order,
            $request->user(),
            $request->file('result_file'),
            $data['notes'] ?? null
        );

        return back()->with(
            'success',
            'Hasil berhasil diunggah dan langsung tersedia untuk direview klien.'
        );
    }

    public function wallet(Request $request, WalletService $wallets): View
    {
        $wallet = $wallets->walletFor($request->user());
        $wallet->load([
            'transactions' => fn ($query) => $query->latest('transacted_at'),
            'withdrawals' => fn ($query) => $query->latest(),
        ]);
        $profile = $request->user()->freelancerProfile;

        return view('freelancer.wallet', compact('wallet', 'profile'));
    }

    public function withdraw(Request $request, WalletService $wallets): RedirectResponse
    {
        $data = $request->validate([
            'amount' => ['required', 'integer', 'min:50000'],
            'destination_type' => ['required', 'in:bank,e_wallet'],
            'destination_provider' => ['required', 'string', 'max:80'],
            'destination_account_number' => ['required', 'string', 'max:30'],
            'destination_account_holder' => ['required', 'string', 'max:120'],
        ]);

        $wallets->requestWithdrawal($request->user(), $data['amount'], $data);
        $request->user()->freelancerProfile()->update([
            'payout_type' => $data['destination_type'],
            'payout_provider' => $data['destination_provider'],
            'payout_account_number' => $data['destination_account_number'],
            'payout_account_holder' => $data['destination_account_holder'],
        ]);

        return back()->with('success', 'Permintaan withdraw berhasil dikirim.');
    }

    public function downloadAsset(
        Request $request,
        Order $order,
        OrderAsset $asset
    ): StreamedResponse {
        $this->ensureAssignee($request, $order);
        abort_unless($asset->order_id === $order->id, 404);

        return Storage::disk('public')->download($asset->file_path, $asset->original_name);
    }

    public function downloadResult(
        Request $request,
        Order $order,
        SubmissionFile $file
    ): StreamedResponse {
        $this->ensureAssignee($request, $order);
        abort_unless($file->submission?->order_id === $order->id, 404);

        return Storage::disk('public')->download($file->file_path, $file->original_name);
    }

    private function jobBoardQuery($user)
    {
        $categoryIds = FreelancerSkill::query()
            ->where('freelancer_id', $user->id)
            ->where('status', FreelancerSkill::STATUS_APPROVED)
            ->pluck('service_category_id');

        return Order::query()
            ->jobBoard()
            ->whereHas(
                'package',
                fn ($query) => $query->whereIn('service_category_id', $categoryIds)
            );
    }

    private function ensureAssignee(Request $request, Order $order): void
    {
        abort_unless($order->freelancer_id === $request->user()->id, 403);
    }
}
