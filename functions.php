<?php
// Fungsi untuk escape output html
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}



// Fungsi redirect (ke halaman list modul)
function redirect($module, $action = 'list') {
    header("Location: ?module=$module&action=$action");
    exit;
}

// Fungsi ambil semua baris
function getAll($pdo, $table, $extra = '') {
    $stmt = $pdo->query("SELECT * FROM $table $extra");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fungsi ambil satu baris berdasar id 
function getOne($pdo, $table, $key, $id) {
    $stmt = $pdo->prepare("SELECT * FROM $table WHERE $key = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fungsi untuk menampilkan form kelas
function form_kelas($pdo, $data = null) {
    $is_edit = $data !== null;
    ?>
    <h2><?= $is_edit ? 'Edit Kelas' : 'Tambah Kelas' ?></h2>
    <form method="post" action="?module=kelas&action=<?= $is_edit ? 'edit' : 'add' ?>">
        <?php if ($is_edit): ?>
            <input type="hidden" name="id_kelas" value="<?= e($data['id_kelas']) ?>">
        <?php endif; ?>
        <label for="nama_kelas">Nama Kelas</label>
        <input type="text" id="nama_kelas" name="nama_kelas" required value="<?= e($data['nama_kelas'] ?? '') ?>">
        <label for="jurusan">Jurusan</label>
        <input type="text" id="jurusan" name="jurusan" required value="<?= e($data['jurusan'] ?? '') ?>">
        <button type="submit"><?= $is_edit ? 'Update' : 'Tambah' ?></button>
        <a href="?module=kelas"><button type="button" class="cancel">Batal</button></a>
    </form>
    <?php
}

// Fungsi untuk menampilkan form siswa
function form_siswa($pdo, $data = null) {
    global $all_kelas;
    $is_edit = $data !== null;
    ?>
    <h2><?= $is_edit ? 'Edit Siswa' : 'Tambah Siswa' ?></h2>
    <form method="post" action="?module=siswa&action=<?= $is_edit ? 'edit' : 'add' ?>">
        <?php if ($is_edit): ?>
            <input type="hidden" name="id_siswa" value="<?= e($data['id_siswa']) ?>">
        <?php endif; ?>
        <label for="nisn">NISN</label>
        <input type="text" id="nisn" name="nisn" required value="<?= e($data['nisn'] ?? '') ?>">
        <label for="nama">Nama</label>
        <input type="text" id="nama" name="nama" required value="<?= e($data['nama'] ?? '') ?>">
        <label for="alamat">Alamat</label>
        <textarea id="alamat" name="alamat" required><?= e($data['alamat'] ?? '') ?></textarea>
        <label for="id_kelas">Kelas</label>
        <select id="id_kelas" name="id_kelas" required>
            <option value="">-- Pilih Kelas --</option>
            <?php foreach ($all_kelas as $kelas): ?>
                <option value="<?= e($kelas['id_kelas']) ?>" <?= (isset($data['id_kelas']) && $data['id_kelas'] == $kelas['id_kelas']) ? 'selected' : '' ?>>
                    <?= e($kelas['nama_kelas'] . ' - ' . $kelas['jurusan']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit"><?= $is_edit ? 'Update' : 'Tambah' ?></button>
        <a href="?module=siswa"><button type="button" class="cancel">Batal</button></a>
    </form>
    <?php
}

// Fungsi untuk menampilkan form guru
function form_guru($pdo, $data = null) {
    $is_edit = $data !== null;
    ?>
    <h2><?= $is_edit ? 'Edit Guru' : 'Tambah Guru' ?></h2>
    <form method="post" action="?module=guru&action=<?= $is_edit ? 'edit' : 'add' ?>">
        <?php if ($is_edit): ?>
            <input type="hidden" name="id_guru" value="<?= e($data['id_guru']) ?>">
        <?php endif; ?>
        <label for="nama">Nama</label>
        <input type="text" id="nama" name="nama" required value="<?= e($data['nama'] ?? '') ?>">
        <label for="email">Email</label>
        <input type="text" id="email" name="email" required value="<?= e($data['email'] ?? '') ?>">
        <button type="submit"><?= $is_edit ? 'Update' : 'Tambah' ?></button>
        <a href="?module=guru"><button type="button" class="cancel">Batal</button></a>
    </form>
    <?php
}

// Fungsi untuk menampilkan form mapel
function form_mapel($pdo, $data = null) {
    global $all_guru;
    $is_edit = $data !== null;
    ?>
    <h2><?= $is_edit ? 'Edit Mata Pelajaran' : 'Tambah Mata Pelajaran' ?></h2>
    <form method="post" action="?module=mapel&action=<?= $is_edit ? 'edit' : 'add' ?>">
        <?php if ($is_edit): ?>
            <input type="hidden" name="id_mapel" value="<?= e($data['id_mapel']) ?>">
        <?php endif; ?>
        <label for="nama_mapel">Nama Mata Pelajaran</label>
        <input type="text" id="nama_mapel" name="nama_mapel" required value="<?= e($data['nama_mapel'] ?? '') ?>">
        <label for="id_guru">Guru Pengampu</label>
        <select id="id_guru" name="id_guru" required>
            <option value="">-- Pilih Guru --</option>
            <?php foreach ($all_guru as $guru): ?>
                <option value="<?= e($guru['id_guru']) ?>" <?= (isset($data['id_guru']) && $data['id_guru'] == $guru['id_guru']) ? 'selected' : '' ?>>
                    <?= e($guru['nama']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit"><?= $is_edit ? 'Update' : 'Tambah' ?></button>
        <a href="?module=mapel"><button type="button" class="cancel">Batal</button></a>
    </form>
    <?php
}

// Fungsi untuk menampilkan form nilai
function form_nilai($pdo, $data = null) {
    // load siswa & mapel for dropdown
    $all_siswa = getAll($pdo, "siswa s JOIN kelas k ON s.id_kelas=k.id_kelas ORDER BY s.nama");
    $all_mapel = getAll($pdo, "mapel m JOIN guru g ON m.id_guru=g.id_guru ORDER BY m.nama_mapel");
    $is_edit = $data !== null;
    ?>
    <h2><?= $is_edit ? 'Edit Nilai' : 'Tambah Nilai' ?></h2>
    <form method="post" action="?module=nilai&action=<?= $is_edit ? 'edit' : 'add' ?>">
        <?php if ($is_edit): ?>
            <input type="hidden" name="id_nilai" value="<?= e($data['id_nilai']) ?>">
        <?php endif; ?>
        <label for="id_siswa">Siswa</label>
        <select id="id_siswa" name="id_siswa" required>
            <option value="">-- Pilih Siswa --</option>
            <?php foreach ($all_siswa as $sw): ?>
                <option value="<?= e($sw['id_siswa']) ?>" <?= (isset($data['id_siswa']) && $data['id_siswa'] == $sw['id_siswa']) ? 'selected' : '' ?>>
                    <?= e($sw['nama'] . ' (' . $sw['nama_kelas'] . ')') ?>
                </option>
            <?php endforeach; ?>
        </select>
        <label for="id_mapel">Mata Pelajaran</label>
        <select id="id_mapel" name="id_mapel" required>
            <option value="">-- Pilih Mata Pelajaran --</option>
            <?php foreach ($all_mapel as $mp): ?>
                <option value="<?= e($mp['id_mapel']) ?>" <?= (isset($data['id_mapel']) && $data['id_mapel'] == $mp['id_mapel']) ? 'selected' : '' ?>>
                    <?= e($mp['nama_mapel'] . ' (' . $mp['nama'] . ')') ?>
                </option>
            <?php endforeach; ?>
        </select>
        <label for="nilai_angka">Nilai Angka</label>
        <input type="number" step="0.01" min="0" max="100" id="nilai_angka" name="nilai_angka" required value="<?= e($data['nilai_angka'] ?? '') ?>">
        <button type="submit"><?= $is_edit ? 'Update' : 'Tambah' ?></button>
        <a href="?module=nilai"><button type="button" class="cancel">Batal</button></a>
    </form>
    <?php
}

// Fungsi untuk menampilkan daftar log dari trigger
function tampilkan_log($pdo) {
    $data = getAll($pdo, 'log_nilai', 'ORDER BY waktu_log DESC LIMIT 100');
    echo '<h2>Log Perubahan Nilai (Trigger)</h2>';
    if (!$data) {
        echo '<p>Belum ada log data.</p>';
    } else {
        echo '<table><thead><tr>
            <th>ID Log</th><th>Aksi</th><th>ID Nilai</th><th>Nilai Lama</th><th>Nilai Baru</th><th>Waktu Log</th>
        </tr></thead><tbody>';
        foreach ($data as $row) {
            echo '<tr>
                <td>'.e($row['id_log']).'</td>
                <td>'.e($row['aksi']).'</td>
                <td>'.e($row['id_nilai']).'</td>
                <td>'.e($row['nilai_lama'] ?? '-') .'</td>
                <td>'.e($row['nilai_baru'] ?? '-') .'</td>
                <td>'.e($row['waktu_log']).'</td>
            </tr>';
        }
        echo '</tbody></table>';
    }
}

// Fungsi untuk menampilkan hasil looping stored procedure (disimulasikan dengan SELECT biasa)
function tampilkan_mapel_siswa($siswa_id, $pdo) {
    if (!$siswa_id) {
        echo '<p>Pilih siswa terlebih dahulu.</p>';
        return;
    }
    $stmt = $pdo->prepare("SELECT m.nama_mapel FROM nilai n JOIN mapel m ON n.id_mapel = m.id_mapel WHERE n.id_siswa = ?");
    $stmt->execute([$siswa_id]);
    $mapel_list = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo '<h2>Daftar Mata Pelajaran Siswa</h2>';
    if (!$mapel_list) {
        echo '<p>Tidak ada mata pelajaran ditemukan untuk siswa ini.</p>';
    } else {
        echo '<ul>';
        foreach ($mapel_list as $mapel) {
            echo '<li>' . e($mapel) . '</li>';
        }
        echo '</ul>';
    }
} 

function tampilkan_mapel_per_siswa_form($all_siswa) {
    ?>
    <h2>Tampilkan Mata Pelajaran per Siswa (Looping Stored Procedure)</h2>
    <form method="post" action="?module=looping_sp&action=show">
        <label for="id_siswa">Pilih Siswa</label>
        <select id="id_siswa" name="id_siswa" required>
            <option value="">-- Pilih Siswa --</option>
            <?php foreach($all_siswa as $s): ?>
                <option value="<?= e($s['id_siswa']) ?>"><?= e($s['nama']) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Tampilkan</button>
    </form>
    <?php
}

function tampilkan_rekap_nilai_per_mapel_form($all_mapel) {
    ?>
    <h2>Rekap Nilai per Mata Pelajaran (Stored Procedure)</h2>
    <form method="post" action="?module=rekap_mapel&action=show">
        <label for="id_mapel">Pilih Mata Pelajaran</label>
        <select id="id_mapel" name="id_mapel" required>
            <option value="">-- Pilih Mata Pelajaran --</option>
            <?php foreach ($all_mapel as $m): ?>
                <option value="<?= e($m['id_mapel']) ?>"><?= e($m['nama_mapel']) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Tampilkan Rekap</button>
    </form>
    <?php
}

function tampilkan_rekap_nilai_per_mapel_hasil($pdo, $id_mapel) {
    if (!$id_mapel) {
        echo "<p>Pilih mata pelajaran dulu.</p>";
        return;
    }
    
    // Pastikan stored procedure 'rekap_nilai_mapel' ada di database
    $stmt = $pdo->prepare("CALL rekap_nilai_mapel(?)");
    $stmt->execute([$id_mapel]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$row) {
        echo "<p>Data tidak ditemukan.</p>";
    } else {
        echo "<h3>Rekap Nilai untuk " . e($row['nama_mapel']) . "</h3>";
        echo "<table><tr><th>Rata-rata</th><th>Nilai Tertinggi</th><th>Nilai Terendah</th></tr>";
        echo "<tr><td>" . e($row['rata_rata']) . "</td><td>" . e($row['nilai_tertinggi']) . "</td><td>" . e($row['nilai_terendah']) . "</td></tr></table>";
    }
}

function tampilkan_mapel_per_siswa_hasil($pdo, $id_siswa) {
    if(!$id_siswa) {
        echo "<p>Pilih siswa dulu.</p>";
        return;
    }
    // Gunakan stored procedure looping_mapel_siswa dengan cursor Anda, karena PHP PDO tidak support multiple result set dengan mudah, kita ambil data dengan query alternatif
    $stmt = $pdo->prepare("SELECT m.nama_mapel FROM nilai n JOIN mapel m ON n.id_mapel = m.id_mapel WHERE n.id_siswa = ?");
    $stmt->execute([$id_siswa]);
    $list = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "<h3>Daftar Mata Pelajaran Siswa</h3>";
    if(count($list)==0) {
        echo "<p>Siswa belum ada mata pelajaran.</p>";
    } else {
        echo "<ul>";
        foreach($list as $mp) {
            echo "<li>".e($mp)."</li>";
        }
        echo "</ul>";
    }
}

function tampilkan_dashboard($pdo) {
    // 5 View dan join untuk laporan dan filter
    // Contoh: tampilkan rata-rata nilai per siswa dari view 'view_rata_rata_siswa'
    $rata2 = $pdo->query("SELECT * FROM view_rata_rata_siswa ORDER BY rata_nilai DESC")->fetchAll(PDO::FETCH_ASSOC);
    echo "<h2>Dashboard - Rata-rata Nilai Siswa</h2>";
    echo "<table><thead><tr><th>Nama</th><th>Rata-rata Nilai</th></tr></thead><tbody>";
    foreach($rata2 as $row) {
        echo "<tr><td>".e($row['nama'])."</td><td>".e($row['rata_nilai'])."</td></tr>";
    }
    echo "</tbody></table>";

    // Filter siswa nilai dibawah 75 dari view_siswa_tidak_lulus
    $siswa_tidak_lulus = $pdo->query("SELECT * FROM view_siswa_tidak_lulus ORDER BY nilai_angka ASC")->fetchAll(PDO::FETCH_ASSOC);
    echo "<h2>Siswa Tidak Lulus (Nilai < 75)</h2>";
    if(!$siswa_tidak_lulus) {
        echo "<p>Tidak ada siswa yang tidak lulus.</p>";
    } else {
        echo "<table><thead><tr><th>Nama Siswa</th><th>Mata Pelajaran</th><th>Nilai</th></tr></thead><tbody>";
        foreach($siswa_tidak_lulus as $row) {
            echo "<tr><td>".e($row['nama'])."</td><td>".e($row['nama_mapel'])."</td><td>".e($row['nilai_angka'])."</td></tr>";
        }
        echo "</tbody></table>";
    }
} 

// Fungsi untuk menampilkan log aktivitas
function tampilkan_log_aktivitas($pdo) {
    // Ambil data log_nilai dari database
    $logs = $pdo->query("SELECT * FROM log_nilai ORDER BY waktu_log DESC LIMIT 50")->fetchAll(PDO::FETCH_ASSOC);

    echo "<h2>Log Aktivitas CRUD Nilai (Trigger)</h2>";

    if (empty($logs)) {
        echo "<p>Tidak ada aktivitas.</p>";
        return;
    }

    // Tampilkan tabel log
    echo "<table border='1' cellpadding='8' cellspacing='0'>
        <thead>
            <tr>
                <th>ID Log</th>
                <th>Aksi</th>
                <th>ID Nilai</th>
                <th>Nilai Lama</th>
                <th>Nilai Baru</th>
                <th>Waktu</th>
            </tr>
        </thead>
        <tbody>";

    foreach ($logs as $log) {
        echo "<tr>
            <td>" . e($log['id_log']) . "</td>
            <td>" . e($log['aksi']) . "</td>
            <td>" . e($log['id_nilai']) . "</td>
            <td>" . (is_null($log['nilai_lama']) ? '-' : e($log['nilai_lama'])) . "</td>
            <td>" . (is_null($log['nilai_baru']) ? '-' : e($log['nilai_baru'])) . "</td>
            <td>" . e($log['waktu_log']) . "</td>
        </tr>";
    }

    echo "</tbody></table>";
}
?>
