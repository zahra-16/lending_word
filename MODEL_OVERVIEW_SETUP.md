# Model Overview Feature - Setup Guide

## 📋 Deskripsi

Fitur Model Overview menampilkan katalog lengkap model-model kendaraan dengan:
- Filter berdasarkan kategori model (911, 718, Taycan, dll)
- Filter berdasarkan Body Design, Seats, Drive Type, Fuel Type
- Tampilan card dengan spesifikasi lengkap
- Grouping berdasarkan variant group
- Badge "New" untuk model terbaru

## 🚀 Instalasi

### 1. Setup Database

Jalankan SQL setup untuk membuat tabel baru:

```bash
# Buka PostgreSQL
psql -U postgres -d landing_cms

# Import file SQL
\i models_setup.sql
```

Atau copy-paste isi file `models_setup.sql` ke pgAdmin Query Tool.

### 2. File yang Dibuat

Fitur ini menambahkan file-file berikut:

```
lending_word/
├── models.php                          # Halaman Model Overview (Frontend)
├── models_setup.sql                    # Database setup
├── admin/
│   └── manage_models.php              # Admin panel untuk manage variants
└── app/
    └── models/
        └── ModelVariant.php           # Model class untuk variants
```

### 3. Update Landing Page

Button "Explore" di setiap card model sudah otomatis mengarah ke halaman Model Overview dengan kategori yang sesuai.

## 📖 Cara Menggunakan

### Akses Halaman Model Overview

**Frontend (User):**
- URL: `http://localhost/lending_word/models.php`
- Atau klik button "Explore" di landing page

**Admin Panel:**
- URL: `http://localhost/lending_word/admin/manage_models.php`
- Login dengan kredensial admin terlebih dahulu

### Filter Model

1. **Filter by Category**: Klik radio button di sidebar (All, 718, 911, Taycan, dll)
2. **Filter by Body Design**: Expand "Body Design" dan pilih Coupe, Cabriolet, Targa
3. **Filter by Seats**: Pilih jumlah kursi (2, 4, 5)
4. **Filter by Drive**: Pilih Rear-Wheel Drive, All-Wheel Drive
5. **Filter by Fuel Type**: Pilih Gasoline, Electric, Hybrid
6. **Reset Filter**: Klik tombol "Reset Filter" untuk clear semua filter

### Manage Model Variants (Admin)

1. Login ke admin panel
2. Buka menu "Manage Model Variants"
3. Isi form untuk menambah variant baru:
   - Pilih Category (911, 718, Taycan, dll)
   - Masukkan nama model (e.g., "911 Carrera S")
   - Masukkan variant group (e.g., "911 Carrera Model variants")
   - Upload/masukkan URL gambar
   - Isi spesifikasi: fuel type, drive type, transmission
   - Isi performa: acceleration, power (kW/PS), top speed
   - Isi detail: body design, seats
   - Set sort order
   - Centang "Mark as New" jika model baru
4. Klik "Add Model Variant"

### Delete Model Variant

1. Scroll ke tabel "Existing Model Variants"
2. Klik tombol "Delete" pada variant yang ingin dihapus
3. Konfirmasi penghapusan

## 🎨 Struktur Database

### Tabel: model_categories

Menyimpan kategori model (911, 718, Taycan, dll)

| Column | Type | Description |
|--------|------|-------------|
| id | SERIAL | Primary key |
| name | VARCHAR(100) | Nama kategori |
| slug | VARCHAR(100) | URL-friendly slug |
| count | INT | Jumlah model dalam kategori |
| sort_order | INT | Urutan tampilan |

### Tabel: model_variants

Menyimpan detail setiap variant model

