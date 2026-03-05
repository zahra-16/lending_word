# Model Specification Sections - Quick Start

## 🚀 Install dalam 3 Langkah

### 1️⃣ Import Database (1 menit)
```bash
psql -U postgres -d landing_cms -f database/model_specification_sections.sql
```

### 2️⃣ Login Admin (30 detik)
1. Buka: http://localhost/lending_word/admin/
2. Login: admin / admin123
3. Klik "Manage Model Variants"

### 3️⃣ Manage Content (2 menit)
1. Klik "Specification" pada model
2. Klik "Add New Section"
3. Isi form → Save
4. Klik "Section Images" → Add images
5. Done! ✅

## 📱 Lihat Hasil
http://localhost/lending_word/app/views/frontend/model-detail.php?id=1

---

## 🎯 Use Cases

### Showcase Mesin
```
Section:
- Background: Gambar mobil dari samping
- Title: "Drive"
- Description: "Powerful engine with cutting-edge technology"

Carousel Images:
1. Gambar mesin → "3.0-litre flat-6 engine" → Deskripsi
2. Gambar turbo → "Twin-turbo system" → Deskripsi
3. Gambar exhaust → "Sport exhaust" → Deskripsi
```

### Showcase Interior
```
Section:
- Background: Interior dashboard
- Title: "Interior"
- Description: "Luxurious comfort meets modern technology"

Carousel Images:
1. Dashboard → "Digital cockpit" → Deskripsi
2. Seats → "Sport seats" → Deskripsi
3. Steering → "Multifunction steering" → Deskripsi
```

### Showcase Performance
```
Section:
- Background: Mobil di track
- Title: "Performance"
- Description: "Engineered for the ultimate driving experience"

Carousel Images:
1. Brakes → "Carbon ceramic brakes" → Deskripsi
2. Suspension → "Adaptive suspension" → Deskripsi
3. Aerodynamics → "Active aero" → Deskripsi
```

---

## 💡 Tips

### Image URLs
- **Unsplash**: https://images.unsplash.com/photo-xxxxx?w=1920
- **Local**: /lending_word/public/assets/images/filename.jpg
- **Recommended size**: 
  - Background: 1920x1080px
  - Carousel: 800x600px

### Best Practices
- ✅ Use high-quality images
- ✅ Keep titles short (max 50 chars)
- ✅ Keep descriptions concise (max 200 chars)
- ✅ Use sort_order to control display order
- ✅ Test on mobile after adding

### Common Mistakes
- ❌ Broken image URLs
- ❌ Too long descriptions
- ❌ Forgetting to add carousel images
- ❌ Not setting sort_order

---

## 🔧 Quick Customization

### Change Cards Per View
Edit `model-detail.php`:
```css
.specification-carousel-card {
    min-width: calc(33.333% - 20px); /* 3 cards */
}
```

### Change Card Height
```css
.specification-carousel-card img {
    height: 300px; /* Adjust as needed */
}
```

### Change Background Overlay
```css
.specification-carousel-section::before {
    background: rgba(0,0,0,0.5); /* Adjust opacity */
}
```

---

## 📊 Data Structure

```
model_specification_sections
├── id
├── variant_id (FK)
├── background_image
├── title
├── description
└── sort_order

model_specification_section_images
├── id
├── section_id (FK)
├── image_url
├── title
├── description
└── sort_order
```

---

## ⚡ Quick Commands

### View All Sections
```sql
SELECT * FROM model_specification_sections WHERE variant_id = 1;
```

### View Section Images
```sql
SELECT * FROM model_specification_section_images WHERE section_id = 1;
```

### Delete All Data (Reset)
```sql
DELETE FROM model_specification_section_images;
DELETE FROM model_specification_sections;
```

### Add Section via SQL
```sql
INSERT INTO model_specification_sections 
(variant_id, background_image, title, description, sort_order) 
VALUES (1, 'https://example.com/bg.jpg', 'Title', 'Description', 1);
```

---

## 🎨 Design Reference

Berdasarkan gambar referensi Porsche:
- Full-screen background dengan parallax
- Text overlay di kiri atas
- Carousel cards di bawah dengan 3 cards visible
- White cards dengan shadow
- Arrow navigation kiri-kanan
- Responsive: 3 → 2 → 1 cards

---

## ✅ Done!

Fitur sudah siap digunakan. Tinggal:
1. Import database ✅
2. Manage di admin ✅
3. Lihat hasilnya ✅

**Total waktu setup**: ~5 menit
**Difficulty**: ⭐⭐☆☆☆ (Easy)

---

## 📞 Quick Links

- Setup Guide: `SPECIFICATION_SECTION_SETUP.md`
- Summary: `SPECIFICATION_SECTION_SUMMARY.md`
- Checklist: `SPECIFICATION_SECTION_CHECKLIST.md`
- Test SQL: `test_specification_sections.sql`
