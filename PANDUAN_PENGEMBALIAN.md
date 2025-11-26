# Panduan Instalasi Fitur Pengembalian Barang

## 1. Database Setup

### Langkah 1: Buka phpMyAdmin

- Akses phpMyAdmin melalui browser: `http://localhost/phpmyadmin`
- Login dengan user MySQL Anda

### Langkah 2: Pilih Database

- Klik database sistem inventaris Anda (biasanya `db_inventaris` atau sesuai nama di `config/koneksi.php`)

### Langkah 3: Jalankan SQL

- Klik tab **SQL** di bagian atas
- Buka file `database_pengembalian.sql` dengan text editor
- Copy semua isi file tersebut
- Paste ke kolom SQL di phpMyAdmin
- Klik tombol **Go** atau **Kirim**

### Verifikasi Database

Setelah berhasil, akan muncul 2 tabel baru:

- `tabel_barang_kembali` - Menyimpan header transaksi pengembalian
- `tabel_detail_kembali` - Menyimpan detail barang yang dikembalikan

## 2. Struktur Tabel

### tabel_barang_kembali

| Kolom                | Tipe         | Keterangan                           |
| -------------------- | ------------ | ------------------------------------ |
| id_kembali           | INT          | Primary Key, Auto Increment          |
| no_transaksi_kembali | VARCHAR(50)  | No transaksi unik (RETURN-timestamp) |
| tanggal_kembali      | DATE         | Tanggal pengembalian                 |
| nama_pengembalian    | VARCHAR(100) | Nama orang yang mengembalikan        |
| kondisi_barang       | ENUM         | baik / kurang_baik / rusak           |
| keterangan           | TEXT         | Catatan kondisi                      |
| id_user              | INT          | Foreign Key ke tabel_user (petugas)  |
| created_at           | TIMESTAMP    | Waktu input otomatis                 |

### tabel_detail_kembali

| Kolom             | Tipe | Keterangan                          |
| ----------------- | ---- | ----------------------------------- |
| id_detail_kembali | INT  | Primary Key, Auto Increment         |
| id_kembali        | INT  | Foreign Key ke tabel_barang_kembali |
| id_barang         | INT  | Foreign Key ke tabel_barang         |
| jumlah_kembali    | INT  | Jumlah barang yang dikembalikan     |

## 3. Fitur yang Tersedia

### Menu Pengembalian Barang (barang_kembali.php)

- Menampilkan riwayat semua pengembalian barang
- Menampilkan:
  - No Transaksi
  - Tanggal Pengembalian
  - Nama Pengembalian
  - Barang yang dikembalikan
  - Jumlah
  - Kondisi (Baik/Rusak/Kurang Baik)
  - Petugas yang input
- Tombol untuk input pengembalian baru

### Form Input Pengembalian (tambah_barang_kembali.php)

- Form untuk mencatat barang yang dikembalikan
- Input yang diperlukan:
  - Tanggal Pengembalian (default hari ini)
  - Nama Pengembalian (orang yang mengembalikan)
  - Kondisi Barang (Baik/Kurang Baik/Rusak)
  - Pilih Barang dari dropdown
  - Jumlah yang dikembalikan
  - Keterangan (opsional)

### Logika Stok

1. **Kondisi Baik**: Stok barang otomatis bertambah
2. **Kondisi Rusak**: Stok TIDAK bertambah (barang dicatat sebagai rusak)
3. **Kondisi Kurang Baik**: Stok TIDAK bertambah (perlu perbaikan)

## 4. Cara Menggunakan

### Alur Penggunaan:

1. Login ke sistem sebagai Admin atau Petugas
2. Klik menu **Pengembalian Barang** di sidebar
3. Klik tombol **Input Pengembalian**
4. Isi form:
   - Tanggal pengembalian
   - Nama orang yang mengembalikan
   - Pilih kondisi barang (PENTING!)
   - Pilih barang dari dropdown
   - Masukkan jumlah
   - Tambahkan keterangan jika perlu
5. Klik **Proses Pengembalian**
6. Sistem akan:
   - Membuat no transaksi otomatis (RETURN-xxxxx)
   - Menyimpan data pengembalian
   - Menambah stok jika kondisi baik
   - Menampilkan notifikasi sukses

## 5. Notifikasi

Sistem menggunakan toast notification untuk feedback:

- **Success (Hijau)**: Pengembalian berhasil
- **Error (Merah)**: Jika terjadi kesalahan
- Notifikasi otomatis muncul setelah proses

## 6. Contoh Penggunaan

### Contoh 1: Pengembalian Barang Baik

- Tanggal: 15/01/2025
- Nama Pengembalian: Budi Santoso
- Kondisi: Baik
- Barang: Laptop Dell Latitude
- Jumlah: 2
- Keterangan: Kondisi masih sangat baik
- **Hasil**: Stok laptop bertambah 2 unit

### Contoh 2: Pengembalian Barang Rusak

- Tanggal: 16/01/2025
- Nama Pengembalian: Siti Nurhaliza
- Kondisi: Rusak
- Barang: Mouse Wireless
- Jumlah: 1
- Keterangan: Tombol klik kanan tidak berfungsi
- **Hasil**: Data tercatat, stok TIDAK bertambah

## 7. Tips Penggunaan

1. **Pilih kondisi dengan teliti**: Kondisi menentukan apakah stok bertambah
2. **Catat keterangan**: Jelaskan kondisi atau alasan pengembalian
3. **Cek stok real-time**: Setelah pengembalian, cek stok di menu Data Barang
4. **Filter laporan**: Gunakan DataTables untuk mencari riwayat tertentu

## 8. Troubleshooting

### Error: "Table doesn't exist"

- Pastikan file SQL sudah dijalankan di phpMyAdmin
- Refresh database di phpMyAdmin

### Stok tidak bertambah

- Cek kondisi yang dipilih, hanya kondisi "Baik" yang menambah stok
- Lihat keterangan di notifikasi sukses

### Data tidak muncul

- Refresh halaman (F5)
- Cek apakah transaksi tersimpan di phpMyAdmin (cek tabel_barang_kembali)

## 9. Keamanan

- Hanya user yang sudah login dapat akses
- Sistem mencatat siapa yang input pengembalian (id_user)
- No transaksi unik otomatis (RETURN-timestamp)

---

**Catatan**: Fitur ini terintegrasi penuh dengan sistem inventaris yang ada dan menggunakan notifikasi modern (toast) tanpa alert browser.
