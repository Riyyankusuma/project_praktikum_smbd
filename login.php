<?php
session_start();
require 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
        header("Location: index.php");
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Manajemen Data Siswa</title>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #0171bb, #005a87);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-box {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            animation: fadeIn 0.6s ease-in-out;
        }
        h2 {
            text-align: center;
            color: #005a87;
            margin-bottom: 20px;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            transition: border 0.3s;
        }
        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #0171bb;
            outline: none;
        }
        .btn {
            width: 100%;
            padding: 12px;
            background-color: #ffc107;
            color: #000;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
            margin-top: 10px;
        }
        .btn:hover {
            background-color: #e0ac00;
        }
        .link {
            display: block;
            margin-top: 12px;
            text-align: center;
            color: #0171bb;
            text-decoration: none;
            font-size: 14px;
        }
        .link:hover {
            text-decoration: underline;
        }
        .error {
            color: red;
            margin-bottom: 10px;
            text-align: center;
            font-size: 14px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 500px) {
            .login-box {
                margin: 20px;
            }
        }
    </style>
</head>
<body>
<div class="login-box">
    <h2>Login</h2>
    <?php if ($error): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required />
        <input type="password" name="password" placeholder="Password" required />
        <input type="submit" class="btn" value="Login" />
        <a class="link" href="signup.php">Belum punya akun? Daftar di sini</a>
    </form>
</div>
</body>
</html>
