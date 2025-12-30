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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->integer('POSID');
            $table->unsignedBigInteger('shopId');
            $table->foreign('shopId')->references('id')->on('shops')->onDelete('cascade');
            $table->unsignedBigInteger("categoryId");
            $table->foreign('categoryId')->references('id')->on('expense_categories')->onDelete('cascade');
            $table->string('title');
            $table->decimal('amount', total: 10, places: 2);
            $table->string('note')->nullable();
            $table->dateTime('expenseDate', precision: 0);
            $table->unsignedBigInteger('created_by')->unsigned()->index()->nullable();
            $table->unsignedBigInteger('updated_by')->unsigned()->index()->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExist("Expense");
    }
};
