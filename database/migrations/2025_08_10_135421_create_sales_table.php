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


            $table->dateTimeTz('payment_date')->useCurrent();
            $table->foreignId('created_by')->constrained(table: 'users', indexName: 'fk_sales_user_create')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('updated_by')->constrained(table: 'users', indexName: 'fk_sales_user_update')->onUpdate('cascade')->onDelete('cascade');
            // add note column
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
        // // DDL October 28, 2025: Remove columns added for loyalty program
        // ALTER TABLE Sales
        // DROP COLUMN refunded_amount,
        // DROP COLUMN status,
        // DROP COLUMN shipping_status,
        // DROP COLUMN next_payment_date,
        // ADD COLUMN note TEXT NULL;
    }
};
