# Landing Page CMS - Tema Olahraga (PostgreSQL)

Landing page dengan backend CMS seperti WordPress dimana semua elemen bisa diedit melalui admin panel.

## 🚀 Fitur

- ✅ Landing page responsif dengan tema olahraga
- ✅ Admin panel untuk edit semua konten
- ✅ Edit teks, gambar, dan semua elemen
- ✅ Tema bisa diubah sepenuhnya
- ✅ Database PostgreSQL untuk menyimpan konten
- ✅ Login admin yang aman
- ✅ **Model Overview** - Katalog lengkap model dengan filter (NEW!)
- ✅ **Model Variants** - 22+ variant Porsche dengan spesifikasi detail (NEW!)
- ✅ **Model Specification** - Section carousel untuk showcase mesin, interior, dll (NEW!)
- ✅ **Sound Section** - Section cinematic untuk showcase sound experience (NEW!)

## 📋 Instalasi

### 1. Setup Database

Buka pgAdmin atau PostgreSQL client:

```bash
# Buat database
psql -U postgres
CREATE DATABASE landing_cms;

# Connect ke database
\c landing_cms

# Import setup.sql
\i setup.sql
```

Atau copy-paste isi file `setup.sql` ke pgAdmin Query Tool.

### 2. Konfigurasi Database

Edit file `config.php` jika perlu mengubah kredensial database:

```php
define('DB_HOST', 'localhost');
define('DB_PORT', '5432');
define('DB_NAME', 'landing_cms');
define('DB_USER', 'postgres');
define('DB_PASS', '');
```

### 3. Akses Website

- **Landing Page**: http://localhost/lending_word/
- **Model Overview**: http://localhost/lending_word/models.php (NEW!)
- **Admin Panel**: http://localhost/lending_word/admin/

### 4. Login Admin

```
Username: admin
Password: admin123
```

## 🎨 Cara Menggunakan

### Edit Konten

1. Buka admin panel: http://localhost/lending_word/admin/
2. Login dengan kredensial admin
3. Edit semua konten yang tersedia:
   - Hero Section (judul, subtitle, tombol, gambar)
   - About Section (judul, deskripsi, gambar)
   - Features Section (3 fitur dengan judul dan deskripsi)
   - CTA Section (call-to-action)
   - Footer (copyright, email, phone)
4. Klik "Simpan Perubahan"
5. Preview landing page dengan tombol "Preview Landing Page"

### Ganti Tema

Semua konten bisa diganti termasuk:
- Teks dan judul
- Gambar (ganti URL gambar)
- Warna (edit CSS di index.php)
- Layout (edit HTML di index.php)

### Ganti Gambar

Untuk mengganti gambar, masukkan URL gambar baru di admin panel. Contoh:
- Unsplash: https://images.unsplash.com/photo-xxxxx
- Upload gambar ke folder dan gunakan path relatif: images/foto.jpg

## 📁 Struktur File

```
lending_word/
├── config.php          # Konfigurasi database PostgreSQL
├── index.php           # Landing page utama
├── setup.sql           # Database setup (PostgreSQL)
├── README.md           # Dokumentasi
└── admin/
    ├── index.php       # Admin panel
    ├── login.php       # Halaman login
    └── logout.php      # Logout
```

## 🔒 Keamanan

- Password di-hash menggunakan bcrypt
- Session-based authentication
- Prepared statements untuk mencegah SQL injection

## 🛠️ Teknologi

- PHP 7.4+
- PostgreSQL 12+
- HTML5, CSS3
- PDO untuk database

## 📝 Catatan

- Pastikan Laragon/XAMPP sudah running
- Pastikan PostgreSQL service aktif
- Untuk production, ganti password admin default
- Untuk upload gambar lokal, buat folder `images/` dan upload file disana

## 🏎️ Model Overview Feature (NEW!)

Fitur baru untuk menampilkan katalog lengkap model kendaraan dengan filter dan spesifikasi detail.

### Setup Model Overview

1. Import database tambahan:
```bash
psql -U postgres -d landing_cms -f models_setup.sql
```

2. (Optional) Tambah data kategori lain:
```bash
psql -U postgres -d landing_cms -f models_additional_data.sql
```

### Fitur Model Overview

- ✅ Filter by Category (All, 718, 911, Taycan, Panamera, Macan, Cayenne)
- ✅ Filter by Body Design, Seats, Drive Type, Fuel Type
- ✅ 22+ Porsche 911 variants dengan spesifikasi lengkap
- ✅ Badge "New" untuk model terbaru
- ✅ Responsive design
- ✅ Admin panel untuk manage variants

### Dokumentasi Lengkap

- **Quick Start**: Lihat `MODEL_OVERVIEW_QUICKSTART.md`
- **Full Documentation**: Lihat `MODEL_OVERVIEW_SETUP.md`
- **Summary**: Lihat `MODEL_OVERVIEW_SUMMARY.md`

## 🔊 Sound Section Feature (NEW!)

Fitur baru untuk menampilkan section cinematic yang showcase sound experience kendaraan.

### Setup Sound Section

1. Import database:
```bash
psql -U postgres -d landing_cms -f sound_section_setup.sql
```

2. Edit konten di admin panel:
   - Buka tab "Sound"
   - Edit title, caption, background image, dan button text
   - Klik "Save Changes"

### Fitur Sound Section

- ✅ Full-screen cinematic background
- ✅ Responsive design
- ✅ Interactive button dengan icon
- ✅ Editable via admin panel
- ✅ Style premium sesuai referensi Porsche

### Dokumentasi Lengkap

- **Setup Guide**: Lihat `SOUND_SECTION_SETUP.md`

## 🎨 Model Specification Feature (NEW!)

Fitur baru untuk menampilkan section cinematic dengan background image, title, description, dan carousel gambar untuk showcase detail seperti mesin, interior, performance, dll.

### Setup Specification Section

1. Import database:
```bash
psql -U postgres -d landing_cms -f database/model_specification_sections.sql
```

2. Manage di admin panel:
   - Login ke admin panel
   - Klik "Manage Model Variants"
   - Klik "Specification" pada model yang ingin dikelola
   - Tambah section dan carousel images

### Fitur Specification Section

- ✅ Full-screen cinematic background dengan parallax
- ✅ Title & description overlay
- ✅ Carousel dengan 3 cards per view (responsive)
- ✅ Card dengan image, title, dan description
- ✅ Arrow navigation untuk slide carousel
- ✅ Admin panel lengkap (Add, Update, Delete)
- ✅ Multiple sections per model variant
- ✅ Sort order untuk mengatur urutan

### Dokumentasi Lengkap

- **Setup Guide**: Lihat `SPECIFICATION_SECTION_SETUP.md`
- **Quick Summary**: Lihat `SPECIFICATION_SECTION_SUMMARY.md`

## 🎯 Customisasi Lanjutan

### Tambah Section Baru

1. Tambah data di database:
```sql
INSERT INTO content (section, key_name, value, type) VALUES
('new_section', 'title', 'Judul Baru', 'text');
```

2. Tambah HTML di index.php:
```php
<section class="section">
    <h2><?= getContent('new_section', 'title') ?></h2>
</section>
```

### Ubah Warna Tema

Edit CSS di `index.php`, cari warna utama:
- Primary: #ff6b35 (orange)
- Secondary: #667eea (purple)
- Dark: #2c3e50

Ganti dengan warna tema baru sesuai keinginan.

## 📞 Support

Jika ada pertanyaan atau masalah, silakan hubungi developer.
