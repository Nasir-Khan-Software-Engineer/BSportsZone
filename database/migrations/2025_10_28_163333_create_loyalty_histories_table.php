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
        Schema::create('loyalty_histories', function (Blueprint $table) {
            $table->id();

            // First create the column
            $table->bigInteger('posid');

            // Then define the foreign key
            $table->foreign('posid')->references('POSID')->on('accountinfos')->onDelete('cascade');

            $table->foreignId('card_id')->constrained('loyalty_cards')->onDelete('cascade');
            $table->foreignId('sales_id')->constrained('sales')->onDelete('cascade');

            $table->enum('discount_type', ['Percentage', 'Fixed', 'None']);
            $table->decimal('discount_value', 10, 2);
            $table->decimal('discount_amount', 10, 2);
            $table->text('note')->nullable();
            $table->boolean('isSkipped')->default(false);

            $table->timestamps();

            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');

            $table->index('card_id', 'idx_loyalty_histories_card_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_histories');
        // -- Add index to loyalty_histories.card_id for fast visit count lookup
        // CREATE INDEX idx_loyalty_histories_card_id
        // ON loyalty_histories (card_id);
    }
};
