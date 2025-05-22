<?php
// modules/storedproc.php

include_once 'config.php';  // pastikan koneksi tersedia
include_once 'functions.php';

// Cek action dari URL
$action = $_GET['action'] ?? '';

// Ambil semua siswa untuk dropdown
$query = $pdo->query("SELECT id_siswa, nama FROM siswa ORDER BY nama");
$all_siswa = $query->fetchAll(PDO::FETCH_ASSOC);

// Jika form disubmit
$mapel_list = [];
if ($action === 'run_loop' && !empty($_POST['id_siswa'])) {
    $id_siswa = $_POST['id_siswa'];
    $stmt = $pdo->prepare("CALL sp_get_mapel_siswa(:id_siswa)");
    $stmt->bindParam(':id_siswa', $id_siswa, PDO::PARAM_INT);
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $mapel_list[] = $row['nama_mapel'];
    }
    $stmt->closeCursor();
}
?>

<!-- Tampilkan HTML di bawahnya -->
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

<?php if ($action === 'run_loop'): ?>
    <h3>Hasil Stored Procedure (Looping Mapel Siswa):</h3>
    <?php if (count($mapel_list) > 0): ?>
        <ul>
            <?php foreach ($mapel_list as $nm): ?>
                <li><?= e($nm) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Tidak ada mata pelajaran ditemukan untuk siswa ini.</p>
    <?php endif; ?>
<?php endif; ?>
