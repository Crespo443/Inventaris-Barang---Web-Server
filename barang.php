<?php
session_start();
include 'config/koneksi.php';

// 1. Cek Login
if ($_SESSION['status'] != "sudah_login") {
    header("location:login.php");
}

// 2. CEK ROLE: Kalau bukan admin, tendang keluar!
if ($_SESSION['role'] != 'admin') {
    echo "<script>
            sessionStorage.setItem('notif_pesan', 'Akses Ditolak! Anda bukan Admin.');
            sessionStorage.setItem('notif_tipe', 'error');
            window.location='index.php';
          </script>";
    exit;
}

$pesan = "";
$tipe_pesan = "";

// --- LOGIC HAPUS ---
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];

    // 1. CEK DULU: Apakah barang ini ada di riwayat transaksi (detail keluar)?
    $cek_transaksi = mysqli_query($conn, "SELECT * FROM tabel_detail_keluar WHERE id_barang='$id'");

    if (mysqli_num_rows($cek_transaksi) > 0) {
        echo "<script>
            alert('DILARANG HAPUS! Barang ini ada dalam riwayat transaksi. Data tidak boleh hilang untuk arsip.');
            window.location='barang.php';
        </script>";
    } else {
        // Hapus file gambar jika bukan default
        $cek = mysqli_fetch_assoc(mysqli_query($conn, "SELECT foto_barang FROM tabel_barang WHERE id_barang='$id'"));
        if ($cek['foto_barang'] != 'default.jpg') {
            unlink("assets/img/" . $cek['foto_barang']);
        }

        $qHapus = mysqli_query($conn, "DELETE FROM tabel_barang WHERE id_barang='$id'");
        if ($qHapus) {
            echo "<script>alert('Data Barang Dihapus'); window.location='barang.php';</script>";
        }
    }
}

include 'layout/header.php';
include 'layout/sidebar.php';
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Data Barang</h1>
    <div>
        <button onclick="showToast('Ini contoh toast notification!', 'success')" class="btn btn-sm btn-info shadow-sm mr-2">
            <i class="fas fa-bell"></i> Test Toast
        </button>
        <a href="tambah_barang.php" class="btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Barang
        </a>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Stok Barang Gudang</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Foto</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Stok</th>
                        <th>Harga</th>
                        <th>Supplier</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $query = "SELECT b.*, k.nama_kategori, s.nama_supplier 
                              FROM tabel_barang b
                              LEFT JOIN tabel_kategori k ON b.id_kategori = k.id_kategori
                              LEFT JOIN tabel_supplier s ON b.id_supplier = s.id_supplier
                              ORDER BY b.id_barang DESC";

                    $result = mysqli_query($conn, $query);
                    while ($row = mysqli_fetch_assoc($result)):
                    ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td>
                                <img src="assets/img/<?= $row['foto_barang']; ?>" width="50" height="50" style="object-fit: cover;">
                            </td>
                            <td>
                                <b><?= $row['nama_barang']; ?></b><br>
                                <small>Kode: <?= $row['kode_barang']; ?></small>
                            </td>
                            <td><?= $row['nama_kategori']; ?></td>
                            <td>
                                <?php if ($row['stok'] < 5): ?>
                                    <span class="badge badge-danger">Habis/Low: <?= $row['stok']; ?></span>
                                <?php else: ?>
                                    <span class="badge badge-success"><?= $row['stok']; ?></span>
                                <?php endif; ?>
                            </td>
                            <td>Rp <?= number_format($row['harga_satuan'], 0, ',', '.'); ?></td>
                            <td><small><?= $row['nama_supplier']; ?></small></td>
                            <td>
                                <a href="edit_barang.php?id=<?= $row['id_barang']; ?>" class="btn btn-warning btn-sm" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="konfirmasiHapus(<?= $row['id_barang']; ?>, '<?= addslashes($row['nama_barang']); ?>')" class="btn btn-danger btn-sm" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="modalKonfirmasiHapus" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Konfirmasi Hapus</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-trash fa-3x text-danger mb-3"></i>
                <h5>Yakin ingin menghapus?</h5>
                <p class="mb-0"><strong id="modalNamaItem"></strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="btnKonfirmasiHapus"><i class="fas fa-trash"></i> Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>

<?php if ($pesan != ""): ?>
    <script>
        $(document).ready(function() {
            showNotification('<?= $pesan; ?>', '<?= $tipe_pesan; ?>');
        });
    </script>
<?php endif; ?>

<script>
    // Cek notifikasi dari session storage (dari halaman tambah)
    $(document).ready(function() {
        var notifPesan = sessionStorage.getItem('notif_pesan');
        var notifTipe = sessionStorage.getItem('notif_tipe');

        if (notifPesan && notifTipe) {
            showToast(notifPesan, notifTipe);
            sessionStorage.removeItem('notif_pesan');
            sessionStorage.removeItem('notif_tipe');
        }
    });

    // Konfirmasi hapus
    function konfirmasiHapus(id, nama) {
        $('#modalKonfirmasiHapus').modal('show');
        $('#modalNamaItem').text(nama);
        $('#btnKonfirmasiHapus').off('click').on('click', function() {
            $('#modalKonfirmasiHapus').modal('hide');
            showToast('Menghapus barang...', 'info');
            setTimeout(function() {
                window.location = 'barang.php?hapus=' + id;
            }, 500);
        });
    }
</script>

<?php include 'layout/footer.php'; ?>