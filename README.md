# Awi Code — Invoice & Receipt Manager

Baseline source **v1.0.4** berdasarkan PRD Awi Code tanggal 25 Agustus 2026.

Aplikasi internal satu-owner untuk mencatat transaksi jasa dan menghasilkan **Invoice** serta **Receipt** dari satu sumber data yang sama.

## Stack

- PHP 8.3+
- Laravel 13
- Blade
- MySQL / MariaDB
- CSS + JavaScript custom tanpa Vite/npm
- Dompdf untuk PDF server-side

> Frontend tidak membutuhkan `npm install` atau `npm run dev` karena asset V1 berada langsung di `public/assets`.

## Fitur V1

- Login Owner/Admin tanpa register publik.
- Dashboard ringkas.
- CRUD Customer/PIC dan perusahaan, termasuk arsip/pulihkan serta hapus permanen dengan warning.
- Master Jenis Jasa + opsi `Lainnya`.
- Satu transaksi = satu project.
- Multi-item jasa: nama, deskripsi, qty, satuan, harga satuan, subtotal otomatis.
- Diskon nominal / persen.
- Status pengerjaan internal + progress range slider.
- Catat DP/pembayaran manual di luar sistem.
- Metode: DANA, GoPay, OVO, Bank Jago, BRI, QRIS, ShopeePay, Allo Bank, Lainnya.
- Status pembayaran dihitung otomatis.
- Invoice untuk transaksi final.
- Receipt hanya aktif setelah lunas.
- Nomor dokumen semi-random `AWI-INV-YYMMDD-XXXX` / `AWI-RCP-YYMMDD-XXXX`.
- Public link memakai token random 64 karakter, bukan ID database.
- Regenerate/revoke public token.
- Snapshot dokumen untuk menjaga konsistensi dokumen yang sudah dibagikan.
- Download PDF A4 + print.
- Share WhatsApp dengan template editable.
- Terbilang Bahasa Indonesia otomatis.
- Audit log sederhana + last modified.
- Warning sebelum mengedit transaksi lunas/dokumen final.
- Settings logo, tanda tangan, identitas, footer, template WA.
- Light Mode + Dark Mode.
- Responsive desktop/mobile.
- Global double-submit guard.

## Branding

Logo resmi awal ada di:

`public/brand/awi-code-logo.jpg`

Palette V1 disampling dari logo referensi user:

- Primary Red: `#EF3012`
- Brown: `#934D2B`
- Green Accent: `#A2C611`
- Blue Accent: `#4760A5`
- Blush: `#EDACA1`
- Cream: `#FDFBEC`
- Dark neutral utama: `#2F2524`

Logo dan tanda tangan dapat diganti dari menu **Settings**.

## Setup Lokal

### 1. Install dependency

```bash
composer install
```

### 2. Buat `.env`

Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

Linux/macOS:

```bash
cp .env.example .env
```

### 3. Isi database dan akun owner di `.env`

Contoh:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=awi_code_invoice
DB_USERNAME=root
DB_PASSWORD=

ADMIN_NAME="Awi Code Owner"
ADMIN_EMAIL="email-anda@example.com"
ADMIN_PASSWORD="gunakan-password-kuat"
```

Jangan commit `.env` ke Git.

### 4. Generate APP_KEY

```bash
php artisan key:generate
```

### 5. Migration + seeder

```bash
php artisan migrate --seed
```

Jika migration sudah pernah dijalankan sebelum `ADMIN_EMAIL`/`ADMIN_PASSWORD` diisi:

```bash
php artisan db:seed --class=AdminSeeder
```

### 6. Jalankan

```bash
php artisan serve
```

Buka:

`http://127.0.0.1:8000`

## Alur Penggunaan

1. Login sebagai owner.
2. Tambah Customer/PIC.
3. Buat transaksi project.
4. Tambahkan item jasa dan diskon bila ada.
5. Simpan draft atau **Simpan & Finalkan**.
6. Catat DP/pembayaran yang telah diterima.
7. Generate Invoice jika diperlukan.
8. Saat total pembayaran lunas, Generate Receipt tersedia.
9. Kirim public link lewat WhatsApp atau Download PDF.

## Aturan Invoice / Receipt

