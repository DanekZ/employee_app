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
        Schema::table('attendances', function (Blueprint $table) {
            $table->time('jam_istirahat_mulai')->nullable()->after('jam_masuk');
            $table->time('jam_istirahat_selesai')->nullable()->after('jam_istirahat_mulai');
            $table->string('status_istirahat', 20)->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['jam_istirahat_mulai', 'jam_istirahat_selesai', 'status_istirahat']);
        });
    }
};
