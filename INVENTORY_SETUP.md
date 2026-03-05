# Inventory Section Setup Instructions

## Cara Install Inventory Section:

### 1. Jalankan SQL di pgAdmin atau PostgreSQL client:

```sql
-- Content for Inventory section
INSERT INTO content (section, key_name, value, type) VALUES
('inventory', 'title', 'Find your new or pre-owned Porsche.', 'text'),
('inventory', 'description', 'A Porsche is as individual as its owner. It is always an expression of one''s own personality. We help you find your personal dream vehicle from authorised Porsche Centres.', 'textarea'),
('inventory', 'button_text', 'Find your Porsche', 'text'),
('inventory', 'image', 'https://files.porsche.com/filestore/image/multimedia/none/rd-2023-homepage-ww-banner-02/normal/f7c4c909-e5f4-11ed-8101-005056bbdc38;sK;twebp/porsche-normal.webp', 'image')
ON CONFLICT (section, key_name) DO UPDATE SET value = EXCLUDED.value;
```

### 2. Atau copy-paste isi file `inventory_setup.sql` ke pgAdmin Query Tool

### 3. Refresh halaman admin dan landing page

## Fitur:
- ✅ Inventory section dengan gambar dan teks
- ✅ Bisa edit semua konten dari admin panel (tab Content)
- ✅ Responsive design
- ✅ Styling sesuai tema Porsche

## Edit Konten:
1. Login ke admin panel: http://localhost/lending_word/admin/
2. Pilih tab "Content"
3. Scroll ke section "Inventory"
4. Edit title, description, button text, dan image URL
5. Klik "Save Changes"
