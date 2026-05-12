<?php
session_start();
include "db.php";

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

// CSRF Token Generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Access Control Logic
$role = $_SESSION['role'];
$username = $_SESSION['username'];
$where_clause = ($role == 'Admin') ? "" : "WHERE l.pic = '$username'";
$where_clause_items = ($role == 'Admin') ? "" : "AND l.pic = '$username'";

// 1. Fetch Stats
$q_lokasi = mysqli_query($conn, "SELECT COUNT(*) as total FROM p3k_lokasi l $where_clause");
$total_lokasi = mysqli_fetch_assoc($q_lokasi)['total'];

$today = date('Y-m-d');
$next_month = date('Y-m-d', strtotime('+30 days'));
$q_kritis = mysqli_query($conn, "SELECT COUNT(*) as total FROM p3k_items i 
    JOIN p3k_lokasi l ON i.lokasi_id = l.id 
    WHERE (i.stok <= i.min_stok OR (i.tgl_kadaluarsa <= '$next_month' AND i.tgl_kadaluarsa >= '$today')) $where_clause_items");
$total_kritis = mysqli_fetch_assoc($q_kritis)['total'];

$q_expired = mysqli_query($conn, "SELECT COUNT(*) as total FROM p3k_items i 
    JOIN p3k_lokasi l ON i.lokasi_id = l.id 
    WHERE i.tgl_kadaluarsa < '$today' $where_clause_items");
$total_expired = mysqli_fetch_assoc($q_expired)['total'];

$q_all_items = mysqli_query($conn, "SELECT COUNT(*) as total FROM p3k_items i JOIN p3k_lokasi l ON i.lokasi_id = l.id $where_clause");
$total_items = mysqli_fetch_assoc($q_all_items)['total'];
$health_percentage = ($total_items > 0) ? round((($total_items - $total_kritis) / $total_items) * 100) : 0;

// 2. Fetch Critical Items for Sidebar
$critical_items = mysqli_query($conn, "SELECT i.*, l.nama_lokasi FROM p3k_items i 
    JOIN p3k_lokasi l ON i.lokasi_id = l.id 
    WHERE (i.stok <= i.min_stok OR i.tgl_kadaluarsa <= '$next_month') $where_clause_items
    ORDER BY i.stok ASC LIMIT 10");

// 3. Fetch Recent Activities
$activities = mysqli_query($conn, "SELECT a.*, l.nama_lokasi FROM p3k_activity a 
    JOIN p3k_lokasi l ON a.lokasi_id = l.id 
    " . (($role == 'Admin') ? "" : "WHERE l.pic = '$username'") . "
    ORDER BY a.created_at DESC LIMIT 5");

// 4. Fetch Locations for Grid
$locations = mysqli_query($conn, "SELECT l.*, u.nama as pic_nama,
    (SELECT COUNT(*) FROM p3k_items WHERE lokasi_id = l.id AND (stok <= min_stok OR tgl_kadaluarsa <= '$next_month')) as critical_count
    FROM p3k_lokasi l 
    LEFT JOIN users u ON l.pic = u.username 
    $where_clause");

// 5. Fetch all Users for PIC Selection
$all_users_q = mysqli_query($conn, "SELECT username, nama FROM users WHERE role = 'User' ORDER BY nama ASC");
$user_options = [];
while ($u = mysqli_fetch_assoc($all_users_q)) {
    $user_options[] = $u;
}

// 6. Handle Add Location
if (isset($_POST['add_location']) && $role == 'Admin') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }
    $nama_lokasi = $_POST['nama_lokasi'];
    $pic_username = $_POST['pic_username'];

    $stmt_loc = $conn->prepare("INSERT INTO p3k_lokasi (nama_lokasi, pic) VALUES (?, ?)");
    $stmt_loc->bind_param("ss", $nama_lokasi, $pic_username);
    if ($stmt_loc->execute()) {
        $success_msg = "Lokasi baru berhasil ditambahkan!";
    }
    $stmt_loc->close();
}

// 7. Handle Edit Location
if (isset($_POST['edit_location']) && $role == 'Admin') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }
    $lokasi_id = $_POST['lokasi_id'];
    $nama_lokasi = $_POST['nama_lokasi'];
    $pic_username = $_POST['pic_username'];

    $stmt_edit = $conn->prepare("UPDATE p3k_lokasi SET nama_lokasi = ?, pic = ? WHERE id = ?");
    $stmt_edit->bind_param("ssi", $nama_lokasi, $pic_username, $lokasi_id);
    if ($stmt_edit->execute()) {
        $success_msg = "Detail lokasi berhasil diperbarui!";
    }
    $stmt_edit->close();
}

// 6. Handle Delete Location
if (isset($_POST['delete_location']) && $role == 'Admin') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }
    $lokasi_id = $_POST['lokasi_id'];
    $stmt_del_items = $conn->prepare("DELETE FROM p3k_items WHERE lokasi_id = ?");
    $stmt_del_items->bind_param("i", $lokasi_id);
    $stmt_del_items->execute();
    $stmt_del_items->close();
    $stmt_del_act = $conn->prepare("DELETE FROM p3k_activity WHERE lokasi_id = ?");
    $stmt_del_act->bind_param("i", $lokasi_id);
    $stmt_del_act->execute();
    $stmt_del_act->close();
    $stmt_del_loc = $conn->prepare("DELETE FROM p3k_lokasi WHERE id = ?");
    $stmt_del_loc->bind_param("i", $lokasi_id);
    if ($stmt_del_loc->execute()) {
        $success_msg = "Lokasi berhasil dihapus!";
    }
    $stmt_del_loc->close();
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

