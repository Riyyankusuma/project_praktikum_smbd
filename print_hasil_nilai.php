<?php
require_once 'config.php';
require_once 'functions.php';
require('fpdf/fpdf.php'); // pastikan path sudah benar

// Ambil data hasil nilai dari DB
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

// Inisialisasi FPDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,10,'Hasil Nilai Siswa',0,1,'C');

// Header tabel
$pdf->SetFont('Arial','B',10);
$pdf->SetFillColor(200,200,200);
$pdf->Cell(45,10,'Nama Siswa',1,0,'C',true);
$pdf->Cell(25,10,'Kelas',1,0,'C',true);
$pdf->Cell(45,10,'Mata Pelajaran',1,0,'C',true);
$pdf->Cell(45,10,'Guru Pengampu',1,0,'C',true);
$pdf->Cell(20,10,'Nilai',1,1,'C',true);

// Isi tabel
$pdf->SetFont('Arial','',10);
foreach ($results as $row) {
    $pdf->Cell(45,10, $row['nama_siswa'], 1);
    $pdf->Cell(25,10, $row['nama_kelas'], 1);
    $pdf->Cell(45,10, $row['nama_mapel'], 1);
    $pdf->Cell(45,10, $row['nama_guru'], 1);
    $pdf->Cell(20,10, $row['nilai_angka'], 1, 1, 'C');
}

$pdf->Output('I', 'Hasil_Nilai_Siswa.pdf');
