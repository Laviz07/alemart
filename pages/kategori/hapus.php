<?php
// Memuat konfigurasi utama
require_once __DIR__ . '/../../config/config.php';
// Memuat koneksi database
require_once __DIR__ . '/../../config/koneksi.php';

// Memanggil file autentikasi agar keamanan aktif (juga mencegah bug Vercel)
include __DIR__ . '/../../auth/auth_check.php';

// VALIDASI HAK AKSES: Memeriksa apakah role saat ini adalah 'kasir'. Jika ya, akses penghapusan diblokir.
if ($_SESSION['role'] === 'kasir') {
    $_SESSION['error'] = 'Anda tidak memiliki akses ke halaman ini!';
    header("Location: index.php");
    exit;
}

// Memastikan bahwa parameter 'id' selalu dibawa (contoh: hapus.php?id=3)
// Jika parameter id tidak ada, sistem mencegah error undefined variable dengan mengembalikan user
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

// TYPE CASTING: Keamanan tambahan mencegah SQL Injection dengan memaksa ID berbentuk numerik/angka
$id = (int)$_GET['id'];

// INTEGRITAS DATA (Foreign Key Protection Manual)
// Memeriksa tabel `produk` untuk mencari apakah ada produk yang masih ditautkan ke ID Kategori ini
$cek_produk = mysqli_query($conn, "SELECT id_produk FROM produk WHERE id_kategori = $id");

// Jika mysqli_num_rows > 0, artinya masih ada minimal 1 produk yang menggunakannya
if (mysqli_num_rows($cek_produk) > 0) {
    // Pengguna dilarang menghapus karena bisa menyebabkan error relasi database ("orphan records")
    $_SESSION['error'] = 'Kategori tidak dapat dihapus karena masih digunakan pada produk!';
    header("Location: index.php");
    exit; // Berhenti memproses eksekusi query hapus
}

// Mengeksekusi Query Hapus: Menghapus baris pada tabel kategori di database
$query = mysqli_query($conn, "DELETE FROM kategori WHERE id_kategori = $id");

// Mengecek keberhasilan proses penghapusan
if ($query) {
    // Pesan jika baris berhasil dicopot dari tabel
    $_SESSION['sukses'] = 'Kategori berhasil dihapus!';
} else {
    // Pesan jika terjadi masalah koneksi atau query
    $_SESSION['error'] = 'Gagal menghapus kategori!';
}

// Setelah selesai (baik sukses/gagal), wajib mengembalikan user ke halaman utama (list)
header("Location: index.php");
exit;
