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
        Schema::create('loyalty_settings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('POSID')->unique();
            $table->decimal('minimum_sales_amount', 10, 2);
            $table->enum('minimum_sales_amount_applies_for', ['Single', 'All'])
              ->default('Single');
            $table->integer('validity_period_months');
            $table->integer('max_visits');
            $table->integer('max_visits_per_day');
            $table->text('rules_text')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();

            $table->timestamps();

            // Optional: Foreign keys
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('modified_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_settings');
    }
};
