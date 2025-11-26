<?php
session_start();
include 'config/koneksi.php';
if ($_SESSION['status'] != "sudah_login") {
    header("location:login.php");
}

// --- LOGIC SIMPAN DATA ---
if (isset($_POST['simpan'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_kategori']);
    $kode = mysqli_real_escape_string($conn, $_POST['kode_kategori']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $status = $_POST['status'];

    $query = "INSERT INTO tabel_kategori (nama_kategori, kode_kategori, deskripsi, status) 
              VALUES ('$nama', '$kode', '$deskripsi', '$status')";

    if (mysqli_query($conn, $query)) {
        echo "<script>
                window.location='kategori.php';
                sessionStorage.setItem('notif_pesan', 'Kategori berhasil ditambahkan!');
                sessionStorage.setItem('notif_tipe', 'success');
              </script>";
        exit;
    } else {
        echo "<script>showToast('Gagal menyimpan kategori!', 'error');</script>";
    }
}

include 'layout/header.php';
include 'layout/sidebar.php';
?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Tambah Kategori Baru</h6>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="form-group">
                <label>Nama Kategori</label>
                <input type="text" name="nama_kategori" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Kode Kategori</label>
                <input type="text" name="kode_kategori" class="form-control" placeholder="Contoh: ELEK" required>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Non-Aktif</option>
                </select>
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3"></textarea>
            </div>
            <button type="submit" name="simpan" class="btn btn-primary">Simpan Data</button>
            <a href="kategori.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</div>

<?php include 'layout/footer.php'; ?>