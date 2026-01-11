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
        Schema::create('sales_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('POSID')->default(1);
            $table->foreignId('sales_id')->constrained(table: 'sales', indexName: 'fk_sales_items_Sales')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('product_id')->constrained(table: 'products', indexName: 'fk_sales_item_products')->onUpdate('cascade')->onDelete('cascade');
            
            $table->unsignedBigInteger('variation_id')->nullable();
            $table->string('variant_tagline')->nullable();
            $table->string('type')->default('Service');

            $table->decimal('product_price', $precision = 8, $scale = 2)->default(0);
            $table->decimal('selling_price', $precision = 8, $scale = 2)->default(0);
            $table->integer('quantity')->default(1);
            $table->string('description')->nullable();
            $table->string('discount_type')->nullable()->comment('fixed or percentage');
            $table->decimal('discount_value', $precision = 8, $scale = 2)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unsignedBigInteger('staff_id')->nullable();
            $table->foreign('staff_id')->references('id')->on('employees')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_items');
    }
};
