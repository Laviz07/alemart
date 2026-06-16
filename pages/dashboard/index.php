<?php
include '../../auth/auth_check.php';
require_once '../../config/config.php';
require_once '../../config/koneksi.php';

$page_title = 'Dashboard';
$page = 'dashboard';
include '../../includes/header.php';
include '../../includes/navbar.php';
include '../../includes/sidebar.php';

// Get statistics
$total_produk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM produk"))['total'];
$total_kategori = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM kategori"))['total'];
$total_supplier = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM supplier"))['total'];
$total_transaksi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi WHERE DATE(tanggal_transaksi) = CURDATE()"))['total'];
$total_pendapatan_hari_ini = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_harga) as total FROM transaksi WHERE DATE(tanggal_transaksi) = CURDATE()"))['total'];
$total_pendapatan_hari_ini = $total_pendapatan_hari_ini ? $total_pendapatan_hari_ini : 0;

$stok_tipis_query = mysqli_query($conn, "SELECT nama_produk, stok FROM produk WHERE stok <= 10 ORDER BY stok ASC LIMIT 5");

// Fetch Revenue
$days = isset($_GET['days']) ? (int)$_GET['days'] : 7;
if (!in_array($days, [7, 14, 30, 90])) {
    $days = 7;
}
$interval = $days - 1;

