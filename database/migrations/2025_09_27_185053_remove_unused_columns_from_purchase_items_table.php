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
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->dropForeign('fk_purchase_items_product_stocks');
            $table->dropColumn([
                'product_stock_id',
                'purchase_item_type',
                'discount',
                'tax',
                'description',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->foreignId('product_stock_id')->constrained(table: 'product_stocks', indexName: 'fk_purchase_items_product_stocks')->onUpdate('cascade')->onDelete('cascade');
            $table->enum('purchase_item_type', ['PURCHASE', 'REFUND'])->default('PURCHASE');
            $table->decimal('discount', 8, 2)->default(0);
            $table->decimal('tax', 8, 2)->default(0);
            $table->string('description')->nullable();
        });
    }
};
