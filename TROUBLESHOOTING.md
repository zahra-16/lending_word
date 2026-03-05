# Troubleshooting Login Admin

## Masalah: Tidak bisa login dengan admin/admin123

### Langkah 1: Cek PHP PostgreSQL Extension

Buka browser: `http://localhost/lending_word/check_php.php`

Jika muncul error "could not find driver", berarti PostgreSQL extension belum aktif.

**Solusi:**
1. Buka file `php.ini` (di Laragon: `C:\laragon\bin\php\php-x.x.x\php.ini`)
2. Cari dan uncomment (hapus `;` di depan):
   ```
   extension=pdo_pgsql
   extension=pgsql
   ```
3. Save file
4. Restart Laragon/Apache
5. Refresh `check_php.php` - harus muncul ✓ pdo_pgsql

### Langkah 2: Reset Password Admin

**Opsi A - Via pgAdmin:**
1. Buka pgAdmin
2. Connect ke database `landing_cms`
3. Klik kanan database → Query Tool
4. Copy-paste isi file `reset_admin.sql`
5. Execute (F5)

**Opsi B - Via psql:**
```bash
psql -U postgres -d landing_cms -f reset_admin.sql
```

### Langkah 3: Test Login

Buka: `http://localhost/lending_word/admin/debug_login.php`

Ini akan menampilkan debug info untuk troubleshooting.

**Jika berhasil:**
- Akan muncul "✓ Login BERHASIL!"
- Klik link "Ke Dashboard"

**Jika gagal:**
- Lihat debug information
- Screenshot dan kirim ke developer

### Langkah 4: Login Normal

Setelah berhasil di debug_login.php, coba login normal:

`http://localhost/lending_word/admin/login.php`

```
Username: admin
Password: admin123
```

## Alternatif: Buat Admin Baru Manual

Jika masih gagal, buat admin baru dengan password custom:

1. Buka: `http://localhost/lending_word/generate_password.php`
2. Copy hash yang dihasilkan
3. Jalankan di pgAdmin:
   ```sql
   DELETE FROM admin_users WHERE username = 'admin';
   INSERT INTO admin_users (username, password) VALUES 
   ('admin', 'PASTE_HASH_DISINI');
   ```
4. Login dengan password yang Anda buat

## Cek Database

Pastikan tabel dan data sudah ada:

```sql
-- Cek tabel
SELECT * FROM admin_users;

-- Jika kosong, insert manual
INSERT INTO admin_users (username, password) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
```

## Masih Bermasalah?

Hubungi developer dengan info:
1. Screenshot error
2. Output dari check_php.php
3. Output dari debug_login.php