$grafik_pendapatan_query = mysqli_query($conn, "
    SELECT DATE(tanggal_transaksi) as tanggal, SUM(total_harga) as total_pendapatan 
    FROM transaksi 
    WHERE tanggal_transaksi >= DATE_SUB(CURDATE(), INTERVAL $interval DAY) 
    GROUP BY DATE(tanggal_transaksi) 
    ORDER BY DATE(tanggal_transaksi) ASC
");

$dates_data = [];
for ($i = $interval; $i >= 0; $i--) {
    $date_val = date('Y-m-d', strtotime("-$i days"));
    $dates_data[$date_val] = 0;
}
if ($grafik_pendapatan_query) {
    while($row = mysqli_fetch_assoc($grafik_pendapatan_query)) {
        $dates_data[$row['tanggal']] = (float)$row['total_pendapatan'];
    }
}
$label_grafik = json_encode(array_map(function($d) { return date('d M', strtotime($d)); }, array_keys($dates_data)));
$data_grafik = json_encode(array_values($dates_data));

// Calculate highest and lowest
$highest_val = -1;
$highest_date = '-';
$lowest_val = PHP_INT_MAX;
$lowest_date = '-';

foreach ($dates_data as $d => $val) {
    if ($val > $highest_val) {
        $highest_val = $val;
        $highest_date = $d;
    }
    if ($val < $lowest_val) {
        $lowest_val = $val;
        $lowest_date = $d;
    }
}
if ($highest_val == -1) $highest_val = 0;
if ($lowest_val == PHP_INT_MAX) $lowest_val = 0;
?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">Dashboard</h2>
            <p class="text-muted mb-0">Selamat datang kembali, <?= htmlspecialchars($_SESSION['nama'] ?? 'User'); ?>!</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1 text-white-50">Total Pendapatan (Hari Ini)</p>
                            <h3 class="mb-0 fw-bold">Rp <?= number_format($total_pendapatan_hari_ini, 0, ',', '.'); ?></h3>
                        </div>
                        <div class="fs-1 text-white-50">
                            <i class="bi bi-wallet2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1 text-white-50">Transaksi Hari Ini</p>
                            <h3 class="mb-0 fw-bold"><?= $total_transaksi; ?></h3>
                        </div>
                        <div class="fs-1 text-white-50">
                            <i class="bi bi-cart-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1 text-white-50">Total Produk</p>
                            <h3 class="mb-0 fw-bold"><?= $total_produk; ?></h3>
                        </div>
                        <div class="fs-1 text-white-50">
                            <i class="bi bi-box-seam"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1 text-white-50">Total Kategori</p>
                            <h3 class="mb-0 fw-bold"><?= $total_kategori; ?></h3>
                        </div>
                        <div class="fs-1 text-white-50">
                            <i class="bi bi-tags"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Warning / Stok Tipis -->
    <div class="row g-4">
        <div class="col-12 col-xl-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h5 class="fw-bold mb-0">Peringatan Stok Tipis</h5>
                </div>
                <div class="card-body">
                    <?php if (mysqli_num_rows($stok_tipis_query) > 0): ?>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Nama Produk</th>
                                        <th class="text-end">Sisa Stok</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = mysqli_fetch_assoc($stok_tipis_query)): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['nama_produk']); ?></td>
                                            <td class="text-end">
                                                <span class="badge bg-danger px-3 py-2 fs-6"><?= $row['stok']; ?></span>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-end mt-2">
                            <a href="<?= BASE_URL; ?>/pages/produk/index.php" class="text-decoration-none">Lihat semua produk <i class="bi bi-arrow-right"></i></a>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-check-circle fs-1 text-success d-block mb-2"></i>
                            Stok produk aman.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Grafik Pendapatan -->
        <div class="col-12 col-xl-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-4 pb-0 d-flex justify-content-between align-items-center flex-wrap">
                    <h5 class="fw-bold mb-2 mb-md-0">Grafik Pendapatan (<?= $days; ?> Hari)</h5>
                    <form method="GET" action="">
                        <select name="days" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="7" <?= $days == 7 ? 'selected' : ''; ?>>7 Hari</option>
                            <option value="14" <?= $days == 14 ? 'selected' : ''; ?>>14 Hari</option>
                            <option value="30" <?= $days == 30 ? 'selected' : ''; ?>>30 Hari</option>
                            <option value="90" <?= $days == 90 ? 'selected' : ''; ?>>90 Hari</option>
                        </select>
                    </form>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between flex-wrap mb-3 text-muted small">
                        <div class="mb-1"><i class="bi bi-arrow-up-circle text-success"></i> Tertinggi: <span class="fw-bold text-dark"><?= $highest_date !== '-' ? date('d M Y', strtotime($highest_date)) : '-'; ?></span> (Rp <?= number_format($highest_val, 0, ',', '.'); ?>)</div>
                        <div class="mb-1"><i class="bi bi-arrow-down-circle text-danger"></i> Terendah: <span class="fw-bold text-dark"><?= $lowest_date !== '-' ? date('d M Y', strtotime($lowest_date)) : '-'; ?></span> (Rp <?= number_format($lowest_val, 0, ',', '.'); ?>)</div>
                    </div>
                    <div style="position: relative; height: 300px; width: 100%;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<?php
include '../../includes/footer.php';
include '../../includes/footer_script.php';
?>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    
    // Gradient untuk area di bawah line chart
    let gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(13, 110, 253, 0.5)'); // primary color with opacity
    gradient.addColorStop(1, 'rgba(13, 110, 253, 0.0)');

    const revenueChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= $label_grafik; ?>,
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: <?= $data_grafik; ?>,
                backgroundColor: gradient,
                borderColor: 'rgba(13, 110, 253, 1)',
                borderWidth: 3,
                pointBackgroundColor: '#fff',
                pointBorderColor: 'rgba(13, 110, 253, 1)',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                fill: true,
                tension: 0.4 // Membuat garis melengkung yang mulus
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false // Sembunyikan legend agar lebih bersih
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: { size: 14, weight: 'bold' },
                    bodyFont: { size: 14 },
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)',
                        drawBorder: false
                    },
                    ticks: {
                        callback: function(value, index, values) {
                            if (value >= 1000000) {
                                return 'Rp ' + (value / 1000000) + ' Jt';
                            } else if (value >= 1000) {
                                return 'Rp ' + (value / 1000) + ' rb';
                            }
                            return 'Rp ' + value;
                        }
                    }
                },
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    }
                }
            }
        }
    });
});
</script>
