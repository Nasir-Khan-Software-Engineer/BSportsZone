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
        Schema::create('shops', function (Blueprint $table) {
            $table->bigInteger('POSID');
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('primaryPhone')->unique();
            $table->string('secondaryPhone')->nullable()->unique();
            $table->text('address');
            $table->string('district');
            $table->string('division');
            $table->string('thana');
            $table->text('about')->nullable();
            $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('shops');
    }
};
