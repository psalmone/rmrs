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
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->string('adjustment_number')->unique(); // e.g. ADJ-2026-0001
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->enum('type', ['addition', 'subtraction']); // addition (+) or subtraction (-)
            $table->decimal('quantity', 12, 2);
            $table->decimal('previous_stock', 12, 2);
            $table->decimal('new_stock', 12, 2);
            $table->enum('reason', [
                'physical_audit',     // Discrepancy during manual inventory counting
                'spillage',           // Dropped sack / ruptured bag on warehouse floor
                'bag_damage',         // Rodent / water / torn packaging
                'silo_shrinkage',     // Moisture loss / silo evaporation
                'return_from_buyer',  // Returned undamaged goods
                'initial_stock',      // Initial baseline inventory loading
                'other'               // Miscellaneous
            ]);
            $table->foreignId('adjusted_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('adjustment_date')->useCurrent();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');
    }
};
