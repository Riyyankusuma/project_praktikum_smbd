<?php
// Pastikan variabel $module diset sebelum HTML
if (!isset($module)) {
    $module = isset($_GET['module']) ? $_GET['module'] : 'dashboard';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>CRUD Manajemen Data Siswa Lengkap</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <style>
        * {box-sizing: border-box; margin: 0; padding: 0;}
        body {font-family: 'Poppins', sans-serif; background: #f5f5f5; color: #333;}
        header {background: #005a87; padding: 1rem; color: #fff; text-align: center; font-size: 24px;}
        .container {display: flex; min-height: 100vh; flex-direction: row; justify-content: space-between;}
        aside {width: 250px; background: #0171bb; color: white; padding-top: 1rem;}
        aside nav {display: flex; flex-direction: column; align-items: flex-start;}
        aside nav a {padding: 1rem; color: #f1f1f1; text-decoration: none; font-weight: 600; width: 100%; text-align: left; transition: background-color 0.3s;}
        aside nav a:hover, aside nav a.active {background-color: #005a87;}
        main {flex: 1; background-color: #a0a0a0; padding: 2rem; margin: 1rem 0 1rem 0.5rem; border-radius: 8px; box-shadow: 0 3px 15px rgba(0, 0, 0, 0.1);}
        h2 {font-size: 22px; margin-bottom: 20px;}
        table {width: 100%; border-collapse: collapse; margin-top: 20px; background-color: #d0d0d0;}
        th, td {padding: 12px; border: 1px solid #ddd; text-align: left; background-color: #d0d0d0;}
        th {background-color: #0171bb; color: white;}
        tr:nth-child(even) {background-color: #d4d4d4;}
        form {margin-top: 20px;}
        form label {display: block; margin: 10px 0 5px; font-weight: 600;}
        form input[type="text"], form input[type="number"], form select, form textarea {
            width: 100%; padding: 10px; border-radius: 4px; border: 1px solid #ddd; margin-bottom: 15px; font-size: 16px;
        }
        form button {background: #0171bb; color: white; padding: 0.6rem 1.2rem; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;}
        form button.cancel {background: #aaa;}
        @media (max-width: 768px) {
            .container {flex-direction: column;}
            aside {width: 100%; margin-bottom: 20px;}
            main {margin-left: 0;}
            aside nav a {padding: 15px;}
        }
    </style>
</head>
<body>
    <header>CRUD Manajemen Data Siswa Lengkap</header>
    <div class="container">
        <aside>
            <nav>
               <a href="?module=dashboard" class="<?php echo $module === 'dashboard' ? 'active' : ''; ?>">Dashboard</a>
                <a href="?module=siswa" class="<?php echo $module === 'siswa' ? 'active' : ''; ?>">Siswa</a>
                <a href="?module=nilai" class="<?php echo $module === 'nilai' ? 'active' : ''; ?>">Nilai</a>
                <a href="?module=kelas" class="<?php echo $module === 'kelas' ? 'active' : ''; ?>">Kelas</a>
                <a href="?module=guru" class="<?php echo $module === 'guru' ? 'active' : ''; ?>">Guru</a>
                <a href="?module=mapel" class="<?php echo $module === 'mapel' ? 'active' : ''; ?>">Mapel</a>
                <a href="?module=looping_sp" class="<?php echo $module === 'looping_sp' ? 'active' : ''; ?>">Looping SP Mapel</a>
                <a href="?module=rekap_mapel" class="<?php echo $module === 'rekap_mapel' ? 'active' : ''; ?>">Rekap Nilai Mapel</a>
                <a href="?module=log" class="<?php echo $module === 'log' ? 'active' : ''; ?>">Log Aktivitas</a>
                <a href="?module=hasil_keseluruhan" class="<?php echo $module === 'hasil_keseluruhan' ? 'active' : ''; ?>">Hasil Nilai</a>  <!-- Tambahkan menu ini -->
            </nav>
        </aside>
        <main>