| Column | Type | Description |
|--------|------|-------------|
| id | SERIAL | Primary key |
| category_id | INT | Foreign key ke model_categories |
| name | VARCHAR(200) | Nama variant |
| variant_group | VARCHAR(200) | Group variant (untuk grouping) |
| image | VARCHAR(500) | URL gambar |
| fuel_type | VARCHAR(50) | Jenis bahan bakar |
| drive_type | VARCHAR(50) | Jenis penggerak |
| transmission | VARCHAR(50) | Jenis transmisi |
| acceleration | VARCHAR(50) | Akselerasi 0-100 km/h |
| power_kw | INT | Tenaga dalam kW |
| power_ps | INT | Tenaga dalam PS |
| top_speed | VARCHAR(50) | Kecepatan maksimal |
| body_design | VARCHAR(50) | Desain body |
| seats | INT | Jumlah kursi |
| is_new | BOOLEAN | Badge "New" |
| sort_order | INT | Urutan tampilan |

## 🔧 Customisasi

### Ubah Warna Tema

Edit file `models.php`, cari bagian `<style>` dan ubah warna:

```css
.btn-primary { background: #000; } /* Tombol utama */
.variant-card.new::before { background: #d5001c; } /* Badge "New" */
```

### Tambah Filter Baru

1. Edit `ModelVariant.php`, tambah filter di method `getFilters()`:
```php
'new_filter' => $this->db->query("SELECT DISTINCT new_column FROM model_variants")->fetchAll(PDO::FETCH_COLUMN)
```

2. Edit `models.php`, tambah filter group di sidebar:
```html
<details class="filter-group">
    <summary>New Filter</summary>
    <div class="filter-options">
        <?php foreach ($filters['new_filter'] as $item): ?>
        <label>
            <input type="checkbox" name="new_filter" value="<?= $item ?>">
            <?= htmlspecialchars($item) ?>
        </label>
        <?php endforeach; ?>
    </div>
</details>
```

### Ubah Layout Grid

Edit `models.php`, cari `.variants-grid`:

```css
.variants-grid { 
    grid-template-columns: repeat(auto-fill, minmax(450px, 1fr)); 
    /* Ubah 450px untuk mengatur lebar minimum card */
}
```

## 📱 Responsive Design

Halaman sudah responsive untuk mobile:
- Sidebar pindah ke atas pada layar kecil
- Grid berubah menjadi 1 kolom
- Touch-friendly untuk mobile devices

## 🔗 Integrasi dengan Landing Page

Button "Explore" di landing page sudah terintegrasi:

```php
<a href="/lending_word/models.php?category=<?= strtolower($model['name']) ?>">
    Explore
</a>
```

Ketika user klik "Explore" pada card "911", akan redirect ke:
`http://localhost/lending_word/models.php?category=911`

## 📝 Data Sample

Database sudah include 22 variant model 911:
- 6 variant 911 Carrera (Coupe)
- 6 variant 911 Carrera Cabriolet
- 2 variant 911 Targa
- 2 variant 911 GT3
- 1 variant 911 GT3 RS
- 1 variant 911 Spirit 70
- 2 variant 911 Turbo (New)
- 1 variant 911 GT3 90 F. A. Porsche (New)
- 1 variant 911 Turbo 50 Years

## 🎯 Fitur Mendatang

Untuk pengembangan lebih lanjut, bisa ditambahkan:
- [ ] Filter real-time dengan JavaScript (tanpa reload)
- [ ] Compare feature untuk membandingkan 2-3 model
- [ ] Detail page untuk setiap variant
- [ ] Wishlist/favorite feature
- [ ] Export to PDF untuk spesifikasi
- [ ] Search functionality
- [ ] Sorting (by price, power, speed, etc)

## 🐛 Troubleshooting

### Error: Table does not exist

**Solusi**: Pastikan sudah menjalankan `models_setup.sql`

```bash
psql -U postgres -d landing_cms -f models_setup.sql
```

### Gambar tidak muncul

**Solusi**: Pastikan URL gambar valid atau gunakan placeholder:

```php
onerror="this.src='https://via.placeholder.com/600x300'"
```

### Filter tidak bekerja

**Solusi**: Pastikan JavaScript enabled di browser dan tidak ada error di console.

## 📞 Support

Jika ada pertanyaan atau masalah, silakan hubungi developer.

---

**Version**: 1.0  
**Last Updated**: 2024
