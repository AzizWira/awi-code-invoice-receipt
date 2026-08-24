# PRODUCT REQUIREMENTS DOCUMENT (PRD)

## Awi Code — Invoice & Receipt Manager

**Versi:** 1.0  
**Status:** Baseline Requirement  
**Tanggal:** 25 Agustus 2026  
**Pemilik Produk:** Awi Code

---

## 1. Ringkasan Produk

**Awi Code — Invoice & Receipt Manager** adalah aplikasi web internal untuk membantu pemilik Awi Code membuat, menyimpan, mengelola, dan membagikan dokumen transaksi jasa berupa **Invoice** dan **Receipt** secara profesional.

Sistem ini bukan payment gateway, bukan aplikasi kasir umum, bukan portal customer, dan bukan project management penuh. Fokus utamanya adalah **mencatat transaksi jasa yang sudah final, menghasilkan dokumen transaksi, serta menyimpan histori customer dan pembayaran**.

Satu transaksi mewakili satu project dan dapat memiliki banyak item jasa. Data transaksi yang sama dapat menghasilkan:

- **Invoice**, untuk rincian transaksi saat pembayaran belum lunas atau baru dibayar sebagian;
- **Receipt**, sebagai bukti pembayaran setelah transaksi lunas.

Customer tidak memiliki akun. Customer hanya menerima dokumen melalui **link publik yang aman** dan/atau **PDF**, biasanya dibagikan melalui WhatsApp.

---

## 2. Latar Belakang

Awi Code menangani berbagai jenis jasa, tidak terbatas pada pembuatan website. Dalam praktiknya, setiap customer dapat memiliki lebih dari satu project dan setiap project dapat memiliki beberapa komponen pekerjaan.

Pencatatan manual melalui chat, catatan terpisah, atau dokumen yang dibuat satu per satu dapat menyebabkan:

- data customer dan transaksi tersebar;
- format invoice/receipt tidak konsisten;
- perhitungan diskon, DP, sisa pembayaran, dan total dilakukan berulang;
- sulit melihat histori transaksi customer;
- sulit memastikan dokumen mana yang sudah final atau lunas;
- proses membagikan dokumen ke WhatsApp memerlukan pekerjaan manual berulang.

Produk ini menyatukan proses tersebut ke dalam satu sistem internal yang sederhana.

---

## 3. Tujuan Produk

### 3.1 Tujuan utama

1. Mempercepat pembuatan Invoice dan Receipt Awi Code.
2. Menjaga format dokumen konsisten dan profesional.
3. Menyimpan histori customer dan transaksi secara terstruktur.
4. Mengurangi perhitungan manual untuk subtotal, diskon, pembayaran, sisa, dan total.
5. Mempermudah pengiriman dokumen melalui WhatsApp.
6. Menyediakan PDF dan halaman dokumen publik yang aman.
7. Menyimpan branding Awi Code, logo, tanda tangan, footer, dan template WhatsApp melalui Settings.
8. Menyediakan pengalaman penggunaan yang nyaman pada desktop maupun mobile dengan Light Mode dan Dark Mode.

### 3.2 Non-goals / di luar scope V1

Fitur berikut **tidak termasuk scope utama V1**:

- payment gateway;
- pembayaran otomatis;
- upload bukti pembayaran oleh customer;
- akun/login customer;
- customer portal;
- sistem booking;
- inventory/stok;
- POS/kasir;
- pajak/akuntansi lengkap;
- payroll;
- manajemen tim/staff multi-role;
- project management kompleks seperti task board, sprint, atau issue tracker;
- reminder jatuh tempo otomatis;
- integrasi WhatsApp Business API untuk mengirim pesan tanpa interaksi user.

---

## 4. Pengguna dan Hak Akses

### 4.1 Pengguna

V1 hanya memiliki **satu jenis pengguna internal: Owner/Admin Awi Code**.

Owner/Admin dapat:

- login ke sistem;
- melihat dashboard;
- mengelola customer;
- mengelola jenis jasa;
- membuat dan mengedit transaksi;
- mencatat pembayaran;
- menghasilkan Invoice;
- menghasilkan Receipt setelah transaksi lunas;
- download/print PDF;
- membuka dan membagikan link publik;
- mengirim dokumen melalui WhatsApp;
- mengelola pengaturan branding dan template.

### 4.2 Customer

Customer **tidak mempunyai akun** dan tidak dapat masuk ke dashboard.

Customer hanya dapat membuka dokumen publik melalui URL aman yang diberikan oleh Awi Code.

---

## 5. Terminologi Produk

### Transaction / Transaksi
Data utama yang mewakili satu project customer. Satu transaksi mempunyai customer, project, item jasa, diskon, pembayaran, dan dokumen.

### Invoice
Dokumen rincian transaksi yang dapat diterbitkan ketika project sudah final tetapi pembayaran belum lunas atau baru sebagian.

### Receipt
Dokumen bukti transaksi/pembayaran yang hanya dapat diterbitkan ketika total pembayaran sudah sama dengan total akhir transaksi.

### Payment / Pembayaran
Catatan nominal yang telah diterima Awi Code untuk suatu transaksi. Satu transaksi dapat mempunyai satu atau lebih pembayaran.

### Customer / PIC
Orang yang berkomunikasi dengan Awi Code. Customer dapat mewakili perusahaan/usaha.

### Business / Company
Nama usaha/perusahaan yang diwakili customer. Field ini opsional.

---

## 6. Alur Produk Utama

### 6.1 Transaksi belum lunas

1. Admin login.
2. Admin memilih customer lama atau membuat customer baru.
3. Admin membuat transaksi untuk satu project yang sudah final.
4. Admin menambahkan satu atau lebih item jasa.
5. Sistem menghitung subtotal.
6. Admin dapat memasukkan diskon nominal atau persen.
7. Admin mencatat pembayaran/DP yang sudah diterima jika ada.
8. Sistem menghitung total dibayar dan sisa pembayaran.
9. Admin memfinalisasi transaksi.
10. Admin menghasilkan **Invoice**.
11. Admin dapat download PDF atau mengirim link Invoice melalui WhatsApp.

