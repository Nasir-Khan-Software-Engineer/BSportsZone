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
            $table->bigInteger('posid')->unique();
            $table->decimal('minimum_purchase_amount', 10, 2);
            $table->enum('minimum_purchase_amount_applies_for', ['Single', 'All'])
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
        // ddl script to remove max_discount_amount
        // ALTER TABLE loyalty_settings
        // DROP COLUMN max_discount_amount;
        // ddl to add max_visits_per_day
        // ALTER TABLE loyalty_settings
        // ADD COLUMN max_visits_per_day INTEGER;
        // ALTER TABLE `loyalty_settings`
        // DROP COLUMN `status`;

        // ALTER TABLE loyalty_settings
        // ADD COLUMN minimum_purchase_amount_applies_for ENUM('Single', 'All') NOT NULL DEFAULT 'Single'
        // AFTER minimum_purchase_amount;
    }
};
