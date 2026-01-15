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
        Schema::create('order_lifecycles', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('POSID');
            $table->foreignId('sales_id')->constrained(table: 'sales', indexName: 'FK_order_lifecycles_sales')->onUpdate('cascade')->onDelete('cascade');
            $table->string('status'); // Pending, Confirmed, Cancelled, Delivered to Courier, Received, Customer Returned
            $table->text('note')->nullable();
            $table->string('created_by'); // User Name or Courier (text field, not user id)
            $table->string('updated_by')->nullable(); // User Name or Courier (text field, not user id)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_lifecycles');
    }
};
