<?php
$server = "localhost";
$user = "root";
$pass = "";
$database = "db_inventaris_kampus"; // Harus sama dengan nama database di phpMyAdmin

$conn = mysqli_connect($server, $user, $pass, $database);

if (!$conn) {
    die("<div style='padding:20px;background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;border-radius:5px;margin:20px;font-family:Arial;'>
            <h3><i class='fas fa-exclamation-triangle'></i> Koneksi Database Gagal!</h3>
            <p>Tidak dapat terhubung ke database. Periksa konfigurasi di <code>config/koneksi.php</code></p>
         </div>");
}