### 6.2 Transaksi lunas

1. Admin membuka transaksi yang sudah ada.
2. Admin mencatat pelunasan.
3. Sistem menghitung total pembayaran.
4. Jika total pembayaran >= total akhir, status pembayaran menjadi **Lunas**.
5. Tombol **Generate Receipt** menjadi aktif.
6. Admin menghasilkan Receipt dari data transaksi yang sama tanpa input ulang.
7. Admin dapat download PDF atau mengirim link Receipt melalui WhatsApp.

### 6.3 Transaksi langsung lunas

Jika customer langsung membayar penuh, admin dapat membuat transaksi, mencatat pembayaran penuh, kemudian menghasilkan Receipt. Invoice tidak wajib dibuat terlebih dahulu.

---

## 7. Struktur Navigasi

Navigasi utama V1:

1. **Dashboard**
2. **Transaksi**
3. **Invoice / Receipt**
4. **Customer**
5. **Jenis Jasa**
6. **Settings**

Subfilter pada Transaksi dapat menyediakan:

- Semua;
- Belum Dibayar;
- Dibayar Sebagian;
- Lunas;
- Dibatalkan.

---

## 8. Authentication

### 8.1 Login

Sistem menyediakan login Owner/Admin menggunakan email/username dan password.

Requirement minimum:

- tidak ada register publik;
- password disimpan menggunakan secure hash;
- CSRF protection;
- login rate limiting;
- session aman;
- production menggunakan HTTPS;
- logout tersedia;
- tidak ada credential default yang ditampilkan ke publik.

### 8.2 Session

- user yang belum login tidak dapat membuka dashboard;
- halaman publik Invoice/Receipt tidak membutuhkan login tetapi hanya dapat diakses melalui token publik yang valid;
- logout menghapus session aktif.

---

## 9. Dashboard

Dashboard bertujuan memberikan ringkasan cepat, bukan menjadi dashboard analytics yang kompleks.

### 9.1 Summary cards

Minimal menampilkan:

- Total transaksi;
- Transaksi belum dibayar;
- Transaksi dibayar sebagian;
- Transaksi lunas;
- Total nilai transaksi;
- Total pembayaran yang sudah diterima;
- Sisa pembayaran seluruh transaksi aktif.

### 9.2 Recent transactions

Menampilkan transaksi terbaru dengan informasi:

- customer/perusahaan;
- project;
- total;
- status pembayaran;
- status dokumen;
- action detail.

### 9.3 Quick actions

- Buat Transaksi;
- Tambah Customer;
- Lihat Invoice/Receipt.

---

## 10. Customer Management

### 10.1 Field customer

Field minimum:

- Nama Customer / PIC — wajib;
- Nama Usaha / Perusahaan — opsional;
- Nomor WhatsApp — opsional apabila email diisi;
- Email — opsional apabila nomor WhatsApp diisi;
- Catatan — opsional;
- timestamps.

Minimal salah satu dari **WhatsApp atau Email** harus tersedia.

### 10.2 Fitur customer

- tambah customer;
- edit customer;
- lihat detail customer;
- pencarian customer;
- histori transaksi customer;
- total transaksi customer;
- total nominal transaksi customer;
- soft delete/archive direkomendasikan agar histori dokumen lama tidak rusak.

### 10.3 Tampilan detail customer

Menampilkan:

- identitas customer;
- identitas perusahaan/usaha jika ada;
- informasi kontak;
- catatan;
- daftar semua transaksi yang pernah dibuat;
- status dan total masing-masing transaksi.

---

## 11. Jenis Jasa

Sistem mempunyai master data jenis jasa untuk mempercepat input transaksi.

### 11.1 Default awal yang direkomendasikan

- Pembuatan Website;
- Revisi Website;
- Bug Fix;
- Tugas / Joki;
- Desain;
- Hosting;
- Domain;
- Maintenance;
- Setup / Deploy;
- Lainnya.

### 11.2 Opsi Lainnya

Jika admin memilih **Lainnya**, sistem menampilkan input teks untuk mengisi nama jasa custom.

Contoh:

`Jenis jasa: Lainnya`  
`Nama jasa lainnya: Integrasi API Virtual Try-On`

Opsional UX:

- checkbox/toggle **Simpan sebagai jenis jasa baru** agar nilai custom dapat masuk master data untuk penggunaan berikutnya.

---

## 12. Transaksi / Project

Satu transaksi selalu mewakili **satu project**.

### 12.1 Field transaksi

Minimal:

- ID internal;
- kode transaksi internal;
- nomor dokumen otomatis;
- customer_id;
- Nama Project — wajib;
- Jenis Jasa — wajib;
- Jenis Jasa Lainnya — conditional;
- Deskripsi Pekerjaan — opsional/direkomendasikan;
- Tanggal — satu tanggal transaksi/order;
- Status Pengerjaan;
- Progress Pengerjaan;
- Subtotal;
- Tipe Diskon;
- Nilai Diskon;
- Total Akhir;
- Total Dibayar;
- Sisa Pembayaran;
- Status Pembayaran;
- Status Transaksi/Dokumen;
- Catatan internal opsional;
- timestamps;
- last_modified_at.

### 12.2 Status pengerjaan

Pilihan:

- Menunggu;
- Dikerjakan;
- Revisi;
- Selesai;
- Dibatalkan.

Status pengerjaan bersifat **internal** dan tidak ditampilkan pada Invoice/Receipt publik karena dokumen diberikan sebagai dokumen transaksi final.

### 12.3 Progress pengerjaan

Progress menggunakan **range slider**, bukan dropdown.

Rentang:

`0% — 100%`

Requirement UX:

- nilai persen terlihat langsung;
- slider dapat digeser;
- dapat menggunakan step 1 atau step 5;
- status dan progress tetap dua field terpisah.

