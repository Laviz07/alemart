<?php
// UBAH INI: Isi dengan data yang kamu buat di Menu MySQL cPanel Rumahweb
$host     = "localhost"; // Di Rumahweb selalu localhost
$username = "alen8254"; // Contoh: alemart_kasir
$password = "TnyxWztDFYss21"; 
$database = "alen8254_alemart";     // Contoh: alemart_db

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}