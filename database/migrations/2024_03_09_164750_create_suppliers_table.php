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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->bigInteger('POSID')->primary();
            $table->string('id')->unique();
            $table->string('shortID')->unique();
            $table->string('name');
            $table->string('phone_1')->unique();
            $table->string('phone_2')->nullable();
            $table->string('phone_3')->nullable();
            $table->string('landPhone')->nullable();
            $table->string('email')->unique();
            $table->string('country');
            $table->string('city');
            $table->text('address');
            $table->text('note')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
