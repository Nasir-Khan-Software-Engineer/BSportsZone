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
        Schema::create('return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_id')->constrained(table: 'returns', indexName: 'fk_return_items_return')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('sales_item_id')->constrained(table: 'sales_items', indexName: 'fk_return_items_sales_item')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('qty')->default(1);
            $table->boolean('is_sellable')->default(true);
            $table->decimal('unit_price', $precision = 8, $scale = 2)->default(0);
            $table->timestamps();

            $table->index('return_id', 'idx_return_items_return');
            $table->index('sales_item_id', 'idx_return_items_sales_item');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_items');
    }
};
