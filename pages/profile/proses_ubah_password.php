<?php

session_start();

require_once '../../config/config.php';
require_once '../../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header("Location: index.php");
    exit;
}

/* =========================
   AMBIL DATA
========================= */
$id = $_SESSION['id_user'];

$password_lama =
    trim($_POST['password_lama']);

$password_baru =
    trim($_POST['password_baru']);

$konfirmasi_password =
    trim($_POST['konfirmasi_password']);

/* =========================
   VALIDASI
========================= */
if (
    empty($password_lama) ||
    empty($password_baru) ||
    empty($konfirmasi_password)
) {

    $_SESSION['error'] =
        "Semua field wajib diisi.";

    header("Location: ubah_password.php");
    exit;
}

/* =========================
   GET USER
========================= */
$query = mysqli_query(
    $conn,
    "SELECT * FROM users
     WHERE id_user = '$id'"
);

$user = mysqli_fetch_assoc($query);

if (!$user) {

    $_SESSION['error'] =
        "User tidak ditemukan.";

    header("Location: ubah_password.php");
    exit;
}

/* =========================
   CEK PASSWORD LAMA
========================= */
if (
    !password_verify(
        $password_lama,
        $user['password']
    )
) {

    $_SESSION['error'] =
        "Password lama salah.";

    header("Location: ubah_password.php");
    exit;
}

/* =========================
   CEK KONFIRMASI
========================= */
if (
    $password_baru !==
    $konfirmasi_password
) {

    $_SESSION['error'] =
        "Konfirmasi password tidak cocok.";

    header("Location: ubah_password.php");
    exit;
}

/* =========================
   HASH PASSWORD BARU
========================= */
$password_hash =
    password_hash(
        $password_baru,
        PASSWORD_DEFAULT
    );

/* =========================
   UPDATE PASSWORD
========================= */
$update = mysqli_query(
    $conn,
    "UPDATE users SET

        password = '$password_hash'

     WHERE id_user = '$id'"
);

/* =========================
   RESULT
========================= */
if ($update) {

    $_SESSION['success'] =
        "Password berhasil diubah.";
} else {

    $_SESSION['error'] =
        "Gagal mengubah password.";
}

header("Location: index.php");
exit;
