<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('freelancer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->text('bio')->nullable();
            $table->unsignedTinyInteger('experience_years')->default(0);
            $table->string('portfolio_url', 500)->nullable();
            $table->string('portfolio_file_path', 500)->nullable();
            $table->string('application_status', 25)->default('pending')->index();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->string('payout_type', 20)->nullable();
            $table->string('payout_provider', 80)->nullable();
            $table->string('payout_account_number', 30)->nullable();
            $table->string('payout_account_holder', 120)->nullable();
            $table->timestamps();
        });

        Schema::create('freelancer_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('freelancer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('service_category_id')
                ->constrained('service_categories')
                ->cascadeOnDelete();
            $table->string('status', 20)->default('pending')->index();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->unique(['freelancer_id', 'service_category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('freelancer_skills');
        Schema::dropIfExists('freelancer_profiles');
    }
};
