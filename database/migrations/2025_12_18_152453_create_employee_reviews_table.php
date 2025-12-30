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
        Schema::create('employee_reviews', function (Blueprint $table) {
            $table->id();
            $table->integer('POSID');
            $table->unsignedBigInteger('employee_id');
            $table->date('review_date');
            $table->string('title');
            $table->enum('status', ['positive', 'negative', 'warning']);
            $table->text('details')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            
            $table->index('POSID', 'idx_employee_reviews_posid');
            $table->index('employee_id', 'idx_employee_reviews_employee_id');
            $table->index('review_date', 'idx_employee_reviews_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_reviews');
    }
};
