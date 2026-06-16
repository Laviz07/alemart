<?php
include '../../auth/auth_check.php';
require_once '../../config/config.php';
require_once '../../config/koneksi.php';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_produk = mysqli_real_escape_string($conn, $_GET['id']);

    $q_cek    = mysqli_query($conn, "SELECT foto_produk FROM produk WHERE id_produk = '$id_produk'");
    $data     = mysqli_fetch_assoc($q_cek);

    if ($data) {
        if (!empty($data['foto_produk']) && file_exists('../../assets/uploads/produk/' . $data['foto_produk'])) {
            unlink('../../assets/uploads/produk/' . $data['foto_produk']);
        }

        if (mysqli_query($conn, "DELETE FROM produk WHERE id_produk = '$id_produk'")) {
            $_SESSION['sukses'] = "Produk berhasil dihapus!";
        } else {
            $_SESSION['error'] = "Gagal menghapus produk: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['error'] = "Data produk tidak ditemukan.";
    }
} else {
    $_SESSION['error'] = "ID produk tidak valid.";
}

header("Location: index.php");
exit();