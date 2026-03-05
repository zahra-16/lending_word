# Navbar & Footer Management Setup

## Cara Install:

### 1. Jalankan SQL di pgAdmin atau PostgreSQL client:

Copy-paste isi file `navbar_footer_setup.sql` atau jalankan SQL berikut:

```sql
-- Table for Navbar Links
CREATE TABLE IF NOT EXISTS navbar_links (
    id SERIAL PRIMARY KEY,
    label VARCHAR(100) NOT NULL,
    url VARCHAR(255) NOT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for Footer Sections
CREATE TABLE IF NOT EXISTS footer_sections (
    id SERIAL PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for Footer Links
CREATE TABLE IF NOT EXISTS footer_links (
    id SERIAL PRIMARY KEY,
    section_id INT REFERENCES footer_sections(id) ON DELETE CASCADE,
    label VARCHAR(100) NOT NULL,
    url VARCHAR(255) NOT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for Social Media Links
CREATE TABLE IF NOT EXISTS social_links (
    id SERIAL PRIMARY KEY,
    platform VARCHAR(50) NOT NULL,
    url VARCHAR(255) NOT NULL,
    icon VARCHAR(100),
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default data (lihat file navbar_footer_setup.sql untuk data lengkap)
```

### 2. Refresh admin panel

### 3. Akses tab baru di admin:
- **Tab Navbar**: Manage navbar links (tambah, edit, hapus)
- **Tab Footer**: Manage footer sections, links, dan social media

## Fitur:

### Navbar Management:
- ✅ Tambah/Edit/Hapus navbar links
- ✅ Atur urutan tampilan (sort_order)
- ✅ Custom label dan URL

### Footer Management:
- ✅ Manage footer sections (Company, Legal, dll)
- ✅ Tambah/Edit/Hapus links per section
- ✅ Manage social media links dengan icon
- ✅ Edit newsletter, contact, social media text dari tab Content
- ✅ Responsive design

## Cara Menggunakan:

### Edit Navbar:
1. Login admin panel
2. Pilih tab "Navbar"
3. Tambah link baru atau edit yang ada
4. Atur sort_order untuk urutan tampilan
5. Klik Update/Add

### Edit Footer:
1. Login admin panel
2. Pilih tab "Footer"
3. Setiap section bisa ditambah link baru
4. Edit social media links
5. Edit text footer di tab "Content" section "Footer"

### Icon Social Media:
Gunakan Font Awesome icons:
- Facebook: `fab fa-facebook`
- Instagram: `fab fa-instagram`
- Twitter: `fab fa-twitter`
- LinkedIn: `fab fa-linkedin`
- YouTube: `fab fa-youtube`
- Pinterest: `fab fa-pinterest`

## Struktur Footer:
```
┌─────────────────────────────────────────────┐
│  Newsletter  │  Contact  │  Social Media    │
├─────────────────────────────────────────────┤
│  Company Links  │  Legal Links  │  etc...   │
├─────────────────────────────────────────────┤
│  Copyright & Bottom Text                    │
└─────────────────────────────────────────────┘
```
