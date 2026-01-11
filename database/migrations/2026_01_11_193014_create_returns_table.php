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
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->integer('POSID');
            $table->foreignId('customer_id')->constrained(table: 'customers', indexName: 'fk_returns_customer')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('sale_id')->constrained(table: 'sales', indexName: 'fk_returns_sale')->onUpdate('cascade')->onDelete('cascade');
            $table->string('reason')->nullable();
            $table->text('note')->nullable();
            $table->string('status')->default('pending'); // pending, completed, cancelled
            $table->decimal('total_amount', $precision = 8, $scale = 2)->default(0);
            $table->decimal('total_payable_atm', $precision = 8, $scale = 2)->default(0);
            $table->decimal('adjustment_amt', $precision = 8, $scale = 2)->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            
            $table->index('POSID', 'idx_returns_posid');
            $table->index('customer_id', 'idx_returns_customer');
            $table->index('sale_id', 'idx_returns_sale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('returns');
    }
};
