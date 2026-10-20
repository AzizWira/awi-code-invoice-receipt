PATCH v1.0.4 — Awi Code Invoice & Receipt Manager

Patch dibuat untuk source v1.0.3. Ekstrak ISI folder patch ke akar project.
Fiturnya: responsive, rekap bulanan/tahunan & cetak/PDF, perbaikan stale subtotal/total dalam satu penyimpanan, alat audit total lama.

1. Backup database dan project.
2. Copy file patch, replace/merge di project. Jangan hapus file project yang lain.
3. php artisan optimize:clear
4. php artisan awicode:recalculate-totals (dry-run, aman)
5. Uji tambah item setelah DP cukup 1 kali save.

Khusus Hostinger folder app/ & public_html/: setelah git pull, jalankan:
  cd ~/domains/invoice.awicode.com
  cp app/public/assets/app.css public_html/assets/app.css
  cp app/public/assets/app.js public_html/assets/app.js
  cd app
  /opt/alt/php84/usr/bin/php artisan optimize:clear

Tidak perlu migration. Jangan jalankan migrate:fresh.
Jika ingin memperbaiki transaksi lama yang tidak sinkron: backup database dan jalankan
  php artisan awicode:recalculate-totals --apply
Perintah ini tidak mengubah snapshot dokumen yang sudah dibagikan.

Catatan lengkap: README.md & docs/QA-v1.0.4.md.
