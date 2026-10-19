# Aplikasi Karyawan — Sinarta MJS
 
Aplikasi manajemen kepegawaian berbasis web untuk mengelola presensi absensi berbasis GPS, pengajuan izin/cuti, lembur, perjalanan dinas, alur approval atasan, serta kalkulasi dan penerbitan slip gaji (payroll) karyawan secara otomatis.
 
## Fitur Utama
 
- **Presensi Harian (GPS & Maps)**: Check-in dan check-out dengan perekaman titik koordinat GPS realtime dan tautan verifikasi lokasi Google Maps.
- **Pengajuan Izin / Cuti**: Mendukung 4 jenis izin (tidak masuk kerja, datang terlambat, pulang lebih awal, keluar kantor sementara) dengan validasi tanggal dan jam.
- **Pengajuan Lembur**: Pengajuan jam lembur dan deskripsi pekerjaan dengan persetujuan atasan. Terintegrasi langsung dengan kalkulasi penambahan gaji lembur.
- **Pengajuan Dinas Luar**: Pengajuan tugas luar kantor dengan pemilihan transportasi (kendaraan dinas, pribadi, umum) serta jam keluar-kembali.
- **Alur Approval Terpadu**: Atasan dapat meninjau, menyetujui (*Approve*), atau menolak (*Reject*) berkas pengajuan bawahan secara langsung.
- **Rekap & Laporan Visualisasi**: Pemantauan kehadiran seluruh staf, filter interaktif (karyawan, tanggal, bulan, status), tren kehadiran harian, dan metrik kedisiplinan kerja tim.
- **Sistem Penggajian & Slip Gaji (Payroll)**:
  - Konfigurasi tarif global: rate lembur/jam, rate dinas/jam, potongan alpha, dan potongan izin.
  - Pengelolaan gaji pokok per karyawan melalui modal interaktif.
  - Perhitungan otomatis slip gaji bulanan (Gaji Pokok + Lembur + Dinas - Potongan Alpha/Izin).
  - Pratinjau rincian komponen pendapatan & potongan serta penerbitan (*publish*) massal.
  - Halaman **Slip Gaji Saya** untuk karyawan melihat riwayat dan mencetak dokumen slip gaji resmi (*print-friendly*).

## Tech Stack
 
- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Blade Views + Tailwind CSS + Alpine.js
- **Icons**: FontAwesome 6
- **Database**: MySQL / SQLite
- **Testing**: Pest PHP
- **Code Formatter**: Laravel Pint
 
## Struktur Hak Akses (Role)
 
| Role | Cakupan Hak Akses Utama |
|---|---|
| **Karyawan** | Presensi GPS, ajukan izin/lembur/dinas, pantau status pengajuan mandiri, akses & cetak **Slip Gaji Saya** |
| **Atasan** | Seluruh akses Karyawan + Dashboard Approval (Approve/Reject), Rekap Absensi Tim, Laporan Visualisasi Presensi, Manajemen Penggajian (konfigurasi rate, gaji pokok, generate & terbitkan slip gaji) |
| **Admin** | Manajemen pengguna, presensi, akses approval, rekapitulasi laporan absensi seluruh divisi |
 
## Instalasi & Menjalankan Aplikasi
 
```bash
git clone <repo-url>
cd employee_app
composer install
npm install
cp .env.example .env
php artisan key:generate
```
 
Sesuaikan konfigurasi database pada file `.env`, lalu jalankan migrasi dan seeding data:
 
```bash
php artisan migrate --seed
npm run build
php artisan serve
```
 
## Menjalankan Pengujian (Testing)
 
```bash
php artisan test
```