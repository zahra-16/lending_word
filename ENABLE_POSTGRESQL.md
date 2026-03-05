# SOLUSI: Enable PostgreSQL Extension di Laragon

## Masalah
Error: "could not find driver" - PostgreSQL extension belum aktif

## Solusi Step-by-Step

### Langkah 1: Enable Extension di php.ini

1. **Buka Laragon**
2. **Klik Menu → PHP → php.ini**
3. **Cari baris berikut** (gunakan Ctrl+F):
   ```
   ;extension=pdo_pgsql
   ;extension=pgsql
   ```

4. **Hapus tanda `;` di depannya** sehingga menjadi:
   ```
   extension=pdo_pgsql
   extension=pgsql
   ```

5. **Save file** (Ctrl+S)

### Langkah 2: Restart Laragon

1. Klik **Stop All**
2. Tunggu sampai berhenti
3. Klik **Start All**

### Langkah 3: Verifikasi Extension Aktif

Buka browser: `http://localhost/lending_word/check_php.php`

Harus muncul:
```
✓ pdo_pgsql extension loaded
✓ pgsql extension loaded
```

### Langkah 4: Insert Admin ke Database

**Opsi A - Via pgAdmin (RECOMMENDED):**

1. Buka **pgAdmin**
2. Connect ke PostgreSQL
3. Klik database **landing_cms**
4. Klik kanan → **Query Tool**
5. Copy-paste isi file **reset_admin.sql**
6. Klik **Execute** (F5)
7. Harus muncul: "Password Hash BENAR"

**Opsi B - Via psql:**

```bash
# Buka Command Prompt
cd C:\laragon\www\lending_word

# Jalankan SQL
psql -U postgres -d landing_cms -f reset_admin.sql
```

### Langkah 5: Test Login

1. Buka: `http://localhost/lending_word/admin/debug_login.php`
2. Klik **Test Login**
3. Harus muncul: "✓ Login BERHASIL!"

### Langkah 6: Login Normal

Buka: `http://localhost/lending_word/admin/login.php`

```
Username: admin
Password: admin123
```

## Jika Masih Error

### Cek 1: Apakah database sudah dibuat?
```sql
-- Di pgAdmin atau psql
\l  -- list databases
```
Harus ada database **landing_cms**

### Cek 2: Apakah tabel sudah dibuat?
```sql
-- Connect ke landing_cms
\c landing_cms

-- List tables
\dt

-- Cek isi tabel
SELECT * FROM admin_users;
```

### Cek 3: Jalankan setup.sql lengkap
Jika tabel belum ada, jalankan **setup.sql** di pgAdmin

## Troubleshooting Lainnya

### Error: "database landing_cms does not exist"
```sql
CREATE DATABASE landing_cms;
```

### Error: "relation admin_users does not exist"
Jalankan **setup.sql** atau **reset_admin.sql**

### Error: "password authentication failed"
Cek password PostgreSQL Anda di **app/Database.php**:
```php
$pass = ''; // Ganti dengan password postgres Anda
```

## Kontak Support

Jika masih bermasalah, kirim screenshot:
1. Output dari check_php.php
2. Output dari debug_login.php
3. Error message yang muncul
