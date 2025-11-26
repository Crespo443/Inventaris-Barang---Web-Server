<?php
session_start();
include 'config/koneksi.php';

// Cek Login
if ($_SESSION['status'] != "sudah_login") {
    header("location:login.php");
    exit;
}

$id_user = $_SESSION['id_user'];
$pesan = "";
$tipe_pesan = "";

// Ambil data user
$query_user = "SELECT * FROM tabel_user WHERE id_user='$id_user'";
$result = mysqli_query($conn, $query_user);
$user_data = mysqli_fetch_assoc($result);

// Proses Update Profil
if (isset($_POST['update_profil'])) {
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);

    // Cek apakah username sudah digunakan user lain
    $cek_username = mysqli_query($conn, "SELECT * FROM tabel_user WHERE username='$username' AND id_user != '$id_user'");
    if (mysqli_num_rows($cek_username) > 0) {
        $pesan = "Username sudah digunakan oleh user lain!";
        $tipe_pesan = "error";
    } else {
        $update = "UPDATE tabel_user SET nama_lengkap='$nama_lengkap', username='$username' WHERE id_user='$id_user'";
        if (mysqli_query($conn, $update)) {
            $_SESSION['nama_lengkap'] = $nama_lengkap;
            $_SESSION['username'] = $username;
            $pesan = "Profil berhasil diperbarui!";
            $tipe_pesan = "success";
            // Reload data user
            $result = mysqli_query($conn, $query_user);
            $user_data = mysqli_fetch_assoc($result);
        } else {
            $pesan = "Gagal memperbarui profil!";
            $tipe_pesan = "error";
        }
    }
}

// Proses Upload Foto Profil
if (isset($_POST['upload_foto'])) {
    if ($_FILES['foto_profil']['name'] != "") {
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        $file_name = $_FILES['foto_profil']['name'];
        $file_size = $_FILES['foto_profil']['size'];
        $file_tmp = $_FILES['foto_profil']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Validasi ekstensi file
        if (!in_array($file_ext, $allowed_ext)) {
            $pesan = "Format file tidak didukung! Gunakan JPG, JPEG, PNG, atau GIF.";
            $tipe_pesan = "error";
        }
        // Validasi ukuran file (max 2MB)
        else if ($file_size > 2097152) {
            $pesan = "Ukuran file terlalu besar! Maksimal 2MB.";
            $tipe_pesan = "error";
        } else {
            // Hapus foto lama jika bukan default
            if ($user_data['foto_profil'] != 'default-profile.jpg' && file_exists("assets/img/profile/" . $user_data['foto_profil'])) {
                unlink("assets/img/profile/" . $user_data['foto_profil']);
            }

            // Generate nama file unik
            $new_file_name = 'profile_' . $id_user . '_' . time() . '.' . $file_ext;

            // Upload file
            if (move_uploaded_file($file_tmp, 'assets/img/profile/' . $new_file_name)) {
                // Update database
                $update = "UPDATE tabel_user SET foto_profil='$new_file_name' WHERE id_user='$id_user'";
                if (mysqli_query($conn, $update)) {
                    $pesan = "Foto profil berhasil diperbarui!";
                    $tipe_pesan = "success";
                    // Reload data user
                    $result = mysqli_query($conn, $query_user);
                    $user_data = mysqli_fetch_assoc($result);
                } else {
                    $pesan = "Gagal menyimpan foto ke database!";
                    $tipe_pesan = "error";
                }
            } else {
                $pesan = "Gagal mengupload foto!";
                $tipe_pesan = "error";
            }
        }
    } else {
        $pesan = "Pilih file foto terlebih dahulu!";
        $tipe_pesan = "error";
    }
}

// Proses Ubah Password
if (isset($_POST['ubah_password'])) {
    $password_lama = md5($_POST['password_lama']);
    $password_baru = md5($_POST['password_baru']);
    $konfirmasi_password = md5($_POST['konfirmasi_password']);

    // Cek password lama
    if ($password_lama != $user_data['password']) {
        $pesan = "Password lama tidak sesuai!";
        $tipe_pesan = "error";
    }
    // Cek konfirmasi password
    else if ($password_baru != $konfirmasi_password) {
        $pesan = "Konfirmasi password tidak cocok!";
        $tipe_pesan = "error";
    } else {
        $update = "UPDATE tabel_user SET password='$password_baru' WHERE id_user='$id_user'";
        if (mysqli_query($conn, $update)) {
            $pesan = "Password berhasil diubah!";
            $tipe_pesan = "success";
        } else {
            $pesan = "Gagal mengubah password!";
            $tipe_pesan = "error";
        }
    }
}

include 'layout/header.php';
include 'layout/sidebar.php';
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Pengaturan Profil</h1>
</div>

<div class="row">
    <!-- Foto Profil -->
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Foto Profil</h6>
            </div>
            <div class="card-body text-center">
                <?php
                $foto_path = "assets/img/profile/" . $user_data['foto_profil'];
                if (!file_exists($foto_path)) {
                    $foto_path = "https://ui-avatars.com/api/?name=" . urlencode($user_data['nama_lengkap']) . "&size=200&background=4e73df&color=fff";
                }
                ?>
                <img src="<?= $foto_path; ?>" class="rounded-circle mb-3" width="150" height="150" style="object-fit: cover; border: 3px solid #4e73df;" id="preview-foto">

                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label class="btn btn-primary btn-sm">
                            <i class="fas fa-camera"></i> Pilih Foto
                            <input type="file" name="foto_profil" class="d-none" accept="image/*" id="input-foto" onchange="previewImage(this)">
                        </label>
                    </div>
                    <button type="submit" name="upload_foto" class="btn btn-success btn-sm"><i class="fas fa-upload"></i> Upload Foto</button>
                    <small class="d-block text-muted mt-2">Format: JPG, PNG, GIF (Max 2MB)</small>
                </form>
            </div>
        </div>
    </div>

    <!-- Form Update Profil -->
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Informasi Profil</h6>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" value="<?= $user_data['nama_lengkap']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" value="<?= $user_data['username']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <input type="text" class="form-control" value="<?= ucfirst($user_data['role']); ?>" readonly>
                    </div>
                    <button type="submit" name="update_profil" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>

        <!-- Form Ubah Password -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-danger">Ubah Password</h6>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="form-group">
                        <label>Password Lama</label>
                        <input type="password" name="password_lama" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Password Baru</label>
                        <input type="password" name="password_baru" class="form-control" required minlength="5">
                    </div>
                    <div class="form-group">
                        <label>Konfirmasi Password Baru</label>
                        <input type="password" name="konfirmasi_password" class="form-control" required minlength="5">
                    </div>
                    <button type="submit" name="ubah_password" class="btn btn-danger">
                        <i class="fas fa-key"></i> Ubah Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php if ($pesan != ""): ?>
    <script>
        $(document).ready(function() {
            showNotification('<?= $pesan; ?>', '<?= $tipe_pesan; ?>');
        });
    </script>
<?php endif; ?>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#preview-foto').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<?php include 'layout/footer.php'; ?>