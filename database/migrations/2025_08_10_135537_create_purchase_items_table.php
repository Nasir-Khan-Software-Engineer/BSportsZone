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
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained(table: 'purchases', indexName: 'fk_purchase_items_purchases')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('product_id')->constrained(table: 'products', indexName: 'fk_purchase_item_products')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('product_stock_id')->constrained(table: 'product_stocks', indexName: 'fk_purchase_items_product_stocks')->onUpdate('cascade')->onDelete('cascade');
            // removed this product_stock_id
            // this column will be act like barcode, but in future we use actual barcode number or replace the id as barcode
            $table->enum('purchase_item_type', ['PURCHASE', 'REFUND'])->default('PURCHASE');

            $table->decimal('product_price', $precision = 8, $scale = 2)->default(0);
            $table->decimal('selling_price', $precision = 8, $scale = 2)->default(0);
            $table->decimal('discount', $precision = 8, $scale = 2)->default(0);
            $table->decimal('tax', $precision = 8, $scale = 2)->default(0);
            $table->integer('quantity')->default(1);
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_items');
    }
};
