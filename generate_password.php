<?php
// Script untuk generate password hash
$password = 'admin123';
$hash = password_hash($password, PASSWORD_BCRYPT);

echo "Password: $password\n";
echo "Hash: $hash\n\n";

// Test verify
if (password_verify($password, $hash)) {
    echo "✓ Password verify berhasil!\n";
} else {
    echo "✗ Password verify gagal!\n";
}

// Hash yang akan digunakan di database
echo "\n--- Copy hash ini ke database ---\n";
echo $hash;
