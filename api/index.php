<!-- api/index.php -->
<?php 

// Atur konfigurasi PHP yang biasanya kamu tulis di php.ini
ini_set('upload_max_filesize', '10M');
ini_set('post_max_size', '10M');
ini_set('memory_limit', '512M');

require __DIR__. '/../public/index.php';