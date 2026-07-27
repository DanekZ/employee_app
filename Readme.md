# Aplikasi Karyawan — Sinarta MJS
 
Aplikasi manajemen karyawan berbasis web untuk mengelola absensi, pengajuan izin, lembur, dinas keluar kantor, dan slip gaji karyawan.
 
## Fitur
 
- **Absensi** — check-in/check-out dengan foto selfie dan validasi lokasi (geofencing)
- **Izin** — pengajuan dengan 4 jenis: tidak masuk kerja, datang terlambat, pulang lebih awal, dan izin keluar kantor sementara
- **Lembur** — pengajuan lembur dengan approval atasan
- **Dinas** — pengajuan dinas keluar kantor dengan approval atasan
- **Approval Workflow** — atasan dapat menyetujui/menolak pengajuan izin, lembur, dan dinas bawahannya
- **Slip Gaji** — perhitungan otomatis (gaji pokok, lembur, uang saku dinas, potongan alpha/izin), diterbitkan oleh atasan/admin dengan notifikasi email ke karyawan
## Tech Stack
 
- **Backend**: Laravel + Inertia.js
- **Frontend**: Vue 3 (Composition API) + Tailwind CSS
- **Auth**: Laravel Sanctum / session-based (bawaan starter kit)
- **Testing**: Pest
## Instalasi
 
```bash
git clone <repo-url>
cd <nama-project>
composer install
npm install
cp .env.example .env
php artisan key:generate
```
 
Sesuaikan konfigurasi database di `.env`, lalu jalankan:
 
```bash
php artisan migrate
npm run dev
php artisan serve
```
 
## Testing
 
```bash
php artisan test
```
 
## Struktur Role
 
| Role | Akses |
|---|---|
| Karyawan | Absensi, ajukan izin/lembur/dinas, lihat slip gaji sendiri |
| Atasan | Semua akses karyawan + approval pengajuan bawahan, generate & terbitkan slip gaji |
| Admin | Manajemen user, pengaturan gaji, rekap & laporan |