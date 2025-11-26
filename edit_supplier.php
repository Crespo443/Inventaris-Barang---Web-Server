<?php
session_start();
include 'config/koneksi.php';
if ($_SESSION['status'] != "sudah_login") {
    header("location:login.php");
}

$id = intval($_GET['id']);
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tabel_supplier WHERE id_supplier='$id'"));

if (!$data) {
    echo "<script>
            sessionStorage.setItem('notif_pesan', 'Data supplier tidak ditemukan!');
            sessionStorage.setItem('notif_tipe', 'error');
            window.location='supplier.php';
          </script>";
    exit;
}

// --- LOGIC UPDATE ---
if (isset($_POST['update'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_supplier']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat_supplier']);
    $telp = mysqli_real_escape_string($conn, $_POST['no_telp']);
    $email = mysqli_real_escape_string($conn, $_POST['email_supplier']);
    $pj = mysqli_real_escape_string($conn, $_POST['penanggung_jawab']);

    $query = "UPDATE tabel_supplier SET 
              nama_supplier='$nama', 
              alamat_supplier='$alamat', 
              no_telp='$telp', 
              email_supplier='$email', 
              penanggung_jawab='$pj' 
              WHERE id_supplier='$id'";

    if (mysqli_query($conn, $query)) {
        echo "<script>
                window.location='supplier.php';
                sessionStorage.setItem('notif_pesan', 'Supplier berhasil diperbarui!');
                sessionStorage.setItem('notif_tipe', 'success');
              </script>";
        exit;
    } else {
        echo "<script>showToast('Gagal memperbarui supplier!', 'error');</script>";
    }
}

include 'layout/header.php';
include 'layout/sidebar.php';
?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Edit Data Supplier</h6>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="form-group">
                <label>Nama Supplier</label>
                <input type="text" name="nama_supplier" class="form-control" value="<?= $data['nama_supplier']; ?>" required>
            </div>
            <div class="form-group">
                <label>Penanggung Jawab</label>
                <input type="text" name="penanggung_jawab" class="form-control" value="<?= $data['penanggung_jawab']; ?>" required>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>No. Telepon</label>
                        <input type="text" name="no_telp" class="form-control" value="<?= $data['no_telp']; ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email_supplier" class="form-control" value="<?= $data['email_supplier']; ?>">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Alamat</label>
                <textarea name="alamat_supplier" class="form-control" rows="3" required><?= $data['alamat_supplier']; ?></textarea>
            </div>

            <button type="submit" name="update" class="btn btn-primary">
                <i class="fas fa-save"></i> Perbarui Data
            </button>
            <a href="supplier.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </form>
    </div>
</div>

<?php include 'layout/footer.php'; ?>