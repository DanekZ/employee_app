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
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->enum('jenis', ['tidak_masuk', 'terlambat', 'pulang_awal', 'keluar_kantor'])->after('user_id');
            $table->renameColumn('tanggal', 'tanggal_mulai');
        });

        Schema::table('leave_requests', function (Blueprint $table) {
            $table->date('tanggal_selesai')->nullable()->after('tanggal_mulai');
            $table->integer('durasi_menit')->nullable()->after('tanggal_selesai');
            $table->time('jam_mulai')->nullable()->after('durasi_menit');
            $table->time('jam_selesai')->nullable()->after('jam_mulai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropColumn(['jenis', 'tanggal_selesai', 'durasi_menit', 'jam_mulai', 'jam_selesai']);
        });

        Schema::table('leave_requests', function (Blueprint $table) {
            $table->renameColumn('tanggal_mulai', 'tanggal');
        });
    }
};