---

## 13. Item Jasa dalam Transaksi

Satu transaksi dapat mempunyai banyak item.

### 13.1 Field item

- Nama Item — wajib;
- Deskripsi — opsional;
- Qty — wajib, default 1;
- Satuan — default `project`, dapat dipilih/diisi;
- Harga Satuan — wajib;
- Subtotal — otomatis `qty × harga satuan`;
- Urutan tampil.

### 13.2 Satuan

Pilihan awal dapat mencakup:

- project;
- fitur;
- halaman;
- bulan;
- tahun;
- pcs;
- paket;
- lainnya.

Jika memilih lainnya, admin dapat mengisi satuan custom.

### 13.3 Dynamic item row

Form transaksi harus mendukung:

- tambah item;
- hapus item;
- mengubah urutan item bila diperlukan;
- kalkulasi subtotal real-time.

---

## 14. Perhitungan Harga dan Diskon

### 14.1 Subtotal

`Subtotal = jumlah seluruh subtotal item`

### 14.2 Diskon

Diskon mendukung dua mode:

- nominal Rupiah;
- persentase.

Admin memilih mode diskon lalu mengisi nilai.

Contoh nominal:

`Subtotal Rp3.000.000`  
`Diskon Rp300.000`  
`Total Rp2.700.000`

Contoh persen:

`Subtotal Rp3.000.000`  
`Diskon 10% = Rp300.000`  
`Total Rp2.700.000`

### 14.3 Validation

- diskon nominal tidak boleh negatif;
- diskon nominal tidak boleh melebihi subtotal;
- diskon persen berada pada 0–100%;
- harga tidak boleh negatif;
- qty harus lebih besar dari 0.

---

## 15. Pembayaran

### 15.1 Prinsip

Sistem **tidak menerima pembayaran secara online**. Admin hanya mencatat pembayaran yang telah terjadi di luar sistem.

### 15.2 Catatan pembayaran

Satu transaksi dapat memiliki satu atau lebih payment record.

Field minimum:

- transaction_id;
- label pembayaran, misalnya DP/Pelunasan/Pembayaran;
- metode pembayaran;
- nama metode lainnya jika memilih Lainnya;
- nominal;
- catatan opsional;
- timestamps sistem.

Tanggal pembayaran khusus **tidak wajib ditampilkan di Invoice/Receipt**.

### 15.3 Metode pembayaran

Pilihan default:

- DANA;
- GoPay;
- OVO;
- Bank Jago;
- BRI;
- QRIS;
- ShopeePay;
- Allo Bank;
- Lainnya.

Jika memilih **Lainnya**, muncul input teks nama metode pembayaran.

### 15.4 Input DP

Untuk mempermudah admin, form dapat menyediakan dua cara input:

- nominal Rupiah;
- persentase dari total transaksi.

Jika menggunakan persen, sistem mengonversi otomatis menjadi nominal payment record.

Contoh:

`Total Rp2.000.000`  
`DP 50%`  
`Nominal pembayaran Rp1.000.000`

### 15.5 Status pembayaran otomatis

Status pembayaran tidak perlu dipilih manual.

- jika total dibayar = 0 → **Belum Dibayar**;
- jika total dibayar > 0 dan < total akhir → **Dibayar Sebagian**;
- jika total dibayar >= total akhir → **Lunas**.

Overpayment harus diberi warning dan tidak boleh terjadi tanpa confirmation eksplisit.

---

## 16. Invoice

### 16.1 Fungsi

Invoice adalah dokumen rincian transaksi final yang dapat digunakan ketika customer belum melunasi pembayaran.

Invoice bukan payment gateway dan tidak memiliki tombol pembayaran online.

### 16.2 Isi Invoice

Minimal:

- logo Awi Code;
- label **INVOICE**;
- nomor invoice;
- tanggal;
- nama customer/PIC;
- nama perusahaan/usaha jika ada;
- nama project;
- jenis jasa;
- daftar item;
- qty;
- satuan;
- harga satuan;
- subtotal item;
- subtotal transaksi;
- diskon;
- total akhir;
- total pembayaran yang telah diterima;
- sisa pembayaran;
- status pembayaran;
- terbilang;
- catatan/footer invoice;
- nama penanda tangan;
- gambar tanda tangan Awi Code.

### 16.3 Invoice tidak menampilkan

- progress pengerjaan;
- status internal pengerjaan;
- catatan internal;
- history perubahan;
- data teknis/token.

---

## 17. Receipt

### 17.1 Fungsi

Receipt adalah bukti pembayaran/transaksi setelah customer melunasi total transaksi.

### 17.2 Eligibility

Tombol **Generate Receipt** hanya aktif jika:

`Total Dibayar >= Total Akhir`

Jika belum lunas, tombol disabled dan UI menjelaskan bahwa Receipt hanya tersedia setelah transaksi lunas.

### 17.3 Isi Receipt

Minimal:

- logo Awi Code;
- label **RECEIPT**;
- nomor dokumen;
- tanggal;
- customer/PIC;
- perusahaan/usaha jika ada;
- nama project;
- item transaksi;
- total akhir;
- metode pembayaran yang tercatat;
- status **LUNAS**;
- terbilang;
- footer Receipt;
- nama penanda tangan;
- gambar tanda tangan.

Receipt tidak perlu menampilkan tanggal setiap payment record.

---

## 18. Nomor Dokumen dan Identifier

### 18.1 Format nomor

Format baseline:

`AWI-INV-YYMMDD-XXXX`

Contoh:

`AWI-INV-260825-K8F2`

Receipt dapat menggunakan nomor transaksi yang sama dengan suffix/type berbeda atau nomor Receipt tersendiri sesuai implementasi, tetapi harus tetap dapat ditelusuri secara internal.

Contoh opsi:

- Invoice: `AWI-INV-260825-K8F2`
- Receipt: `AWI-RCP-260825-K8F2`

### 18.2 Random code

Bagian random harus menggunakan random generator yang tidak mudah ditebak, bukan hanya sequence increment biasa.

