<?php
session_start();
include 'config/koneksi.php';
if ($_SESSION['status'] != "sudah_login") {
    header("location:login.php");
}

// Ambil ID dari URL
$id = intval($_GET['id']);
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tabel_kategori WHERE id_kategori='$id'"));

if (!$data) {
    echo "<script>
            sessionStorage.setItem('notif_pesan', 'Data kategori tidak ditemukan!');
            sessionStorage.setItem('notif_tipe', 'error');
            window.location='kategori.php';
          </script>";
    exit;
}

// --- LOGIC UPDATE DATA ---
if (isset($_POST['update'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_kategori']);
    $kode = mysqli_real_escape_string($conn, $_POST['kode_kategori']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $status = $_POST['status'];

    $query = "UPDATE tabel_kategori SET 
              nama_kategori='$nama', 
              kode_kategori='$kode', 
              deskripsi='$deskripsi', 
              status='$status' 
              WHERE id_kategori='$id'";

    if (mysqli_query($conn, $query)) {
        echo "<script>
                window.location='kategori.php';
                sessionStorage.setItem('notif_pesan', 'Kategori berhasil diperbarui!');
                sessionStorage.setItem('notif_tipe', 'success');
              </script>";
        exit;
    } else {
        echo "<script>showToast('Gagal memperbarui kategori!', 'error');</script>";
    }
}

include 'layout/header.php';
include 'layout/sidebar.php';
?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Edit Kategori</h6>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="form-group">
                <label>Nama Kategori</label>
                <input type="text" name="nama_kategori" class="form-control" value="<?= $data['nama_kategori']; ?>" required>
            </div>
            <div class="form-group">
                <label>Kode Kategori</label>
                <input type="text" name="kode_kategori" class="form-control" value="<?= $data['kode_kategori']; ?>" required>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="aktif" <?= ($data['status'] == 'aktif') ? 'selected' : ''; ?>>Aktif</option>
                    <option value="nonaktif" <?= ($data['status'] == 'nonaktif') ? 'selected' : ''; ?>>Non-Aktif</option>
                </select>
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3"><?= $data['deskripsi']; ?></textarea>
            </div>
            <button type="submit" name="update" class="btn btn-primary">
                <i class="fas fa-save"></i> Perbarui Data
            </button>
            <a href="kategori.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </form>
    </div>
</div>

<?php include 'layout/footer.php'; ?>