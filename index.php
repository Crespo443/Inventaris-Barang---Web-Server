<?php
session_start();
// Cek Login
if ($_SESSION['status'] != "sudah_login") {
    header("location:login.php");
}

// Panggil Koneksi
include 'config/koneksi.php';

// Hitung jumlah data untuk widget dashboard
$jml_kategori = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tabel_kategori"));
$jml_barang = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tabel_barang"));
$jml_transaksi = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tabel_barang_keluar"));

// Judul Halaman
$judul = "Dashboard";

// 1. Load Header & Sidebar
include 'layout/header.php';
include 'layout/sidebar.php';
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
</div>

<div class="row">

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Kategori</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $jml_kategori; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-tags fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Barang</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $jml_barang; ?> Unit</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-box fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Transaksi Keluar</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $jml_transaksi; ?> x</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-sign-out-alt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Selamat Datang</h6>
    </div>
    <div class="card-body">
        <p>Halo <b><?= $_SESSION['nama_lengkap']; ?></b>, anda login sebagai <b><?= $_SESSION['role']; ?></b>.</p>
        <p>Silahkan gunakan menu di sebelah kiri untuk mengelola inventaris.</p>
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

<?php
// 3. Load Footer
include 'layout/footer.php';
?>