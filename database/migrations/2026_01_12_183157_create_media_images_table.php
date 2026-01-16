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
        Schema::create('media_images', function (Blueprint $table) {
            $table->id();
            $table->integer('POSID');
            $table->string('file_name'); // Stored file name with timestamp
            $table->string('file_path'); // Full path to the file
            $table->integer('size'); // File size in bytes
            $table->string('type'); // File type/extension (gif, jpg, jpeg, png)
            $table->string('relation'); // Product, Banner, Review, etc.
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            
            $table->index('POSID', 'idx_media_images_posid');
            $table->index('relation', 'idx_media_images_relation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_images');
    }
};
