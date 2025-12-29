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
        Schema::create('pos_settings', function (Blueprint $table) {
        $table->bigIncrements('id');

        $table->bigInteger('posid');
        $table->decimal('adjustment_min', 10, 2)->default(-5);
        $table->decimal('adjustment_max', 10, 2)->default(5);

        $table->timestamps();
        
        $table->unsignedBigInteger('created_by')->nullable();
        $table->unsignedBigInteger('updated_by')->nullable();

        $table->unique('posid');
        $table->foreign('posid')->references('posid')->on('accountinfos')->onDelete('cascade');
        $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_settings');
    }
};
