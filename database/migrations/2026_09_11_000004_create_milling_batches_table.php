<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('milling_batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_number')->unique(); // e.g. BATCH-2026-001
            $table->foreignId('operator_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('palay_intake_id')->nullable()->constrained('palay_intakes')->nullOnDelete();
            $table->string('palay_variety')->default('Inbred');
            $table->integer('input_sacks')->default(0);
            $table->decimal('input_weight_kg', 12, 2)->default(0);
            $table->dateTime('started_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->enum('status', ['in_progress', 'completed', 'cancelled'])->default('in_progress');
            $table->decimal('total_milled_output_kg', 12, 2)->default(0);
            $table->decimal('recovery_rate_pct', 5, 2)->default(0); // recovery % = (output / input) * 100
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('milling_batches');
    }
};
