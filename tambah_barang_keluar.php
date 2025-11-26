<?php
session_start();
include 'config/koneksi.php';
if ($_SESSION['status'] != "sudah_login") {
    header("location:login.php");
}

if (isset($_POST['simpan'])) {
    // 1. Tangkap Inputan Form
    $no_transaksi   = "TRX-" . time(); // Bikin nomor transaksi otomatis (TRX-123456...)
    $tgl_keluar     = $_POST['tanggal_keluar'];
    $nama_penerima  = $_POST['nama_penerima'];
    $divisi         = $_POST['divisi'];
    $id_barang      = $_POST['id_barang'];
    $jumlah         = $_POST['jumlah'];
    $keterangan     = $_POST['keterangan'];
    $id_user        = $_SESSION['id_user']; // Siapa yang login saat ini

    // 2. Cek Stok Barang Dulu (Validasi)
    $cek_stok = mysqli_query($conn, "SELECT stok FROM tabel_barang WHERE id_barang = '$id_barang'");
    $data_stok = mysqli_fetch_assoc($cek_stok);

    if ($data_stok['stok'] < $jumlah) {
        echo "<script>showToast('Stok tidak cukup! Sisa stok: " . $data_stok['stok'] . "', 'error');</script>";
    } else {
        // Jika stok cukup, Lakukan 3 langkah penyimpanan:

        // A. Simpan ke Tabel Transaksi (Header)
        $insertHeader = mysqli_query($conn, "INSERT INTO tabel_barang_keluar (no_transaksi, tanggal_keluar, nama_penerima, divisi_tujuan, keterangan, id_user) 
                                             VALUES ('$no_transaksi', '$tgl_keluar', '$nama_penerima', '$divisi', '$keterangan', '$id_user')");

        // Ambil ID dari transaksi yang baru saja dibuat
        $id_keluar_baru = mysqli_insert_id($conn);

        if ($insertHeader) {
            // B. Simpan ke Tabel Detail Transaksi
            mysqli_query($conn, "INSERT INTO tabel_detail_keluar (id_keluar, id_barang, jumlah_keluar) 
                                 VALUES ('$id_keluar_baru', '$id_barang', '$jumlah')");

            // C. Kurangi Stok di Tabel Barang
            mysqli_query($conn, "UPDATE tabel_barang SET stok = stok - $jumlah WHERE id_barang = '$id_barang'");

            echo "<script>
                    window.location='barang_keluar.php';
                    sessionStorage.setItem('notif_pesan', 'Transaksi berhasil! Stok berkurang.');
                    sessionStorage.setItem('notif_tipe', 'success');
                  </script>";
            exit;
        } else {
            echo "<script>showToast('Gagal membuat transaksi!', 'error');</script>";
        }
    }
}

include 'layout/header.php';
include 'layout/sidebar.php';
?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Input Transaksi Barang Keluar</h6>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Tanggal Keluar</label>
                        <input type="date" name="tanggal_keluar" class="form-control" value="<?= date('Y-m-d'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Penerima</label>
                        <input type="text" name="nama_penerima" class="form-control" placeholder="Cth: Budi Santoso" required>
                    </div>
                    <div class="form-group">
                        <label>Divisi / Bagian</label>
                        <select name="divisi" class="form-control">
                            <option value="Umum">Umum</option>
                            <option value="IT">IT</option>
                            <option value="Keuangan">Keuangan</option>
                            <option value="HRD">HRD</option>
                            <option value="Gudang">Gudang</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <div class="form-group">
                                <label>Pilih Barang</label>
                                <select name="id_barang" class="form-control" required>
                                    <option value="">-- Pilih Barang --</option>
                                    <?php
                                    // Tampilkan barang beserta stoknya di dropdown
                                    $brg = mysqli_query($conn, "SELECT * FROM tabel_barang ORDER BY nama_barang ASC");
                                    while ($b = mysqli_fetch_array($brg)) {
                                        echo "<option value='$b[id_barang]'>$b[nama_barang] (Stok: $b[stok])</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Jumlah Keluar</label>
                                <input type="number" name="jumlah" class="form-control" min="1" required>
                            </div>
                            <div class="form-group">
                                <label>Keterangan (Opsional)</label>
                                <textarea name="keterangan" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" name="simpan" class="btn btn-primary btn-block">Proses Transaksi</button>
            <a href="barang_keluar.php" class="btn btn-secondary btn-block">Batal</a>
        </form>
    </div>
</div>

<?php include 'layout/footer.php'; ?>