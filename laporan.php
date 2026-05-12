<?php
session_start();
include "db.php";

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'Admin') {
    header("Location: dashboard.php");
    exit;
}

// Generate CSRF Token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle Self Change Password
if (isset($_POST['self_change_pass'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }
    $current_pass = $_POST['current_pass'];
    $new_pass = $_POST['new_pass'];
    $user_id = $_SESSION['id'];
    $stmt = $conn->prepare("SELECT pass FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if ($res && (password_verify($current_pass, $res['pass']) || $current_pass === $res['pass'])) {
        $hashed_new = password_hash($new_pass, PASSWORD_DEFAULT);
        $stmt_upd = $conn->prepare("UPDATE users SET pass = ? WHERE id = ?");
        $stmt_upd->bind_param("si", $hashed_new, $user_id);
        if ($stmt_upd->execute()) {
            $success_msg = "Password Anda berhasil diperbarui!";
        }
        $stmt_upd->close();
    } else {
        $error_msg = "Password lama salah!";
    }
}

// Stats for Report
$q_all_locations = mysqli_query($conn, "SELECT l.*, 
    (SELECT COUNT(*) FROM p3k_items WHERE lokasi_id = l.id) as total_items,
    (SELECT COUNT(*) FROM p3k_items WHERE lokasi_id = l.id AND (stok <= min_stok OR tgl_kadaluarsa <= DATE_ADD(CURDATE(), INTERVAL 30 DAY))) as critical_items
    FROM p3k_lokasi l ORDER BY l.nama_lokasi ASC");

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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @media print {
            @page {
                margin: 0;
            }

            body {
                background: white !important;
                padding: 1.5cm 1cm !important;
            }

            .container {
                max-width: 100% !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 20px !important;
            }

            .navbar,
            .mobile-bottom-nav,
            .btn-outline-primary,
            .vr,
            .dropdown,
            .modal,
            footer {
                display: none !important;
            }

            .glass-card {
                background: white !important;
                box-shadow: none !important;
                border: none !important;
                margin-top: 0 !important;
                border-radius: 0 !important;
                overflow: visible !important;
            }

            .container.py-3 {
                margin-top: 0 !important;
                padding-top: 0 !important;
            }

            .table-premium {
                border-collapse: collapse !important;
                width: 100% !important;
            }

            .table-premium thead {
                display: table-header-group !important;
            }

            .table-premium thead th {
                background: #f8fafc !important;
                color: #475569 !important;
                border-bottom: 2px solid #e2e8f0 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .table-premium tbody tr {
                page-break-inside: avoid;
            }

            .table-premium tbody td {
                border-bottom: 1px solid #f1f5f9 !important;
            }

            .badge-status {
                border: 1px solid #ddd !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            h2 {
                margin-top: 0 !important;
            }

            .report-footer {
                page-break-inside: avoid;
            }

            .print-only {
                display: block !important;
            }
        }

        .print-only {
            display: none;
        }
    </style>
</head>

<body>
    <nav class="navbar glass-navbar fixed-top py-2">
        <div class="container-fluid px-4">
            <a href="dashboard.php" class="d-flex align-items-center gap-3 text-decoration-none">
                <img src="assets/images/cba-text.png" alt="Logo CBA" style="height: 35px; width: auto;">
                <div class="brand-text border-start ps-3 d-none d-sm-block">
                    <h1 class="h6 mb-0 fw-bold tracking-tight text-primary">MONITORING KOTAK P3K</h1>
                    <p class="text-secondary tiny mb-0 fw-medium" style="font-size: 0.6rem; letter-spacing: 0.05em;">
                        DIGITAL MONITORING SYSTEM</p>
                </div>
            </a>
            <div class="d-flex align-items-center gap-4">
                <nav class="d-none d-md-flex align-items-center gap-1">
                    <a href="dashboard.php" class="nav-link-premium"><i data-lucide="layout-dashboard"
                            style="width:16px;"></i> Dashboard</a>
                    <?php if ($_SESSION['role'] == 'Admin'): ?>
                        <a href="laporan.php" class="nav-link-premium active"><i data-lucide="file-bar-chart"
                                style="width:16px;"></i> Laporan</a>
                        <a href="pengaturan.php" class="nav-link-premium"><i data-lucide="settings" style="width:16px;"></i>
                            Pengaturan</a>
                    <?php endif; ?>
                </nav>
                <div class="vr opacity-10 d-none d-md-block" style="height: 24px;"></div>
                <div class="dropdown">
                    <div class="user-profile-trigger d-flex align-items-center gap-2" data-bs-toggle="dropdown"
                        role="button">
                        <div class="avatar-circle bg-accent-light text-accent rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 36px; height: 36px;"><i data-lucide="user" style="width:18px;"></i></div>
                        <div class="d-none d-lg-block">
                            <p class="small fw-bold mb-0 lh-1"><?= htmlspecialchars($_SESSION['username']) ?></p>
                            <p class="text-secondary tiny mb-0"><?= $_SESSION['role'] ?></p>
                        </div>
                        <i data-lucide="chevron-down" class="text-secondary" style="width:14px;"></i>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg mt-3 rounded-4 p-2">
                        <li>
                            <a class="dropdown-item rounded-3 small d-flex align-items-center gap-2 py-2" href="#"
                                data-bs-toggle="modal" data-bs-target="#selfChangePassModal">
                                <i data-lucide="key" style="width:16px;"></i>
                                <span class="fw-medium">Ganti Password</span>
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item rounded-3 small d-flex align-items-center gap-2 py-2"
                                href="logout.php"><i data-lucide="log-out" style="width:16px;"
                                    class="text-danger"></i><span class="fw-medium">Keluar</span></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Bottom Nav -->
    <nav class="mobile-bottom-nav desktop-hide shadow-lg">
        <a href="dashboard.php" class="mobile-nav-item"><i data-lucide="layout-dashboard"></i><span>Dashboard</span></a>
        <?php if ($_SESSION['role'] == 'Admin'): ?>
            <a href="laporan.php" class="mobile-nav-item active"><i
                    data-lucide="file-bar-chart"></i><span>Laporan</span></a>
            <a href="pengaturan.php" class="mobile-nav-item"><i data-lucide="settings"></i><span>Atur</span></a>
        <?php endif; ?>
        <a href="logout.php" class="mobile-nav-item text-danger"><i data-lucide="log-out"></i><span>Keluar</span></a>
    </nav>

    <div class="container py-3" style="margin-top: 100px; padding-bottom: 100px;">
        <?php if (isset($success_msg)): ?>
            <div class="alert alert-success glass-card border-0 mb-4 fade-in"><i data-lucide="check-circle" class="me-2"
                    style="width:18px;"></i><?= $success_msg ?></div> <?php endif; ?>
        <?php if (isset($error_msg)): ?>
            <div class="alert alert-danger glass-card border-0 mb-4 fade-in"><i data-lucide="x-circle" class="me-2"
                    style="width:18px;"></i><?= $error_msg ?></div> <?php endif; ?>

        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h2 class="h3 mb-1">Laporan Monitoring Kotak P3K</h2>
                <p class="text-secondary small mb-0">Ringkasan status ketersediaan dan masa kadaluarsa seluruh unit P3K.
                </p>
            </div>
            <button onclick="window.print()" class="btn btn-outline-primary rounded-3"><i data-lucide="printer"
                    class="me-2" style="width:16px;"></i>Cetak Laporan</button>
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
                                    <span class="row-title"><?= htmlspecialchars($row['nama_lokasi']) ?></span>
                                    <span class="row-subtitle">PIC: <?= htmlspecialchars($row['pic']) ?></span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2"><i data-lucide="package"
                                            class="text-secondary" style="width:14px;"></i><span
                                            class="fw-medium"><?= (int) $row['total_items'] ?> Item</span></div>
                                </td>
                                <td><span
                                        class="<?= $row['critical_items'] > 0 ? 'text-danger fw-bold' : 'text-secondary' ?>"><?= (int) $row['critical_items'] ?>
                                        Item</span></td>
                                <td><?php if ($row['critical_items'] > 0): ?> <span class="badge-status badge-critical"><i
                                                data-lucide="alert-circle" style="width:12px;"></i> Butuh Tindakan</span>
                                    <?php else: ?> <span class="badge-status badge-success"><i data-lucide="check-circle"
                                                style="width:12px;"></i> Aman</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="text-secondary small"><?= date('d/m/Y', strtotime($row['last_update'])) ?>
                                        <div class="tiny" style="font-size: 0.7rem;">
                                            <?= date('H:i', strtotime($row['last_update'])) ?> WIB
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer Section for Report -->
        <div class="report-footer mt-5">
            <div class="d-flex justify-content-between px-4">
                <div class="text-center" style="min-width: 200px;">
                    <p class="small mb-5">Dibuat Oleh,</p>
                    <div class="mt-5">
                        <p class="fw-bold mb-0 text-decoration-underline"><?= htmlspecialchars($_SESSION['username']) ?></p>
                        <p class="tiny text-secondary">Administrator</p>
                    </div>
                </div>
                <div class="text-center" style="min-width: 200px;">
                    <p class="small mb-5">Diketahui Oleh,</p>
                    <div class="mt-5">
                        <p class="fw-bold mb-0">____________________</p>
                        <p class="tiny text-secondary">HSE / PIC Terkait</p>
                    </div>
                </div>
            </div>
            <div class="mt-5 pt-3 border-top text-center print-only">
                <p class="tiny text-secondary mb-0">Laporan ini dibuat secara otomatis melalui Sistem Monitoring Kotak P3K Digital</p>
                <p class="tiny text-secondary">&copy; <?= date('Y') ?> PT CBA Chemical Industry | Dicetak pada: <?= date('d/m/Y H:i') ?> WIB</p>
            </div>
        </div>
    </div>

    <!-- Modal Self Change Password -->
    <div class="modal fade" id="selfChangePassModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content glass-card border-0">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title text-primary">Ganti Password Saya</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Password Lama</label>
                            <input type="password" name="current_pass" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Password Baru</label>
                            <input type="password" name="new_pass" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="submit" name="self_change_pass"
                            class="btn-premium w-100 justify-content-center">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>lucide.createIcons(); <?php if (isset($success_msg)): ?> Swal.fire({ icon: 'success', title: 'Berhasil!', text: '<?= $success_msg ?>', timer: 2000, showConfirmButton: false, background: 'rgba(255, 255, 255, 0.95)', customClass: { popup: 'glass-card border-0 shadow-lg' } }); <?php endif; ?> <?php if (isset($error_msg)): ?> Swal.fire({ icon: 'error', title: 'Gagal!', text: '<?= $error_msg ?>', timer: 2000, showConfirmButton: false, background: 'rgba(255, 255, 255, 0.95)', customClass: { popup: 'glass-card border-0 shadow-lg' } }); <?php endif; ?> </script>
    <footer class="py-4 mt-5 border-top border-light text-center">
        <div class="container-fluid px-4">
            <p class="text-secondary small mb-0">&copy; <?= date('Y') ?> <span class="fw-bold text-primary">PT CBA Chemical Industry</span> | Monitoring Kotak P3K - Team IT Pabrik</p>
        </div>
    </footer>
</body>

</html>