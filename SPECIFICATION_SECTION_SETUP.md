# Model Specification Sections - Setup Guide

## 📋 Overview

Fitur baru untuk menampilkan section cinematic dengan background image, title, description, dan carousel gambar dengan title & description (mirip seperti gambar referensi untuk showcase mesin, interior, dll).

## 🚀 Quick Setup

### 1. Import Database

```bash
psql -U postgres -d landing_cms -f database/model_specification_sections.sql
```

Atau copy-paste isi file ke pgAdmin Query Tool.

### 2. Akses Admin Panel

1. Login ke admin: http://localhost/lending_word/admin/
2. Klik "Manage Model Variants"
3. Klik tombol "Specification" pada model yang ingin dikelola

### 3. Manage Specification Sections

**Tambah Section Baru:**
- Klik "Add New Section"
- Isi Background Image URL (gambar background full-screen)
- Isi Title (judul section, contoh: "Drive")
- Isi Description (deskripsi section)
- Isi Sort Order (urutan tampil)
- Klik "Save"

**Tambah Carousel Images:**
- Klik tab "Section Images" atau tombol "Images" pada section
- Klik "Add New Image"
- Isi Image URL (gambar untuk card carousel)
- Isi Title (contoh: "3.0-litre flat-6 engine.")
- Isi Description (deskripsi detail)
- Isi Sort Order (urutan carousel)
- Klik "Save"

**Update/Delete:**
- Klik tombol "Update" untuk edit
- Klik tombol "Delete" untuk hapus (dengan konfirmasi)

## 📁 File Structure

```
lending_word/
├── database/
│   └── model_specification_sections.sql    # Database schema + sample data
├── app/
│   └── models/
│       └── ModelSpecificationSection.php   # Model class
│   └── views/
│       └── frontend/
│           └── model-detail.php            # Updated with specification section
└── admin/
    └── specification.php                   # Admin panel CRUD
```

## 🎨 Features

- ✅ Full-screen cinematic background
- ✅ Title & description overlay
- ✅ Carousel dengan 3 card per view (responsive)
- ✅ Card dengan gambar, title, dan description
- ✅ Arrow navigation untuk carousel
- ✅ Admin panel lengkap (Add, Update, Delete)
- ✅ Multiple sections per model variant
- ✅ Sort order untuk mengatur urutan

## 🔧 Customization

### Ubah Jumlah Card Per View

Edit di `model-detail.php`:

```css
.specification-carousel-card {
    min-width: calc(33.333% - 20px); /* 3 cards */
}

/* Untuk 2 cards: */
min-width: calc(50% - 15px);

/* Untuk 4 cards: */
min-width: calc(25% - 22.5px);
```

### Ubah Tinggi Gambar Card

```css
.specification-carousel-card img {
    height: 300px; /* Ubah sesuai kebutuhan */
}
```

## 📝 Sample Data

Database sudah include sample data untuk variant_id = 1 (911 Targa 4S):

**Section:**
- Title: "Drive"
- Description: "Never before have we been able to make a car so powerful..."
- Background: Car image

**Carousel Images:**
1. 3.0-litre flat-6 engine
2. Performance
3. Precision

## 🎯 Usage Example

```php
// Di model-detail.php, section akan otomatis muncul sebelum sound section
// Jika ada data specification sections untuk variant tersebut

// Untuk menambah section baru via code:
$modelSpec = new ModelSpecificationSection();
$sectionId = $modelSpec->create(
    $variantId,
    'https://example.com/bg.jpg',
    'Interior',
    'Luxurious interior with premium materials',
    1
);

// Tambah carousel images:
$modelSpec->addImage(
    $sectionId,
    'https://example.com/dashboard.jpg',
    'Dashboard',
    'Modern digital dashboard with intuitive controls',
    1
);
```

## 📞 Notes

- Section akan muncul di model-detail.php sebelum sound section
- Bisa multiple sections per model variant
- Carousel otomatis loop (infinite scroll)
- Responsive design (mobile: 1 card, tablet: 2 cards, desktop: 3 cards)
- Background image menggunakan parallax effect (background-attachment: fixed)

## 🔗 Related Files

- Gallery Section: `model_gallery` table (untuk layout 3 gambar berbeda)
- Sound Section: `model_sound` table (untuk audio experience)
- Specification Section: `model_specification_sections` table (untuk carousel showcase)
