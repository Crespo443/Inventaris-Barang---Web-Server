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
    <h1 class="h3 mb-0 text-gray-800">Pengembalian Barang</h1>
    <a href="tambah_barang_kembali.php" class="btn btn-sm btn-success shadow-sm">
        <i class="fas fa-undo fa-sm text-white-50"></i> Input Pengembalian
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-success">Riwayat Pengembalian Barang</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No Transaksi</th>
                        <th>Tanggal Kembali</th>
                        <th>Pengembalian Dari</th>
                        <th>Barang</th>
                        <th>Jumlah</th>
                        <th>Kondisi</th>
                        <th>Petugas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $query = "SELECT 
                                bk.no_transaksi_kembali, bk.tanggal_kembali, bk.nama_pengembalian, bk.kondisi_barang, bk.keterangan,
                                b.nama_barang, dk.jumlah_kembali,
                                u.nama_lengkap
                              FROM tabel_barang_kembali bk
                              JOIN tabel_detail_kembali dk ON bk.id_kembali = dk.id_kembali
                              JOIN tabel_barang b ON dk.id_barang = b.id_barang
                              JOIN tabel_user u ON bk.id_user = u.id_user
                              ORDER BY bk.id_kembali DESC";

                    $result = mysqli_query($conn, $query);
                    if ($result && mysqli_num_rows($result) > 0):
                        while ($row = mysqli_fetch_assoc($result)):
                    ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= $row['no_transaksi_kembali']; ?></td>
                                <td><?= date('d/m/Y', strtotime($row['tanggal_kembali'])); ?></td>
                                <td>
                                    <b><?= $row['nama_pengembalian']; ?></b>
                                </td>
                                <td><?= $row['nama_barang']; ?></td>
                                <td><span class="badge badge-success">+ <?= $row['jumlah_kembali']; ?></span></td>
                                <td>
                                    <?php if ($row['kondisi_barang'] == 'baik'): ?>
                                        <span class="badge badge-success">Baik</span>
                                    <?php elseif ($row['kondisi_barang'] == 'rusak'): ?>
                                        <span class="badge badge-danger">Rusak</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">Kurang Baik</span>
                                    <?php endif; ?>
                                </td>
                                <td><small><?= $row['nama_lengkap']; ?></small></td>
                            </tr>
                    <?php
                        endwhile;
                    else:
                    ?>
                        <tr>
                            <td colspan="8" class="text-center">Belum ada data pengembalian</td>
                        </tr>
                    <?php endif; ?>
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
