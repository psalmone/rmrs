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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g. RICE-SIN-50
            $table->string('name');
            $table->enum('category', ['raw_palay', 'milled_rice', 'by_product'])->default('milled_rice');
            $table->string('variety')->nullable(); // Sinandomeng, Dinorado, RC-222, etc.
            $table->string('unit')->default('sack_50kg'); // sack_50kg, sack_25kg, kg, sack
            $table->decimal('current_stock', 12, 2)->default(0); // in unit quantity
            $table->decimal('reorder_level', 12, 2)->default(20);
            $table->decimal('unit_cost', 10, 2)->default(0);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
