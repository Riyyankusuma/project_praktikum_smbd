<?php
require_once 'config.php';
require_once 'functions.php';
require_once 'header.php';

// Ambil data hasil nilai
$stmt = $pdo->query("
    SELECT s.nama AS nama_siswa, k.nama_kelas, m.nama_mapel, g.nama AS nama_guru, n.nilai_angka
    FROM nilai n
    JOIN siswa s ON n.id_siswa = s.id_siswa
    JOIN mapel m ON n.id_mapel = m.id_mapel
    JOIN guru g ON m.id_guru = g.id_guru
    JOIN kelas k ON s.id_kelas = k.id_kelas
    ORDER BY s.nama, m.nama_mapel
");
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main>
    <h2>Hasil Nilai Siswa</h2>
    <?php if (empty($results)): ?>
        <p>Tidak ada hasil nilai ditemukan.</p>
    <?php else: ?>
        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th>Mata Pelajaran</th>
                    <th>Guru Pengampu</th>
                    <th>Nilai</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $row): ?>
                    <tr>
                        <td><?= h($row['nama_siswa']); ?></td>
                        <td><?= h($row['nama_kelas']); ?></td>
                        <td><?= h($row['nama_mapel']); ?></td>
                        <td><?= h($row['nama_guru']); ?></td>
                        <td><?= h($row['nilai_angka']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <a href="print_hasil_nilai.php" target="_blank" style="display:inline-block; margin-bottom:15px; background:#0171bb; color:#fff; padding:10px 20px; text-decoration:none; border-radius:5px;">Cetak PDF</a>
</main>

<?php require_once 'footer.php'; ?>
