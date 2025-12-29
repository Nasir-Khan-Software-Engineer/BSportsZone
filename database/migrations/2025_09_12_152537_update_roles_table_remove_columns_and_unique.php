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
         Schema::table('roles', function (Blueprint $table) {
            // Drop obsolete columns
            $table->dropColumn(['isActive', 'permissions']);

            // Add unique constraint for POSID + name
            $table->unique(['POSID', 'name']);

            // Optional: add foreign keys for created_by and updated_by
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            // Re-add dropped columns
            $table->boolean('isActive')->default(1);
            $table->text('permissions')->nullable();

            // Drop unique constraint
            $table->dropUnique(['POSID', 'name']);

            // Drop foreign keys
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
        });
    }
};
