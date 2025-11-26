<?php
session_start();
include 'config/koneksi.php';

// Cek apakah tombol login ditekan
if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']); // Enkripsi inputan user dengan MD5 agar cocok dengan database

    // Cek username dan password di tabel_user
    $sql = "SELECT * FROM tabel_user WHERE username='$username' AND password='$password' AND status_aktif='1'";
    $result = mysqli_query($conn, $sql);
    $cek = mysqli_num_rows($result);

    if ($cek > 0) {
        $data = mysqli_fetch_assoc($result);

        // Simpan data user ke session (PENTING!)
        $_SESSION['username'] = $username;
        $_SESSION['nama_lengkap'] = $data['nama_lengkap'];
        $_SESSION['role'] = $data['role'];
        $_SESSION['status'] = "sudah_login";
        $_SESSION['id_user'] = $data['id_user'];

        // Arahkan ke halaman utama (Dashboard)
        header("location:index.php");
    } else {
        $error_login = true;
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Inventaris Barang</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: 'Nunito', sans-serif;
        }

        .login-card {
            width: 420px;
            border-radius: 15px;
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .login-header i {
            font-size: 3rem;
            margin-bottom: 10px;
        }

        .login-body {
            padding: 30px;
        }

        .form-control:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px;
            font-weight: bold;
            transition: transform 0.2s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .input-group-text {
            background-color: #f8f9fc;
            border-right: none;
        }

        .form-control {
            border-left: none;
        }
    </style>
</head>

<body>

    <div class="card login-card shadow-lg">
        <div class="login-header">
            <i class="fas fa-boxes"></i>
            <h3 class="mb-0">Sistem Inventaris</h3>
            <small>Masuk ke Akun Anda</small>
        </div>
        <div class="login-body">
            <?php if (isset($error_login)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong><i class="fas fa-exclamation-circle"></i> Login Gagal!</strong><br>
                    Username atau Password salah!
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            <?php endif; ?>
            <form action="" method="POST">
                <div class="form-group">
                    <label><i class="fas fa-user mr-2"></i>Username</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                        </div>
                        <input type="text" name="username" class="form-control" placeholder="Masukkan username" required autofocus>
                    </div>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-lock mr-2"></i>Password</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        </div>
                        <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                    </div>
                </div>
                <button type="submit" name="login" class="btn btn-primary btn-block btn-login">
                    <i class="fas fa-sign-in-alt mr-2"></i>Masuk
                </button>
            </form>

            <div class="mt-4 text-center">
                <small class="text-muted">
                    <i class="fas fa-info-circle"></i> Demo: <strong>admin</strong> | <strong>12345</strong>
                </small>
            </div>
        </div>
        <div class="card-footer text-center text-muted bg-light">
            <small>&copy; 2025 Sistem Inventaris Barang</small>
        </div>
    </div>

</body>

</html>