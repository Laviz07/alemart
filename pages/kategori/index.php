<?php
// Memanggil autentikasi session pengguna (wajib login)
include __DIR__ . '/../../auth/auth_check.php';

// Memanggil variabel-variabel dasar / URL
require_once __DIR__ . '/../../config/config.php';

// Memanggil koneksi database mysql
require_once __DIR__ . '/../../config/koneksi.php';

// Variabel penanda judul halaman & halaman aktif di sidebar navigasi
$page_title = 'Daftar Kategori';
$page = 'kategori';

// ======== LOGIKA PAGINATION (HALAMAN) ========
// Limit mengatur berapa jumlah data maksimal yang muncul dalam satu halaman (tabel)
$limit = 10;

// Menentukan posisi halaman saat ini dari parameter URL (contoh: ?page_num=2)
// Secara default jika belum ada parameter, anggap berada di halaman 1
$current_page = isset($_GET['page_num']) ? (int) $_GET['page_num'] : 1;
if ($current_page < 1) $current_page = 1;

// Offset menentukan dari urutan data ke-berapa query harus mulai mengambil data
// Contoh: Halaman 2 -> (2 - 1) * 10 = 10 (Artinya lewati 10 data pertama, ambil data urutan 11 dst)
$offset = ($current_page - 1) * $limit;


// ======== LOGIKA PENCARIAN (SEARCHING) ========
// Menangkap kata kunci dari input form cari. Fungsi trim untuk memotong spasi depan/belakang
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Kondisi dasar yang selalu benar (akan digabungkan jika ada search)
$where  = "WHERE 1=1";

if (!empty($search)) {
    // real_escape_string membersihkan huruf berbahaya agar terhindar dari SQL Injection saat mencari
    $search_esc = $conn->real_escape_string($search);
    
    // Menambahkan kondisi SQL: mengambil data dengan kata mirip LIKE
    $where .= " AND nama_kategori LIKE '%$search_esc%'";
}


// ======== MENGHITUNG TOTAL HALAMAN ========
// Query menghitung seluruh baris kategori untuk mencari tau jumlah halamannya nanti
$total_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM kategori $where");
$total_data  = mysqli_fetch_assoc($total_query)['total'];

// Fungsi ceil() membulatkan ke atas angka desimal. Misal: 15 / 10 = 1.5 -> dibulatkan jadi 2 Halaman
$total_pages = ceil($total_data / $limit);


