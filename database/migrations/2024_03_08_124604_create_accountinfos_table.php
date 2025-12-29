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
        Schema::create('accountinfos', function (Blueprint $table) {
            $table->bigInteger('POSID')->primary();
            $table->string('companyName');
            $table->longText('logo')->nullable();
            $table->string('primaryEmail');
            $table->string('secoundaryEmail')->nullable();
            $table->string('primaryPhone');
            $table->string('secondaryPhone')->nullable();
            $table->string('division');
            $table->string('district');
            $table->string('area');
            $table->text('address');
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
        Schema::dropIfExists('accountinfos');
    }
};