- Draft tidak dapat menghasilkan dokumen.
- Invoice dan Receipt berasal dari transaksi yang sama.
- Receipt tidak dapat dibuat jika `total_paid < grand_total`.
- Edit transaksi tidak mengubah snapshot dokumen lama secara otomatis.
- Jika dokumen memang harus mengikuti data terbaru, gunakan **Perbarui Snapshot** secara eksplisit.
- Regenerate Link membuat link lama tidak berlaku.

## Public Security

Halaman publik menggunakan route seperti:

```text
/d/{64-character-random-token}
```

Database hanya menggunakan SHA-256 token untuk lookup. Token asli disimpan terenkripsi dengan Laravel `Crypt` agar owner tetap dapat menyalin ulang link dari dashboard.

Karena itu, `APP_KEY` production tidak boleh diganti sembarangan setelah dokumen dibuat. Jika APP_KEY berubah, token terenkripsi lama tidak dapat didekripsi.

## PDF

PDF menggunakan `dompdf/dompdf` secara langsung. Tidak ada dependency Node/npm.

## Production Checklist

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://domain-anda.com`
- gunakan HTTPS
- gunakan password owner kuat
- jalankan `php artisan optimize`
- pastikan `public/` menjadi document root
- folder `storage/` dan `bootstrap/cache/` writable
- backup database dan `public/uploads/branding`

Lihat juga `docs/DEPLOY-HOSTINGER.md`.

## Perubahan v1.0.1

- Field tambahan untuk opsi **Lainnya** benar-benar tersembunyi dan baru muncul setelah `Lainnya` dipilih.
- Nilai diskon dan baris diskon tidak ditampilkan ketika transaksi menggunakan `Tanpa diskon`.
- Status pengerjaan pada form transaksi menggunakan kontrol tombol kiri/kanan.
- Riwayat aktivitas menampilkan label bahasa manusia, bukan kode developer seperti `payment.updated` atau `document.snapshot_regenerated`.
- URL admin untuk customer, jenis jasa, transaksi, pembayaran, dan dokumen memakai opaque route key 20 karakter, bukan ID angka database.
- Public Invoice/Receipt tetap memakai secure token panjang yang terpisah dari route key admin.

### Upgrade v1.0.0 → v1.0.1

```bash
php artisan migrate
php artisan optimize:clear
```

Migration v1.0.1 membuat route key untuk data lama secara otomatis.

## Perubahan v1.0.2

- Filter tipe pada halaman **Invoice / Receipt** tidak lagi berupa dropdown. Opsi **Semua / Invoice / Receipt** tampil sebagai segmented buttons horizontal.
- Detail transaksi memiliki tombol **Hapus Transaksi Permanen** pada bagian **Aksi Sensitif**.
- Menghapus transaksi turut menghapus rincian item, pembayaran, dokumen Invoice/Receipt, dan riwayat aktivitas transaksi terkait.
- Warning penghapusan dibuat lebih tegas apabila transaksi sudah memiliki pembayaran atau dokumen.
- Tidak ada migration baru pada v1.0.2.

### Upgrade v1.0.1 → v1.0.2

Setelah replace file cukup jalankan:

```bash
php artisan optimize:clear
```


## Perubahan v1.0.3

- Seluruh dialog JavaScript native (`alert`, `confirm`, dan fallback `prompt`) dihapus dari asset aplikasi dan diganti dengan modal/toast custom Awi Code yang mengikuti Light/Dark Mode.
- Konfirmasi aksi sensitif seperti hapus transaksi, hapus pembayaran, batalkan transaksi, revoke/regenerate dokumen, arsip customer, dan nonaktifkan jenis jasa sekarang memakai dialog custom.
- Warning ketika mencoba menghapus satu-satunya item transaksi sekarang menggunakan modal custom, bukan `alert()` browser.
- Fallback salin link menggunakan dialog custom jika Clipboard API dan fallback copy tidak tersedia.
- Customer sekarang tetap memiliki dua mekanisme: **Arsipkan/Pulihkan** untuk menjaga histori, serta **Hapus Customer Permanen** sebagai aksi sensitif.
- Hapus Customer Permanen dapat dilakukan walaupun customer mempunyai transaksi. Sistem menghapus transaksi customer terlebih dahulu; item jasa, pembayaran, dan Invoice/Receipt ikut terhapus melalui relasi cascade, bersama activity log terkait.
- Warning hapus customer menyebutkan jumlah transaksi dan dampak penghapusan apabila histori sudah ada.
- Cache-busting asset diperbarui ke `v=1.0.3`.
- Tidak ada migration database baru pada v1.0.3.

