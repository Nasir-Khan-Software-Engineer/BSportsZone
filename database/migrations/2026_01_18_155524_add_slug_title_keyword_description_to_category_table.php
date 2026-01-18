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
        Schema::table('category', function (Blueprint $table) {
            $table->string('slug', 100)->nullable()->after('name');
            $table->string('title')->nullable()->after('slug');
            $table->text('keyword')->nullable()->after('title');
            $table->text('description')->nullable()->after('keyword');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('category', function (Blueprint $table) {
            $table->dropColumn(['slug', 'title', 'keyword', 'description']);
        });
    }
};
