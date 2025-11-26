<?php
session_start();
include 'config/koneksi.php';
if ($_SESSION['status'] != "sudah_login") {
    header("location:login.php");
}

$pesan = "";
$tipe_pesan = "";

// --- LOGIC SIMPAN ---
if (isset($_POST['simpan'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_barang']);
    $kode = mysqli_real_escape_string($conn, $_POST['kode_barang']);
    $stok = intval($_POST['stok']);
    $harga = intval($_POST['harga']);
    $id_kategori = intval($_POST['id_kategori']);
    $id_supplier = intval($_POST['id_supplier']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi_barang']);

    // Validasi dan Upload Foto
    $foto = 'default.jpg';
    $upload_error = false;

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
            if (!move_uploaded_file($file_tmp, 'assets/img/' . $foto)) {
                $pesan = "Gagal mengupload foto!";
                $tipe_pesan = "error";
                $upload_error = true;
                $foto = 'default.jpg';
            }
        }
    }

    // Simpan ke database jika tidak ada error upload
    if (!$upload_error) {
        $query = "INSERT INTO tabel_barang (nama_barang, kode_barang, stok, harga_satuan, foto_barang, deskripsi_barang, id_kategori, id_supplier) 
                  VALUES ('$nama', '$kode', '$stok', '$harga', '$foto', '$deskripsi', '$id_kategori', '$id_supplier')";

        if (mysqli_query($conn, $query)) {
            echo "<script>
                    window.location='barang.php';
                    sessionStorage.setItem('notif_pesan', 'Barang berhasil ditambahkan!');
                    sessionStorage.setItem('notif_tipe', 'success');
                  </script>";
            exit;
        } else {
            $pesan = "Gagal menyimpan data: " . mysqli_error($conn);
            $tipe_pesan = "error";

            // Hapus foto jika gagal simpan database
            if ($foto != 'default.jpg' && file_exists('assets/img/' . $foto)) {
                unlink('assets/img/' . $foto);
            }
        }
    }
}

include 'layout/header.php';
include 'layout/sidebar.php';
?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Input Barang Masuk</h6>
    </div>
    <div class="card-body">
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nama Barang</label>
                        <input type="text" name="nama_barang" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Kode Barang</label>
                        <input type="text" name="kode_barang" class="form-control" placeholder="Contoh: LPT-001" required>
                    </div>
                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="id_kategori" class="form-control" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php
                            $kat = mysqli_query($conn, "SELECT * FROM tabel_kategori WHERE status='aktif'");
                            while ($k = mysqli_fetch_array($kat)) {
                                echo "<option value='$k[id_kategori]'>$k[nama_kategori]</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Supplier</label>
                        <select name="id_supplier" class="form-control" required>
                            <option value="">-- Pilih Supplier --</option>
                            <?php
                            $sup = mysqli_query($conn, "SELECT * FROM tabel_supplier");
                            while ($s = mysqli_fetch_array($sup)) {
                                echo "<option value='$s[id_supplier]'>$s[nama_supplier]</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Stok Awal</label>
                        <input type="number" name="stok" class="form-control" value="0" required>
                    </div>
                    <div class="form-group">
                        <label>Harga Satuan (Rp)</label>
                        <input type="number" name="harga" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Foto Barang</label>
                        <div class="custom-file">
                            <input type="file" name="foto" class="custom-file-input" id="inputFoto" accept="image/*" onchange="previewFoto(this)">
                            <label class="custom-file-label" for="inputFoto">Pilih foto...</label>
                        </div>
                        <small class="text-muted d-block mt-1">Format: JPG, PNG, GIF (Max 5MB)</small>
                        <div class="mt-3" id="previewContainer" style="display: none;">
                            <img id="previewImage" src="" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi_barang" class="form-control" rows="3"></textarea>
                    </div>
                </div>
            </div>

            <button type="submit" name="simpan" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan Barang
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
                $(input).next('.custom-file-label').html('Pilih foto...');
                $('#previewContainer').hide();
                return;
            }

            // Validasi tipe file
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!allowedTypes.includes(file.type)) {
                showToast('Format file tidak didukung! Gunakan JPG, PNG, atau GIF.', 'error');
                input.value = '';
                $(input).next('.custom-file-label').html('Pilih foto...');
                $('#previewContainer').hide();
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                $('#previewImage').attr('src', e.target.result);
                $('#previewContainer').fadeIn();
            }
            reader.readAsDataURL(file);
        } else {
            $('#previewContainer').hide();
            $(input).next('.custom-file-label').html('Pilih foto...');
        }
    }
</script>

<?php include 'layout/footer.php'; ?>