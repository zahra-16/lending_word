<?php
// Taruh di root, buka di browser, lalu HAPUS
echo "<pre>";
echo htmlspecialchars(file_get_contents(__DIR__ . '/app/database.php'));
echo "</pre>";