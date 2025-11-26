<?php
session_start();
include 'config/koneksi.php';
if ($_SESSION['status'] != "sudah_login") {
    header("location:login.php");
}

include 'layout/header.php';
include 'layout/sidebar.php';
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Transaksi Barang Keluar</h1>
    <a href="tambah_barang_keluar.php" class="btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50"></i> Input Barang Keluar
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Riwayat Pengeluaran Barang</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No Transaksi</th>
                        <th>Tanggal</th>
                        <th>Penerima</th>
                        <th>Barang</th>
                        <th>Jumlah</th>
                        <th>Petugas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    // QUERY JOIN 4 TABEL SEKALIGUS (Transaksi, Detail, Barang, User)
                    $query = "SELECT 
                                bk.no_transaksi, bk.tanggal_keluar, bk.nama_penerima, bk.divisi_tujuan,
                                b.nama_barang, dk.jumlah_keluar,
                                u.nama_lengkap
                              FROM tabel_barang_keluar bk
                              JOIN tabel_detail_keluar dk ON bk.id_keluar = dk.id_keluar
                              JOIN tabel_barang b ON dk.id_barang = b.id_barang
                              JOIN tabel_user u ON bk.id_user = u.id_user
                              ORDER BY bk.id_keluar DESC";

                    $result = mysqli_query($conn, $query);
                    while ($row = mysqli_fetch_assoc($result)):
                    ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= $row['no_transaksi']; ?></td>
                            <td><?= $row['tanggal_keluar']; ?></td>
                            <td>
                                <b><?= $row['nama_penerima']; ?></b><br>
                                <small>(<?= $row['divisi_tujuan']; ?>)</small>
                            </td>
                            <td><?= $row['nama_barang']; ?></td>
                            <td><span class="badge badge-danger">- <?= $row['jumlah_keluar']; ?></span></td>
                            <td><small><?= $row['nama_lengkap']; ?></small></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

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
</script>

<?php include 'layout/footer.php'; ?>