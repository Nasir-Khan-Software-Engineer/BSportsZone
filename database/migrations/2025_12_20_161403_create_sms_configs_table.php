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
        Schema::create('sms_configs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('POSID');
            $table->string('base_url');
            $table->string('username');
            $table->string('api_key');
            $table->string('sender_id');
            $table->string('campaign_id')->nullable()->default('null');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            
            $table->unique('POSID');
            $table->foreign('POSID')->references('POSID')->on('accountinfos')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_configs');
    }
};
