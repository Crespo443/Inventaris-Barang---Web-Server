<?php
// Ambil foto profil user dari database
$id_user_session = $_SESSION['id_user'];
$query_foto = mysqli_query($conn, "SELECT foto_profil FROM tabel_user WHERE id_user='$id_user_session'");
$data_foto = mysqli_fetch_assoc($query_foto);
$foto_profil = $data_foto['foto_profil'] ?? 'default-profile.jpg';
$foto_path = "assets/img/profile/" . $foto_profil;

// Jika foto tidak ada, gunakan default avatar
if (!file_exists($foto_path)) {
    $foto_path = "https://ui-avatars.com/api/?name=" . urlencode($_SESSION['nama_lengkap']) . "&size=60&background=4e73df&color=fff";
}
?>

<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-boxes"></i>
        </div>
        <div class="sidebar-brand-text mx-3">INVENTARIS</div>
    </a>

    <hr class="sidebar-divider my-0">

    <li class="nav-item">
        <a class="nav-link" href="index.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <hr class="sidebar-divider">

    <?php if ($_SESSION['role'] == 'admin'): ?>

        <div class="sidebar-heading">
            Master Data
        </div>

        <li class="nav-item">
            <a class="nav-link" href="kategori.php">
                <i class="fas fa-fw fa-tags"></i>
                <span>Data Kategori</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="supplier.php">
                <i class="fas fa-fw fa-truck"></i>
                <span>Data Supplier</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="barang.php">
                <i class="fas fa-fw fa-box"></i>
                <span>Data Barang</span></a>
        </li>

        <hr class="sidebar-divider">

    <?php endif; ?>
    <div class="sidebar-heading">
        Transaksi
    </div>

    <li class="nav-item">
        <a class="nav-link" href="barang_keluar.php">
            <i class="fas fa-fw fa-sign-out-alt"></i>
            <span>Barang Keluar</span></a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="barang_kembali.php">
            <i class="fas fa-fw fa-undo"></i>
            <span>Pengembalian Barang</span></a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">

    <li class="nav-item">
        <a class="nav-link" href="profil.php">
            <i class="fas fa-fw fa-user-cog"></i>
            <span>Pengaturan Profil</span></a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="#" onclick="konfirmasiLogout()">
            <i class="fas fa-fw fa-power-off"></i>
            <span>Keluar</span></a>
    </li>

</ul>
<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">

        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
            <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                <i class="fa fa-bars"></i>
            </button>
            <h5 class="m-0 font-weight-bold text-primary">Sistem Inventaris Barang</h5>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown no-arrow">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown">
                        <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                            Halo, <b><?= $_SESSION['nama_lengkap']; ?></b> (<?= ucfirst($_SESSION['role']); ?>)
                        </span>
                        <img class="img-profile rounded-circle" src="<?= $foto_path; ?>" style="object-fit: cover; width: 40px; height: 40px;">
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in">
                        <a class="dropdown-item" href="profil.php">
                            <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                            Profil Saya
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#" onclick="konfirmasiLogout()">
                            <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                            Keluar
                        </a>
                    </div>
                </li>
            </ul>
        </nav>
        <div class="container-fluid">

            <script>
                function konfirmasiLogout() {
                    $('#modalNotifikasi').modal('show');
                    $('#modalNotifHeader').removeClass().addClass('modal-header bg-warning text-white');
                    $('#modalNotifIcon').html('<i class="fas fa-sign-out-alt text-warning"></i>');
                    $('#modalNotifTitle').text('Konfirmasi Logout');
                    $('#modalNotifMessage').text('Yakin ingin keluar dari sistem?');
                    $('#modalNotifikasi').find('.modal-footer').html(
                        '<button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>' +
                        '<button type="button" class="btn btn-danger" onclick="window.location=\'logout.php\'"><i class="fas fa-sign-out-alt"></i> Ya, Keluar</button>'
                    );
                }
            </script>