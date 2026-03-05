<?php
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "<pre>";
echo "Password: " . $password . "\n\n";
echo "Hash:\n" . $hash . "\n\n";
echo "Verify test:\n";
var_dump(password_verify('admin123', $hash));
