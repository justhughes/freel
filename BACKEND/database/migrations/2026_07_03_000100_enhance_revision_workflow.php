<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_revisions', function (Blueprint $table) {
            $table->foreignId('forwarded_by')
                ->nullable()
                ->after('requested_by')
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('result_submission_id')
                ->nullable()
                ->after('order_submission_id')
                ->constrained('order_submissions')
                ->nullOnDelete();

            $table->unsignedTinyInteger('approved_revision_number')
                ->nullable()
                ->after('revision_number');

            $table->text('admin_notes')->nullable()->after('notes');
            $table->timestamp('forwarded_at')->nullable()->after('requested_at');
            $table->timestamp('rejected_at')->nullable()->after('completed_at');

            $table->index(['order_id', 'status'], 'order_revisions_order_status_index');
        });

        $legacyRevisions = DB::table('order_revisions')
            ->whereIn('status', ['open', 'in_progress', 'completed'])
            ->get();

        foreach ($legacyRevisions as $legacyRevision) {
            $orderStatus = DB::table('orders')
                ->where('id', $legacyRevision->order_id)
                ->value('status');

            $wasAlreadyForwarded = in_array(
                $legacyRevision->status,
                ['in_progress', 'completed'],
                true
            ) || $orderStatus === 'revision';

            DB::table('order_revisions')
                ->where('id', $legacyRevision->id)
                ->update([
                    'status' => $wasAlreadyForwarded
                        ? ($legacyRevision->status === 'open' ? 'forwarded' : $legacyRevision->status)
                        : 'pending_admin',
                    'approved_revision_number' => $wasAlreadyForwarded
                        ? $legacyRevision->revision_number
                        : null,
                    'forwarded_at' => $wasAlreadyForwarded
                        ? $legacyRevision->requested_at
                        : null,
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('order_revisions', function (Blueprint $table) {
            $table->dropIndex('order_revisions_order_status_index');
            $table->dropConstrainedForeignId('forwarded_by');
            $table->dropConstrainedForeignId('result_submission_id');
            $table->dropColumn([
                'approved_revision_number',
                'admin_notes',
                'forwarded_at',
                'rejected_at',
            ]);
        });
    }
};
