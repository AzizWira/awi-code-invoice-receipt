Awi Code Invoice & Receipt Manager — Patch v1.0.3
=================================================

Patch ini dibuat untuk baseline v1.0.2.

Perubahan utama:
1. Semua alert/confirm/prompt JavaScript native diganti modal/toast custom Awi Code.
2. Dialog custom otomatis mengikuti Light Mode dan Dark Mode.
3. Customer tetap bisa Arsip/Pulihkan, dan kini juga bisa Hapus Permanen.
4. Hapus Permanen customer yang memiliki transaksi akan menghapus transaksi dan data turunannya setelah warning kuat.
5. Asset cache-busting diperbarui ke v1.0.3.

Cara pasang:
- Extract isi patch ke root project dan replace file yang sama.
- Jalankan: php artisan optimize:clear
- Restart php artisan serve bila sedang berjalan.

Tidak ada migration baru pada v1.0.3.
