# Landing Page CMS - Tema Olahraga (PostgreSQL + MVC)

Landing page dengan backend CMS seperti WordPress dengan arsitektur MVC (Model-View-Controller).

## 🏗️ Struktur Project (MVC Pattern)

```
lending_word/
├── app/
│   ├── Database.php              # Database connection (Singleton)
│   ├── models/
│   │   ├── Content.php           # Model untuk data konten
│   │   └── Admin.php             # Model untuk autentikasi admin
│   ├── controllers/
│   │   ├── FrontendController.php # Controller landing page
│   │   └── AdminController.php    # Controller admin panel
│   └── views/
│       ├── frontend/
│       │   └── index.php         # View landing page
│       └── admin/
│           ├── login.php         # View login admin
│           └── dashboard.php     # View dashboard admin
├── public/
│   └── assets/
│       └── css/
│           └── style.css         # Stylesheet
├── admin/
│   ├── index.php                 # Entry point admin dashboard
│   ├── login.php                 # Entry point login
│   └── logout.php                # Entry point logout
├── index.php                     # Entry point landing page
├── setup.sql                     # Database setup (PostgreSQL)
└── README.md                     # Dokumentasi
```

## 📚 Penjelasan Struktur MVC

### 1. **Model** (app/models/)
Model bertanggung jawab untuk berinteraksi dengan database.

- **Content.php**: Handle semua operasi data konten
  - `get($section, $key)` - Ambil satu konten
  - `getAll()` - Ambil semua konten
  - `getAllGrouped()` - Ambil konten dikelompokkan per section
  - `update($id, $value)` - Update satu konten
  - `bulkUpdate($data)` - Update banyak konten sekaligus

- **Admin.php**: Handle autentikasi admin
  - `login($username, $password)` - Proses login
  - `isLoggedIn()` - Cek status login
  - `setSession($userId)` - Set session login
  - `logout()` - Hapus session

### 2. **View** (app/views/)
View bertanggung jawab untuk menampilkan data ke user.

- **frontend/index.php**: Tampilan landing page
- **admin/login.php**: Tampilan halaman login
- **admin/dashboard.php**: Tampilan admin panel untuk edit konten

### 3. **Controller** (app/controllers/)
Controller bertanggung jawab untuk logika bisnis dan menghubungkan Model dengan View.

- **FrontendController.php**:
  - `index()` - Tampilkan landing page

- **AdminController.php**:
  - `login()` - Handle login admin
  - `dashboard()` - Tampilkan dashboard dan handle update konten
  - `logout()` - Handle logout

### 4. **Database** (app/Database.php)
Singleton class untuk koneksi database PostgreSQL menggunakan PDO.

## 🚀 Instalasi

### 1. Setup Database PostgreSQL

```bash
# Buka psql
psql -U postgres

# Buat database
CREATE DATABASE landing_cms;

# Connect ke database
\c landing_cms

# Import setup.sql
\i setup.sql
```

Atau gunakan pgAdmin:
1. Buat database `landing_cms`
2. Buka Query Tool
3. Copy-paste isi `setup.sql`
4. Execute

### 2. Konfigurasi Database

Edit `app/Database.php` jika perlu:

```php
$host = 'localhost';
$port = '5432';
$dbname = 'landing_cms';
$user = 'postgres';
$pass = '';
```

### 3. Akses Website

- **Landing Page**: http://localhost/lending_word/
- **Admin Panel**: http://localhost/lending_word/admin/

### 4. Login Admin

```
Username: admin
Password: admin123
```

## 🎯 Cara Kerja Sistem

### Flow Landing Page:
1. User akses `index.php`
2. `FrontendController->index()` dipanggil
3. Controller load `Content` model
4. Model ambil data dari database
5. Data dikirim ke view `frontend/index.php`
6. View render HTML dengan data

### Flow Admin Panel:
1. Admin akses `admin/login.php`
2. `AdminController->login()` dipanggil
3. Controller validasi dengan `Admin` model
4. Jika valid, set session dan redirect ke dashboard
5. Di dashboard, `AdminController->dashboard()` load data via `Content` model
6. Admin edit konten, submit form
7. Controller update data via `Content->bulkUpdate()`
8. Redirect dengan pesan sukses

## 🎨 Cara Menggunakan

### Edit Konten
1. Login ke admin panel
2. Edit semua field yang tersedia
3. Klik "Simpan Perubahan"
4. Preview landing page

### Tambah Section Baru

**1. Tambah data di database:**
```sql
INSERT INTO content (section, key_name, value, type) VALUES
('testimonial', 'title', 'Testimoni', 'text'),
('testimonial', 'quote', 'Sangat membantu!', 'textarea');
```

**2. Tambah di view (app/views/frontend/index.php):**
```php
<section class="section">
    <h2><?= $getContent('testimonial', 'title') ?></h2>
    <p><?= $getContent('testimonial', 'quote') ?></p>
</section>
```

### Tambah Fitur Baru

**1. Buat method di Model:**
```php
// app/models/Content.php
public function getBySection($section) {
    $stmt = $this->db->prepare("SELECT * FROM content WHERE section = ?");
    $stmt->execute([$section]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
```

**2. Gunakan di Controller:**
```php
// app/controllers/FrontendController.php
$heroContent = $this->contentModel->getBySection('hero');
```

## 🔒 Keamanan

- Password hashing dengan bcrypt
- Session-based authentication
- Prepared statements (SQL injection prevention)
- Input sanitization dengan htmlspecialchars

## 🛠️ Teknologi

- PHP 7.4+ (OOP, MVC Pattern)
- PostgreSQL 12+
- PDO (PHP Data Objects)
- HTML5, CSS3

## 📝 Keuntungan Struktur MVC

✅ **Separation of Concerns**: Logic terpisah dari tampilan
✅ **Maintainable**: Mudah maintenance dan debug
✅ **Scalable**: Mudah ditambah fitur baru
✅ **Reusable**: Model dan Controller bisa dipakai ulang
✅ **Testable**: Mudah untuk unit testing

## 📞 Support

Jika ada pertanyaan tentang struktur MVC atau masalah lainnya, silakan hubungi developer.
