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
        Schema::create('employee_designations', function (Blueprint $table) {
            $table->id();
            $table->integer('POSID');
            $table->string('name');
            $table->timestamps();
            $table->index('POSID', 'idx_employee_designations_posid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_designations');
    }
};