### 18.3 Database ID

Primary key database tidak boleh digunakan langsung sebagai public identifier.

---

## 19. Public Secure Link

### 19.1 Tujuan

Customer dapat membuka Invoice/Receipt tanpa login, tetapi URL tidak boleh mudah ditebak atau dienumerasi.

### 19.2 Token

Dokumen publik menggunakan token random panjang, misalnya:

`/d/7mK2qX9pV4sL3F...`

Requirement:

- token dibuat menggunakan cryptographically secure random source;
- token memiliki entropy tinggi;
- tidak memakai ID numerik berurutan;
- invalid token menghasilkan 404/generic not found;
- token dapat diregenerate/revoke oleh admin bila dibutuhkan;
- token Invoice dan Receipt dapat dipisahkan atau ditautkan aman ke dokumen yang sama.

### 19.3 Privacy

Halaman publik hanya menampilkan informasi yang memang termasuk dokumen. Data internal tidak boleh bocor melalui HTML, API response, source map, atau identifier tersembunyi.

---

## 20. PDF dan Print

### 20.1 Output

Setiap Invoice dan Receipt harus dapat:

- dibuka sebagai halaman web;
- dicetak melalui layout print;
- di-download sebagai PDF.

### 20.2 PDF requirement

- ukuran A4;
- layout profesional dan bersih;
- tabel tidak terpotong secara buruk;
- logo dan tanda tangan tampil jelas;
- background tidak boros tinta;
- warna tetap readable bila dicetak grayscale;
- font embedded/renderer-safe;
- tidak menampilkan navigation dashboard;
- nama file informatif.

Contoh nama file:

`AWI-INV-260825-K8F2-PT-ABC.pdf`

`AWI-RCP-260825-K8F2-PT-ABC.pdf`

---

## 21. WhatsApp Sharing

### 21.1 Tombol kirim WhatsApp

Dari detail Invoice/Receipt tersedia tombol **Kirim via WhatsApp**.

Tombol membuka WhatsApp/WhatsApp Web dengan:

- nomor customer jika tersedia;
- template pesan;
- link publik dokumen.

Sistem tidak wajib mengirim pesan otomatis melalui API.

### 21.2 Template editable

Template dapat diedit dari Settings.

### 21.3 Default Invoice template

```text
Halo Kak {customer},

Berikut kami kirimkan invoice untuk project {project}.

Nomor Invoice: {document_number}
Total: {total}
Sisa Pembayaran: {remaining}

Detail invoice dapat dilihat melalui tautan berikut:
{public_link}

Terima kasih.
Awi Code
```

### 21.4 Default Receipt template

```text
Halo Kak {customer},

Terima kasih, pembayaran untuk project {project} telah kami terima sepenuhnya.

Berikut receipt/bukti transaksi dari Awi Code:
{public_link}

Total: {total}
Status: Lunas

Terima kasih telah menggunakan jasa Awi Code.
```

### 21.5 Placeholder yang didukung

Minimal:

- `{customer}`;
- `{company}`;
- `{project}`;
- `{document_number}`;
- `{total}`;
- `{paid}`;
- `{remaining}`;
- `{public_link}`;
- `{brand_name}`.

---

## 22. Terbilang

Sistem otomatis mengonversi total Rupiah menjadi teks Bahasa Indonesia.

Contoh:

`Rp2.750.000`  
→ **Dua juta tujuh ratus lima puluh ribu rupiah**

Requirement:

- dihitung otomatis;
- hasil mengikuti total akhir setelah diskon;
- digunakan pada Invoice dan Receipt;
- kapitalisasi dapat disesuaikan pada presentation layer.

---

## 23. Edit, Warning, dan Audit Trail

### 23.1 Edit transaksi

Transaksi boleh diedit selama memiliki hak akses admin.

### 23.2 Warning transaksi lunas

Jika transaksi sudah Lunas dan admin memilih Edit, sistem wajib menampilkan custom warning, misalnya:

> Transaksi ini sudah lunas dan mungkin sudah memiliki Receipt yang dibagikan kepada customer. Perubahan dapat membuat dokumen lama berbeda dengan data terbaru. Lanjutkan edit?

Action:

- Batal;
- Lanjut Edit.

### 23.3 Warning dokumen final

Jika Invoice/Receipt sudah pernah difinalisasi/dibagikan, perubahan nilai finansial harus memiliki confirmation tambahan.

### 23.4 Last modified

Detail transaksi menampilkan:

- dibuat pada;
- terakhir diubah pada.

### 23.5 Audit log sederhana

Direkomendasikan menyimpan event penting:

- transaksi dibuat;
- transaksi diedit;
- item berubah;
- diskon berubah;
- pembayaran ditambah/diedit/dihapus;
- status pembayaran berubah;
- Invoice di-generate/finalize;
- Receipt di-generate;
- public token di-regenerate/revoke.

Audit log tidak tampil pada dokumen customer.

---

## 24. Invoice / Receipt Library

Halaman **Invoice / Receipt** menampilkan dokumen yang pernah dibuat.

### 24.1 Informasi list

- nomor dokumen;
- tipe: Invoice / Receipt;
- customer/perusahaan;
- project;
- total;
- status;
- tanggal;
- terakhir diubah;
- action.

### 24.2 Action

- lihat;
- download PDF;
- print;
- copy link;
- kirim WhatsApp;
- regenerate token;
- revoke public access;
- kembali ke transaksi.

### 24.3 Search/filter

- search nomor dokumen;
- search customer;
- search project;
- filter tipe dokumen;
- filter status pembayaran;
- filter tanggal/bulan jika diperlukan.

---

## 25. Settings

Settings harus memungkinkan konfigurasi tanpa edit source code.

### 25.1 Branding

- Nama brand — default **Awi Code**;
- Logo — upload gambar;
- Nama penanda tangan;
- Gambar tanda tangan — upload gambar;
- Nomor WhatsApp Awi Code;
- Email Awi Code;
- alamat opsional;
- informasi tambahan opsional.

