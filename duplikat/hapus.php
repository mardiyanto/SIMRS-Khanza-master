<?php
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "sik_back");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil parameter dari URL
$no_keluar = $_GET['no_keluar'];
$kode_brng = $_GET['kode_brng'];

// Hapus semua data duplikat kecuali satu data terbaru
$sql = "DELETE FROM detail_pengeluaran_obat_bhp
        WHERE id NOT IN (
            SELECT id FROM (
                SELECT MIN(id) AS id
                FROM detail_pengeluaran_obat_bhp
                WHERE no_keluar = ? AND kode_brng = ?
            ) AS temp
        )
        AND no_keluar = ? AND kode_brng = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $no_keluar, $kode_brng, $no_keluar, $kode_brng);

if ($stmt->execute()) {
    echo "Data duplikat berhasil dihapus. <a href='index.php'>Kembali</a>";
} else {
    echo "Gagal menghapus data: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
