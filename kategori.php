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

// --- LOGIC HAPUS DATA ---
if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($conn, $_GET['hapus']);

    // 1. CEK DULU: Apakah kategori ini dipakai di tabel barang?
    $cek_terpakai = mysqli_query($conn, "SELECT nama_barang FROM tabel_barang WHERE id_kategori='$id' LIMIT 3");
    $jumlah_barang = mysqli_num_rows($cek_terpakai);

    if ($jumlah_barang > 0) {
        // Ambil nama barang yang menggunakan kategori ini
        $list_barang = [];
        while ($b = mysqli_fetch_assoc($cek_terpakai)) {
            $list_barang[] = $b['nama_barang'];
        }
        $nama_barang = implode(', ', $list_barang);

        // Hitung total barang
        $total_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM tabel_barang WHERE id_kategori='$id'");
        $total_data = mysqli_fetch_assoc($total_query);
        $total_barang = $total_data['total'];

        $pesan = "⚠️ Kategori tidak dapat dihapus! Masih digunakan oleh {$total_barang} barang. Contoh: {$nama_barang}" . ($total_barang > 3 ? ', dll.' : '');
        $tipe_pesan = "error";
    } else {
        // Jika aman (tidak dipakai), baru boleh hapus
        $qHapus = mysqli_query($conn, "DELETE FROM tabel_kategori WHERE id_kategori='$id'");
        if ($qHapus) {
            $pesan = "✅ Kategori berhasil dihapus!";
            $tipe_pesan = "success";
        } else {
            $pesan = "❌ Gagal menghapus kategori!";
            $tipe_pesan = "error";
        }
    }
}

include 'layout/header.php';
include 'layout/sidebar.php';
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Data Kategori Barang</h1>
    <a href="tambah_kategori.php" class="btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Kategori
    </a>
</div>

<!-- Info Alert -->
<div class="alert alert-info alert-dismissible fade show" role="alert">
    <i class="fas fa-info-circle"></i>
    <strong>Informasi:</strong> Kategori yang masih digunakan oleh barang <strong>tidak dapat dihapus</strong>. Hapus atau ubah kategori barang terlebih dahulu.
    <button type="button" class="close" data-dismiss="alert">
        <span>&times;</span>
    </button>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Tabel Kategori</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Kategori</th>
                        <th>Kode</th>
                        <th>Deskripsi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $query = mysqli_query($conn, "SELECT * FROM tabel_kategori ORDER BY id_kategori DESC");
                    while ($row = mysqli_fetch_assoc($query)):
                    ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= $row['nama_kategori']; ?></td>
                            <td><span class="badge badge-secondary"><?= $row['kode_kategori']; ?></span></td>
                            <td><?= $row['deskripsi']; ?></td>
                            <td>
                                <?php if ($row['status'] == 'aktif'): ?>
                                    <span class="badge badge-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Non-Aktif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="edit_kategori.php?id=<?= $row['id_kategori']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <button onclick="konfirmasiHapus(<?= $row['id_kategori']; ?>, '<?= addslashes($row['nama_kategori']); ?>')" class="btn btn-danger btn-sm">Hapus</button>
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
    $(document).ready(function() {
        var notifPesan = sessionStorage.getItem('notif_pesan');
        var notifTipe = sessionStorage.getItem('notif_tipe');
        if (notifPesan && notifTipe) {
            showToast(notifPesan, notifTipe);
            sessionStorage.removeItem('notif_pesan');
            sessionStorage.removeItem('notif_tipe');
        }
    });

    function konfirmasiHapus(id, nama) {
        $('#modalKonfirmasiHapus').modal('show');
        $('#modalNamaItem').text(nama);
        $('#btnKonfirmasiHapus').off('click').on('click', function() {
            $('#modalKonfirmasiHapus').modal('hide');
            showToast('Menghapus kategori...', 'info');
            setTimeout(function() {
                window.location = 'kategori.php?hapus=' + id;
            }, 500);
        });
    }
</script>

<?php include 'layout/footer.php'; ?>