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
        Schema::table('salary_settings', function (Blueprint $table) {
            $table->decimal('uang_bensin_per_dinas', 12, 2)->default(0)->after('uang_saku_dinas_per_hari');
        });

        Schema::table('payslips', function (Blueprint $table) {
            $table->integer('jumlah_dinas_bawa_kendaraan')->default(0)->after('jumlah_hari_dinas');
            $table->decimal('nominal_bensin', 12, 2)->default(0)->after('nominal_dinas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salary_settings', function (Blueprint $table) {
            $table->dropColumn('uang_bensin_per_dinas');
        });

        Schema::table('payslips', function (Blueprint $table) {
            $table->dropColumn(['jumlah_dinas_bawa_kendaraan', 'nominal_bensin']);
        });
    }
};
