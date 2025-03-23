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
        Schema::table('settings', function (Blueprint $table) {
            $table->longText('terms_conditions');
            $table->longText('refund_policy');
            $table->longText('privacy_policy');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('terms_conditions');
            $table->dropColumn('refund_policy');
            $table->dropColumn('privacy_policy');
        });
    }
};
