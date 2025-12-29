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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->integer('posid');
            $table->string('name');
            $table->string('gender');
            $table->string('email')->nullable()->unique();
            $table->string('phone1')->unique();
            $table->string('phone2')->nullable()->unique();
            $table->string('address')->nullable();
            $table->text('note')->nullable();
            $table->boolean('isActive')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->unsignedBigInteger('latest_card_id')->nullable();
            $table->index('latest_card_id', 'idx_latest_card_id');
           
            $table->string('hasLoyalty')->default('No'); // only for yes no search
            $table->enum('type', ['General', 'Loyal'])->default('General');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
        // ALTER TABLE customers
        // ADD COLUMN latest_card_id BIGINT UNSIGNED NULL,
        // ADD INDEX idx_latest_card_id (latest_card_id),
        // ADD CONSTRAINT fk_latest_card
        // FOREIGN KEY (latest_card_id) REFERENCES loyalty_cards(id)
        // ON DELETE SET NULL;

        // ALTER TABLE customers
        // ADD COLUMN type ENUM('General', 'Loyal') NOT NULL DEFAULT 'General' AFTER latest_card_id;
    }
};
