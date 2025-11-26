<?php
session_start();
include 'config/koneksi.php';
if ($_SESSION['status'] != "sudah_login") {
    header("location:login.php");
}

$id = intval($_GET['id']);
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tabel_barang WHERE id_barang='$id'"));

if (!$data) {
    echo "<script>
            sessionStorage.setItem('notif_pesan', 'Data tidak ditemukan!');
            sessionStorage.setItem('notif_tipe', 'error');
            window.location='barang.php';
          </script>";
    exit;
}

$pesan = "";
$tipe_pesan = "";

// --- LOGIC UPDATE ---
if (isset($_POST['update'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_barang']);
    $kode = mysqli_real_escape_string($conn, $_POST['kode_barang']);
    $stok = intval($_POST['stok']);
    $harga = intval($_POST['harga']);
    $id_kategori = intval($_POST['id_kategori']);
    $id_supplier = intval($_POST['id_supplier']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi_barang']);
    $foto_lama = $_POST['foto_lama'];

    $foto = $foto_lama;
    $upload_error = false;

    // Cek ganti foto atau tidak
    if ($_FILES['foto']['name'] != "") {
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        $file_name = $_FILES['foto']['name'];
        $file_size = $_FILES['foto']['size'];
        $file_tmp = $_FILES['foto']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Validasi ekstensi file
        if (!in_array($file_ext, $allowed_ext)) {
            $pesan = "Format file tidak didukung! Gunakan JPG, JPEG, PNG, atau GIF.";
            $tipe_pesan = "error";
            $upload_error = true;
        }
        // Validasi ukuran file (max 5MB)
        else if ($file_size > 5242880) {
            $pesan = "Ukuran file terlalu besar! Maksimal 5MB.";
            $tipe_pesan = "error";
            $upload_error = true;
        } else {
            // Generate nama file unik
            $foto = 'barang_' . time() . '_' . uniqid() . '.' . $file_ext;

            // Upload file
            if (move_uploaded_file($file_tmp, 'assets/img/' . $foto)) {
                // Hapus foto lama
                if ($foto_lama != 'default.jpg' && file_exists("assets/img/$foto_lama")) {
                    unlink("assets/img/$foto_lama");
                }
            } else {
                $pesan = "Gagal mengupload foto!";
                $tipe_pesan = "error";
                $upload_error = true;
                $foto = $foto_lama;
            }
        }
    }

    // Update database jika tidak ada error upload
    if (!$upload_error) {
        $query = "UPDATE tabel_barang SET 
                  nama_barang='$nama', kode_barang='$kode', stok='$stok', harga_satuan='$harga', 
                  foto_barang='$foto', deskripsi_barang='$deskripsi', id_kategori='$id_kategori', id_supplier='$id_supplier'
                  WHERE id_barang='$id'";

        if (mysqli_query($conn, $query)) {
            echo "<script>
                    window.location='barang.php';
                    sessionStorage.setItem('notif_pesan', 'Data barang berhasil diperbarui!');
                    sessionStorage.setItem('notif_tipe', 'success');
                  </script>";
            exit;
        } else {
            $pesan = "Gagal memperbarui data: " . mysqli_error($conn);
            $tipe_pesan = "error";

            // Hapus foto baru jika gagal update database
            if ($foto != $foto_lama && $foto != 'default.jpg' && file_exists('assets/img/' . $foto)) {
                unlink('assets/img/' . $foto);
            }
        }
    }

    // Reload data jika ada perubahan
    $data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tabel_barang WHERE id_barang='$id'"));
}

include 'layout/header.php';
include 'layout/sidebar.php';
?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Edit Data Barang</h6>
    </div>
    <div class="card-body">
        <form method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="foto_lama" value="<?= $data['foto_barang']; ?>">

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nama Barang</label>
                        <input type="text" name="nama_barang" class="form-control" value="<?= $data['nama_barang']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Kode Barang</label>
                        <input type="text" name="kode_barang" class="form-control" value="<?= $data['kode_barang']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="id_kategori" class="form-control" required>
                            <?php
                            $kat = mysqli_query($conn, "SELECT * FROM tabel_kategori");
                            while ($k = mysqli_fetch_array($kat)) {
                                // Cek jika id kategori sama dengan data barang, maka tambahkan attribute 'selected'
                                $selected = ($k['id_kategori'] == $data['id_kategori']) ? "selected" : "";
                                echo "<option value='$k[id_kategori]' $selected>$k[nama_kategori]</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Supplier</label>
                        <select name="id_supplier" class="form-control" required>
                            <?php
                            $sup = mysqli_query($conn, "SELECT * FROM tabel_supplier");
                            while ($s = mysqli_fetch_array($sup)) {
                                $selected = ($s['id_supplier'] == $data['id_supplier']) ? "selected" : "";
                                echo "<option value='$s[id_supplier]' $selected>$s[nama_supplier]</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Stok</label>
                        <input type="number" name="stok" class="form-control" value="<?= $data['stok']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Harga</label>
                        <input type="number" name="harga" class="form-control" value="<?= $data['harga_satuan']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Ganti Foto Barang</label>
                        <div class="custom-file">
                            <input type="file" name="foto" class="custom-file-input" id="inputFoto" accept="image/*" onchange="previewFoto(this)">
                            <label class="custom-file-label" for="inputFoto">Pilih foto baru...</label>
                        </div>
                        <small class="text-muted d-block mt-1">Kosongkan jika tidak ingin mengganti foto. Format: JPG, PNG, GIF (Max 5MB)</small>
                        <div class="mt-3">
                            <img id="previewImage" src="assets/img/<?= $data['foto_barang']; ?>" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi_barang" class="form-control" rows="3"><?= $data['deskripsi_barang']; ?></textarea>
                    </div>
                </div>
            </div>

            <button type="submit" name="update" class="btn btn-primary">
                <i class="fas fa-save"></i> Perbarui Data
            </button>
            <a href="barang.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </form>
    </div>
</div>

<?php if ($pesan != ""): ?>
    <script>
        $(document).ready(function() {
            showToast('<?= $pesan; ?>', '<?= $tipe_pesan; ?>');
        });
    </script>
<?php endif; ?>

<script>
    // Preview foto sebelum upload
    function previewFoto(input) {
        const file = input.files[0];
        if (file) {
            const fileName = file.name;
            $(input).next('.custom-file-label').html(fileName);

            // Validasi ukuran file
            if (file.size > 5242880) {
                showToast('Ukuran file terlalu besar! Maksimal 5MB.', 'error');
                input.value = '';
                $(input).next('.custom-file-label').html('Pilih foto baru...');
                return;
            }

            // Validasi tipe file
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!allowedTypes.includes(file.type)) {
                showToast('Format file tidak didukung! Gunakan JPG, PNG, atau GIF.', 'error');
                input.value = '';
                $(input).next('.custom-file-label').html('Pilih foto baru...');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                $('#previewImage').attr('src', e.target.result);
            }
            reader.readAsDataURL(file);
        } else {
            $(input).next('.custom-file-label').html('Pilih foto baru...');
        }
    }
</script>

<?php include 'layout/footer.php'; ?>