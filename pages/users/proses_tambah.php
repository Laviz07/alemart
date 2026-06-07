<?php

session_start();

require_once '../../config/config.php';
require_once '../../config/koneksi.php';

/* =========================
   VALIDASI METHOD
========================= */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header("Location: index.php");
    exit;
}

/* =========================
   AMBIL DATA
========================= */
$nama     = trim(htmlspecialchars($_POST['nama']));
$username = trim(htmlspecialchars($_POST['username']));
$password = trim(htmlspecialchars($_POST['password']));
$role     = trim(htmlspecialchars($_POST['role']));

/* =========================
   VALIDASI INPUT
========================= */
if (
    empty($nama) ||
    empty($username) ||
    empty($password) ||
    empty($role)
) {

    $_SESSION['error'] =
        "Semua field wajib diisi.";

    header("Location: tambah.php");
    exit;
}

/* =========================
   CEK USERNAME
========================= */
$username_check = mysqli_query(
    $conn,
    "SELECT * FROM users
     WHERE username = '$username'"
);

if (mysqli_num_rows($username_check) > 0) {

    $_SESSION['error'] =
        "Username sudah digunakan.";

    header("Location: tambah.php");
    exit;
}

/* =========================
   HASH PASSWORD
========================= */
$password_hash =
    password_hash($password, PASSWORD_DEFAULT);


/* =========================
   UPLOAD AVATAR
========================= */
$avatar = null;

if (
    isset($_FILES['avatar']) &&
    $_FILES['avatar']['error'] === 0
) {

    $target_dir =
        "../../assets/uploads/avatar/";

    /* buat folder jika belum ada */
    if (!is_dir($target_dir)) {

        mkdir($target_dir, 0777, true);
    }

    /* validasi ukuran */
    $max_size = 2 * 1024 * 1024;

    if ($_FILES['avatar']['size'] > $max_size) {

        $_SESSION['error'] =
            "Ukuran avatar maksimal 2MB.";

        header("Location: tambah.php");
        exit;
    }

    /* ambil extension */
    $extension =
        strtolower(
            pathinfo(
                $_FILES['avatar']['name'],
                PATHINFO_EXTENSION
            )
        );

    /* validasi extension */
    $allowed =
        ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($extension, $allowed)) {

        $_SESSION['error'] =
            "Format avatar tidak valid.";

        header("Location: tambah.php");
        exit;
    }

    /* generate nama random */
    $avatar =
        uniqid('avatar_', true) .
        '.' .
        $extension;

    $target_file =
        $target_dir . $avatar;

    /* upload file */
    if (
        !move_uploaded_file(
            $_FILES['avatar']['tmp_name'],
            $target_file
        )
    ) {

        $_SESSION['error'] =
            "Gagal upload avatar.";

        header("Location: tambah.php");
        exit;
    }
}

/* =========================
   INSERT USER
========================= */
$query = mysqli_query(
    $conn,
    "INSERT INTO users
    (
        nama,
        username,
        password,
        role,
        avatar
    )
    VALUES
    (
        '$nama',
        '$username',
        '$password_hash',
        '$role',
        " . ($avatar ? "'$avatar'" : "NULL") . "
    )"
);

/* =========================
   RESULT
========================= */
if ($query) {

    $_SESSION['success'] =
        "User berhasil ditambahkan.";
} else {
    /* hapus avatar jika gagal insert */
    if ($avatar && file_exists("../../assets/img/avatar/" . $avatar)) {
        unlink("../../assets/img/avatar/" . $avatar);
    }

    $_SESSION['error'] =
        "Gagal menambahkan user.";
}

header("Location: index.php");
exit;
