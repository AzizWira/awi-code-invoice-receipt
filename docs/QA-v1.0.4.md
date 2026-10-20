# QA V1.0.4 — Awi Code

## Skenario wajib: bug transaksi setelah DP

1. Buat customer dan transaksi Final berisi jasa Website Rp2.000.000 tanpa diskon.
2. Catat DP Rp1.000.000 (DANA). Detail transaksi seharusnya menunjukkan subtotal/total Rp2.000.000, pembayaran Rp1.000.000, sisa Rp1.000.000, status *Dibayar Sebagian*.
3. **Edit transaksi hanya sekali**: tambah item "Fitur Login" 1 fitur Rp500.000, klik **Simpan & Finalkan**.
4. Pada halaman detail **segera setelah redirect**, tanpa masuk Edit/Simpan ulang, verifikasi subtotal=total=Rp2.500.000, pembayaran tetap Rp1.000.000, sisa Rp1.500.000, status *Dibayar Sebagian*.
5. Edit lagi, ganti fitur menjadi Rp1.000.000 dan diskon 10%; verifikasi subtotal Rp3.000.000, diskon Rp300.000, total Rp2.700.000, sisa Rp1.700.000 setelah hanya sekali simpan.
6. Bayar sisa Rp1.700.000; verifikasi status *Lunas*, sisa Rp0, Receipt dapat dibuat.
7. Bila Invoice pernah dibuat sebelum pengeditan, pastikan snapshot lama tidak berubah tanpa pilihan **Perbarui Snapshot**.
8. Ulangi dengan tanpa DP, harga nol, diskon nominal, hapus item, dan nilai baru kurang dari DP (butuh konfirmasi overpayment).

## Rekap

1. Buat 2 transaksi Final dalam bulan yang sama, satu Dibayar Sebagian, satu Lunas, serta 1 Draft dan 1 Dibatalkan. Pastikan hanya 2 Final yang dihitung.
2. Bulanan: validasi subtotal, diskon, nilai, dibayar, sisa, jumlah, status, dan daftar rincian.
3. Tahunan: periksa 12 bulan (termasuk bulan tanpa transaksi) dan kecocokan total akumulasi.
4. Ubah nilai transaksi Final; pastikan rekap berdasarkan tanggal transaksi menyesuaikan tanpa penggandaan.
5. Tes dengan tahun tanpa data: semua total nol dan UI tidak error.
6. Cetak menggunakan tombol **Cetak Rekap**; coba pilih **Save as PDF** dan pastikan header/periode, ringkasan, rincian, serta footnote dapat dibaca.
7. Klik **Unduh PDF**; buka PDF di pembaca terpisah; pastikan tidak menghasilkan error GD (GD harus aktif di PHP).
8. Coba akses `/reports` tanpa login dan pastikan redirect ke login.

## Responsive & tema

- Uji viewport 360, 390, 430, 768, 1024, 1440 px (Chrome DevTools).
- Dashboard, Transaksi, Detail, Form Edit, Customer, Jenis Jasa, Dokumen, dan Rekap: tidak ada konten terpotong/overflow layar yang tidak perlu.
- Pada ponsel, tabel admin tampil sebagai kartu dengan label kolom; tidak perlu scroll horisontal lebar.
- Side nav bisa dibuka/ditutup lewat tombol tutup, backdrop, Esc; tidak menghalangi konten setelah ditutup.
- Filter periode bulanan/tahunan: input Bulan hanya ditampilkan saat Bulanan aktif.
- Uji Light & Dark, termasuk modal konfirmasi dan rekap.
- Cetak Invoice/Receipt lama harus tetap portrait A4; cetak rekap menggunakan layout landscape.

## Pemeriksaan server

- `php -l` untuk seluruh PHP yang berubah dan `node --check public/assets/app.js`.
- `php artisan optimize:clear`, `php artisan route:list --path=reports`.
- `php artisan awicode:recalculate-totals` untuk menemukan data lama yang stale. Lakukan backup sebelum `--apply`.

**Catatan:** Regression checklist ini perlu dijalankan pada lingkungan Laravel yang memiliki `vendor/` dan database. Pemeriksaan sintaks saja tidak menggantikan uji browser/integrasi.
