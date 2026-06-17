<?php
// Memanggil pengecekan autentikasi agar hanya user yang login yang bisa masuk
include __DIR__ . '/../../auth/auth_check.php';

// Memanggil konfigurasi dasar (seperti konstanta aplikasi)
require_once __DIR__ . '/../../config/config.php';

// Memanggil koneksi database
require_once __DIR__ . '/../../config/koneksi.php';

// VALIDASI HAK AKSES: Memblokir akses jika user yang login adalah 'kasir'
if ($_SESSION['role'] === 'kasir') {
    $_SESSION['error'] = 'Anda tidak memiliki akses ke halaman ini!';
    header("Location: index.php"); // Lempar kembali ke halaman kategori awal
    exit; // Hentikan eksekusi kode di bawahnya
}

// Variabel untuk kebutuhan judul header dan indikator sidebar
$page_title = 'Tambah Kategori';
$page = 'kategori';

// Mengecek apakah ada data form yang dikirim menggunakan metode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Menghapus spasi kosong di awal dan di akhir teks (mencegah input nakal)
    $nama_kategori = trim($_POST['nama_kategori']);

    // Validasi apakah inputan kosong setelah di-trim
    if (empty($nama_kategori)) {
        $_SESSION['error'] = 'Nama kategori wajib diisi!';
    } else {
        // VALIDASI DUPLIKASI: Mengecek ke tabel apakah nama kategori sudah digunakan sebelumnya
        // mysqli_real_escape_string digunakan untuk MENCEGAH serangan SQL Injection
        $cek = mysqli_query($conn, "SELECT id_kategori FROM kategori WHERE nama_kategori = '" . $conn->real_escape_string($nama_kategori) . "'");
        
        // Menghitung jumlah baris yang ditemukan. Jika > 0 berarti nama tersebut sudah ada.
        if (mysqli_num_rows($cek) > 0) {
            $_SESSION['error'] = 'Nama kategori sudah ada!';
        } else {
            // Mengeksekusi perintah penambahan data (INSERT) ke dalam tabel database
            $query = mysqli_query($conn, "INSERT INTO kategori (nama_kategori) VALUES ('" . $conn->real_escape_string($nama_kategori) . "')");
            
            // Jika eksekusi tambah data berhasil
            if ($query) {
                $_SESSION['sukses'] = 'Kategori berhasil ditambahkan!';
                header("Location: index.php"); // Redirect ke tabel list kategori
                exit; // Hentikan proses
            } else {
                $_SESSION['error'] = 'Gagal menambahkan kategori!';
            }
        }
    }
}

// Memuat komponen User Interface (Header, Navbar Atas, Sidebar Kiri)
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';
include __DIR__ . '/../../includes/sidebar.php';
?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Tambah Kategori</h2>
            <p class="text-muted mb-0">Tambahkan kategori produk baru</p>
        </div>
        <!-- Tombol kembali ke halaman list kategori -->
        <a href="index.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Menampilkan pesan error (jika ada) via Bootstrap Alert -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
            <!-- Form untuk input. action="" berarti data disubmit ke file ini sendiri -->
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="nama_kategori" class="form-label">Nama Kategori</label>
                    <input type="text" class="form-control" id="nama_kategori" name="nama_kategori" required>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php 
// Memuat komponen penutup User Interface dan script JS Bootstrap
include __DIR__ . '/../../includes/footer.php';
include __DIR__ . '/../../includes/footer_script.php'; 
?>
