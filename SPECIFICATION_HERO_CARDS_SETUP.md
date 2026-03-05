# Specification Hero Cards - Setup Guide

## 🎯 Overview

Sekarang setiap specification section memiliki 2 jenis konten yang bisa dikelola:

1. **Hero Cards** - Cards yang ditampilkan di bawah hero section (3-4 cards per row)
2. **Carousel Images** - Images yang ditampilkan dalam carousel slider

## 📋 Setup Database

### 1. Jalankan SQL Update

```bash
psql -U postgres -d landing_cms -f database/specification_cards_update.sql
```

Atau copy-paste isi file ke pgAdmin Query Tool.

### 2. Struktur Database

**Tabel yang diupdate:**
- `model_specification_section_images` → `model_specification_carousel_images` (renamed)
- `model_specification_hero_cards` (new table)

## 🎨 Cara Menggunakan

### 1. Akses Admin Panel

```
http://localhost/lending_word/admin/?tab=variants
```

### 2. Manage Specification

1. Klik "Specification" pada model variant yang ingin dikelola
2. Klik "Add Section" untuk membuat hero section baru
3. Isi:
   - Background Image URL (hero background)
   - Title (judul hero)
   - Description (deskripsi hero)
   - Sort Order (urutan tampil)

### 3. Manage Hero Cards

1. Klik tombol "Cards" pada section yang sudah dibuat
2. Klik "Add Card" untuk menambah card baru
3. Isi:
   - Image URL (gambar card)
   - Title (judul card, contoh: "Engine Power")
   - Description (deskripsi singkat)
   - Sort Order (urutan tampil)

### 4. Manage Carousel Images

1. Klik tombol "Carousel" pada section
2. Klik "Add Image" untuk menambah gambar carousel
3. Isi data yang sama seperti hero cards

## 📊 Contoh Data

### Hero Section
```
Background: https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=1920
Title: Downforce update
Description: The Porsche 911 GT3 RS is on course for record times – thanks to active aerodynamics, high downforce and a consistent lightweight construction.
```

### Hero Cards (3 cards)
```
Card 1:
- Image: https://images.unsplash.com/photo-1614200187524-dc4b892acf16?w=600
- Title: Engine Power
- Description: Twin-turbocharged 3.0L flat-six engine delivering exceptional performance

Card 2:
- Image: https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=600
- Title: Aerodynamics
- Description: Active aerodynamics with adjustable rear wing for maximum downforce

Card 3:
- Image: https://images.unsplash.com/photo-1583121274602-3e2820c69888?w=600
- Title: Lightweight
- Description: Carbon fiber components reduce weight while maintaining structural integrity
```

## 🎯 Perbedaan Hero Cards vs Carousel

| Feature | Hero Cards | Carousel Images |
|---------|-----------|-----------------|
| Posisi | Di bawah hero section | Di section terpisah |
| Layout | Grid 3-4 cards per row | Horizontal slider |
| Ukuran | Medium (300-400px) | Large (600-800px) |
| Fungsi | Highlight fitur utama | Detail spesifikasi |

## ✅ Checklist

- [ ] Import database update
- [ ] Buat specification section
- [ ] Tambah hero cards (minimal 3)
- [ ] (Optional) Tambah carousel images
- [ ] Preview di frontend

## 🔧 Troubleshooting

### Error: Table already exists
Jika tabel sudah ada, skip bagian CREATE TABLE dan jalankan hanya INSERT data sample.

### Cards tidak muncul
1. Pastikan section_id sudah benar
2. Cek sort_order (mulai dari 0 atau 1)
3. Pastikan image URL valid

### Admin panel error
1. Clear browser cache
2. Refresh halaman
3. Cek console browser untuk error JavaScript

## 📝 Notes

- Hero cards ideal untuk 3-6 items
- Carousel images ideal untuk 4-8 items
- Gunakan image dengan aspect ratio konsisten
- Recommended image size: 600x400px untuk cards, 800x600px untuk carousel