### 25.2 Document settings

- Footer Invoice;
- Footer Receipt;
- template WhatsApp Invoice;
- template WhatsApp Receipt;
- format nomor dokumen bila dibuat configurable;
- default satuan item;
- default jenis jasa bila diperlukan.

### 25.3 Default footer

Contoh default:

> Terima kasih telah mempercayakan kebutuhan digital Anda kepada Awi Code.

Footer dapat diedit secara bebas dari Settings.

### 25.4 Upload requirement

Logo dan tanda tangan:

- mendukung PNG/JPG/WebP;
- validasi MIME dan file size;
- preview sebelum/atau sesudah upload;
- file lama dapat diganti;
- storage tidak boleh mengeksekusi file upload sebagai script;
- rekomendasi tanda tangan menggunakan PNG/WebP transparan.

---

## 26. UI/UX dan Brand Direction

### 26.1 Brand

Nama brand utama: **Awi Code**.

Logo resmi untuk implementasi adalah **logo yang diberikan user pada referensi visual terbaru**. Sistem tidak boleh mengganti logo tersebut dengan logo generik atau logo hasil recreate tanpa persetujuan.

### 26.2 Color palette — source of truth

**Palette warna dari gambar referensi terbaru yang diberikan user menjadi source of truth visual.**

Implementasi harus mengambil warna langsung dari aset referensi tersebut dan menjadikannya design tokens. Jangan mengganti identitas warna dengan palette SaaS generik, purple/blue AI gradient, atau warna lain yang tidak berasal dari referensi.

Karena file sumber palette merupakan aset visual, nilai HEX final harus **disampling langsung dari aset asli saat integrasi UI** agar tidak terjadi perubahan warna akibat perkiraan manual.

Design tokens minimum:

| Token | Fungsi |
|---|---|
| `brand-primary` | tombol utama, active state, key accent |
| `brand-secondary` | secondary action, badge, supporting element |
| `brand-accent` | highlight terkontrol |
| `brand-muted` | muted brand surface |
| `bg-light` | background Light Mode |
| `surface-light` | card/table/form Light Mode |
| `text-light-primary` | teks utama Light Mode |
| `text-light-secondary` | teks sekunder Light Mode |
| `border-light` | border Light Mode |
| `bg-dark` | background Dark Mode |
| `surface-dark` | card/table/form Dark Mode |
| `text-dark-primary` | teks utama Dark Mode |
| `text-dark-secondary` | teks sekunder Dark Mode |
| `border-dark` | border Dark Mode |
| `success` | status Lunas/success |
| `warning` | warning/edit finalized |
| `danger` | destructive/error |

### 26.3 Light Mode

Light Mode wajib tersedia dan menggunakan:

- background terang dari/selaras dengan palette referensi;
- surface yang cukup kontras terhadap background;
- brand primary sebagai accent, bukan memenuhi seluruh halaman;
- teks high contrast;
- tabel dan form mudah dibaca;
- Invoice/Receipt print-friendly.

### 26.4 Dark Mode

Dark Mode wajib tersedia.

Dark Mode bukan sekadar membalik warna. Requirement:

- menggunakan dark neutral yang selaras dengan palette brand;
- brand colors disesuaikan luminance-nya tanpa mengubah identitas;
- card/surface dapat dibedakan dari page background;
- border tetap terlihat;
- text contrast memenuhi readability;
- success/warning/danger tetap jelas;
- gambar logo tetap terlihat pada surface gelap.

### 26.5 Theme behavior

- theme switch tersedia dari UI;
- pilihan Light/Dark disimpan agar persisten;
- direkomendasikan opsi `System` bila implementasi ingin mengikuti OS, tetapi Light dan Dark tetap wajib;
- tidak boleh terjadi flash tema yang salah secara mencolok saat page load jika bisa dihindari.

### 26.6 Dashboard style

Arah desain:

- modern;
- profesional;
- clean;
- sedikit personality Awi Code;
- tidak terasa seperti template admin generik;
- spacing lega;
- card dan table konsisten;
- responsive;
- iconography konsisten;
- hindari dekorasi berlebihan.

### 26.7 No AI-slop rules

Hindari:

- gradient ungu/biru generik sebagai identitas utama;
- neon glow;
- glassmorphism di semua kartu;
- blob/orb besar acak;
- shadow berlebihan;
- radius ekstrem pada semua komponen;
- terlalu banyak badge warna-warni;
- animasi tidak fungsional;
- layout dashboard yang tampak seperti hasil template AI tanpa identitas.

### 26.8 Public document design

Invoice/Receipt publik menggunakan identitas palette dan logo yang sama, tetapi tampilannya lebih formal:

- white/neutral document surface;
- accent brand secukupnya;
- hierarchy angka total jelas;
- tabel bersih;
- logo tidak terlalu besar;
- tanda tangan pada area akhir;
- layout tetap baik saat PDF/print;
- Dark Mode dashboard tidak mengubah styling PDF yang harus print-friendly.

---

## 27. Responsive Design

Aplikasi harus nyaman digunakan di desktop maupun HP.

### 27.1 Minimum QA viewport

Direkomendasikan pengujian minimal:

- 360 × 800;
- 390 × 844;
- 768 × 1024;
- 1366 × 768;
- 1440 × 900.

### 27.2 Mobile behavior

- sidebar menjadi drawer/bottom navigation sesuai desain;
- tabel lebar menggunakan responsive strategy yang jelas;
- form item transaksi tidak boleh memaksa horizontal scroll yang buruk;
- action utama tetap mudah dijangkau;
- modal tidak melebihi viewport;
- range slider nyaman disentuh;
- tombol WhatsApp, PDF, dan finalisasi memiliki touch target memadai.

---

## 28. Empty, Loading, Success, dan Error States

Setiap halaman wajib memiliki state yang jelas.

### Empty state contoh

