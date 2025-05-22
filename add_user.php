<?php
require 'config.php';

$username = 'admin';
$password = password_hash('admin123', PASSWORD_DEFAULT);
$nama = 'Administrator';

$stmt = $pdo->prepare("INSERT INTO users (username, password, nama_lengkap) VALUES (?, ?, ?)");
$stmt->execute([$username, $password, $nama]);

echo "User berhasil ditambahkan!";
