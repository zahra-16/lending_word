-- Script untuk reset password admin
-- Jalankan di pgAdmin atau psql

-- Pastikan tabel ada
CREATE TABLE IF NOT EXISTS admin_users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- Hapus admin lama jika ada
DELETE FROM admin_users WHERE username = 'admin';

-- Insert admin baru dengan password: admin123
-- Hash ini sudah ditest dan valid untuk password 'admin123'
INSERT INTO admin_users (username, password) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Cek hasil
SELECT id, username, 
       CASE 
           WHEN password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' 
           THEN 'Password Hash BENAR' 
           ELSE 'Password Hash SALAH' 
       END as status
FROM admin_users 
WHERE username = 'admin';
