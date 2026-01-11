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
        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id();
            $table->integer('POSID');
            $table->unsignedBigInteger('purchase_id')->nullable();
            $table->unsignedBigInteger('purchase_item_id')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('product_variant_id');
            $table->integer('quantity');
            $table->string('transaction_type'); // 'add', 'move', 'update', 'sale'
            $table->integer('old_stock');
            $table->integer('new_stock');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('purchase_id')->references('id')->on('purchases')->onDelete('set null');
            $table->foreign('purchase_item_id')->references('id')->on('purchase_items')->onDelete('set null');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('product_variant_id')->references('id')->on('variations')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            
            $table->index('POSID', 'idx_stock_transactions_posid');
            $table->index('product_variant_id', 'idx_stock_transactions_variant');
            $table->index('transaction_type', 'idx_stock_transactions_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transactions');
    }
};
