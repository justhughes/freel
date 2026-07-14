<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->char('order_code', 17)->unique();
            $table->foreignId('client_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('service_package_id')
                ->constrained('service_packages')
                ->restrictOnDelete();
            $table->foreignId('production_slot_id')
                ->nullable()
                ->constrained('production_slots')
                ->nullOnDelete();
            $table->foreignId('freelancer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('voucher_id')->nullable()->constrained('vouchers')->nullOnDelete();
            $table->string('title', 180);
            $table->string('business_name', 120);
            $table->longText('product_description');
            $table->text('target_audience')->nullable();
            $table->string('visual_reference', 255)->nullable();
            $table->longText('brief')->nullable();
            $table->string('platform', 50)->nullable();
            $table->string('content_size', 50)->nullable();
            $table->unsignedSmallInteger('quantity')->default(1);
            $table->string('speed_type', 15)->default('regular');
            $table->date('booking_date');
            $table->date('start_date')->nullable();
            $table->timestamp('deadline_at')->nullable();
            $table->unsignedBigInteger('base_price');
            $table->unsignedBigInteger('speed_fee')->default(0);
            $table->unsignedBigInteger('subtotal');
            $table->unsignedBigInteger('discount_amount')->default(0);
            $table->unsignedBigInteger('total_amount');
            $table->unsignedBigInteger('freelancer_earning')->default(0);
            $table->unsignedBigInteger('platform_margin')->default(0);
            $table->unsignedTinyInteger('revision_limit')->default(0);
            $table->unsignedTinyInteger('revision_used')->default(0);
            $table->string('status', 30)->default('pending_payment')->index();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('taken_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['service_package_id', 'status']);
            $table->index(['freelancer_id', 'status']);
            $table->index(['client_id', 'status']);
        });

        Schema::create('order_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('uploaded_by')->constrained('users')->restrictOnDelete();
            $table->string('asset_type', 20)->default('raw');
            $table->string('original_name', 255);
            $table->string('file_path', 500);
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('file_size')->default(0);
            $table->timestamps();
        });

        Schema::create('voucher_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voucher_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->unsignedBigInteger('discount_amount');
            $table->timestamp('used_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voucher_usages');
        Schema::dropIfExists('order_assets');
        Schema::dropIfExists('orders');
    }
};
