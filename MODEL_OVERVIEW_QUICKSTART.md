# 🚗 Model Overview Feature - Quick Start

## Setup Database (5 menit)

1. Buka pgAdmin atau PostgreSQL terminal
2. Connect ke database `landing_cms`
3. Run file SQL:

```sql
\i models_setup.sql
```

Atau copy-paste isi `models_setup.sql` ke Query Tool.

## Akses Halaman

### User (Frontend)
- **Model Overview**: http://localhost/lending_word/models.php
- Atau klik button **"Explore"** di landing page

### Admin (Backend)
- Login: http://localhost/lending_word/admin/login.php
- Dashboard → Tab **"Model Variants"**
- Atau langsung: http://localhost/lending_word/admin/manage_models.php

## Fitur

✅ Filter by Category (All, 718, 911, Taycan, Panamera, Macan, Cayenne)  
✅ Filter by Body Design (Coupe, Cabriolet, Targa)  
✅ Filter by Seats (2, 4)  
✅ Filter by Drive Type (Rear-Wheel, All-Wheel)  
✅ Filter by Fuel Type (Gasoline, Electric, Hybrid)  
✅ Grouping by Variant Group  
✅ Badge "New" untuk model terbaru  
✅ Responsive design  
✅ Admin panel untuk manage variants  

## Data Sample

Database sudah include **22 model variants** dari Porsche 911:
- 911 Carrera (6 variants)
- 911 Carrera Cabriolet (6 variants)
- 911 Targa (2 variants)
- 911 GT3 (2 variants)
- 911 GT3 RS
- 911 Spirit 70
- 911 Turbo (2 variants - New)
- 911 GT3 90 F. A. Porsche (New)
- 911 Turbo 50 Years

## File Structure

```
lending_word/
├── models.php                      # Frontend page
├── models_setup.sql                # Database setup
├── MODEL_OVERVIEW_SETUP.md         # Full documentation
├── admin/
│   └── manage_models.php          # Admin management
└── app/
    └── models/
        └── ModelVariant.php       # Model class
```

## Troubleshooting

**Error: Table does not exist**
```bash
psql -U postgres -d landing_cms -f models_setup.sql
```

**Gambar tidak muncul**
- Pastikan URL gambar valid
- Atau gunakan placeholder image

**Button Explore tidak bekerja**
- Clear browser cache
- Pastikan file `models.php` ada di root folder

## Next Steps

📖 Baca dokumentasi lengkap: `MODEL_OVERVIEW_SETUP.md`  
🎨 Customize warna dan layout sesuai kebutuhan  
🔧 Tambah filter atau fitur baru  

---

**Ready to use!** 🎉
