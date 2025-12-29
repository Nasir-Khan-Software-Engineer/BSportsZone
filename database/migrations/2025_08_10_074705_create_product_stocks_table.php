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
        Schema::create('product_stocks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('posid')->default(1);
            $table->foreignId('product_id')->constrained(table: 'products', indexName: 'product_stock_product')->onUpdate('cascade')->onDelete('cascade');
            $table->enum('change_type', ['IN', 'OUT']);
            $table->integer('quantity')->default(1);
            $table->decimal('price', $precision = 8, $scale = 2)->default(0);
            $table->decimal('discount', $precision = 8, $scale = 2)->default(0);
            $table->decimal('tax', $precision = 8, $scale = 2)->default(0);
            $table->enum('reference_type', ['PURCHASE', 'SALE', 'MANUAL_ADJUSTMENT']);
            $table->bigInteger('reference_id')->nullable();
            $table->foreignId('created_by')->constrained(table: 'users', indexName: 'product_stock_user')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_stocks');
    }
};
