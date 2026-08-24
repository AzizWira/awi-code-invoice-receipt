# Deploy Hostinger — Awi Code Invoice & Receipt

Dokumen ini memakai pola Laravel standard: source berada di folder aplikasi, sedangkan document root domain diarahkan ke folder `public` bila Hostinger mendukung custom document root.

## Requirement

- PHP 8.3+; PHP 8.4 direkomendasikan.
- MySQL/MariaDB.
- Composer 2.
- Extension umum Laravel: mbstring, openssl, pdo_mysql, tokenizer, xml, ctype, fileinfo.

## Langkah ringkas

```bash
composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate
```

Isi `.env` production:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://invoice.domainanda.com
APP_TIMEZONE=Asia/Jakarta

DB_CONNECTION=mysql
DB_HOST=...
DB_PORT=3306
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...

ADMIN_NAME="Awi Code Owner"
ADMIN_EMAIL="..."
ADMIN_PASSWORD="..."
```

Lalu:

```bash
php artisan migrate --seed --force
php artisan optimize
```

Setelah owner berhasil dibuat, Anda boleh menghapus `ADMIN_PASSWORD` dari `.env` lalu menjalankan kembali:

```bash
php artisan config:clear
php artisan optimize
```

Akun yang sudah tersimpan di database tidak ikut terhapus.

## Folder Permission

Pastikan writable:

```text
storage/
bootstrap/cache/
public/uploads/branding/
```

## Document Root

Pilihan terbaik adalah document root mengarah langsung ke:

```text
/path-project/public
```

Jika panel hosting memaksa `public_html`, salin isi `public/` ke `public_html` dan sesuaikan path pada `public_html/index.php` menuju `vendor/autoload.php` dan `bootstrap/app.php` milik folder project.

Jangan memindahkan `.env`, `vendor`, `app`, `routes`, atau source Laravel ke area publik.
