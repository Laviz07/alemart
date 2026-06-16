<?php
include '../../auth/auth_check.php';
require_once '../../config/config.php';
require_once '../../config/koneksi.php';

$page_title = 'Daftar Produk';
$page = 'produk';

$limit = 10;
$current_page = isset($_GET['page_num']) ? (int) $_GET['page_num'] : 1;
if ($current_page < 1) $current_page = 1;
$offset = ($current_page - 1) * $limit;

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where  = "WHERE 1=1";
if (!empty($search)) {
    $search_esc = $conn->real_escape_string($search);
    $where .= " AND p.nama_produk LIKE '%$search_esc%'";
}

$total_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM produk p $where");
$total_data  = mysqli_fetch_assoc($total_query)['total'];
$total_pages = ceil($total_data / $limit);

$query = mysqli_query($conn, "
    SELECT p.*, k.nama_kategori 
    FROM produk p
    LEFT JOIN kategori k ON p.id_kategori = k.id_kategori
    $where 
    ORDER BY p.id_produk DESC 
    LIMIT $limit OFFSET $offset
");

include '../../includes/header.php';
include '../../includes/navbar.php';
include '../../includes/sidebar.php';
?>

<div class="main-content">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">Produk</h2>
            <p class="text-muted mb-0">Kelola produk AleMart</p>
        </div>
        <a href="tambah.php" class="btn btn-success">
            <i class="bi bi-plus-lg"></i> Tambah Produk
        </a>
    </div>

    <?php if (isset($_SESSION['sukses'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['sukses']; unset($_SESSION['sukses']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">

            <form method="GET" class="row g-3 mb-4" id="filterForm">
                <div class="col-md-9">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" id="searchInput"
                            class="form-control border-start-0"
                            placeholder="Cari nama produk..."
                            value="<?= htmlspecialchars($search); ?>">
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Gambar</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>Satuan</th>
                            <th>Harga Jual</th>
                            <th>Stok</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($query) > 0): ?>
                            <?php $no = $offset + 1;
                            while ($produk = mysqli_fetch_assoc($query)): ?>
                                <tr>
                                    <td><?= $no++; ?></td>

                                    <td>
                                        <?php if (!empty($produk['foto_produk'])): ?>
                                            <img src="<?= BASE_URL; ?>/assets/uploads/produk/<?= htmlspecialchars($produk['foto_produk']); ?>"
                                                 class="rounded-3 object-fit-cover border"
                                                 style="width:52px;height:52px;">
                                        <?php else: ?>
                                            <div class="rounded-3 bg-secondary-subtle text-secondary d-flex align-items-center justify-content-center"
                                                 style="width:52px;height:52px;font-size:20px;">
                                                <i class="bi bi-box-seam"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>

                                    <td class="fw-semibold"><?= htmlspecialchars($produk['nama_produk']); ?></td>

                                    <td>
                                        <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle">
                                            <?= htmlspecialchars($produk['nama_kategori'] ?? '-'); ?>
                                        </span>
                                    </td>

                                    <td><?= htmlspecialchars($produk['satuan']); ?></td>

                                    <td>Rp <?= number_format($produk['harga_jual'], 0, ',', '.'); ?></td>

                                    <td>
                                        <span class="badge <?= $produk['stok'] <= 10 ? 'bg-danger' : 'bg-success'; ?>">
                                            <?= $produk['stok']; ?>
                                        </span>
                                    </td>

                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="edit.php?id=<?= $produk['id_produk']; ?>" class="btn btn-warning btn-sm">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <a href="hapus.php?id=<?= $produk['id_produk']; ?>"
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('Yakin ingin menghapus produk ini?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Data produk tidak ditemukan
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_pages > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-end">
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= ($current_page == $i) ? 'active' : ''; ?>">
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

<?php include '../../includes/footer.php'; ?>

<script>
    const filterForm  = document.getElementById("filterForm");
    const searchInput = document.getElementById("searchInput");
    let searchTimer;
    searchInput.addEventListener("input", () => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => filterForm.submit(), 500);
    });
</script>

<?php include '../../includes/footer_script.php'; ?>