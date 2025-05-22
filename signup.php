<?php
session_start();
require 'config.php'; // koneksi ke database

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $nama = trim($_POST['nama_lengkap'] ?? '');

    if ($username && $password && $nama) {
        // Cek apakah username sudah ada
        $cek = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $cek->execute([$username]);

        if ($cek->rowCount() > 0) {
            $error = "Username sudah terdaftar!";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password, nama_lengkap) VALUES (?, ?, ?)");
            $stmt->execute([$username, $hash, $nama]);
            $success = "Pendaftaran berhasil! Silakan login.";
        }
    } else {
        $error = "Semua field wajib diisi!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sign Up</title>
    <style>
        body { font-family: sans-serif; background-color: #f0f0f0; }
        .signup-box {
            margin: 100px auto;
            padding: 30px;
            background: #fff;
            max-width: 400px;
            border-radius: 10px;
            box-shadow: 0 0 10px #aaa;
        }
        input { width: 100%; padding: 10px; margin-top: 10px; }
        .btn { background: #28a745; color: white; border: none; cursor: pointer; }
        .link { margin-top: 15px; display: block; text-align: center; }
    </style>
</head>
<body>
<div class="signup-box">
    <h2>Sign Up</h2>
    <?php if ($error): ?>
        <p style="color:red"><?= $error ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
        <p style="color:green"><?= $success ?></p>
    <?php endif; ?>
    <form method="POST">
        <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" required />
        <input type="text" name="username" placeholder="Username" required />
        <input type="password" name="password" placeholder="Password" required />
        <input type="submit" class="btn" value="Daftar" />
    </form>
    <a class="link" href="login.php">Sudah punya akun? Login di sini</a>
</div>
</body>
</html>
