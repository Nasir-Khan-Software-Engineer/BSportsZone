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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->integer('POSID');
            $table->string('code');
            $table->string('name'); 
            $table->string('type')->default('Service');
            $table->integer('unit_id')->nullable();
            $table->integer('brand_id')->nullable();
            $table->string('image')->nullable();
            $table->double('price'); // service price 
            $table->text('description')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->unique(['POSID', 'code'], 'posid_code_unique');
            $table->unique(['POSID', 'name'], 'posid_name_unique');

            $table->unsignedBigInteger('staff_id')->nullable();
            $table->foreign('staff_id')->references('id')->on('employees')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
