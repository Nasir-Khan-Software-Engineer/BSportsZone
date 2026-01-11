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
        Schema::create('return_payments', function (Blueprint $table) {
            $table->id();
            $table->integer('POSID');
            $table->foreignId('return_id')->constrained(table: 'returns', indexName: 'fk_return_payments_return')->onUpdate('cascade')->onDelete('cascade');
            $table->string('payment_method');
            $table->string('payment_via');
            $table->decimal('amount', $precision = 8, $scale = 2);
            $table->string('transaction_id')->nullable();
            $table->text('note')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            
            $table->index('POSID', 'idx_return_payments_posid');
            $table->index('return_id', 'idx_return_payments_return');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_payments');
    }
};
