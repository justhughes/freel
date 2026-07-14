<?php

namespace App\Services;

use App\Models\FreelancerSkill;
use App\Models\Order;
use App\Models\OrderAssignment;
use App\Models\OrderRevision;
use App\Models\OrderSubmission;
use App\Models\SubmissionFile;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderWorkflowService
{
    public function __construct(private readonly WalletService $wallets)
    {
    }

    public function takeJob(Order $order, User $freelancer): Order
    {
        return DB::transaction(function () use ($order, $freelancer) {
            $locked = Order::query()
                ->with('package.category')
                ->lockForUpdate()
                ->findOrFail($order->id);

            if (! $freelancer->isFreelancer() || ! $freelancer->isActive()) {
                throw ValidationException::withMessages([
                    'job' => 'Akun freelancer belum aktif.',
                ]);
            }

            if ($locked->status !== Order::STATUS_QUEUE || $locked->freelancer_id !== null) {
                throw ValidationException::withMessages([
                    'job' => 'Pekerjaan sudah diambil freelancer lain.',
                ]);
            }

            $hasSkill = FreelancerSkill::query()
                ->where('freelancer_id', $freelancer->id)
                ->where('service_category_id', $locked->package->service_category_id)
                ->where('status', FreelancerSkill::STATUS_APPROVED)
                ->exists();

            if (! $hasSkill) {
                throw ValidationException::withMessages([
                    'job' => 'Pekerjaan tidak sesuai dengan bidang keahlian yang disetujui.',
                ]);
            }

            $locked->update([
                'freelancer_id' => $freelancer->id,
                'status' => Order::STATUS_PROCESS,
                'taken_at' => now(),
                'start_date' => now()->toDateString(),
            ]);

            OrderAssignment::query()->create([
                'order_id' => $locked->id,
                'freelancer_id' => $freelancer->id,
                'assigned_by' => $freelancer->id,
                'action' => 'taken',
                'assigned_at' => now(),
            ]);

            return $locked->fresh(['client', 'package.category', 'assets']);
        });
    }

    public function submitWork(
        Order $order,
        User $freelancer,
        UploadedFile $file,
        ?string $notes = null
    ): OrderSubmission {
        return DB::transaction(function () use ($order, $freelancer, $file, $notes) {
            $locked = Order::query()
                ->lockForUpdate()
                ->findOrFail($order->id);

            if ($locked->freelancer_id !== $freelancer->id) {
                throw ValidationException::withMessages([
                    'submission' => 'Pesanan bukan tugas freelancer ini.',
                ]);
            }

            if (! in_array($locked->status, [Order::STATUS_PROCESS, Order::STATUS_REVISION], true)) {
                throw ValidationException::withMessages([
                    'submission' => 'Status pesanan belum dapat dikirim untuk review.',
                ]);
            }

            $activeRevision = null;

            if ($locked->status === Order::STATUS_REVISION) {
                $activeRevision = OrderRevision::query()
                    ->where('order_id', $locked->id)
                    ->whereIn('status', [
                        OrderRevision::STATUS_FORWARDED,
                        OrderRevision::STATUS_IN_PROGRESS,
                    ])
                    ->latest('forwarded_at')
                    ->lockForUpdate()
                    ->first();

                if (! $activeRevision) {
                    throw ValidationException::withMessages([
                        'submission' => 'Tidak ada revisi aktif yang sudah diteruskan oleh admin.',
                    ]);
                }

                if ($activeRevision->status === OrderRevision::STATUS_FORWARDED) {
                    $activeRevision->update([
                        'status' => OrderRevision::STATUS_IN_PROGRESS,
                    ]);
                }
            }

            OrderSubmission::query()
                ->where('order_id', $locked->id)
                ->where('is_current', true)
                ->update(['is_current' => false]);

            $version = ((int) OrderSubmission::query()
                ->where('order_id', $locked->id)
                ->max('version')) + 1;

            $submission = OrderSubmission::query()->create([
                'order_id' => $locked->id,
                'freelancer_id' => $freelancer->id,
                'version' => $version,
                'submission_type' => $activeRevision ? 'revision' : 'draft',
                'notes' => $notes,
                'submitted_at' => now(),
                'is_current' => true,
            ]);

            $path = $file->store("orders/{$locked->order_code}/submissions", 'public');

            SubmissionFile::query()->create([
                'order_submission_id' => $submission->id,
                'original_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'mime_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize() ?: 0,
                'is_final' => false,
            ]);

            if ($activeRevision) {
                $activeRevision->update([
                    'status' => OrderRevision::STATUS_COMPLETED,
                    'result_submission_id' => $submission->id,
                    'completed_at' => now(),
                ]);
            }

            $locked->update([
                'status' => Order::STATUS_REVIEW,
                'submitted_at' => now(),
            ]);

            return $submission->fresh('files');
        });
    }

    /**
     * Klien mengajukan catatan revisi. Revisi belum dihitung sebagai kuota
     * sampai admin meneruskannya kepada freelancer.
     */
    public function requestRevision(Order $order, User $client, string $notes): OrderRevision
    {
        return DB::transaction(function () use ($order, $client, $notes) {
            $locked = Order::query()
                ->with('currentSubmission')
                ->lockForUpdate()
                ->findOrFail($order->id);

            if ($locked->client_id !== $client->id) {
                throw ValidationException::withMessages([
                    'revision' => 'Pesanan bukan milik klien ini.',
                ]);
            }

            if (! $locked->canRequestRevision()) {
                throw ValidationException::withMessages([
                    'revision' => 'Batas revisi sudah habis, masih ada revisi yang diproses, atau hasil belum dapat direvisi.',
                ]);
            }

            $requestNumber = ((int) OrderRevision::query()
                ->where('order_id', $locked->id)
                ->max('revision_number')) + 1;

            $revision = OrderRevision::query()->create([
                'order_id' => $locked->id,
                'order_submission_id' => $locked->currentSubmission?->id,
                'requested_by' => $client->id,
                'revision_number' => $requestNumber,
                'notes' => trim($notes),
                'status' => OrderRevision::STATUS_PENDING_ADMIN,
                'requested_at' => now(),
            ]);

            $locked->update([
                'status' => Order::STATUS_REVISION_REQUESTED,
            ]);

            return $revision;
        });
    }

    public function forwardRevision(
        OrderRevision $revision,
        User $admin,
        ?string $adminNotes = null
    ): OrderRevision {
        return DB::transaction(function () use ($revision, $admin, $adminNotes) {
            $lockedRevision = OrderRevision::query()
                ->with('order')
                ->lockForUpdate()
                ->findOrFail($revision->id);

            if (! $admin->isAdmin()) {
                throw ValidationException::withMessages([
                    'revision' => 'Hanya admin yang dapat meneruskan revisi.',
                ]);
            }

            if ($lockedRevision->status !== OrderRevision::STATUS_PENDING_ADMIN) {
                throw ValidationException::withMessages([
                    'revision' => 'Pengajuan revisi ini sudah diproses.',
                ]);
            }

            $order = Order::query()
                ->lockForUpdate()
                ->findOrFail($lockedRevision->order_id);

            if ($order->status !== Order::STATUS_REVISION_REQUESTED) {
                throw ValidationException::withMessages([
                    'revision' => 'Status pesanan sudah berubah dan pengajuan tidak dapat diteruskan.',
                ]);
            }

            if (! $order->freelancer_id) {
                throw ValidationException::withMessages([
                    'revision' => 'Pesanan belum memiliki freelancer.',
                ]);
            }

            if ($order->revision_used >= $order->revision_limit) {
                throw ValidationException::withMessages([
                    'revision' => 'Batas revisi pesanan sudah habis.',
                ]);
            }

            $approvedNumber = $order->revision_used + 1;

            $lockedRevision->update([
                'status' => OrderRevision::STATUS_FORWARDED,
                'approved_revision_number' => $approvedNumber,
                'forwarded_by' => $admin->id,
                'admin_notes' => $adminNotes ? trim($adminNotes) : null,
                'forwarded_at' => now(),
            ]);

            $order->update([
                'status' => Order::STATUS_REVISION,
                'revision_used' => $approvedNumber,
            ]);

            return $lockedRevision->fresh([
                'order.client',
                'order.freelancer',
                'submission.files',
                'forwarder',
            ]);
        });
    }

    public function rejectRevision(
        OrderRevision $revision,
        User $admin,
        string $adminNotes
    ): OrderRevision {
        return DB::transaction(function () use ($revision, $admin, $adminNotes) {
            $lockedRevision = OrderRevision::query()
                ->lockForUpdate()
                ->findOrFail($revision->id);

            if (! $admin->isAdmin()) {
                throw ValidationException::withMessages([
                    'revision' => 'Hanya admin yang dapat menolak revisi.',
                ]);
            }

            if ($lockedRevision->status !== OrderRevision::STATUS_PENDING_ADMIN) {
                throw ValidationException::withMessages([
                    'revision' => 'Pengajuan revisi ini sudah diproses.',
                ]);
            }

            $order = Order::query()
                ->lockForUpdate()
                ->findOrFail($lockedRevision->order_id);

            if ($order->status !== Order::STATUS_REVISION_REQUESTED) {
                throw ValidationException::withMessages([
                    'revision' => 'Status pesanan sudah berubah dan pengajuan tidak dapat ditolak.',
                ]);
            }

            $lockedRevision->update([
                'status' => OrderRevision::STATUS_REJECTED,
                'forwarded_by' => $admin->id,
                'admin_notes' => trim($adminNotes),
                'rejected_at' => now(),
            ]);

            $order->update([
                'status' => Order::STATUS_REVIEW,
            ]);

            return $lockedRevision->fresh(['order', 'requester', 'forwarder']);
        });
    }

    public function approveResult(Order $order, User $client): Order
    {
        return DB::transaction(function () use ($order, $client) {
            $locked = Order::query()
                ->with(['currentSubmission.files', 'freelancer'])
                ->lockForUpdate()
                ->findOrFail($order->id);

            if ($locked->client_id !== $client->id || $locked->status !== Order::STATUS_REVIEW) {
                throw ValidationException::withMessages([
                    'order' => 'Hasil pesanan belum dapat disetujui.',
                ]);
            }

            if (! $locked->currentSubmission) {
                throw ValidationException::withMessages([
                    'order' => 'File hasil belum tersedia.',
                ]);
            }

            if ($locked->revisions()
                ->whereIn('status', [
                    OrderRevision::STATUS_PENDING_ADMIN,
                    OrderRevision::STATUS_FORWARDED,
                    OrderRevision::STATUS_IN_PROGRESS,
                ])
                ->exists()) {
                throw ValidationException::withMessages([
                    'order' => 'Masih ada proses revisi yang belum selesai.',
                ]);
            }

            $locked->currentSubmission->files()->update(['is_final' => true]);
            $locked->currentSubmission->update(['submission_type' => 'final']);
            $locked->update([
                'status' => Order::STATUS_DONE,
                'completed_at' => now(),
            ]);

            $this->wallets->creditOrderEarning($locked->fresh('freelancer'));

            return $locked->fresh(['currentSubmission.files', 'freelancer']);
        });
    }
}
