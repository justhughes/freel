<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_categories', function (Blueprint $table) {
            $table->id();
            $table->char('code', 4)->unique();
            $table->string('name', 80)->unique();
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('freelancer_quota')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('service_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_category_id')
                ->constrained('service_categories')
                ->restrictOnDelete();
            $table->char('code', 8)->unique();
            $table->string('name', 120);
            $table->string('slug', 140)->unique();
            $table->text('description');
            $table->json('includes')->nullable();
            $table->unsignedBigInteger('base_price');
            $table->unsignedTinyInteger('regular_days')->default(3);
            $table->unsignedTinyInteger('fast_days')->nullable();
            $table->unsignedTinyInteger('express_days')->nullable();
            $table->decimal('fast_fee_percent', 5, 2)->default(30.00);
            $table->decimal('express_fee_percent', 5, 2)->default(60.00);
            $table->unsignedTinyInteger('revision_limit')->default(1);
            $table->unsignedSmallInteger('total_slot')->default(0);
            $table->decimal('freelancer_fee_percent', 5, 2)->default(80.00);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->unique(['service_category_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_packages');
        Schema::dropIfExists('service_categories');
    }
};
