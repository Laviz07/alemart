<?php
include '../../auth/auth_check.php';
require_once '../../config/config.php';
require_once '../../config/koneksi.php';

$page = 'users';
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Tambah User | AleMart</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= BASE_URL; ?>/assets/css/style.css">

</head>

<body>

    <?php
    include '../../includes/navbar.php';
    include '../../includes/sidebar.php';
    ?>

    <div class="main-content">

        <!-- PAGE HEADER -->
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">

            <div>

                <h2 class="fw-bold mb-1">
                    Tambah User
                </h2>

                <p class="text-muted mb-0">
                    Tambahkan akun user baru ke sistem AleMart
                </p>

            </div>

            <a href="index.php"
                class="btn btn-light border">

                <i class="bi bi-arrow-left"></i>
                Kembali

            </a>

        </div>

        <!-- FORM -->
        <form
            action="proses_tambah.php"
            method="POST"
            enctype="multipart/form-data">

            <!-- CARD DATA USER -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">

                <div class="card-body p-4">

                    <div class="d-flex align-items-center gap-2 mb-4">

                        <div class="bg-success bg-opacity-10 text-success rounded-3 d-flex align-items-center justify-content-center"
                            style="width:44px; height:44px;">

                            <i class="bi bi-person-plus fs-5"></i>

                        </div>

                        <div>

                            <h5 class="fw-bold mb-0">
                                Data User
                            </h5>

                            <small class="text-muted">
                                Isi data user dengan benar
                            </small>

                        </div>

                    </div>
                    <?php if (isset($_SESSION['error'])) : ?>

                        <div class="alert alert-danger">

                            <?= $_SESSION['error']; ?>

                        </div>

                    <?php
                        unset($_SESSION['error']);
                    endif;
                    ?>


                    <div class="row g-4">

                        <!-- NAMA -->
                        <div class="col-md-6">

                            <label class="form-label fw-semibold">
                                Nama Lengkap
                            </label>

                            <input
                                type="text"
                                name="nama"
                                class="form-control"
                                placeholder="Masukkan nama lengkap"
                                required>

                        </div>

                        <!-- USERNAME -->
                        <div class="col-md-6">

                            <label class="form-label fw-semibold">
                                Username
                            </label>

                            <input
                                type="text"
                                name="username"
                                class="form-control"
                                placeholder="Masukkan username"
                                required>

                        </div>

                        <!-- PASSWORD -->
                        <div class="col-md-6">

                            <label class="form-label fw-semibold">
                                Password
                            </label>

                            <div class="input-group">

                                <input
                                    type="password"
                                    name="password"
                                    id="password"
                                    class="form-control"
                                    placeholder="Masukkan password"
                                    required>

                                <button
                                    class="btn btn-outline-secondary"
                                    type="button"
                                    id="togglePassword">

                                    <i class="bi bi-eye"></i>

                                </button>

                            </div>

                        </div>

                        <!-- ROLE -->
                        <div class="col-md-6">

                            <label class="form-label fw-semibold">
                                Role
                            </label>

                            <select
                                name="role"
                                class="form-select"
                                required>

                                <option value="">
                                    Pilih Role
                                </option>

                                <option value="admin">
                                    Admin
                                </option>

                                <option value="kasir">
                                    Kasir
                                </option>

                            </select>

                        </div>

                        <!-- AVATAR -->
                        <div class="col-12">

                            <label class="form-label fw-semibold">
                                Avatar User
                            </label>

                            <input
                                type="file"
                                name="avatar"
                                class="form-control"
                                accept=".jpg,.jpeg,.png">

                            <small class="text-muted">
                                Format: JPG, JPEG, PNG
                            </small>

                        </div>

                    </div>

                </div>

            </div>

            <!-- ACTION -->
            <div class="d-flex gap-2 justify-content-end">

                <button
                    type="reset"
                    class="btn btn-light border px-4">

                    Reset

                </button>

                <button
                    type="submit"
                    class="btn btn-success px-4">

                    <i class="bi bi-check-circle"></i>
                    Simpan User

                </button>

            </div>

        </form>

    </div>

    <?php include '../../includes/footer.php'; ?>

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SCRIPT -->
    <script>
        /* =========================
           TOGGLE PASSWORD
        ========================= */
        const togglePassword =
            document.getElementById("togglePassword");

        const password =
            document.getElementById("password");

        togglePassword.addEventListener("click", () => {

            const type =
                password.getAttribute("type") === "password" ?
                "text" :
                "password";

            password.setAttribute("type", type);

            togglePassword.innerHTML =
                type === "password" ?
                '<i class="bi bi-eye"></i>' :
                '<i class="bi bi-eye-slash"></i>';

        });
    </script>

</body>

</html>