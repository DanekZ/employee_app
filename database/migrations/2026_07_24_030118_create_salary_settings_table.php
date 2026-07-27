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
       Schema::create('salary_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('rate_lembur_per_jam', 12, 2)->default(0);
            $table->decimal('potongan_per_hari_alpha', 12, 2)->default(0);
            $table->decimal('potongan_per_hari_izin', 12, 2)->default(0);
            $table->decimal('uang_saku_dinas_per_hari', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_settings');
    }
};
