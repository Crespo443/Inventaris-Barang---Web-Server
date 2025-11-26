<?php
session_start();
include 'config/koneksi.php';
if ($_SESSION['status'] != "sudah_login") {
    header("location:login.php");
}

// --- LOGIC SIMPAN ---
if (isset($_POST['simpan'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_supplier']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat_supplier']);
    $telp = mysqli_real_escape_string($conn, $_POST['no_telp']);
    $email = mysqli_real_escape_string($conn, $_POST['email_supplier']);
    $pj = mysqli_real_escape_string($conn, $_POST['penanggung_jawab']);

    $query = "INSERT INTO tabel_supplier (nama_supplier, alamat_supplier, no_telp, email_supplier, penanggung_jawab, tanggal_kerjasama) 
              VALUES ('$nama', '$alamat', '$telp', '$email', '$pj', NOW())";

    if (mysqli_query($conn, $query)) {
        echo "<script>
                window.location='supplier.php';
                sessionStorage.setItem('notif_pesan', 'Supplier berhasil ditambahkan!');
                sessionStorage.setItem('notif_tipe', 'success');
              </script>";
        exit;
    } else {
        echo "<script>showToast('Gagal menyimpan supplier!', 'error');</script>";
    }
}

include 'layout/header.php';
include 'layout/sidebar.php';
?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Tambah Supplier Baru</h6>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="form-group">
                <label>Nama Supplier (PT/CV/Toko)</label>
                <input type="text" name="nama_supplier" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Penanggung Jawab (Nama Sales/Kontak)</label>
                <input type="text" name="penanggung_jawab" class="form-control" required>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>No. Telepon</label>
                        <input type="text" name="no_telp" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Email (Opsional)</label>
                        <input type="email" name="email_supplier" class="form-control">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Alamat Lengkap</label>
                <textarea name="alamat_supplier" class="form-control" rows="3" required></textarea>
            </div>

            <button type="submit" name="simpan" class="btn btn-primary">Simpan Data</button>
            <a href="supplier.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</div>

<?php include 'layout/footer.php'; ?>