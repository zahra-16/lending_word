<?php
// Redirect to new location
header('Location: /lending_word/app/views/frontend/models.php' . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : ''));
exit;
