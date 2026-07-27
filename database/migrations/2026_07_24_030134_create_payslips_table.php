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
      // payslips
        Schema::create('payslips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('periode');
            $table->decimal('gaji_pokok', 12, 2);
            $table->integer('total_jam_lembur')->default(0);
            $table->decimal('nominal_lembur', 12, 2)->default(0);
            $table->integer('jumlah_hari_alpha')->default(0);
            $table->decimal('potongan_absen', 12, 2)->default(0);
            $table->integer('jumlah_hari_dinas')->default(0);
            $table->decimal('nominal_dinas', 12, 2)->default(0);
            $table->integer('jumlah_hari_izin')->default(0);
            $table->decimal('potongan_izin', 12, 2)->default(0);
            $table->decimal('pph21', 12, 2)->default(0);
            $table->decimal('total_gaji_bersih', 12, 2);
            $table->enum('status', ['draft', 'terbit'])->default('draft');
            $table->timestamp('diterbitkan_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'periode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payslips');
    }
};
