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
        Schema::create('pos_feature', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('POSID');
            $table->foreign('POSID')
                ->references('POSID')
                ->on('accountinfos')
                ->onDelete('cascade');

            $table->foreignId('feature_id')
                ->constrained('site_features')
                ->onDelete('cascade');
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();

            $table->unique(['POSID', 'feature_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_feature');
    }
};