**Customer**  
Belum ada customer. Tambahkan customer pertama untuk mulai membuat transaksi.

**Transaksi**  
Belum ada transaksi. Buat transaksi pertama Awi Code.

**Dokumen**  
Belum ada Invoice atau Receipt yang dibuat.

### Loading

- tombol submit disabled saat request berjalan;
- tampilkan spinner/progress yang wajar;
- cegah double submit.

### Success

Gunakan toast/alert untuk:

- data tersimpan;
- Invoice berhasil dibuat;
- Receipt berhasil dibuat;
- pembayaran berhasil dicatat;
- link berhasil disalin.

### Error

Error harus:

- menjelaskan apa yang gagal;
- tidak membocorkan stack trace di production;
- mempertahankan input user jika memungkinkan;
- memberi retry/action relevan.

---

## 29. Search, Sort, dan Filter

### 29.1 Customer

- search nama;
- search perusahaan;
- search WhatsApp/email.

### 29.2 Transaksi

- search project;
- search customer/perusahaan;
- search nomor dokumen;
- filter status pembayaran;
- filter status pengerjaan;
- filter jenis jasa;
- sort terbaru/terlama;
- sort total bila dibutuhkan.

### 29.3 Dokumen

- tipe Invoice/Receipt;
- customer;
- project;
- nomor dokumen;
- status.

---

## 30. Data Model Rekomendasi

Nama tabel dapat disesuaikan dengan framework, tetapi struktur konseptual berikut direkomendasikan.

### 30.1 `users`

- id;
- name;
- email/username;
- password;
- timestamps.

### 30.2 `customers`

- id;
- name;
- company_name nullable;
- whatsapp nullable;
- email nullable;
- note nullable;
- archived_at nullable;
- timestamps.

### 30.3 `service_types`

- id;
- name;
- is_active;
- sort_order;
- timestamps.

### 30.4 `transactions`

- id;
- internal_code;
- public_document_base_code;
- customer_id;
- project_name;
- service_type_id nullable;
- custom_service_type nullable;
- description nullable;
- transaction_date;
- work_status;
- progress_percent;
- subtotal;
- discount_type nullable;
- discount_value;
- discount_amount;
- grand_total;
- total_paid;
- remaining_amount;
- payment_status;
- transaction_status;
- internal_note nullable;
- finalized_at nullable;
- timestamps.

### 30.5 `transaction_items`

- id;
- transaction_id;
- name;
- description nullable;
- quantity;
- unit;
- unit_price;
- line_total;
- sort_order;
- timestamps.

### 30.6 `payments`

- id;
- transaction_id;
- label nullable;
- method;
- custom_method nullable;
- amount;
- note nullable;
- timestamps.

### 30.7 `documents`

- id;
- transaction_id;
- type (`invoice`, `receipt`);
- document_number;
- public_token_hash/public_token strategy;
- status;
- generated_at;
- finalized_at nullable;
- revoked_at nullable;
- snapshot_json nullable;
- timestamps.

**Snapshot direkomendasikan** agar dokumen yang sudah final dapat mempertahankan data saat diterbitkan, meskipun transaksi kemudian diedit. Jika digunakan, regenerasi dokumen harus eksplisit.

### 30.8 `settings`

Key/value atau structured settings untuk:

- brand name;
- logo path;
- signer name;
- signature path;
- business contact;
- Invoice footer;
- Receipt footer;
- WhatsApp templates;
- theme preference;
- document preferences.

### 30.9 `activity_logs`

- id;
- user_id;
- action;
- subject_type;
- subject_id;
- summary;
- before_data nullable;
- after_data nullable;
- timestamps.

---

## 31. Business Rules

**BR-01 — Single owner**  
V1 hanya memerlukan satu role Owner/Admin.

**BR-02 — One transaction, one project**  
Satu transaksi hanya mewakili satu project.

**BR-03 — Multi item**  
Satu transaksi dapat mempunyai banyak item jasa.

**BR-04 — Customer reuse**  
Satu customer dapat mempunyai banyak transaksi.

**BR-05 — Company optional**  
Customer dapat mewakili perusahaan/usaha, tetapi company tidak wajib.

**BR-06 — Contact requirement**  
Minimal WhatsApp atau email customer harus tersedia.

**BR-07 — Invoice eligibility**  
Invoice dapat dibuat untuk transaksi final meskipun belum lunas.

**BR-08 — Receipt eligibility**  
Receipt hanya dapat dibuat setelah total pembayaran mencapai total akhir.

**BR-09 — No payment gateway**  
Semua pembayaran terjadi di luar sistem dan hanya dicatat oleh admin.

**BR-10 — Auto payment status**  
Status pembayaran ditentukan dari total payment record, bukan dropdown manual.

**BR-11 — Discount modes**  
Diskon mendukung nominal dan persen.

**BR-12 — Public security**  
Public URL tidak boleh menggunakan sequential database ID.

**BR-13 — Edit warning**  
Transaksi lunas/final harus memiliki warning sebelum edit.

**BR-14 — Receipt consistency**  
Receipt harus merepresentasikan transaksi yang lunas dan tidak boleh tersedia saat remaining amount > 0.

**BR-15 — Public document scope**  
Progress/status pengerjaan internal tidak tampil ke customer.

**BR-16 — One input source**  
Invoice dan Receipt berasal dari data transaksi yang sama; tidak ada input ulang project/item.

**BR-17 — Branding configurable**  
Logo dan tanda tangan dapat diganti melalui Settings.

**BR-18 — Theme mandatory**  
Light Mode dan Dark Mode wajib tersedia untuk dashboard/internal UI.

**BR-19 — Palette locked**  
Palette referensi user terbaru menjadi source of truth visual dan tidak boleh diganti tanpa persetujuan.

---

## 32. Validation Rules

### Customer

- nama wajib;
- WhatsApp atau email minimal salah satu;
- email harus valid jika diisi;
- WhatsApp dinormalisasi agar dapat dipakai pada link WhatsApp.

### Transaction

