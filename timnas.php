<?php
// pendaftaran_timnas.php - Sistem pendaftaran timnas serba ada

// Koneksi database
$host = 'localhost';
$db = 'pendaftaran_timnas';
$user = 'root'; // Perbarui sesuai kebutuhan
$pass = '';     // Perbarui sesuai kebutuhan

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

// Inisialisasi pesan
$message = "";

// Fungsi pembantu untuk mencatat aktivitas
function logActivity($pdo, $action, $table) {
    $stmt = $pdo->prepare("INSERT INTO log_aktivitas (aksi, tabel) VALUES (?, ?)");
    $stmt->execute([$action, $table]);
}

// Fungsi pembantu untuk mengambil aktivitas terbaru
function getRecentActivities($pdo) {
    $stmt = $pdo->query("SELECT aksi, tabel, waktu FROM log_aktivitas ORDER BY waktu DESC LIMIT 5");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Menangani pengiriman formulir
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Tambah Pemain
if ($page === 'add_player' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $umur = intval($_POST['umur'] ?? 0);
    $id_pos = intval($_POST['id_pos'] ?? 0);
    if ($nama === '' || $umur <= 0 || $id_pos <= 0) {
        $message = '<div class="error">Silakan isi semua kolom dengan benar.</div>';
    } else {
        try {
            $stmt = $pdo->prepare("CALL tambah_pemain(?, ?, ?)");
            $stmt->execute([$nama, $umur, $id_pos]);
            logActivity($pdo, "INSERT", "pemain");
            $message = '<div class="success">Pemain berhasil ditambahkan!</div>';
        } catch (Exception $e) {
            $message = '<div class="error">Kesalahan saat menambahkan pemain: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
}

// Perbarui Pemain
if ($page === 'update_player' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pemain = intval($_POST['id_pemain'] ?? 0);
    $nama = trim($_POST['nama'] ?? '');
    $umur = intval($_POST['umur'] ?? 0);
    $id_pos = intval($_POST['id_pos'] ?? 0);
    if ($id_pemain <= 0 || $nama === '' || $umur <= 0 || $id_pos <= 0) {
        $message = '<div class="error">Silakan isi semua kolom dengan benar.</div>';
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE pemain SET nama_pemain = ?, umur = ?, id_posisi = ? WHERE id_pemain = ?");
            $stmt->execute([$nama, $umur, $id_pos, $id_pemain]);
            logActivity($pdo, "UPDATE", "pemain");
            $message = '<div class="success">Pemain berhasil diperbarui!</div>';
        } catch (Exception $e) {
            $message = '<div class="error">Kesalahan saat memperbarui pemain: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
}

// Hapus Pemain
if ($page === 'delete_player' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pemain = intval($_POST['id_pemain'] ?? 0);
    if ($id_pemain <= 0) {
        $message = '<div class="error">Tidak ada pemain yang dipilih untuk dihapus.</div>';
    } else {
        try {
            // Cek apakah pemain masih terdaftar di pendaftaran
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM pendaftaran WHERE id_pemain = ?");
            $stmt->execute([$id_pemain]);
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                $message = '<div class="error">Pemain masih terdaftar di pendaftaran dan tidak dapat dihapus.</div>';
            } else {
                $stmt = $pdo->prepare("SELECT nama_pemain FROM pemain WHERE id_pemain = ?");
                $stmt->execute([$id_pemain]);
                $player = $stmt->fetch(PDO::FETCH_ASSOC);
                $playerName = $player ? $player['nama_pemain'] : 'Tidak Diketahui';

                $stmt = $pdo->prepare("DELETE FROM pemain WHERE id_pemain = ?");
                $stmt->execute([$id_pemain]);
                logActivity($pdo, "DELETE", "pemain");
                $message = '<div class="success">Pemain berhasil dihapus!</div>';
            }
        } catch (Exception $e) {
            $message = '<div class="error">Kesalahan saat menghapus pemain: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
}

// Tambah Tim
if ($page === 'add_team' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $kategori = trim($_POST['kategori'] ?? '');
    if ($nama === '' || $kategori === '') {
        $message = '<div class="error">Silakan isi semua kolom.</div>';
    } else {
        try {
            $stmt = $pdo->prepare("CALL tambah_tim(?, ?)");
            $stmt->execute([$nama, $kategori]);
            logActivity($pdo, "INSERT", "tim");
            $message = '<div class="success">Tim berhasil ditambahkan!</div>';
        } catch (Exception $e) {
            $message = '<div class="error">Kesalahan saat menambahkan tim: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
}

// Hapus Tim
if ($page === 'delete_team' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_tim = intval($_POST['id_tim'] ?? 0);
    if ($id_tim <= 0) {
        $message = '<div class="error">Tidak ada tim yang dipilih untuk dihapus.</div>';
    } else {
        try {
            // Cek apakah tim masih terdaftar di pendaftaran
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM pendaftaran WHERE id_tim = ?");
            $stmt->execute([$id_tim]);
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                $message = '<div class="error">Tim masih terdaftar di pendaftaran dan tidak dapat dihapus.</div>';
            } else {
                $stmt = $pdo->prepare("DELETE FROM tim WHERE id_tim = ?");
                $stmt->execute([$id_tim]);
                logActivity($pdo, "DELETE", "tim");
                $message = '<div class="success">Tim berhasil dihapus!</div>';
            }
        } catch (Exception $e) {
            $message = '<div class="error">Kesalahan saat menghapus tim: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
}

// Tambah Posisi
if ($page === 'add_position' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_posisi = trim($_POST['nama_posisi'] ?? '');
    if ($nama_posisi === '') {
        $message = '<div class="error">Silakan masukkan nama posisi.</div>';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO posisi (nama_posisi) VALUES (?)");
            $stmt->execute([$nama_posisi]);
            logActivity($pdo, "INSERT", "posisi");
            $message = '<div class="success">Posisi berhasil ditambahkan!</div>';
        } catch (Exception $e) {
            $message = '<div class="error">Kesalahan saat menambahkan posisi: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
}

// Hapus Posisi
if ($page === 'delete_position' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_posisi = intval($_POST['id_posisi'] ?? 0);
    if ($id_posisi <= 0) {
        $message = '<div class="error">Tidak ada posisi yang dipilih untuk dihapus.</div>';
    } else {
        try {
            $stmt = $pdo->prepare("DELETE FROM posisi WHERE id_posisi = ?");
            $stmt->execute([$id_posisi]);
            logActivity($pdo, "DELETE", "posisi");
            $message = '<div class="success">Posisi berhasil dihapus!</div>';
        } catch (Exception $e) {
            $message = '<div class="error">Kesalahan saat menghapus posisi: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
}

// Daftarkan Pemain
if ($page === 'register_player' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_p = intval($_POST['id_p'] ?? 0);
    $id_t = intval($_POST['id_t'] ?? 0);
    if ($id_p <= 0 || $id_t <= 0) {
        $message = '<div class="error">Silakan pilih pemain dan tim.</div>';
    } else {
        try {
            $stmt = $pdo->prepare("CALL daftarkan_pemain(?, ?)");
            $stmt->execute([$id_p, $id_t]);
            logActivity($pdo, "INSERT", "pendaftaran");
            $message = '<div class="success">Pemain berhasil didaftarkan!</div>';
        } catch (Exception $e) {
            $message = '<div class="error">Kesalahan saat mendaftar pemain: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
}

// Fungsi pembantu untuk mengambil semua posisi
function getPositions($pdo) {
    $stmt = $pdo->query("SELECT id_posisi, nama_posisi FROM posisi ORDER BY nama_posisi");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fungsi pembantu untuk mengambil semua pemain
function getPlayers($pdo) {
    $stmt = $pdo->query("SELECT p.id_pemain, p.nama_pemain, p.umur, pos.nama_posisi 
                         FROM pemain p JOIN posisi pos ON p.id_posisi = pos.id_posisi ORDER BY p.nama_pemain");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fungsi pembantu untuk mengambil semua tim
function getTeams($pdo) {
    $stmt = $pdo->query("SELECT id_tim, nama_tim FROM tim ORDER BY nama_tim");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fungsi pembantu untuk mengambil semua posisi lengkap
function getAllPositions($pdo) {
    $stmt = $pdo->query("SELECT * FROM posisi ORDER BY id_posisi");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Escape output untuk HTML
function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Pendaftaran Timnas - Sistem Pendaftaran Tim Nasional</title>
<style>
    /* Gaya yang sama seperti sebelumnya */
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f4f6f8;
        margin: 0; padding: 0;
        color: #333;
    }
    header {
        background: #345995;
        color: white;
        padding: 20px 30px;
        text-align: center;
        font-size: 1.8rem;
        font-weight: bold;
        letter-spacing: 1px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    nav {
        background: #244673;
        padding: 10px 0;
        display: flex;
        justify-content: center;
        gap: 12px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.15);
        flex-wrap: wrap;
    }
    nav a {
        color: #cbdcea;
        text-decoration: none;
        font-weight: 600;
        padding: 8px 16px;
        border-radius: 4px;
        transition: background-color 0.25s ease;
    }
    nav a:hover, nav a.active {
        background: #eebbc3;
        color: #2a1a1a;
        box-shadow: 0 3px 8px rgba(238, 187, 195, 0.7);
    }
    main {
        max-width: 900px;
        margin: 30px auto;
        background: white;
        padding: 25px 30px;
        border-radius: 10px;
        box-shadow: 0 6px 18px rgba(0,0,0,0.1);
    }
    h2 {
        margin-top: 0;
        color: #345995;
        margin-bottom: 20px;
    }
    form label {
        display: block;
        margin: 15px 0 6px 0;
        font-weight: 600;
    }
    input[type="text"], input[type="number"], select {
        width: 100%;
        padding: 8px 12px;
        font-size: 1rem;
        border: 1px solid #ccc;
        border-radius: 6px;
        box-sizing: border-box;
        transition: border-color 0.25s ease;
    }
    input[type="text"]:focus, input[type="number"]:focus, select:focus {
        border-color: #345995;
        outline: none;
    }
    input[type="submit"] {
        margin-top: 16px;
        background-color: #345995;
        color: white;
        border: none;
        padding: 10px 18px;
        font-size: 1rem;
        font-weight: bold;
        cursor: pointer;
        border-radius: 6px;
        transition: background-color 0.3s ease;
    }
    input[type="submit"]:hover {
        background-color: #244673;
    }
    table {
        border-collapse: collapse;
        width: 100%;
        margin-top: 10px;
        font-size: 0.95rem;
    }
    th, td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: left;
        vertical-align: middle;
    }
    th {
        background-color: #345995;
        color: white;
    }
    tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    .message {
        margin-bottom: 20px;
        padding: 12px 20px;
        border-radius: 7px;
        font-weight: 600;
    }
    .success {
        background-color: #d1e7dd;
        color: #0f5132;
        border: 1px solid #badbcc;
    }
    .error {
        background-color: #f8d7da;
        color: #842029;
        border: 1px solid #f5c2c7;
    }
    footer {
        text-align: center;
        margin: 40px 0 20px 0;
        font-size: 0.9rem;
        color: #777;
    }
    .button {
        background-color: #345995;
        color: white;
        border: none;
        padding: 6px 12px;
        font-size: 0.9rem;
        font-weight: bold;
        cursor: pointer;
        border-radius: 4px;
        transition: background-color 0.3s ease;
        margin-right: 5px;
    }
    .button:hover {
        background-color: #244673;
    }
    form.inline-form {
        display: inline;
        margin: 0;
    }
</style>
</head>
<body>

<header>Pendaftaran Timnas - Sistem Pendaftaran Tim Nasional</header>

<nav>
    <a href="?page=home" class="<?= $page === 'home' ? 'active' : ''; ?>">Beranda</a>
    <a href="?page=add_player" class="<?= $page === 'add_player' ? 'active' : ''; ?>">Tambah Pemain</a>
    <a href="?page=add_team" class="<?= $page === 'add_team' ? 'active' : ''; ?>">Tambah Tim</a>
    <a href="?page=add_position" class="<?= $page === 'add_position' ? 'active' : ''; ?>">Tambah Posisi</a>
    <a href="?page=register_player" class="<?= $page === 'register_player' ? 'active' : ''; ?>">Daftarkan Pemain</a>
    <a href="?page=view_players" class="<?= $page === 'view_players' ? 'active' : ''; ?>">Lihat Pemain</a>
    <a href="?page=view_teams" class="<?= $page === 'view_teams' ? 'active' : ''; ?>">Lihat Tim</a>
    <a href="?page=view_registration" class="<?= $page === 'view_registration' ? 'active' : ''; ?>">Lihat Pendaftaran</a>
    <a href="?page=loop_positions" class="<?= $page === 'loop_positions' ? 'active' : ''; ?>">Hitung Pemain per Posisi</a>
</nav>

<main>
    <?php if ($message) echo '<div class="message">' . $message . '</div>';

    if ($page === 'home'): ?>
        <h2>Selamat datang di Sistem Pendaftaran Tim Nasional</h2>
        <p>Gunakan navigasi di atas untuk menambah pemain, tim, posisi, mendaftar pemain ke tim, dan melihat informasi.</p>
        <h2>Aktivitas Terbaru</h2>
        <?php
        $activities = getRecentActivities($pdo);
        if (empty($activities)): ?>
            <p>Tidak ada aktivitas terbaru ditemukan.</p>
        <?php else: ?>
            <table>
                <thead><tr><th>Aksi</th><th>Tabel</th><th>Tanggal & Waktu</th></tr></thead>
                <tbody>
                <?php foreach ($activities  as $act): ?>
                    <tr>
                        <td><?= h($act['aksi']); ?></td>
                        <td><?= h($act['tabel']); ?></td>
                        <td><?= h($act['waktu']); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif;

    elseif ($page === 'add_player'): ?>
        <h2>Tambah Pemain</h2>
        <form method="POST" action="?page=add_player">
            <label for="nama">Nama Pemain</label>
            <input type="text" name="nama" required>

            <label for="umur">Usia</label>
            <input type="number" name="umur" min="1" required>

            <label for="id_pos">Pilih Posisi</label>
            <select name="id_pos" required>
                <option value="">-- Pilih Posisi --</option>
                <?php foreach (getPositions($pdo) as $pos): ?>
                    <option value="<?= h($pos['id_posisi']); ?>"><?= h($pos['nama_posisi']); ?></option>
                <?php endforeach; ?>
            </select>

            <input type="submit" value="Tambah Pemain">
        </form>

        <!-- Table of existing players with edit/delete -->
        <h3>Daftar Pemain</h3>
        <?php $players = getPlayers($pdo); ?>
        <?php if(empty($players)): ?>
            <p>Tidak ada pemain ditemukan.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr><th>ID</th><th>Nama</th><th>Usia</th><th>Posisi</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                <?php foreach ($players as $player): ?>
                    <tr>
                        <td><?= h($player['id_pemain']); ?></td>
                        <td><?= h($player['nama_pemain']); ?></td>
                        <td><?= h($player['umur']); ?></td>
                        <td><?= h($player['nama_posisi']); ?></td>
                        <td>
                            <form method="GET" action="?page=update_player" class="inline-form">
                                <input type="hidden" name="id_pemain" value="<?= h($player['id_pemain']); ?>">
                                <input type="submit" value="Edit" class="button">
                            </form>
                            <form method="POST" action="?page=delete_player" class="inline-form" onsubmit="return confirm('Yakin hapus pemain?');">
                                <input type="hidden" name="id_pemain" value="<?= h($player['id_pemain']); ?>">
                                <input type="submit" value="Hapus" class="button">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

    <?php elseif ($page === 'add_team'): ?>
        <h2>Tambah Tim</h2>
        <form method="POST" action="?page=add_team">
            <label for="nama">Nama Tim</label>
            <input type="text" name="nama" required>

            <label for="kategori">Kategori Usia</label>
            <input type="text" name="kategori" required placeholder="misalnya, U18, U21">

            <input type="submit" value="Tambah Tim">
        </form>

        <!-- Table of existing teams with delete -->
        <h3>Daftar Tim</h3>
        <?php $teams = getTeams($pdo); ?>
        <?php if(empty($teams)): ?>
            <p>Tidak ada tim ditemukan.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr><th>ID</th><th>Nama Tim</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                <?php foreach ($teams as $team): ?>
                    <tr>
                        <td><?= h($team['id_tim']); ?></td>
                        <td><?= h($team['nama_tim']); ?></td>
                        <td>
                            <form method="POST" action="?page=delete_team" class="inline-form" onsubmit="return confirm('Yakin hapus tim?');">
                                <input type="hidden" name="id_tim" value="<?= h($team['id_tim']); ?>">
                                <input type="submit" value="Hapus" class="button">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

    <?php elseif ($page === 'add_position'): ?>
        <h2>Tambah Posisi</h2>
        <form method="POST" action="?page=add_position">
            <label for="nama_posisi">Nama Posisi</label>
            <input type="text" name="nama_posisi" required>

            <input type="submit" value="Tambah Posisi">
        </form>

        <!-- Table of existing positions with delete -->
        <h3>Daftar Posisi</h3>
        <?php $positions = getAllPositions($pdo); ?>
        <?php if(empty($positions)): ?>
            <p>Tidak ada posisi ditemukan.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr><th>ID</th><th>Nama Posisi</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                <?php foreach ($positions as $pos): ?>
                    <tr>
                        <td><?= h($pos['id_posisi']); ?></td>
                        <td><?= h($pos['nama_posisi']); ?></td>
                        <td>
                            <form method="POST" action="?page=delete_position" class="inline-form" onsubmit="return confirm('Yakin hapus posisi?');">
                                <input type="hidden" name="id_posisi" value="<?= h($pos['id_posisi']); ?>">
                                <input type="submit" value="Hapus" class="button">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

    <?php elseif ($page === 'register_player'): ?>
        <h2>Daftarkan Pemain ke Tim</h2>
        <form method="POST" action="?page=register_player">
            <label for="id_p">Pilih Pemain</label>
            <select name="id_p" required>
                <option value="">-- Pilih Pemain --</option>
                <?php foreach (getPlayers($pdo) as $player): ?>
                    <option value="<?= h($player['id_pemain']); ?>"><?= h($player['nama_pemain']); ?></option>
                <?php endforeach; ?>
            </select>

            <label for="id_t">Pilih Tim</label>
            <select name="id_t" required>
                <option value="">-- Pilih Tim --</option>
                <?php foreach (getTeams($pdo) as $team): ?>
                    <option value="<?= h($team['id_tim']); ?>"><?= h($team['nama_tim']); ?></option>
                <?php endforeach; ?>
            </select>

            <input type="submit" value="Daftarkan Pemain">
        </form>

        <!-- Table of current registrations -->
        <h3>Daftar Pendaftaran</h3>
        <?php
        $stmt = $pdo->query("SELECT pd.id_pendaftaran, p.nama_pemain, t.nama_tim, pd.tanggal_daftar 
                             FROM pendaftaran pd 
                             JOIN pemain p ON pd.id_pemain = p.id_pemain 
                             JOIN tim t ON pd.id_tim = t.id_tim
                             ORDER BY pd.tanggal_daftar DESC");
        $registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <?php if(empty($registrations)): ?>
            <p>Tidak ada pendaftaran ditemukan.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr><th>Nama Pemain</th><th>Nama Tim</th><th>Tanggal Pendaftaran</th></tr>
                </thead>
                <tbody>
                <?php foreach ($registrations as $reg): ?>
                    <tr>
                        <td><?= h($reg['nama_pemain']); ?></td>
                        <td><?= h($reg['nama_tim']); ?></td>
                        <td><?= h($reg['tanggal_daftar']); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

    <?php elseif ($page === 'view_players'):
        $players = getPlayers($pdo);
    ?>
        <h2>Daftar Pemain (dengan Posisi)</h2>
        <?php if (empty($players)): ?>
            <p>Tidak ada pemain ditemukan.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr><th>ID</th><th>Nama Pemain</th><th>Usia</th><th>Posisi</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($players as $player): ?>
                        <tr>
                            <td><?= h($player['id_pemain']); ?></td>
                            <td><?= h($player['nama_pemain']); ?></td>
                            <td><?= h($player['umur']); ?></td>
                            <td><?= h($player['nama_posisi']); ?></td>
                            <td>
                                <form method="GET" action="?page=update_player" class="inline-form">
                                    <input type="hidden" name="id_pemain" value="<?= h($player['id_pemain']); ?>">
                                    <input type="submit" value="Edit" class="button">
                                </form>
                                <form method="POST" action="?page=delete_player" class="inline-form" onsubmit="return confirm('Yakin hapus pemain?');">
                                    <input type="hidden" name="id_pemain" value="<?= h($player['id_pemain']); ?>">
                                    <input type="submit" value="Hapus" class="button">
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

    <?php elseif ($page === 'view_teams'):
        $teams = getTeams($pdo);
    ?>
        <h2>Daftar Tim</h2>
        <?php if (empty($teams)): ?>
            <p>Tidak ada tim ditemukan.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr><th>ID</th><th>Nama Tim</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($teams as $team): ?>
                        <tr>
                            <td><?= h($team['id_tim']); ?></td>
                            <td><?= h($team['nama_tim']); ?></td>
                            <td>
                                <form method="POST" action="?page=delete_team" class="inline-form" onsubmit="return confirm('Yakin hapus tim?');">
                                    <input type="hidden" name="id_tim" value="<?= h($team['id_tim']); ?>">
                                    <input type="submit" value="Hapus" class="button">
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

    <?php elseif ($page === 'view_registration'):
        $stmt = $pdo->query("SELECT p.nama_pemain, t.nama_tim, pd.tanggal_daftar 
                             FROM pendaftaran pd 
                             JOIN pemain p ON pd.id_pemain = p.id_pemain 
                             JOIN tim t ON pd.id_tim = t.id_tim
                             ORDER BY pd.tanggal_daftar DESC");
        $registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
        <h2>Pendaftaran Pemain</h2>
        <?php if (empty($registrations)): ?>
            <p>Tidak ada pendaftaran ditemukan.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr><th>Nama Pemain</th><th>Nama Tim</th><th>Tanggal Pendaftaran</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($registrations as $registration): ?>
                        <tr>
                            <td><?= h($registration['nama_pemain']); ?></td>
                            <td><?= h($registration['nama_tim']); ?></td>
                            <td><?= h($registration['tanggal_daftar']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

    <?php elseif ($page === 'loop_positions'):
        $positions = getPositions($pdo);
        $playerCounts = [];
        foreach ($positions as $pos) {
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM pemain WHERE id_posisi = ?");
            $stmt->execute([$pos['id_posisi']]);
            $count = $stmt->fetchColumn();
            $playerCounts[$pos['nama_posisi']] = $count;
        }
    ?>
        <h2>Hitung Pemain per Posisi</h2>
        <table>
            <thead><tr><th>Posisi</th><th>Jumlah Pemain</th></tr></thead>
            <tbody>
                <?php foreach ($playerCounts as $position => $count): ?>
                    <tr>
                        <td><?= h($position); ?></td>
                        <td><?= h($count); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php endif; ?>
</main>

<footer>
    &copy; <?= date("Y"); ?> Pendaftaran Timnas. Semua hak dilindungi.
</footer>

</body>
</html>

