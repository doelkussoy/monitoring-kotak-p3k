<?php
session_start();
include "db.php";

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'Admin') {
    header("Location: dashboard.php");
    exit;
}

// Stats for Report
$q_all_locations = mysqli_query($conn, "SELECT l.*, 
    (SELECT COUNT(*) FROM p3k_items WHERE lokasi_id = l.id) as total_items,
    (SELECT COUNT(*) FROM p3k_items WHERE lokasi_id = l.id AND (stok <= min_stok OR tgl_kadaluarsa <= DATE_ADD(CURDATE(), INTERVAL 30 DAY))) as critical_items
    FROM p3k_lokasi l");

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - MONITORING KOTAK P3K</title>
    <link rel="shortcut icon" href="assets/images/cba-text.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <nav class="navbar glass-navbar fixed-top py-2">
        <div class="container-fluid px-4">
            <a href="dashboard.php" class="d-flex align-items-center gap-3 text-decoration-none">
                <img src="assets/images/cba-text.png" alt="Logo CBA" style="height: 35px; width: auto;">
                <div class="brand-text border-start ps-3">
                    <h1 class="h6 mb-0 fw-bold tracking-tight text-primary">MONITORING KOTAK P3K</h1>
                    <p class="text-secondary tiny mb-0 fw-medium" style="font-size: 0.6rem; letter-spacing: 0.05em;">
                        DIGITAL MONITORING SYSTEM</p>
                </div>
            </a>

            <div class="d-flex align-items-center gap-4">
                <nav class="d-none d-md-flex align-items-center gap-1">
                    <a href="dashboard.php" class="nav-link-premium">
                        <i data-lucide="layout-dashboard" style="width:16px;"></i>
                        Dashboard
                    </a>
                    <?php if ($_SESSION['role'] == 'Admin'): ?>
                        <a href="laporan.php" class="nav-link-premium active">
                            <i data-lucide="file-bar-chart" style="width:16px;"></i>
                            Laporan
                        </a>
                        <a href="pengaturan.php" class="nav-link-premium">
                            <i data-lucide="settings" style="width:16px;"></i>
                            Pengaturan
                        </a>
                    <?php endif; ?>
                </nav>

                <div class="vr opacity-10 d-none d-md-block" style="height: 24px;"></div>

                <div class="dropdown">
                    <div class="user-profile-trigger d-flex align-items-center gap-2" data-bs-toggle="dropdown"
                        role="button">
                        <div class="avatar-circle bg-accent-light text-accent rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 36px; height: 36px;">
                            <i data-lucide="user" style="width:18px;"></i>
                        </div>
                        <div class="d-none d-lg-block">
                            <p class="small fw-bold mb-0 lh-1"><?= htmlspecialchars($_SESSION['username']) ?></p>
                            <p class="text-secondary tiny mb-0"><?= $_SESSION['role'] ?></p>
                        </div>
                        <i data-lucide="chevron-down" class="text-secondary" style="width:14px;"></i>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg mt-3 rounded-4 p-2">
                        <li>
                            <a class="dropdown-item rounded-3 small d-flex align-items-center gap-2 py-2"
                                href="logout.php">
                                <i data-lucide="log-out" style="width:16px;" class="text-danger"></i>
                                <span class="fw-medium">Keluar Sistem</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Bottom Nav -->
    <nav class="mobile-bottom-nav desktop-hide">
        <a href="dashboard.php" class="mobile-nav-item">
            <i data-lucide="layout-dashboard"></i>
            <span>Dashboard</span>
        </a>
        <?php if ($_SESSION['role'] == 'Admin'): ?>
            <a href="laporan.php" class="mobile-nav-item active">
                <i data-lucide="file-bar-chart"></i>
                <span>Laporan</span>
            </a>
            <a href="pengaturan.php" class="mobile-nav-item">
                <i data-lucide="settings"></i>
                <span>Atur</span>
            </a>
        <?php endif; ?>
        <a href="logout.php" class="mobile-nav-item text-danger">
            <i data-lucide="log-out"></i>
            <span>Keluar</span>
        </a>
    </nav>

    <div class="container" style="margin-top: 120px; padding-bottom: 50px;">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h2 class="h3 mb-1">Laporan Monitoring Kotak P3K</h2>
                <p class="text-secondary small mb-0">Ringkasan status ketersediaan dan masa kadaluarsa seluruh unit P3K.
                </p>
            </div>
            <button onclick="window.print()" class="btn btn-outline-primary rounded-3">
                <i data-lucide="printer" class="me-2" style="width:16px;"></i>Cetak Laporan
            </button>
        </div>

        <div class="glass-card overflow-hidden">
            <div class="table-responsive">
                <table class="table-premium">
                    <thead>
                        <tr>
                            <th style="width: 40%;">INFORMASI LOKASI</th>
                            <th style="width: 15%;">TOTAL ITEM</th>
                            <th style="width: 15%;">KRITIS</th>
                            <th style="width: 15%;">STATUS</th>
                            <th class="text-end" style="width: 15%;">TERAKHIR UPDATE</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($q_all_locations)): ?>
                            <tr>
                                <td>
                                    <span class="row-title"><?= $row['nama_lokasi'] ?></span>
                                    <span class="row-subtitle">PIC: <?= $row['pic'] ?></span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <i data-lucide="package" class="text-secondary" style="width:14px;"></i>
                                        <span class="fw-medium"><?= $row['total_items'] ?> Item</span>
                                    </div>
                                </td>
                                <td>
                                    <span
                                        class="<?= $row['critical_items'] > 0 ? 'text-danger fw-bold' : 'text-secondary' ?>">
                                        <?= $row['critical_items'] ?> Item
                                    </span>
                                </td>
                                <td>
                                    <?php if ($row['critical_items'] > 0): ?>
                                        <span class="badge-status badge-critical">
                                            <i data-lucide="alert-circle" style="width:12px;"></i>
                                            Butuh Tindakan
                                        </span>
                                    <?php else: ?>
                                        <span class="badge-status badge-success">
                                            <i data-lucide="check-circle" style="width:12px;"></i>
                                            Aman
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="text-secondary small">
                                        <?= date('d/m/Y', strtotime($row['last_update'])) ?>
                                        <div class="tiny" style="font-size: 0.7rem;">
                                            <?= date('H:i', strtotime($row['last_update'])) ?> WIB</div>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>



    <!-- Footer -->
    <footer class="py-4 mt-5 border-top border-light">
        <div class="container-fluid px-4 text-center">
            <p class="text-secondary small mb-0">
                &copy; <?= date('Y') ?> <span class="fw-bold text-primary">PT CBA Chemical Industry</span> | Monitoring Kotak P3K - Team IT Pabrik
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>lucide.createIcons();</script>
    </body>

</html>