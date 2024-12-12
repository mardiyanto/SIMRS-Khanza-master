<?php
// Koneksi ke database
$host = "171.16.3.6";
$username = "aka";
$password = "Rsw1sm4r1n1";
$dbname = "sik"; // Ganti dengan nama database Anda

$conn = new mysqli($host, $username, $password, $dbname);
// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Proses hapus data individual
if (isset($_GET['hapus'])) {
    $no_keluar = $_GET['no_keluar'];
    $kode_brng = $_GET['kode_brng'];

    // Query untuk menghapus data
    $sqlHapus = "DELETE FROM detail_pengeluaran_obat_bhp WHERE no_keluar = ? AND kode_brng = ?";
    $stmtHapus = $conn->prepare($sqlHapus);
    $stmtHapus->bind_param("ss", $no_keluar, $kode_brng);

    if ($stmtHapus->execute()) {
        echo "<script>alert('Data berhasil dihapus'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data'); window.location='index.php';</script>";
    }

    $stmtHapus->close();
}

// Proses hapus data terpilih
if (isset($_POST['hapus_selected'])) {
    if (isset($_POST['selected']) && is_array($_POST['selected'])) {
        $selected = $_POST['selected'];

        // Mulai transaksi untuk memastikan konsistensi
        $conn->begin_transaction();

        try {
            $sqlHapusSelected = "DELETE FROM detail_pengeluaran_obat_bhp WHERE no_keluar = ? AND kode_brng = ?";
            $stmtHapusSelected = $conn->prepare($sqlHapusSelected);

            foreach ($selected as $identifier) {
                // Pisahkan identifier menjadi no_keluar dan kode_brng
                list($no_keluar, $kode_brng) = explode('|', $identifier, 2);

                // Binding parameter dan eksekusi
                $stmtHapusSelected->bind_param("ss", $no_keluar, $kode_brng);
                $stmtHapusSelected->execute();
            }

            // Commit transaksi
            $conn->commit();
            echo "<script>alert('Data terpilih berhasil dihapus'); window.location='index.php';</script>";
        } catch (Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            $conn->rollback();
            echo "<script>alert('Gagal menghapus data terpilih'); window.location='index.php';</script>";
        }

        $stmtHapusSelected->close();
    } else {
        echo "<script>alert('Tidak ada data yang dipilih'); window.location='index.php';</script>";
    }
}

// Query untuk menghitung total data duplikat
$sqlCount = "SELECT COUNT(*) AS total FROM detail_pengeluaran_obat_bhp GROUP BY no_keluar, kode_brng, no_batch HAVING COUNT(*) > 1";
$resultCount = $conn->query($sqlCount);
$totalRows = $resultCount->fetch_assoc()['total'];

// Tentukan jumlah data per halaman
$perPage = 250;
$totalPages = ceil($totalRows / $perPage);

// Tentukan halaman saat ini
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page);
$offset = ($page - 1) * $perPage;

// Query untuk mencari data duplikat dengan paging
$sql = "
SELECT no_keluar, kode_brng, no_batch, COUNT(*) AS jumlah_duplikat
FROM detail_pengeluaran_obat_bhp
GROUP BY no_keluar, kode_brng, no_batch
HAVING jumlah_duplikat > 1
LIMIT $offset, $perPage
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Duplikat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        // Script untuk memilih atau membatalkan semua checkbox
        document.addEventListener('DOMContentLoaded', function () {
            const selectAll = document.getElementById('selectAll');
            selectAll.addEventListener('change', function () {
                const checkboxes = document.querySelectorAll('input[name="selected[]"]');
                checkboxes.forEach(cb => cb.checked = selectAll.checked);
            });
        });
    </script>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Data Duplikat</h2>
    <form method="POST" action="">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll"></th>
                    <th>No Keluar</th>
                    <th>Kode Barang</th>
                    <th>No Batch</th>
                    <th>Jumlah Duplikat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Membuat identifier unik untuk setiap checkbox
                        $identifier = htmlspecialchars($row['no_keluar']) . '|' . htmlspecialchars($row['kode_brng']);
                        echo "<tr>";
                        echo "<td><input type='checkbox' name='selected[]' value='" . htmlspecialchars($identifier) . "'></td>";
                        echo "<td>" . htmlspecialchars($row['no_keluar']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['kode_brng']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['no_batch']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['jumlah_duplikat']) . "</td>";
                        echo "<td>
                            <a href='?hapus=true&no_keluar=" . urlencode($row['no_keluar']) . "&kode_brng=" . urlencode($row['kode_brng']) . "' 
                               class='btn btn-danger btn-sm' 
                               onclick='return confirm(\"Apakah Anda yakin ingin menghapus data ini?\");'>
                               Hapus
                            </a>
                        </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center'>Tidak ada data duplikat</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <div class="d-flex justify-content-between">
            <button type="submit" name="hapus_selected" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data terpilih?');">
                Hapus Terpilih
            </button>
        </div>
    </form>

    <!-- Pagination -->
    <nav>
        <ul class="pagination justify-content-center">
            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $page - 1 ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $page + 1 ?>" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>