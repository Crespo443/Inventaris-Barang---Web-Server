-- SQL untuk membuat database dan tabel pengembalian barang
-- Jalankan di phpMyAdmin

-- 0. Membuat Database (jika belum ada)
CREATE DATABASE IF NOT EXISTS `db_inventaris` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `db_inventaris`;

-- 1. Tabel Header Transaksi Pengembalian
CREATE TABLE IF NOT EXISTS `tabel_barang_kembali` (
  `id_kembali` int(11) NOT NULL AUTO_INCREMENT,
  `no_transaksi_kembali` varchar(50) NOT NULL,
  `tanggal_kembali` date NOT NULL,
  `nama_pengembalian` varchar(100) NOT NULL COMMENT 'Nama orang yang mengembalikan',
  `kondisi_barang` enum('baik','kurang_baik','rusak') NOT NULL DEFAULT 'baik',
  `keterangan` text,
  `id_user` int(11) NOT NULL COMMENT 'Petugas yang input',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_kembali`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Tabel Detail Transaksi Pengembalian
CREATE TABLE IF NOT EXISTS `tabel_detail_kembali` (
  `id_detail_kembali` int(11) NOT NULL AUTO_INCREMENT,
  `id_kembali` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `jumlah_kembali` int(11) NOT NULL,
  PRIMARY KEY (`id_detail_kembali`),
  KEY `id_kembali` (`id_kembali`),
  KEY `id_barang` (`id_barang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Foreign Key Constraints
ALTER TABLE `tabel_barang_kembali`
  ADD CONSTRAINT `fk_kembali_user` FOREIGN KEY (`id_user`) REFERENCES `tabel_user` (`id_user`) ON DELETE CASCADE;

ALTER TABLE `tabel_detail_kembali`
  ADD CONSTRAINT `fk_detail_kembali` FOREIGN KEY (`id_kembali`) REFERENCES `tabel_barang_kembali` (`id_kembali`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_detail_barang_kembali` FOREIGN KEY (`id_barang`) REFERENCES `tabel_barang` (`id_barang`) ON DELETE CASCADE;