- customer wajib;
- project wajib;
- tanggal wajib;
- minimal satu item;
- progress 0–100;
- custom service type wajib jika memilih Lainnya.

### Item

- nama wajib;
- qty > 0;
- harga >= 0;
- unit wajib/default.

### Payment

- nominal > 0;
- method wajib;
- custom method wajib jika method = Lainnya;
- overpayment memerlukan confirmation atau dibatasi sesuai keputusan implementasi.

### Settings upload

- file image valid;
- ukuran maksimum dikonfigurasi;
- ekstensi saja tidak cukup untuk validasi MIME.

---

## 33. Security Requirements

Minimum:

- HTTPS production;
- CSRF protection;
- output escaping;
- secure password hashing;
- session cookie secure/HTTPOnly/SameSite sesuai kebutuhan;
- rate limit login;
- authorization untuk seluruh admin route;
- public token entropy tinggi;
- tidak mengekspos DB ID pada public URL;
- upload validation;
- file upload disimpan secara aman;
- production error tidak menampilkan stack trace;
- database backup direkomendasikan;
- audit perubahan finansial direkomendasikan.

Public Invoice/Receipt harus menggunakan header/cache policy yang tidak menyebabkan data sensitif tersimpan secara tidak perlu pada shared cache.

---

## 34. Data Integrity dan Snapshot Dokumen

Karena Invoice/Receipt dapat sudah dikirim lalu transaksi diedit, implementasi direkomendasikan memakai salah satu pola berikut:

### Opsi direkomendasikan: snapshot

Saat dokumen difinalisasi, sistem menyimpan snapshot data dokumen.

Keuntungan:

- PDF/link yang sudah dikirim tetap konsisten;
- perubahan transaksi berikutnya tidak diam-diam mengubah dokumen lama;
- admin dapat memilih **Regenerate Document** bila memang ingin memperbarui dokumen.

Jika dokumen diregenerate, simpan timestamp dan audit event.

---

## 35. Performance dan Reliability

- halaman list menggunakan pagination bila data membesar;
- query transaction/customer menggunakan index yang relevan;
- PDF generation tidak menyebabkan duplicate document akibat double click;
- submit button memiliki double-submit guard;
- monetary calculation dilakukan server-side menggunakan decimal/integer minor-unit yang aman, bukan float mentah;
- data transaksi tidak boleh hilang jika PDF generation gagal;
- kegagalan WhatsApp deep-link tidak mengubah transaksi.

---

## 36. Accessibility

- contrast text memenuhi standar readability;
- keyboard navigation untuk form utama;
- label form eksplisit;
- focus state terlihat pada Light dan Dark Mode;
- error tidak hanya ditandai dengan warna;
- tombol icon memiliki accessible label;
- range slider memiliki value yang terbaca;
- modal/warning dapat dioperasikan keyboard.

---

## 37. Acceptance Criteria V1

### Authentication

- guest tidak dapat membuka dashboard;
- admin dapat login/logout;
- tidak ada register publik.

### Customer

- admin dapat membuat customer dengan WA, email, atau keduanya;
- perusahaan opsional;
- detail customer menampilkan histori transaksi.

### Transaction

- admin dapat membuat satu project dengan banyak item;
- subtotal dihitung otomatis;
- qty dan harga menghasilkan line subtotal benar;
- diskon nominal dan persen bekerja;
- progress menggunakan slider;
- jenis jasa Lainnya menampilkan custom input.

### Payment

- admin dapat mencatat DP/pembayaran;
- DP persen dapat dikonversi ke nominal;
- metode pembayaran Lainnya menampilkan custom input;
- total paid dan remaining dihitung otomatis;
- status pembayaran berubah otomatis.

### Invoice

- Invoice dapat dibuat saat transaksi belum lunas;
- Invoice menampilkan data final yang dibutuhkan;
- Invoice dapat dibuka melalui secure public link;
- Invoice dapat di-download PDF;
- Invoice dapat dibagikan melalui WhatsApp.

### Receipt

- Receipt tidak dapat dibuat jika transaksi belum lunas;
- Receipt dapat dibuat jika transaksi lunas;
- Receipt menampilkan status Lunas;
- Receipt dapat dibuka via secure link dan PDF.

### Settings

- admin dapat upload logo;
- admin dapat upload tanda tangan;
- admin dapat mengubah footer Invoice;
- admin dapat mengubah footer Receipt;
- admin dapat mengubah template WhatsApp.

### Security

- public link tidak menggunakan DB ID sequential;
- invalid/revoked token tidak membuka dokumen;
- edit transaksi lunas memunculkan warning.

### UI

- palette mengikuti referensi user;
- logo mengikuti aset user;
- Light Mode tersedia;
- Dark Mode tersedia;
- pilihan theme persisten;
- mobile dan desktop usable;
- PDF tetap print-friendly terlepas dari theme dashboard.

---

## 38. Recommended Implementation Order

### Phase 1 — Foundation

- project setup;
- authentication;
- database foundation;
- admin layout;
- design tokens palette;
- Light/Dark Mode;
- Settings branding dasar.

### Phase 2 — Master Data dan Customer

- customer CRUD;
- customer history shell;
- jenis jasa;
- unit item;
- payment methods.

### Phase 3 — Transaction Engine

- transaction CRUD;
- dynamic item rows;
- calculation engine;
- discount;
- status pengerjaan;
- progress slider;
- validation.

### Phase 4 — Payments

- payment records;
- DP nominal/persen;
- payment method/custom method;
- auto payment status;
- remaining calculation.

### Phase 5 — Invoice

- document numbering;
- Invoice template;
- secure public token;
- public page;
- PDF/print;
- snapshot/finalization.

### Phase 6 — Receipt

- receipt eligibility;
- Receipt template;
- receipt public page;
- PDF;
- Lunas state.

### Phase 7 — WhatsApp dan Settings Lengkap

- WhatsApp templates;
- placeholder rendering;
- deep link;
- logo/signature upload;
- footer editor;
- document preferences.

