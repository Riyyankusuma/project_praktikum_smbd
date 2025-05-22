<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'config.php'; // Menghubungkan file konfigurasi database
require 'functions.php'; // Menghubungkan file fungsi
require 'header.php'; // Menghubungkan file header

// Modul aktif
$module = $_GET['module'] ?? 'kelas';
$action = $_GET['action'] ?? 'list';
$error = ''; // Deklarasi variabel error

$module = $_GET['module'] ?? '';
if (!empty($module)) {
    $file = "modules/$module.php";
    if (file_exists($file)) {
        include_once $file;
    } else {
        echo "Modul tidak ditemukan.";
    }
}

// Proses CRUD dan Stored Procedure tiap modul
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($module) {
        case 'kelas':
            if ($action === 'add') {
                $nama = $_POST['nama_kelas'] ?? '';
                $jurusan = $_POST['jurusan'] ?? '';
                if ($nama && $jurusan) {
                    $stmt = $pdo->prepare("INSERT INTO kelas (nama_kelas, jurusan) VALUES (?,?)");
                    $stmt->execute([$nama, $jurusan]);
                    redirect('kelas');
                } else {
                    $error = "Semua field wajib diisi.";
                }
            } elseif ($action === 'edit') {
                $id = intval($_POST['id_kelas']);
                $nama = $_POST['nama_kelas'] ?? '';
                $jurusan = $_POST['jurusan'] ?? '';
                if ($nama && $jurusan) {
                    $stmt = $pdo->prepare("UPDATE kelas SET nama_kelas=?, jurusan=? WHERE id_kelas=?");
                    $stmt->execute([$nama, $jurusan, $id]);
                    redirect('kelas');
                } else {
                    $error = "Semua field wajib diisi.";
                }
            } elseif ($action === 'delete') {
                $id = intval($_POST['id_kelas']);
                $stmt = $pdo->prepare("DELETE FROM kelas WHERE id_kelas = ?");
                $stmt->execute([$id]);
                redirect('kelas');
            }
            break;

        case 'siswa':
            if ($action === 'add') {
                $nisn = $_POST['nisn'] ?? '';
                $nama = $_POST['nama'] ?? '';
                $alamat = $_POST['alamat'] ?? '';
                $id_kelas = intval($_POST['id_kelas'] ?? 0);
                if ($nisn && $nama && $alamat && $id_kelas) {
                    $stmt = $pdo->prepare("CALL tambah_siswa(?, ?, ?, ?)");
                    $stmt->execute([$nisn, $nama, $alamat, $id_kelas]);
                    redirect('siswa');
                } else {
                    $error = "Semua field wajib diisi.";
                }
            } elseif ($action === 'edit') {
                $id = intval($_POST['id_siswa']);
                $nisn = $_POST['nisn'] ?? '';
                $nama = $_POST['nama'] ?? '';
                $alamat = $_POST['alamat'] ?? '';
                $id_kelas = intval($_POST['id_kelas'] ?? 0);
                if ($nisn && $nama && $alamat && $id_kelas) {
                    $stmt = $pdo->prepare("UPDATE siswa SET nisn=?, nama=?, alamat=?, id_kelas=? WHERE id_siswa=?");
                    $stmt->execute([$nisn, $nama, $alamat, $id_kelas, $id]);
                    redirect('siswa');
                } else {
                    $error = "Semua field wajib diisi.";
                }
            } elseif ($action === 'delete') {
                $id = intval($_POST['id_siswa']);
                $stmt = $pdo->prepare("DELETE FROM siswa WHERE id_siswa = ?");
                $stmt->execute([$id]);
                redirect('siswa');
            }
            break;

        case 'guru':
            if ($action === 'add') {
                $nama = $_POST['nama'] ?? '';
                $email = $_POST['email'] ?? '';
                if ($nama && $email) {
                    $stmt = $pdo->prepare("INSERT INTO guru (nama,email) VALUES (?,?)");
                    $stmt->execute([$nama, $email]);
                    redirect('guru');
                } else {
                    $error = "Semua field wajib diisi.";
                }
            } elseif ($action === 'edit') {
                $id = intval($_POST['id_guru']);
                $nama = $_POST['nama'] ?? '';
                $email = $_POST['email'] ?? '';
                if ($nama && $email) {
                    $stmt = $pdo->prepare("UPDATE guru SET nama=?, email=? WHERE id_guru=?");
                    $stmt->execute([$nama,$email,$id]);
                    redirect('guru');
                } else {
                    $error = "Semua field wajib diisi.";
                }
            } elseif ($action === 'delete') {
                $id = intval($_POST['id_guru']);
                $stmt = $pdo->prepare("DELETE FROM guru WHERE id_guru = ?");
                $stmt->execute([$id]);
                redirect('guru');
            }
            break;

        case 'mapel':
            if ($action === 'add') {
                $nama_mapel = $_POST['nama_mapel'] ?? '';
                $id_guru = intval($_POST['id_guru'] ?? 0);
                if ($nama_mapel && $id_guru) {
                    $stmt = $pdo->prepare("INSERT INTO mapel (nama_mapel, id_guru) VALUES (?,?)");
                    $stmt->execute([$nama_mapel,$id_guru]);
                    redirect('mapel');
                } else {
                    $error = "Semua field wajib diisi.";
                }
            } elseif ($action === 'edit') {
                $id = intval($_POST['id_mapel']);
                $nama_mapel = $_POST['nama_mapel'] ?? '';
                $id_guru = intval($_POST['id_guru'] ?? 0);
                if ($nama_mapel && $id_guru) {
                    $stmt = $pdo->prepare("UPDATE mapel SET nama_mapel=?, id_guru=? WHERE id_mapel=?");
                    $stmt->execute([$nama_mapel, $id_guru, $id]);
                    redirect('mapel');
                } else {
                    $error = "Semua field wajib diisi.";
                }
            } elseif ($action === 'delete') {
                $id = intval($_POST['id_mapel']);
                $stmt = $pdo->prepare("DELETE FROM mapel WHERE id_mapel = ?");
                $stmt->execute([$id]);
                redirect('mapel');
            }
            break;

        case 'nilai':
            if ($action === 'add') {
                $id_siswa = intval($_POST['id_siswa'] ?? 0);
                $id_mapel = intval($_POST['id_mapel'] ?? 0);
                $nilai_angka = floatval($_POST['nilai_angka'] ?? 0);
                if ($id_siswa && $id_mapel && $nilai_angka >= 0) {
                    $stmt = $pdo->prepare("INSERT INTO nilai (id_siswa, id_mapel, nilai_angka) VALUES (?,?,?)");
                    $stmt->execute([$id_siswa, $id_mapel, $nilai_angka]);
                    redirect('nilai');
                } else {
                    $error = "Semua field wajib diisi dengan benar.";
                }
            } elseif ($action === 'edit') {
                $id = intval($_POST['id_nilai']);
                $id_siswa = intval($_POST['id_siswa'] ?? 0);
                $id_mapel = intval($_POST['id_mapel'] ?? 0);
                $nilai_angka = floatval($_POST['nilai_angka'] ?? 0);
                if ($id_siswa && $id_mapel && $nilai_angka >= 0) {
                    $stmt = $pdo->prepare("UPDATE nilai SET id_siswa=?, id_mapel=?, nilai_angka=? WHERE id_nilai=?");
                    $stmt->execute([$id_siswa, $id_mapel, $nilai_angka, $id]);
                    redirect('nilai');
                } else {
                    $error = "Semua field wajib diisi dengan benar.";
                }
            } elseif ($action === 'delete') {
                $id = intval($_POST['id_nilai']);
                $stmt = $pdo->prepare("DELETE FROM nilai WHERE id_nilai = ?");
                $stmt->execute([$id]);
                redirect('nilai');
            }
            break;

        case 'storedproc':
            // Contoh memanggil looping stored procedure tampilkan_mapel_siswa dan menampilkan hasilnya
            if ($action === 'run_loop') {
                $id_siswa = intval($_POST['id_siswa'] ?? 0);
                if ($id_siswa) {
                    $stmt = $pdo->prepare("SELECT m.nama_mapel FROM nilai n JOIN mapel m ON n.id_mapel = m.id_mapel WHERE n.id_siswa = ?");
                    $stmt->execute([$id_siswa]);
                    $mapel_list = $stmt->fetchAll(PDO::FETCH_COLUMN);
                } else {
                    $error = "Pilih siswa terlebih dahulu.";
                }
            }
            break;

        case 'log':
            // Tidak ada POST di log, hanya tampilan
            break;
    }
}

