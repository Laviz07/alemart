<?php
include '../../auth/auth_check.php';
require_once '../../config/config.php';
require_once '../../config/koneksi.php';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_produk = mysqli_real_escape_string($conn, $_GET['id']);

    $q_cek = mysqli_query($conn, "SELECT foto_produk FROM produk WHERE id_produk = '$id_produk'");
    $data  = mysqli_fetch_assoc($q_cek);

    if ($data) {
        $cek_pembelian = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM detail_pembelian WHERE id_produk = '$id_produk'"));
        $cek_transaksi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM detail_transaksi WHERE id_produk = '$id_produk'"));

        if ($cek_pembelian['total'] > 0 || $cek_transaksi['total'] > 0) {
            $_SESSION['error'] = "Produk tidak bisa dihapus karena sudah digunakan dalam data pembelian atau transaksi.";
        } else {
            if (!empty($data['foto_produk']) && file_exists('../../assets/uploads/produk/' . $data['foto_produk'])) {
                unlink('../../assets/uploads/produk/' . $data['foto_produk']);
            }
            if (mysqli_query($conn, "DELETE FROM produk WHERE id_produk = '$id_produk'")) {
                $_SESSION['success'] = "Produk berhasil dihapus!";
            } else {
                $_SESSION['error'] = "Gagal menghapus produk: " . mysqli_error($conn);
            }
        }
    } else {
        $_SESSION['error'] = "Data produk tidak ditemukan.";
    }
} else {
    $_SESSION['error'] = "ID produk tidak valid.";
}

header("Location: index.php");
exit();