# Daftar Halaman — Aplikasi Karyawan (Absensi, Izin, Lembur, Dinas)

Stack: Laravel + Inertia.js + Vue

---

## 0. Palet Warna Brand (Sinarta MJS)

| Warna | Kode | Keterangan |
|---|---|---|
| Primary (maroon) | `#800020` | Warna utama — tombol aksi utama |
| Primary variant (crimson) | `#A3082F` | Logo, nav aktif, hover state primary |
| Secondary (amber/emas) | `#EAB308` | Aksen, badge status "Pending" |
| Accent (hijau) | `#22C55E` | Badge/status "Approved", tombol sukses |
| Netral — background | Putih / off-white | Background utama halaman |
| Netral — teks nav | `#374151` | Warna teks navigasi/body |

---

## 1. Halaman Umum (Semua Role)

| Halaman | Keterangan |
|---|---|
| Login | Autentikasi masuk aplikasi |
| Register | Opsional — atau cukup admin yang buatkan akun karyawan |
| Dashboard | Ringkasan status absen hari ini, jumlah pengajuan pending, sisa cuti |
| Profil | Edit data diri, ganti password |

---

## 2. Halaman Karyawan

| Halaman | Keterangan |
|---|---|
| Absen | Check-in/check-out dengan kamera untuk foto selfie |
| Riwayat Absensi | List absensi per bulan (tanggal, jam masuk-keluar, status) |
| Ajukan Izin | Form pengajuan izin |
| Ajukan Lembur | Form pengajuan lembur |
| Ajukan Dinas Keluar Kantor | Form pengajuan dinas |
| Riwayat Pengajuan Saya | List semua pengajuan izin/lembur/dinas beserta statusnya |

---

## 3. Halaman Atasan

| Halaman | Keterangan |
|---|---|
| Daftar Pengajuan Pending | List pengajuan bawahan yang perlu di-approve/reject |
| Riwayat Approval | History pengajuan yang sudah diproses |
| Rekap Absensi Tim | Lihat absensi seluruh bawahan (opsional) |
| Generate Slip Gaji | Pilih periode & karyawan, sistem hitung otomatis, lalu terbitkan slip gaji |

---

## 4. Halaman Admin (opsional, jika role admin terpisah dari atasan)

| Halaman | Keterangan |
|---|---|
| Manajemen User | Tambah/edit/hapus karyawan, atur relasi atasan-bawahan, atur gaji pokok |
| Rekap Absensi Keseluruhan | Laporan absensi semua karyawan, filter per bulan/divisi |
| Export Laporan | Export ke Excel/PDF untuk payroll atau laporan bulanan |
| Pengaturan Gaji | Atur rate lembur per jam, potongan per hari alpha |

---

## 5. Halaman Karyawan — Slip Gaji

| Halaman | Keterangan |
|---|---|
| Slip Gaji Saya | List slip gaji per periode, lihat rincian, download PDF. Notifikasi email dikirim saat slip gaji terbit |

---

## Konsep Fitur Slip Gaji

**Alur:**
1. Atasan/admin pilih periode (misal Juli 2026) → generate untuk 1 karyawan atau massal
2. Sistem hitung otomatis: Gaji Pokok + (jam lembur approved × rate) − (hari alpha × potongan) − PPh 21 (saat ini 0)
3. Slip gaji tersimpan (status draft → terbit)
4. Email notifikasi terkirim ke karyawan, mengarahkan login ke aplikasi untuk lihat/download
5. Karyawan download slip gaji dalam bentuk PDF dari halaman "Slip Gaji Saya"

**Tabel tambahan yang dibutuhkan:**
- Kolom `gaji_pokok` di tabel `users`
- Tabel `salary_settings` (rate lembur per jam, potongan per hari alpha — bisa diubah admin)
- Tabel `payslips` (data slip gaji tersimpan per periode, bersifat historis — tidak dihitung ulang otomatis kalau rate berubah di kemudian hari)

**Catatan:** Perhitungan lembur & potongan absen diambil dari data `overtime_requests` dan `attendances` yang sudah ada. PDF generator disarankan pakai package `barryvdh/laravel-dompdf`.

---

## Prioritas Pengerjaan (MVP)

1. Login, Dashboard, Profil
2. Absen + Riwayat Absensi
3. Ajukan Izin/Lembur/Dinas + Riwayat Pengajuan Saya
4. Daftar Pengajuan Pending + Riwayat Approval (Atasan)
5. *(Tahap lanjut)* Rekap Absensi Tim, Manajemen User, Export Laporan
6. *(Tahap lanjut)* Slip Gaji — Generate (Atasan/Admin) + Slip Gaji Saya (Karyawan)