// Data kelas, guru untuk dropdown
$all_kelas = getAll($pdo, 'kelas', 'ORDER BY nama_kelas');
$all_guru = getAll($pdo, 'guru', 'ORDER BY nama');

// Data siswa untuk stored procedure looping
$all_siswa = getAll($pdo, 'siswa', 'ORDER BY nama');

// Tampilkan halaman / module
switch ($module) { 
    
    case 'kelas':
        if ($action === 'add' || $action === 'edit') {
            $editdata = ($action === 'edit' && isset($_GET['id'])) ? getOne($pdo, 'kelas', 'id_kelas', intval($_GET['id'])) : null;
            form_kelas($pdo, $editdata);
        } else {
            $data = getAll($pdo, 'kelas', 'ORDER BY id_kelas');
            echo '<h2>Data Kelas <a href="?module=kelas&action=add" style="font-weight:normal;font-size:0.8em;margin-left:1em;">[Tambah Kelas]</a></h2>';
            if (!$data) {
                echo '<p>Belum ada data kelas.</p>';
            } else {
                echo '<table><thead><tr><th>ID</th><th>Nama Kelas</th><th>Jurusan</th><th>Aksi</th></tr></thead><tbody>';
                foreach ($data as $row) {
                    echo '<tr>
                            <td>'.e($row['id_kelas']).'</td>
                            <td>'.e($row['nama_kelas']).'</td>
                            <td>'.e($row['jurusan']).'</td>
                            <td class="action-buttons">
                                <a href="?module=kelas&action=edit&id='.e($row['id_kelas']).'"><button>Edit</button></a>
                                <form method="post" action="?module=kelas&action=delete" onsubmit="return confirm(\'Yakin ingin hapus data ini?\')" style="display:inline;">
                                    <input type="hidden" name="id_kelas" value="'.e($row['id_kelas']).'">
                                    <button type="submit">Hapus</button>
                                </form>
                            </td>
                          </tr>';
                }
                echo '</tbody></table>';
            }
        }
        break;
    case 'siswa':
        if ($action === 'add' || $action === 'edit') {
            $editdata = ($action === 'edit' && isset($_GET['id'])) ? getOne($pdo, 'siswa', 'id_siswa', intval($_GET['id'])) : null;
            form_siswa($pdo, $editdata);
        } else {
            $data = $pdo->query("SELECT s.*, k.nama_kelas FROM siswa s LEFT JOIN kelas k ON s.id_kelas = k.id_kelas ORDER BY s.id_siswa")->fetchAll(PDO::FETCH_ASSOC);
            echo '<h2>Data Siswa <a href="?module=siswa&action=add" style="font-weight:normal;font-size:0.8em;margin-left:1em;">[Tambah Siswa]</a></h2>';
            if (!$data) {
                echo '<p>Belum ada data siswa.</p>';
            } else {
                echo '<table><thead><tr><th>ID</th><th>NISN</th><th>Nama</th><th>Alamat</th><th>Kelas</th><th>Aksi</th></tr></thead><tbody>';
                foreach ($data as $row) {
                    echo '<tr>
                        <td>'.e($row['id_siswa']).'</td>
                        <td>'.e($row['nisn']).'</td>
                        <td>'.e($row['nama']).'</td>
                        <td>'.e($row['alamat']).'</td>
                        <td>'.e($row['nama_kelas'] ?? '-').'</td>
                        <td class="action-buttons">
                            <a href="?module=siswa&action=edit&id='.e($row['id_siswa']).'"><button>Edit</button></a>
                            <form method="post" action="?module=siswa&action=delete" onsubmit="return confirm(\'Yakin ingin hapus data ini?\')" style="display:inline;">
                                <input type="hidden" name="id_siswa" value="'.e($row['id_siswa']).'">
                                <button type="submit">Hapus</button>
                            </form>
                        </td>
                    </tr>';
                }
                echo '</tbody></table>';
            }
        }
        break;
    case 'guru':
        if ($action === 'add' || $action === 'edit') {
            $editdata = ($action === 'edit' && isset($_GET['id'])) ? getOne($pdo, 'guru', 'id_guru', intval($_GET['id'])) : null;
            form_guru($pdo, $editdata);
        } else {
            $data = getAll($pdo, 'guru', 'ORDER BY id_guru');
            echo '<h2>Data Guru <a href="?module=guru&action=add" style="font-weight:normal;font-size:0.8em;margin-left:1em;">[Tambah Guru]</a></h2>';
            if (!$data) {
                echo '<p>Belum ada data guru.</p>';
            } else {
                echo '<table><thead><tr><th>ID</th><th>Nama</th><th>Email</th><th>Aksi</th></tr></thead><tbody>';
                foreach ($data as $row) {
                    echo '<tr>
                                <td>'.e($row['id_guru']).'</td>
                                <td>'.e($row['nama']).'</td>
                                <td>'.e($row['email']).'</td>
                                <td class="action-buttons">
                                    <a href="?module=guru&action=edit&id='.e($row['id_guru']).'"><button>Edit</button></a>
                                    <form method="post" action="?module=guru&action=delete" onsubmit="return confirm(\'Yakin ingin hapus data ini?\')" style="display:inline;">
                                        <input type="hidden" name="id_guru" value="'.e($row['id_guru']).'">
                                        <button type="submit">Hapus</button>
                                    </form>
                                </td>
                              </tr>';
                }
                echo '</tbody></table>';
            }
        }
        break; 
    case 'mapel':
        if ($action === 'add' || $action === 'edit') {
            $editdata = ($action === 'edit' && isset($_GET['id'])) ? getOne($pdo, 'mapel', 'id_mapel', intval($_GET['id'])) : null;
            form_mapel($pdo, $editdata);
        } else {
            $data = $pdo->query("SELECT m.*, g.nama AS guru_nama FROM mapel m LEFT JOIN guru g ON m.id_guru = g.id_guru ORDER BY m.id_mapel")->fetchAll(PDO::FETCH_ASSOC);
            echo '<h2>Data Mata Pelajaran <a href="?module=mapel&action=add" style="font-weight:normal;font-size:0.8em;margin-left:1em;">[Tambah Mapel]</a></h2>';
            if (!$data) {
                echo '<p>Belum ada data mata pelajaran.</p>';
            } else {
                echo '<table><thead><tr><th>ID</th><th>Nama Mata Pelajaran</th><th>Guru Pengampu</th><th>Aksi</th></tr></thead><tbody>';
                foreach ($data as $row) {
                    echo '<tr>
                        <td>'.e($row['id_mapel']).'</td>
                        <td>'.e($row['nama_mapel']).'</td>
                        <td>'.e($row['guru_nama'] ?? '-').'</td>
                        <td class="action-buttons">
                            <a href="?module=mapel&action=edit&id='.e($row['id_mapel']).'"><button>Edit</button></a>
                            <form method="post" action="?module=mapel&action=delete" onsubmit="return confirm(\'Yakin ingin hapus data ini?\')" style="display:inline;">
                              
                                <input type="hidden" name="id_mapel" value="'.e($row['id_mapel']).'">
                                <button type="submit">Hapus</button>
                            </form>
                        </td>
                    </tr>';
                }
                echo '</tbody></table>';
            }
        }
        break;

    case 'nilai':
        if ($action === 'add' || $action === 'edit') {
            $editdata = ($action === 'edit' && isset($_GET['id'])) ? getOne($pdo, 'nilai', 'id_nilai', intval($_GET['id'])) : null;
            form_nilai($pdo, $editdata);
        } else {
            $data = $pdo->query("SELECT n.*, s.nama AS nama_siswa, m.nama_mapel FROM nilai n JOIN siswa s ON n.id_siswa=s.id_siswa JOIN mapel m ON n.id_mapel=m.id_mapel ORDER BY n.id_nilai")->fetchAll(PDO::FETCH_ASSOC);
            echo '<h2>Data Nilai <a href="?module=nilai&action=add" style="font-weight:normal;font-size:0.8em;margin-left:1em;">[Tambah Nilai]</a></h2>';
            if (!$data) {
                echo '<p>Belum ada data nilai.</p>';
            } else {
                echo '<table><thead><tr><th>ID</th><th>Nama Siswa</th><th>Mata Pelajaran</th><th>Nilai Angka</th><th>Aksi</th></tr></thead><tbody>';
                foreach ($data as $row) {
                    echo '<tr>
                            <td>'.e($row['id_nilai']).'</td>
                            <td>'.e($row['nama_siswa']).'</td>
                            <td>'.e($row['nama_mapel']).'</td> 
                            <td>'.e($row['nilai_angka']).'</td>
                            <td class="action-buttons">
                                <a href="?module=nilai&action=edit&id='.e($row['id_nilai']).'"><button>Edit</button></a>
                                <form method="post" action="?module=nilai&action=delete" onsubmit="return confirm(\'Yakin ingin hapus data ini?\')" style="display:inline;">
                                    <input type="hidden" name="id_nilai" value="'.e($row['id_nilai']).'">
                                    <button type="submit">Hapus</button>
                                </form>
                            </td>
                          </tr>';
                }
                echo '</tbody></table>';
            }
        }
        break;

    case 'storedproc':
        // Form untuk memilih siswa dan menampilkan looping mapel via stored procedure simulation
        ?>
        <h2>Looping Stored Procedure - Tampilkan Mata Pelajaran Siswa</h2>
        <form method="post" action="?module=storedproc&action=run_loop">
            <label for="id_siswa">Pilih Siswa:</label>
            <select id="id_siswa" name="id_siswa" required>
                <option value="">-- Pilih Siswa --</option>
                <?php foreach ($all_siswa as $sw): ?>
                    <option value="<?= e($sw['id_siswa']) ?>" <?= (isset($_POST['id_siswa']) && $_POST['id_siswa'] == $sw['id_siswa']) ? 'selected' : '' ?>>
                        <?= e($sw['nama']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Jalankan Stored Procedure</button>
            <a href="?module=storedproc"><button type="button" class="cancel">Reset</button></a>
        </form>
        <?php
        if ($action === 'run_loop' && isset($mapel_list)) {
            echo '<h3>Hasil Stored Procedure (Looping Mapel Siswa):</h3>';
            if (count($mapel_list) > 0) {
                echo '<ul>';
                foreach ($mapel_list as $nm) {
                    echo '<li>' . e($nm) . '</li>';
                }
                echo '</ul>';
            } else {
                echo '<p>Tidak ada mata pelajaran ditemukan untuk siswa ini.</p>';
            }
        }
        break; 

    case 'rekap_mapel':
        $all_mapel = getAll($pdo, 'mapel', 'ORDER BY nama_mapel');
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'show') {
            $id_mapel = (int)($_POST['id_mapel'] ?? 0);
            tampilkan_rekap_nilai_per_mapel_hasil($pdo, $id_mapel);
        } else {
            tampilkan_rekap_nilai_per_mapel_form($all_mapel);
        }
        break;

    case 'log':
        tampilkan_log($pdo);
        break;  

    case 'Log':
        tampilkan_log_aktivitas($pdo);
        break;

    default:
        echo "<p>Modul tidak ditemukan.</p>";
        break; 
    case 'dashboard':
        tampilkan_dashboard($pdo);
        break;  
    case 'hasil_keseluruhan': // module untuk menampilkan hasil nilai
        include 'results.php';
        break;
}

require 'footer.php'; // Menghubungkan file footer
?>
