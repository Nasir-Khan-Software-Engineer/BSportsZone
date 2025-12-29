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
        Schema::create('sms_histories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('posid');
            $table->string('to_number');
            $table->string('from_number')->nullable();
            $table->string('source');
            $table->integer('message_length');
            $table->integer('sms_count');
            $table->timestamps();
            
            $table->foreign('posid')->references('POSID')->on('accountinfos')->onDelete('cascade');
            $table->index(['posid', 'created_at']);
            $table->index('source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_histories');
    }
};