### Upgrade v1.0.2 → v1.0.3

Replace file dari patch kemudian jalankan:

```bash
php artisan optimize:clear
```

Tidak perlu menjalankan migration untuk v1.0.3.

## Perubahan v1.0.4 — Responsive, Rekap & Koreksi Total

### Tampilan responsive

- Navigasi mobile memperoleh tombol tutup; drawer dapat ditutup lewat backdrop, tombol, Escape, atau setelah klik menu.
- Konten, stat cards, form filter, form item, ringkasan dan topbar ditata ulang pada tablet dan ponsel.
- Daftar customer, transaksi, dokumen, jenis jasa, item, pembayaran dan dashboard berubah menjadi kartu berlabel pada layar kecil; desktop tetap memakai tabel.
- Light/Dark Mode dan palette Awi Code tetap dipertahankan; dokumen Invoice/Receipt publik tidak berubah gaya cetaknya.

### Rekap bulanan/tahunan

- Menu **Rekap Bulanan / Tahunan**: `GET /reports`.
- Pilih Bulanan atau Tahunan, tahun, dan bulan (hanya untuk mode bulanan).
- Total nilai transaksi, pembayaran, sisa, jumlah transaksi/lunas; rincian transaksi beserta status pembayaran.
- Laporan tahunan menampilkan 12 bulan dan grafik perbandingan sederhana.
- **Cetak Rekap** dari browser dan **Unduh PDF** A4 landscape (`GET /reports/pdf`).
- Rekap **hanya transaksi Final**, transaksi Draft/Dibatalkan tidak dihitung.
- **Semua angka berdasarkan tanggal transaksi**, bukan tanggal DP/pelunasan. Pembayaran tidak mempunyai kolom tanggal terpisah pada versi ini, sehingga laporan tidak boleh ditafsirkan sebagai arus kas bulanan.
- Jika sebuah transaksi berganti nilai di kemudian hari, laporan periode tanggal transaksinya mengikuti **nilai terkini**; snapshot dokumen Invoice/Receipt tidak otomatis berubah.

### Fix perubahan item setelah DP

- Akar masalah: `loadMissing('items', 'payments')` di `TransactionCalculator` mempertahankan koleksi item lama yang telah dihapus dan dibuat ulang saat `update`. Perhitungan memakai koleksi stale sampai request selanjutnya.
- Diganti dengan **reload paksa kedua relasi dari database** saat recalculation sehingga satu kali Simpan langsung memperbarui nilai subtotal, diskon, total, sudah dibayar, sisa, dan status.
- Validasi estimasi total menyelaraskan aturan pembulatan qty dan harga dengan `syncItems`.
- DP tetap tersimpan. Receipt/Invoice lama mempertahankan snapshot seperti sebelumnya dan harus di-refresh eksplisit bila diperlukan.

### Pemeliharaan nilai lama

Cek apakah transaksi di database masih memiliki total stale dari versi sebelumnya:

```bash
php artisan awicode:recalculate-totals
```

Perintah di atas **hanya memeriksa** dan tidak mengubah data. Jika ditemukan ketidaksinkronan, lakukan backup database terlebih dahulu, lalu perbaiki melalui:

```bash
php artisan awicode:recalculate-totals --apply
```

**PENTING:** perintah ini memperbarui total transaksi internal, **tidak menyunting snapshot PDF/Invoice/Receipt yang sebelumnya dibagikan**. Tinjau dan klik Perbarui Snapshot pada dokumen terkait hanya jika memang ingin mengganti dokumen publiknya.

### Upgrade v1.0.3 → v1.0.4

1. Backup database dan source yang sedang berjalan.
2. Overlay isi patch v1.0.4 (folder `app/`, `routes/`, `resources/`, `public/assets/`, `VERSION` dan README).
3. Jalankan `php artisan optimize:clear`, restart server bila perlu.
4. Jika memakai Hostinger `app/` + `public_html/` terpisah, salin **hanya asset** baru:

   ```bash
   cd ~/domains/invoice.awicode.com
   cp app/public/assets/app.css public_html/assets/app.css
   cp app/public/assets/app.js public_html/assets/app.js
   cd app
   php artisan optimize:clear
   ```

5. Tes halaman `/reports`, cetak, PDF, dan alur edit transaksi dengan DP.
6. **Tidak ada migration baru**; jangan jalankan `migrate:fresh`.

Detail skenario pengujian ada pada `docs/QA-v1.0.4.md`.
