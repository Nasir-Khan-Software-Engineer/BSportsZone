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
        Schema::table('accountinfos', function (Blueprint $table) {
            $table->string('productCodePrefix', 100)->nullable()->after('POSID');
            $table->string('invoiceNumberPrefix', 100)->nullable()->after('productCodePrefix');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accountinfos', function (Blueprint $table) {
            $table->dropColumn([
                'productCodePrefix',
                'invoiceNumberPrefix'
            ]);
        });
    }
};
