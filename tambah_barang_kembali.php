<?php
session_start();
include 'config/koneksi.php';
if ($_SESSION['status'] != "sudah_login") {
    header("location:login.php");
}

if (isset($_POST['simpan'])) {
    $no_transaksi = "RETURN-" . time();
    $tgl_kembali = $_POST['tanggal_kembali'];
    $nama_pengembalian = mysqli_real_escape_string($conn, $_POST['nama_pengembalian']);
    $id_barang = intval($_POST['id_barang']);
    $jumlah = intval($_POST['jumlah']);
    $kondisi = $_POST['kondisi_barang'];
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $id_user = $_SESSION['id_user'];

    // Ambil data barang
    $cek_barang = mysqli_query($conn, "SELECT nama_barang FROM tabel_barang WHERE id_barang = '$id_barang'");
    $data_barang = mysqli_fetch_assoc($cek_barang);

    if ($data_barang) {
        // A. Simpan ke Tabel Transaksi Kembali (Header)
        $insertHeader = mysqli_query($conn, "INSERT INTO tabel_barang_kembali (no_transaksi_kembali, tanggal_kembali, nama_pengembalian, kondisi_barang, keterangan, id_user) 
                                             VALUES ('$no_transaksi', '$tgl_kembali', '$nama_pengembalian', '$kondisi', '$keterangan', '$id_user')");

        $id_kembali_baru = mysqli_insert_id($conn);

        if ($insertHeader) {
            // B. Simpan ke Tabel Detail Kembali
            mysqli_query($conn, "INSERT INTO tabel_detail_kembali (id_kembali, id_barang, jumlah_kembali) 
                                 VALUES ('$id_kembali_baru', '$id_barang', '$jumlah')");

            // C. Tambah Stok di Tabel Barang (hanya jika kondisi baik)
            if ($kondisi == 'baik') {
                mysqli_query($conn, "UPDATE tabel_barang SET stok = stok + $jumlah WHERE id_barang = '$id_barang'");
                $pesan = "Pengembalian berhasil! Stok bertambah.";
            } else {
                $pesan = "Pengembalian berhasil! Stok tidak bertambah (kondisi: $kondisi).";
            }

            echo "<script>
                    window.location='barang_kembali.php';
                    sessionStorage.setItem('notif_pesan', '$pesan');
                    sessionStorage.setItem('notif_tipe', 'success');
                  </script>";
            exit;
        } else {
            echo "<script>showToast('Gagal membuat transaksi pengembalian!', 'error');</script>";
        }
    } else {
        echo "<script>showToast('Barang tidak ditemukan!', 'error');</script>";
    }
}

include 'layout/header.php';
include 'layout/sidebar.php';
?>

<div class="card shadow mb-4">
    <div class="card-header py-3 bg-success text-white">
        <h6 class="m-0 font-weight-bold">Input Pengembalian Barang</h6>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Tanggal Pengembalian</label>
                        <input type="date" name="tanggal_kembali" class="form-control" value="<?= date('Y-m-d'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Pengembalian (Dari Siapa)</label>
                        <input type="text" name="nama_pengembalian" class="form-control" placeholder="Contoh: Budi Santoso" required>
                    </div>
                    <div class="form-group">
                        <label>Kondisi Barang</label>
                        <select name="kondisi_barang" class="form-control" required>
                            <option value="baik">Baik (Stok +)</option>
                            <option value="kurang_baik">Kurang Baik (Stok +)</option>
                            <option value="rusak">Rusak (Tidak masuk stok)</option>
                        </select>
                        <small class="text-muted">Stok hanya bertambah jika kondisi baik</small>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <div class="form-group">
                                <label>Pilih Barang yang Dikembalikan</label>
                                <select name="id_barang" class="form-control" required>
                                    <option value="">-- Pilih Barang --</option>
                                    <?php
                                    $brg = mysqli_query($conn, "SELECT * FROM tabel_barang ORDER BY nama_barang ASC");
                                    while ($b = mysqli_fetch_array($brg)) {
                                        echo "<option value='$b[id_barang]'>$b[nama_barang] (Stok: $b[stok])</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Jumlah Dikembalikan</label>
                                <input type="number" name="jumlah" class="form-control" min="1" required>
                            </div>
                            <div class="form-group">
                                <label>Keterangan</label>
                                <textarea name="keterangan" class="form-control" rows="3" placeholder="Catatan kondisi atau alasan pengembalian"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" name="simpan" class="btn btn-success btn-block">
                <i class="fas fa-check"></i> Proses Pengembalian
            </button>
            <a href="barang_kembali.php" class="btn btn-secondary btn-block">
                <i class="fas fa-arrow-left"></i> Batal
            </a>
        </form>
    </div>
</div>

<?php include 'layout/footer.php'; ?>