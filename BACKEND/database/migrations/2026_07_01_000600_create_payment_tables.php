<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name', 50);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('payment_channels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_method_id')
                ->constrained('payment_methods')
                ->cascadeOnDelete();
            $table->string('code', 20)->unique();
            $table->string('name', 80);
            $table->string('account_name', 120)->nullable();
            $table->string('account_number', 50)->nullable();
            $table->text('instructions')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->char('payment_code', 17)->unique();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_method_id')->constrained()->restrictOnDelete();
            $table->foreignId('payment_channel_id')->constrained()->restrictOnDelete();
            $table->unsignedTinyInteger('attempt_number')->default(1);
            $table->string('gateway_reference', 100)->nullable()->unique();
            $table->unsignedBigInteger('amount');
            $table->string('status', 20)->default('pending')->index();
            $table->string('payment_url', 500)->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('failure_reason', 255)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['order_id', 'attempt_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('payment_channels');
        Schema::dropIfExists('payment_methods');
    }
};
