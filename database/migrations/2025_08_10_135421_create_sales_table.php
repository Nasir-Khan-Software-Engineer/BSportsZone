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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('POSID');
            $table->foreignId('shop_id')->constrained(table: 'shops', indexName: 'fk_sales_shop')->onUpdate('cascade')->onDelete('cascade');
            $table->string('invoice_code');

            $table->decimal('total_amount', $precision = 8, $scale = 2)->default(0);
            $table->decimal('discount_amount', $precision = 8, $scale = 2)->default(0);
            $table->decimal('total_payable_amount', $precision = 8, $scale = 2)->default(0);
            $table->string('sales_from')->default('offline');
            $table->foreignId('customerId')->constrained(table: 'customers', indexName: 'FK_Sales_customer')->onUpdate('cascade')->onDelete('cascade');

            $table->dateTimeTz('payment_date')->useCurrent();
            $table->foreignId('created_by')->constrained(table: 'users', indexName: 'fk_sales_user_create')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('updated_by')->constrained(table: 'users', indexName: 'fk_sales_user_update')->onUpdate('cascade')->onDelete('cascade');
            $table->string('discount_type')->nullable(); 
            $table->decimal('discount_value', 15, 2)->nullable();
            $table->text('note')->nullable();
            $table->decimal('adjustmentAmt', 10, 2)->nullable()->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