### Phase 8 — Dashboard, Search, Audit

- summary dashboard;
- filters/search;
- document library;
- last modified;
- activity log;
- edit warnings.

### Phase 9 — Hardening

- responsive QA;
- Light/Dark QA;
- security review;
- document print/PDF QA;
- calculation tests;
- feature tests;
- error/empty/loading states;
- deployment checks.

---

## 39. Test Scenario Kritis

1. Customer perorangan hanya memiliki WhatsApp.
2. Customer perusahaan memiliki PIC + company + email.
3. Transaksi satu item tanpa diskon.
4. Transaksi beberapa item.
5. Diskon nominal.
6. Diskon persen.
7. Customer belum bayar → Invoice.
8. Customer DP 50% → Invoice menunjukkan dibayar dan sisa dengan benar.
9. Customer bayar dua tahap → total pembayaran terakumulasi.
10. Customer langsung lunas → Receipt tersedia.
11. Receipt dicoba sebelum lunas → ditolak.
12. Edit transaksi Lunas → warning muncul.
13. Payment dihapus sehingga transaksi kembali belum lunas → status diperbarui dan Receipt existing tidak boleh silently berubah.
14. Public token salah → dokumen tidak ditemukan.
15. Public token di-revoke → dokumen tidak lagi dapat dibuka.
16. WhatsApp customer kosong tetapi email tersedia → tombol WhatsApp disabled/menjelaskan nomor belum tersedia.
17. Jenis jasa Lainnya → custom value tersimpan.
18. Metode pembayaran Lainnya → custom value tersimpan.
19. Logo diganti dari Settings → dokumen baru menggunakan branding terbaru sesuai aturan snapshot.
20. Dark Mode aktif → seluruh admin page readable.
21. PDF dari Dark Mode tetap memakai print document theme yang benar.

---

## 40. Definition of Done V1

V1 dianggap selesai apabila:

1. Owner dapat login dan seluruh halaman internal terlindungi.
2. Customer dan histori transaksi dapat dikelola.
3. Satu transaksi dapat berisi banyak item jasa.
4. Perhitungan qty, harga, subtotal, diskon, total, payment, dan remaining akurat.
5. DP nominal/persen dapat dicatat.
6. Status pembayaran otomatis benar.
7. Invoice dapat dibuat dan dibagikan saat transaksi belum lunas.
8. Receipt hanya tersedia setelah lunas.
9. Invoice/Receipt berasal dari satu transaksi yang sama.
10. PDF dan print layout lolos QA.
11. Secure public link tidak mudah ditebak.
12. WhatsApp sharing bekerja dengan template editable.
13. Logo dan tanda tangan dapat diupload lewat Settings.
14. Footer Invoice/Receipt dapat diedit.
15. Terbilang otomatis benar.
16. Last modified dan warning edit transaksi lunas bekerja.
17. Light Mode dan Dark Mode tersedia dan konsisten.
18. Palette dan logo mengikuti referensi visual Awi Code yang diberikan user.
19. UI responsive pada HP dan desktop.
20. Tidak ada fitur payment gateway/customer portal yang keluar dari scope.

---

## 41. Keputusan Produk yang Sudah Dikunci

Keputusan berikut menjadi baseline dan tidak perlu ditanyakan ulang kecuali owner mengubah requirement:

- Nama brand: **Awi Code**.
- Sistem digunakan sendiri oleh owner/admin.
- Sistem mendukung berbagai jenis jasa, tidak hanya website.
- `Lainnya` pada jenis jasa memunculkan input custom.
- Customer dapat mewakili perusahaan/usaha.
- Satu customer dapat mempunyai banyak transaksi.
- Satu transaksi = satu project.
- Satu transaksi dapat mempunyai banyak item.
- Item mendukung nama, deskripsi, qty, satuan, harga, subtotal.
- Hanya satu tanggal utama transaksi.
- Status pengerjaan: Menunggu, Dikerjakan, Revisi, Selesai, Dibatalkan.
- Progress menggunakan slider.
- Diskon mendukung nominal dan persen.
- Tidak ada biaya tambahan khusus di luar item jasa.
- Tidak ada payment gateway.
- Tidak ada upload bukti bayar oleh customer.
- Pembayaran dikonfirmasi/dicatat sendiri oleh admin.
- Metode pembayaran default: DANA, GoPay, OVO, Jago, BRI, QRIS, ShopeePay, Allo Bank, Lainnya.
- DP bebas nominal, dengan bantuan input persen.
- Riwayat payment record didukung tetapi tanggal pembayaran tidak wajib ditampilkan pada dokumen.
- Invoice dan Receipt berada pada satu sistem/transaksi yang sama.
- Invoice dapat digunakan sebelum lunas.
- Receipt dibuat ketika sudah lunas.
- Customer hanya melihat dokumen final, bukan progress project.
- Nomor dokumen menggunakan format profesional semi-random seperti `AWI-INV-260825-K8F2`.
- Public link menggunakan token aman yang lebih panjang daripada nomor dokumen.
- WhatsApp template editable dari Settings dan mempunyai default formal.
- Terbilang tersedia.
- Footer dokumen editable dari Settings.
- Logo dan tanda tangan diupload dari Settings.
- Edit transaksi Lunas wajib menampilkan warning.
- Last modified wajib tersedia.
- Palette mengikuti gambar referensi terbaru dari user.
- Logo mengikuti gambar/logo referensi terbaru dari user.
- Light Mode dan Dark Mode wajib tersedia.

---

## 42. Requirement Change Log

Setiap perubahan besar setelah development dimulai sebaiknya dicatat agar requirement lama tidak hilang.

Format:

```text
Tanggal:
Versi PRD:
Perubahan:
Alasan:
Dampak ke UI:
Dampak ke Database:
Dampak ke Business Rule:
Dampak ke Dokumen/PDF:
```

---

**End of PRD — Awi Code Invoice & Receipt Manager — Baseline v1.0**