$locations = mysqli_query($conn, "SELECT l.*, u.nama as pic_nama,
    (SELECT COUNT(*) FROM p3k_items WHERE lokasi_id = l.id AND (stok <= min_stok OR tgl_kadaluarsa <= '$next_month')) as critical_count
    FROM p3k_lokasi l 
    LEFT JOIN users u ON l.pic = u.username 
    $where_clause");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - MONITORING KOTAK P3K</title>
    <link rel="shortcut icon" href="assets/images/cba-text.png" type="image/x-icon">
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#2563eb">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('sw.js')
                    .then(reg => console.log('Service Worker registered'))
                    .catch(err => console.log('Service Worker registration failed', err));
            });
        }
    </script>
    <style>
        .stat-card {
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        .stat-icon {
            position: absolute;
            right: -10px;
            bottom: -10px;
            width: 80px;
            height: 80px;
            opacity: 0.05;
            color: var(--primary);
        }

        .location-card {
            transition: all 0.3s;
            border: 1px solid transparent;
        }

        .location-card:hover {
            transform: translateY(-5px);
            border-color: #e2e8f0;
            box-shadow: var(--shadow-lg);
        }

        .btn-delete-lokasi {
            background: #fff1f2;
            color: #e11d48;
            border: 1px solid #fecdd3;
            padding: 8px;
            border-radius: 10px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 10;
        }

        .btn-delete-lokasi:hover {
            background: #e11d48;
            color: white;
            transform: scale(1.1);
        }
    </style>
</head>

<body>
    <nav class="navbar glass-navbar fixed-top py-2">
        <div class="container-fluid px-4">
            <a href="dashboard.php" class="d-flex align-items-center gap-3 text-decoration-none">
                <img src="assets/images/cba-text.png" alt="Logo CBA" style="height: 40px; width: auto;">
                <div class="brand-text border-start ps-3 d-none d-sm-block">
                    <h1 class="h6 mb-0 fw-bold tracking-tight text-primary">MONITORING KOTAK P3K</h1>
                    <p class="text-secondary tiny mb-0 fw-medium" style="font-size: 0.6rem; letter-spacing: 0.05em;">
                        DIGITAL MONITORING SYSTEM</p>
                </div>
            </a>

            <div class="d-flex align-items-center gap-4">
                <nav class="d-none d-md-flex align-items-center gap-1">
                    <a href="dashboard.php" class="nav-link-premium active">
                        <i data-lucide="layout-dashboard" style="width:16px;"></i>
                        Dashboard
                    </a>
                    <?php if ($_SESSION['role'] == 'Admin'): ?>
                        <a href="laporan.php" class="nav-link-premium">
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
                            <a class="dropdown-item rounded-3 small d-flex align-items-center gap-2 py-2" href="#"
                                data-bs-toggle="modal" data-bs-target="#selfChangePassModal">
                                <i data-lucide="key" style="width:16px;"></i>
                                <span class="fw-medium">Ganti Password</span>
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item rounded-3 small d-flex align-items-center gap-2 py-2"
                                href="logout.php">
                                <i data-lucide="log-out" style="width:16px;" class="text-danger"></i>
                                <span class="fw-medium">Keluar</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Bottom Nav -->
    <nav class="mobile-bottom-nav desktop-hide shadow-lg">
        <a href="dashboard.php" class="mobile-nav-item active">
            <i data-lucide="layout-dashboard"></i>
            <span>Dashboard</span>
        </a>
        <?php if ($_SESSION['role'] == 'Admin'): ?>
            <a href="laporan.php" class="mobile-nav-item">
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

    <div class="container-fluid px-4 py-3" style="margin-top: 80px; padding-bottom: 100px;">
        <?php if (isset($success_msg)): ?>
            <div class="alert alert-success glass-card border-0 mb-4 fade-in">
                <i data-lucide="check-circle" class="me-2" style="width:18px;"></i>
                <?= $success_msg ?>
            </div>
        <?php endif; ?>
        <?php if (isset($error_msg)): ?>
            <div class="alert alert-danger glass-card border-0 mb-4 fade-in">
                <i data-lucide="x-circle" class="me-2" style="width:18px;"></i>
                <?= $error_msg ?>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-end mb-4">
                    <div>
                        <h3 class="mb-1">Statistik Monitoring</h3>
                        <p class="text-secondary small mb-0">
                            <?= ($_SESSION['role'] == 'Admin') ? 'Overview kondisi persediaan P3K di seluruh area.' : 'Daftar lokasi P3K di bawah tanggung jawab Anda.' ?>
                        </p>
                    </div>
                    <?php if ($_SESSION['role'] == 'Admin'): ?>
                        <button class="btn-premium btn-sm" data-bs-toggle="modal" data-bs-target="#addLocationModal">
                            <i data-lucide="plus" style="width:16px;"></i>
                            Tambah Lokasi
                        </button>
                    <?php endif; ?>
                </div>

                <div class="row g-4 mb-5">
                    <div class="col-md-6 col-lg-3">
                        <div class="glass-card stat-card">
                            <p class="text-secondary small fw-medium mb-1">Total Lokasi</p>
                            <h2 class="mb-0"><?= $total_lokasi ?></h2>
                            <span class="badge-status badge-success mt-2">Terdaftar</span>
                            <i data-lucide="map-pin" class="stat-icon"></i>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="glass-card stat-card">
                            <p class="text-secondary small fw-medium mb-1">Item Expired</p>
                            <h2 class="mb-0 text-danger"><?= $total_expired ?></h2>
                            <span class="badge-status badge-critical bg-danger text-white mt-2">LEWAT EXPIRED</span>
                            <i data-lucide="x-circle" class="stat-icon text-danger opacity-25"></i>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="glass-card stat-card">
                            <p class="text-secondary small fw-medium mb-1">Stok Rendah</p>
                            <h2 class="mb-0 text-warning"><?= $total_kritis ?></h2>
                            <span class="badge-status badge-warning mt-2">Butuh Tindakan</span>
                            <i data-lucide="alert-triangle" class="stat-icon"></i>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="glass-card stat-card">
                            <p class="text-secondary small fw-medium mb-1">Capacity Health</p>
                            <h2 class="mb-0"><?= $health_percentage ?>%</h2>
                            <div class="progress mt-3" style="height: 6px; background: #e2e8f0; border-radius: 10px;">
                                <div class="progress-bar bg-primary"
                                    style="width: <?= $health_percentage ?>%; border-radius: 10px;"></div>
                            </div>
                            <i data-lucide="activity" class="stat-icon"></i>
                        </div>
                    </div>
                </div>

                <h4 class="mb-4">Area & Lokasi Kotak P3K</h4>
                <div class="row g-4">
                    <?php while ($loc = mysqli_fetch_assoc($locations)): ?>
                        <div class="col-md-6 col-xl-4">
                            <a href="location_detail.php?id=<?= $loc['id'] ?>" class="text-decoration-none">
                                <div class="glass-card location-card p-4 h-100">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div class="bg-light p-3 rounded-4 text-primary">
                                            <i data-lucide="box" style="width:24px; height:24px;"></i>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <?php if ($loc['critical_count'] > 0): ?>
                                                <span class="badge-status badge-critical">MASALAH</span>
                                            <?php else: ?>
                                                <span class="badge-status badge-success">OK</span>
                                            <?php endif; ?>
                                            <?php if ($_SESSION['role'] == 'Admin'): ?>
                                                <div class="d-flex gap-1">
                                                    <button type="button"
                                                        onclick="event.preventDefault(); event.stopPropagation();"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editLocationModal<?= $loc['id'] ?>"
                                                        class="btn btn-sm btn-outline-warning border-0 rounded-3 p-2">
                                                        <i data-lucide="edit-3" style="width:14px;"></i>
                                                    </button>
                                                    <form id="deleteForm<?= $loc['id'] ?>" action="" method="POST" class="mb-0">
                                                        <input type="hidden" name="csrf_token"
                                                            value="<?= $_SESSION['csrf_token'] ?>">
                                                        <input type="hidden" name="lokasi_id" value="<?= $loc['id'] ?>">
                                                        <input type="hidden" name="delete_location" value="1">
                                                        <button type="button"
                                                            onclick="event.preventDefault(); event.stopPropagation(); confirmDelete(<?= $loc['id'] ?>, '<?= htmlspecialchars($loc['nama_lokasi']) ?>')"
                                                            class="btn-delete-lokasi">
                                                            <i data-lucide="trash-2" style="width:14px;"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <h5 class="mb-1 text-dark"><?= htmlspecialchars($loc['nama_lokasi']) ?></h5>
                                    <p class="text-secondary small mb-1">PIC: <span
                                            class="fw-semibold"><?= htmlspecialchars($loc['pic_nama'] ?? $loc['pic']) ?></span>
                                    </p>
                                    <p class="text-secondary tiny mb-3">Update terakhir:
                                        <?= date('d M Y', strtotime($loc['last_update'])) ?></p>
                                    <div class="d-flex align-items-center gap-2 text-primary small fw-semibold">
                                        Lihat Detail
                                        <i data-lucide="chevron-right" style="width:14px;"></i>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="glass-card mb-4" style="height: fit-content;">
                    <div class="p-4 border-bottom">
                        <h6 class="mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="alert-circle" class="text-danger" style="width:18px;"></i>
                            Item Kritis
                        </h6>
                    </div>
                    <div class="sidebar-critical">
                        <?php if (mysqli_num_rows($critical_items) > 0): ?>
                            <?php while ($item = mysqli_fetch_assoc($critical_items)):
                                $is_expired = ($item['tgl_kadaluarsa'] <= $next_month);
                                $is_low_stock = ($item['stok'] <= $item['min_stok']);
                                ?>
                                <div class="critical-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="me-2">
                                            <h6 class="item-name mb-0"><?= htmlspecialchars($item['nama_item']) ?></h6>
                                            <div class="item-location">
                                                <i data-lucide="map-pin" style="width:10px; height:10px;"></i>
                                                <?= htmlspecialchars($item['nama_lokasi']) ?>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <span class="item-qty"><?= $item['stok'] ?></span>
                                            <span class="tiny text-secondary"
                                                style="font-size: 0.65rem;"><?= htmlspecialchars($item['satuan']) ?></span>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-1 mt-1">
                                        <?php if ($is_low_stock): ?>
                                            <span class="badge-pill bg-danger text-white">STOK RENDAH</span>
                                        <?php endif; ?>
                                        <?php if ($is_expired): ?>
                                            <span class="badge-pill bg-warning text-dark">KADALUARSA</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="p-4 text-center">
                                <p class="text-secondary small mb-0">Semua item aman.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="p-3 bg-light text-center rounded-bottom">
                        <a href="laporan.php" class="small text-primary text-decoration-none fw-semibold">Lihat
                            Semua</a>
                    </div>
                </div>

                <div class="glass-card">
                    <div class="p-4 border-bottom">
                        <h6 class="mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="clock" class="text-primary" style="width:18px;"></i>
                            Aktivitas Terbaru
                        </h6>
                    </div>
                    <div class="p-4">
                        <?php while ($act = mysqli_fetch_assoc($activities)): ?>
                            <div class="mb-4 last-child-mb-0">
                                <div class="d-flex gap-3">
                                    <div class="flex-shrink-0 mt-1">
                                        <div class="bg-primary rounded-circle" style="width:8px; height:8px;"></div>
                                    </div>
                                    <div>
                                        <p class="small mb-1 fw-medium"><?= htmlspecialchars($act['message']) ?></p>
                                        <p class="text-secondary tiny mb-0" style="font-size: 0.7rem;">
                                            <?= htmlspecialchars($act['nama_lokasi']) ?> •
                                            <?= date('H:i', strtotime($act['created_at'])) ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Add Location -->
    <div class="modal fade" id="addLocationModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content glass-card border-0">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title">Tambah Lokasi Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nama Lokasi</label>
                            <input type="text" name="nama_lokasi" class="form-control"
                                placeholder="Contoh: Gedung Produksi B" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Penanggung Jawab (PIC)</label>
                            <select name="pic_username" class="form-control" required>
                                <option value="">-- Pilih PIC --</option>
                                <?php foreach ($user_options as $uo): ?>
                                    <option value="<?= $uo['username'] ?>"><?= htmlspecialchars($uo['nama']) ?>
                                        (<?= $uo['username'] ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="add_location" class="btn-premium">Simpan Lokasi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php
    // Reset locations pointer to generate edit modals
    mysqli_data_seek($locations, 0);
    while ($loc = mysqli_fetch_assoc($locations)): ?>
        <div class="modal fade" id="editLocationModal<?= $loc['id'] ?>" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content glass-card border-0">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title">Edit Lokasi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="lokasi_id" value="<?= $loc['id'] ?>">
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Nama Lokasi</label>
                                <input type="text" name="nama_lokasi" class="form-control"
                                    value="<?= htmlspecialchars($loc['nama_lokasi']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Penanggung Jawab (PIC)</label>
                                <select name="pic_username" class="form-control" required>
                                    <option value="">-- Pilih PIC --</option>
                                    <?php foreach ($user_options as $uo): ?>
                                        <option value="<?= $uo['username'] ?>" <?= $uo['username'] == $loc['pic'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($uo['nama']) ?> (<?= $uo['username'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" name="edit_location" class="btn-premium">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endwhile; ?>

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
    <script>
        lucide.createIcons();
        function confirmDelete(id, name) {
            Swal.fire({
                title: 'Hapus Lokasi?',
                text: "Semua item dan riwayat di " + name + " akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                background: 'rgba(255, 255, 255, 0.9)',
                backdrop: `rgba(15, 23, 42, 0.1)`,
                customClass: { popup: 'glass-card border-0 shadow-lg', confirmButton: 'btn-premium bg-danger border-0 px-4 py-2 rounded-3', cancelButton: 'btn btn-light border-0 px-4 py-2 rounded-3 ms-2' },
                buttonsStyling: false
            }).then((result) => { if (result.isConfirmed) { document.getElementById('deleteForm' + id).submit(); } })
        }
        <?php if (isset($success_msg)): ?> Swal.fire({ icon: 'success', title: 'Berhasil!', text: '<?= $success_msg ?>', timer: 2000, showConfirmButton: false, background: 'rgba(255, 255, 255, 0.9)', customClass: { popup: 'glass-card border-0 shadow-lg' } }); <?php endif; ?>
        <?php if (isset($error_msg)): ?> Swal.fire({ icon: 'error', title: 'Gagal!', text: '<?= $error_msg ?>', timer: 2000, showConfirmButton: false, background: 'rgba(255, 255, 255, 0.9)', customClass: { popup: 'glass-card border-0 shadow-lg' } }); <?php endif; ?>
    </script>
    <footer class="py-4 mt-5 border-top border-light">
        <div class="container-fluid px-4 text-center">
            <p class="text-secondary small mb-0">&copy; <?= date('Y') ?> <span class="fw-bold text-primary">PT CBA
                    Chemical Industry</span> | Monitoring Kotak P3K - Team IT Pabrik</p>
        </div>
    </footer>
</body>

</html>