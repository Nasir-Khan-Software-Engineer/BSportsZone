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
        Schema::create('loyalty_cards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->bigInteger('POSID'); // matches accountinfos
            $table->string('card_number', 20)->unique();
            $table->date('valid_until');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('POSID')->references('POSID')->on('accountinfos')->onDelete('cascade');

            $table->unique(['POSID', 'card_number']);

            $table->index('customer_id', 'idx_loyalty_cards_customer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_cards');
        // -- Add index to loyalty_cards.customer_id for fast joins
        // CREATE INDEX idx_loyalty_cards_customer_id
        // ON loyalty_cards (customer_id);
    }
};
