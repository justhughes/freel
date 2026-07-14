<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_package_id')
                ->constrained('service_packages')
                ->cascadeOnDelete();
            $table->date('production_date');
            $table->unsignedSmallInteger('total_slots');
            $table->unsignedSmallInteger('reserved_slots')->default(0);
            $table->string('status', 15)->default('open')->index();
            $table->timestamps();

            $table->unique(['service_package_id', 'production_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_slots');
    }
};
