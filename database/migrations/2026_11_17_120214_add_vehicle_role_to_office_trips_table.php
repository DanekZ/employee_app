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
        Schema::table('office_trips', function (Blueprint $table) {
            $table->string('peran_kendaraan', 30)->nullable()->after('alat_transportasi');
            $table->foreignId('menumpang_user_id')->nullable()->after('peran_kendaraan')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('office_trips', function (Blueprint $table) {
            $table->dropForeign(['menumpang_user_id']);
            $table->dropColumn(['peran_kendaraan', 'menumpang_user_id']);
        });
    }
};
