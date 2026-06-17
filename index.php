<?php
session_start();
// Tambahkan __DIR__ agar file config selalu akurat terpanggil
require_once __DIR__ . '/config/config.php';

if (isset($_SESSION['id_user'])) {
    header("Location: " . BASE_URL . "/pages/dashboard/index.php");
} else {
    header("Location: " . BASE_URL . "/auth/login.php");
}
exit;