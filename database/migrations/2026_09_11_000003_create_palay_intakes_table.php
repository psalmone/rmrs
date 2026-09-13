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
        Schema::create('palay_intakes', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique(); // e.g. TKT-2026-0001
            $table->foreignId('farmer_id')->nullable()->constrained('farmers')->nullOnDelete();
            $table->string('farmer_name');
            $table->string('variety_type')->default('Inbred'); // Inbred, Hybrid, Fresh Wet, Dry
            $table->integer('bag_count')->default(0);
            $table->decimal('gross_weight_kg', 10, 2)->default(0);
            $table->decimal('tare_weight_kg', 10, 2)->default(0);
            $table->decimal('moisture_content_pct', 5, 2)->default(14.00); // Standard base is ~14%
            $table->decimal('deduction_kg', 10, 2)->default(0); // Deductions for moisture/foreign matter
            $table->decimal('net_weight_kg', 10, 2)->default(0);
            $table->decimal('price_per_kg', 8, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid');
            $table->foreignId('received_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('intake_date')->useCurrent();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('palay_intakes');
    }
};
