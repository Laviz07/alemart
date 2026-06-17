<?php
// Memanggil pengecekan autentikasi agar sistem memastikan pengguna sudah login
include __DIR__ . '/../../auth/auth_check.php';

// Memanggil konfigurasi aplikasi
require_once __DIR__ . '/../../config/config.php';

// Memanggil file koneksi untuk menghubungkan PHP ke database MySQL
require_once __DIR__ . '/../../config/koneksi.php';

// VALIDASI HAK AKSES: Memeriksa apakah role saat ini adalah 'kasir'. Jika ya, akses diblokir.
if ($_SESSION['role'] === 'kasir') {
    $_SESSION['error'] = 'Anda tidak memiliki akses ke halaman ini!';
    header("Location: index.php"); // Mengalihkan pengguna
    exit; // Mematikan eksekusi kode selanjutnya
}

// Menetapkan variabel dasar untuk title dan sidebar aktif
$page_title = 'Edit Kategori';
$page = 'kategori';

// Memastikan parameter 'id' dikirim melalui URL (contoh: edit.php?id=1)
if (!isset($_GET['id'])) {
    header("Location: index.php"); // Jika tidak ada id, kembalikan ke list kategori
    exit;
}

// TYPE CASTING: Mengubah parameter 'id' secara paksa menjadi angka (Integer)
// Ini adalah pengamanan ampuh melawan serangan SQL Injection yang mencoba memanipulasi parameter URL
$id = (int)$_GET['id'];

// Mengambil data kategori dari database berdasarkan ID yang diminta
$query = mysqli_query($conn, "SELECT * FROM kategori WHERE id_kategori = $id");

// Mengonversi hasil query menjadi bentuk array (kolom database -> array asosiatif)
$data = mysqli_fetch_assoc($query);

// Validasi jika ternyata data dengan ID tersebut tidak ditemukan di database
if (!$data) {
    $_SESSION['error'] = 'Kategori tidak ditemukan!';
    header("Location: index.php");
    exit;
}

// Mengecek jika pengguna sudah menekan tombol submit form (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Mengambil nilai input dari form dan menghapus spasi kiri/kanan
    $nama_kategori = trim($_POST['nama_kategori']);

    // Mengecek jika pengguna mengirimkan input yang kosong
    if (empty($nama_kategori)) {
        $_SESSION['error'] = 'Nama kategori wajib diisi!';
    } else {
        // Cek jika nama kategori baru BEDA dengan nama aslinya (berarti ada perubahan nama)
        if ($nama_kategori !== $data['nama_kategori']) {
            // Mencari apakah nama baru yang diketik sudah dimiliki oleh kategori lain
            $cek = mysqli_query($conn, "SELECT id_kategori FROM kategori WHERE nama_kategori = '" . $conn->real_escape_string($nama_kategori) . "'");
            
            // Jika hasilnya > 0, artinya nama baru sudah ada di database (Duplikat)
            if (mysqli_num_rows($cek) > 0) {
                $_SESSION['error'] = 'Nama kategori sudah ada!';
            }
        }
        
        // Jika tidak ada error apa pun yang ditangkap
        if (!isset($_SESSION['error'])) {
            // Menjalankan query UPDATE untuk mengubah data kategori pada ID yang sesuai
            $update = mysqli_query($conn, "UPDATE kategori SET nama_kategori = '" . $conn->real_escape_string($nama_kategori) . "' WHERE id_kategori = $id");
            
            if ($update) {
                $_SESSION['sukses'] = 'Kategori berhasil diupdate!';
                header("Location: index.php"); // Alihkan ke index dengan sukses
                exit;
            } else {
                $_SESSION['error'] = 'Gagal mengupdate kategori!';
            }
        }
    }
}

// Memuat header, navbar, dan sidebar UI
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';
include __DIR__ . '/../../includes/sidebar.php';
?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Edit Kategori</h2>
            <p class="text-muted mb-0">Ubah data kategori</p>
        </div>
        <!-- Tombol kembali ke tabel kategori -->
        <a href="index.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Menampilkan peringatan / alert error statis Bootstrap jika proses gagal -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
            <!-- Form pengisian edit data. Atribut value diisi dengan data asli dari tabel database -->
            <!-- Fungsi htmlspecialchars() melindungi aplikasi dari serangan XSS (Cross Site Scripting) -->
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="nama_kategori" class="form-label">Nama Kategori</label>
                    <input type="text" class="form-control" id="nama_kategori" name="nama_kategori" value="<?= htmlspecialchars($data['nama_kategori']); ?>" required>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-save"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php 
// Memuat penutup layout halaman (footer) dan penampung javascript global
include __DIR__ . '/../../includes/footer.php';
include __DIR__ . '/../../includes/footer_script.php'; 
?>
