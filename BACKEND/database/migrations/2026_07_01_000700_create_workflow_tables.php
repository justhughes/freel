<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('freelancer_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 20)->default('taken');
            $table->timestamp('assigned_at');
            $table->timestamp('released_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('order_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('freelancer_id')->constrained('users')->restrictOnDelete();
            $table->unsignedTinyInteger('version');
            $table->string('submission_type', 20)->default('draft');
            $table->text('notes')->nullable();
            $table->timestamp('submitted_at');
            $table->boolean('is_current')->default(true)->index();
            $table->timestamps();

            $table->unique(['order_id', 'version']);
        });

        Schema::create('submission_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_submission_id')
                ->constrained('order_submissions')
                ->cascadeOnDelete();
            $table->string('original_name', 255);
            $table->string('file_path', 500);
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('file_size')->default(0);
            $table->boolean('is_final')->default(false);
            $table->timestamps();
        });

        Schema::create('order_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_submission_id')
                ->nullable()
                ->constrained('order_submissions')
                ->nullOnDelete();
            $table->foreignId('requested_by')->constrained('users')->restrictOnDelete();
            $table->unsignedTinyInteger('revision_number');
            $table->text('notes');
            $table->string('status', 20)->default('open')->index();
            $table->timestamp('requested_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['order_id', 'revision_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_revisions');
        Schema::dropIfExists('submission_files');
        Schema::dropIfExists('order_submissions');
        Schema::dropIfExists('order_assignments');
    }
};
