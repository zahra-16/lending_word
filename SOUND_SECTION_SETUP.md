# Sound Section Setup

## 📋 Instalasi

### 1. Import Database

Jalankan SQL setup untuk menambahkan sound section:

```bash
psql -U postgres -d landing_cms -f sound_section_setup.sql
```

Atau copy-paste isi file `sound_section_setup.sql` ke pgAdmin Query Tool.

### 2. Akses Admin Panel

1. Buka: http://localhost/lending_word/admin/
2. Login dengan kredensial admin
3. Klik tab **"Sound"**
4. Edit konten sound section:
   - **Title**: Judul utama (contoh: "Set the pace: 9,000 revolutions per minute.")
   - **Caption**: Deskripsi/subtitle
   - **Background Image URL**: URL gambar background
   - **Button Text**: Teks tombol (contoh: "Hold for sound")
5. Klik "Save Changes"

### 3. Preview

Buka landing page: http://localhost/lending_word/#sound

## 🎨 Fitur Sound Section

- ✅ Full-screen background image
- ✅ Cinematic overlay effect
- ✅ Responsive design
- ✅ Interactive button dengan icon
- ✅ Editable via admin panel
- ✅ Style sesuai referensi Porsche

## 🎯 Customisasi

### Ganti Background Image

Di admin panel, masukkan URL gambar baru di field "Background Image URL".

Contoh URL:
- Porsche: `https://files.porsche.com/filestore/image/...`
- Unsplash: `https://images.unsplash.com/photo-xxxxx`
- Local: `images/sound-bg.jpg`

### Edit Warna & Style

Edit file: `public/assets/css/style.css`

Cari section: `/* ================= SOUND SECTION ================= */`

Ubah:
- Background overlay: `.sound-bg { filter: brightness(0.6); }`
- Button color: `.sound-btn { background: rgba(255,255,255,0.95); }`
- Text color: `.sound-content { color: #fff; }`

## 📁 File yang Dimodifikasi

```
lending_word/
├── sound_section_setup.sql          # Database setup
├── SOUND_SECTION_SETUP.md           # Dokumentasi ini
├── public/assets/css/style.css      # CSS untuk sound section
├── app/views/frontend/index.php     # Frontend view
└── app/views/admin/dashboard.php    # Admin panel
```

## 🔧 Troubleshooting

### Sound section tidak muncul?

1. Pastikan sudah import `sound_section_setup.sql`
2. Clear browser cache (Ctrl+F5)
3. Cek database apakah data sound sudah ada:
   ```sql
   SELECT * FROM content WHERE section = 'sound';
   ```

### Gambar tidak muncul?

1. Pastikan URL gambar valid dan accessible
2. Cek console browser untuk error
3. Gunakan URL absolute (https://...)

### Button tidak berfungsi?

Button saat ini hanya tampilan. Untuk menambahkan audio player:

1. Tambah field `audio_url` di database
2. Tambah JavaScript untuk play audio
3. Update admin panel untuk input audio URL

## 📞 Support

Jika ada pertanyaan atau masalah, silakan hubungi developer.
