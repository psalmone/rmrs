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
        Schema::create('milling_outputs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('milling_batch_id')->constrained('milling_batches')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->enum('output_type', ['milled_rice', 'darak_bran', 'binlid_broken', 'ipa_husk']);
            $table->decimal('quantity_units', 10, 2)->default(0); // number of sacks/bags
            $table->decimal('weight_kg', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('milling_outputs');
    }
};
