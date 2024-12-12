<?php
// Koneksi ke database
$conn = new mysqli("171.16.3.6", "aka", "Rsw1sm4r1n1", "sik_back");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Proses penghapusan data
if (isset($_POST['selected_data'])) {
    $selected_data = $_POST['selected_data'];

    // Iterasi data yang dipilih
    foreach ($selected_data as $data) {
        list($no_keluar, $kode_brng) = explode('|', $data);

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
        $stmt->execute();
    }
    echo "Data terpilih berhasil dihapus. <a href='index.php'>Kembali</a>";
} else {
    echo "Tidak ada data yang dipilih. <a href='index.php'>Kembali</a>";
}

$conn->close();
?>
