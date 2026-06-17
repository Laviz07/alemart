<?php
session_start();
require_once __DIR__ . '/../config/config.php';

if (!isset($_SESSION['login'])) {
    // Gunakan BASE_URL agar pengalihan halaman 100% akurat
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}