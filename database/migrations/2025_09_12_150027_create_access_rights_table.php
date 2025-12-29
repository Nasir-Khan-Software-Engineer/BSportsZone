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
        Schema::create('access_rights', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->string('route_name')->nullable();
            $table->string('short_id')->nullable();
            $table->string('description')->nullable()->unique();
            $table->timestamps();

            // Unique constraint for route_name + short_id combination
            $table->unique(['route_name', 'short_id']);

            // Check constraint: route_name or short_id must not be null
            //$table->check('route_name IS NOT NULL OR short_id IS NOT NULL');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('access_rights');
    }
};
