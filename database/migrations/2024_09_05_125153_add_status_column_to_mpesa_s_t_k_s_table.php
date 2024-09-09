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
        Schema::table('mpesa_s_t_k_s', function (Blueprint $table) {
            $table->string('status')->after('result_code')->default('pending');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mpesa_s_t_k_s', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