// ======== MENAMPILKAN DATA ========
// Query mengekstrak seluruh data berdasar halaman & pencarian yang sedang aktif
$query = mysqli_query($conn, "
    SELECT * FROM kategori
    $where 
    ORDER BY id_kategori DESC 
    LIMIT $limit OFFSET $offset
");

// Memuat header antarmuka (UI) dari HTML & CSS eksternal
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';
include __DIR__ . '/../../includes/sidebar.php';
?>

<div class="main-content">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">Kategori</h2>
            <p class="text-muted mb-0">Kelola kategori AleMart</p>
        </div>
        
        <!-- MEMBATASI AKSES BUTTON TAMBAH -->
        <!-- Jika role yang sedang aktif bukan 'kasir', maka tombol ini akan dimunculkan di layar -->
        <?php if ($_SESSION['role'] !== 'kasir'): ?>
        <a href="tambah.php" class="btn btn-success">
            <i class="bi bi-plus-lg"></i> Tambah Kategori
        </a>
        <?php endif; ?>
    </div>

    <!-- LOGIKA POP-UP NOTIFIKASI SUKSES (SWEETALERT2) -->
    <!-- Jika dari skrip lain (misal tambah.php) membawa session 'sukses', render alert Javascript -->
    <?php if (isset($_SESSION['sukses'])): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Konfigurasi SweetAlert dengan ikon checklist & hilang dalam 2 detik
                Swal.fire({ icon: 'success', title: 'Berhasil', text: '<?= $_SESSION['sukses']; ?>', showConfirmButton: false, timer: 2000 });
            });
        </script>
        <!-- Hapus nilai session seketika agar saat direfresh, pop-up tak terus muncul -->
        <?php unset($_SESSION['sukses']); ?>
    <?php endif; ?>

    <!-- LOGIKA POP-UP NOTIFIKASI GAGAL/ERROR (SWEETALERT2) -->
    <?php if (isset($_SESSION['error'])): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Konfigurasi SweetAlert dengan ikon tanda silang (error)
                Swal.fire({ icon: 'error', title: 'Oops...', text: '<?= $_SESSION['error']; ?>' });
            });
        </script>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">

            <!-- FORM PENCARIAN -->
            <form method="GET" class="row g-3 mb-4" id="filterForm">
                <div class="col-md-9">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" id="searchInput"
                            class="form-control border-start-0"
                            placeholder="Cari nama kategori..."
                            value="<?= htmlspecialchars($search); ?>">
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th width="10%">No</th>
                            <th width="70%">Nama Kategori</th>
                            <!-- MEMBATASI AKSES JUDUL KOLOM "ACTION" -->
                            <?php if ($_SESSION['role'] !== 'kasir'): ?>
                            <th class="text-center" width="20%">Action</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Pengecekan ada atau tidaknya data yang diambil dari database -->
                        <?php if (mysqli_num_rows($query) > 0): ?>
                            <?php 
                            // Penomoran baris selalu nyambung per halaman ($offset + 1)
                            $no = $offset + 1;
                            
                            // Looping data selama barisnya masih ada (fetch per row jadi array)
                            while ($kategori = mysqli_fetch_assoc($query)): ?>
                                <tr>
                                    <td><?= $no++; ?></td>

                                    <td class="fw-semibold">
                                        <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-3 py-2 fs-6">
                                            <!-- Melindungi output teks kategori dari ancaman XSS -->
                                            <?= htmlspecialchars($kategori['nama_kategori']); ?>
                                        </span>
                                    </td>

                                    <!-- MEMBATASI AKSES TOMBOL EDIT DAN HAPUS -->
                                    <?php if ($_SESSION['role'] !== 'kasir'): ?>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <!-- URL Edit akan membawa param "?id=X" ke halaman edit.php -->
                                            <a href="edit.php?id=<?= $kategori['id_kategori']; ?>" class="btn btn-warning btn-sm">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </a>
                                            <!-- Tombol hapus memicu SweetAlert melalui class "btn-delete" -->
                                            <!-- Atribut data-id & data-nama dilempar ke Javascript -->
                                            <button type="button" class="btn btn-danger btn-sm btn-delete" 
                                                    data-id="<?= $kategori['id_kategori']; ?>" 
                                                    data-nama="<?= htmlspecialchars($kategori['nama_kategori']); ?>">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                        </div>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <!-- DYNAMIC COLSPAN: Menyesuaikan apakah kolom aksi ditiadakan untuk kasir (colspan=2) atau 3 -->
                                <td colspan="<?= ($_SESSION['role'] === 'kasir') ? 2 : 3; ?>" class="text-center py-5 text-muted">
                                    <i class="bi bi-tags fs-1 d-block mb-2"></i>
                                    Data kategori tidak ditemukan
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- LOGIKA MENAMPILKAN TOMBOL NOMOR HALAMAN (PAGINATION) -->
            <?php if ($total_pages > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-end">
                        <!-- Perulangan memunculkan kotak pagination 1, 2, 3 sesuai total_pages -->
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <!-- Menandai nomor yang aktif sekarang menjadi warna biru tua (.active) -->
                            <li class="page-item <?= ($current_page == $i) ? 'active' : ''; ?>">
                                <!-- Menyimpan query URL & search parameter agar cari tidak hilang saat geser page -->
                                <a class="page-link" href="?page_num=<?= $i; ?>&search=<?= urlencode($search); ?>">
                                    <?= $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php 
// Memuat struktur penutup UI HTML
include __DIR__ . '/../../includes/footer.php'; 
?>

<!-- ================= JAVASCRIPT SECTION ================= -->
<script>
    // FUNGSI PENCARIAN REALTIME 
    const filterForm  = document.getElementById("filterForm");
    const searchInput = document.getElementById("searchInput");
    let searchTimer;

    // Menunggu pengetikan user selama 0,5 detik. Jika dia berhenti ngetik (500ms berlalu), baru Form di-submit paksa
    // Ini teknik debounce agar browser tidak memberatkan server MySQL di tiap 1 ketikan
    searchInput.addEventListener("input", () => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => filterForm.submit(), 500);
    });

    // FUNGSI KONFIRMASI HAPUS SWEETALERT2
    const deleteButtons = document.querySelectorAll('.btn-delete');
    
    // Looping & menempel event 'click' pada setiap tombol tong sampah "Hapus"
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Mengambil id & nama yang sebelumnya disuntikkan ke "data-id" dan "data-nama" di atas
            const id   = this.dataset.id;
            const nama = this.dataset.nama;
            
            // Konfigurasi SweetAlert untuk persetujuan ya/tidak (warning message box)
            Swal.fire({
                title: 'Hapus Kategori?',
                text: `Anda akan menghapus kategori "${nama}"`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                // Jika "Ya" dipencet, paksa browser pindah haluan menuju hapus.php
                if (result.isConfirmed) { window.location.href = `hapus.php?id=${id}`; }
            });
        });
    });
</script>

<?php 
// Memuat plugin dan import javascript global pendukung UI (seperti file js sweetalert/bootstrap)
include __DIR__ . '/../../includes/footer_script.php'; 
?>
