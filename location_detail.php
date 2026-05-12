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

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: dashboard.php");
    exit;
}

// Fetch Location Details with Prepared Statement
$stmt_loc = $conn->prepare("SELECT l.*, u.nama as pic_nama FROM p3k_lokasi l LEFT JOIN users u ON l.pic = u.username WHERE l.id = ?");
$stmt_loc->bind_param("i", $id);
$stmt_loc->execute();
$lokasi = $stmt_loc->get_result()->fetch_assoc();
$stmt_loc->close();

if (!$lokasi) {
    header("Location: dashboard.php");
    exit;
}

// Access Control
if ($_SESSION['role'] != 'Admin' && $_SESSION['username'] != $lokasi['pic']) {
    echo "<script>alert('Akses Ditolak!'); window.location='dashboard.php';</script>";
    exit;
}

// Handle Stock Adjustment
if (isset($_POST['adjust_stock'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }
    $item_id = $_POST['item_id'];
    $new_stok = $_POST['stok'];
    $new_min_stok = $_POST['min_stok'];
    $new_expired = $_POST['tgl_kadaluarsa'];
    $item_name = $_POST['item_name'];
    $stmt = $conn->prepare("UPDATE p3k_items SET stok = ?, min_stok = ?, tgl_kadaluarsa = ? WHERE id = ?");
    $stmt->bind_param("iisi", $new_stok, $new_min_stok, $new_expired, $item_id);
    if ($stmt->execute()) {
        $msg = "Update $item_name (Stok: $new_stok, Min: $new_min_stok, Exp: $new_expired) oleh " . $_SESSION['username'];
        $stmt_act = $conn->prepare("INSERT INTO p3k_activity (lokasi_id, activity_type, message) VALUES (?, 'UPDATE', ?)");
        $stmt_act->bind_param("is", $id, $msg);
        $stmt_act->execute();
        $stmt_act->close();
        $success_msg = "Perubahan berhasil disimpan!";
    }
    $stmt->close();
}

// Handle Bulk Save
if (isset($_POST['save_all'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }
    $item_ids = $_POST['item_ids'];
    $stoks = $_POST['stoks'];
    $min_stoks = $_POST['min_stoks'];
    $exps = $_POST['tgl_kadaluarsas'];

    $count = 0;
    for ($i = 0; $i < count($item_ids); $i++) {
        $stmt = $conn->prepare("UPDATE p3k_items SET stok = ?, min_stok = ?, tgl_kadaluarsa = ? WHERE id = ?");
        $stmt->bind_param("iisi", $stoks[$i], $min_stoks[$i], $exps[$i], $item_ids[$i]);
        if ($stmt->execute()) {
            $count++;
        }
        $stmt->close();
    }

    if ($count > 0) {
        $msg = "Melakukan update massal pada $count item oleh " . $_SESSION['username'];
        $stmt_act = $conn->prepare("INSERT INTO p3k_activity (lokasi_id, activity_type, message) VALUES (?, 'UPDATE', ?)");
        $stmt_act->bind_param("is", $id, $msg);
        $stmt_act->execute();
        $stmt_act->close();
        $success_msg = "Seluruh perubahan berhasil disimpan!";
    }
}

// Handle Add Item
if (isset($_POST['add_item'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }
    $nama_item = $_POST['nama_item'];
    $stok = $_POST['stok'];
    $min_stok = $_POST['min_stok'];
    $satuan = $_POST['satuan'];
    $tgl_kadaluarsa = $_POST['tgl_kadaluarsa'];
    $stmt = $conn->prepare("INSERT INTO p3k_items (lokasi_id, nama_item, stok, min_stok, satuan, tgl_kadaluarsa) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isiiss", $id, $nama_item, $stok, $min_stok, $satuan, $tgl_kadaluarsa);
    if ($stmt->execute()) {
        $msg = "Menambahkan item baru: $nama_item oleh " . $_SESSION['username'];
        $stmt_act = $conn->prepare("INSERT INTO p3k_activity (lokasi_id, activity_type, message) VALUES (?, 'UPDATE', ?)");
        $stmt_act->bind_param("is", $id, $msg);
        $stmt_act->execute();
        $stmt_act->close();
        $success_msg = "Item baru berhasil ditambahkan!";
    }
    $stmt->close();
}

// Handle Delete Item
if (isset($_POST['delete_item'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }
    $item_id = $_POST['item_id'];
    $item_name = $_POST['item_name'];
    $stmt = $conn->prepare("DELETE FROM p3k_items WHERE id = ?");
    $stmt->bind_param("i", $item_id);
    if ($stmt->execute()) {
        $msg = "Menghapus item: $item_name oleh " . $_SESSION['username'];
        $stmt_act = $conn->prepare("INSERT INTO p3k_activity (lokasi_id, activity_type, message) VALUES (?, 'UPDATE', ?)");
        $stmt_act->bind_param("is", $id, $msg);
        $stmt_act->execute();
        $stmt_act->close();
        $success_msg = "Item berhasil dihapus!";
    }
    $stmt->close();
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

$stmt_items = $conn->prepare("SELECT * FROM p3k_items WHERE lokasi_id = ? ORDER BY nama_item ASC");
$stmt_items->bind_param("i", $id);
$stmt_items->execute();
$items = $stmt_items->get_result();
$stmt_items->close();
$next_month = date('Y-m-d', strtotime('+30 days'));
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Lokasi - <?= htmlspecialchars($lokasi['nama_lokasi']) ?> | MONITORING KOTAK P3K</title>
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
            .navbar, .mobile-bottom-nav, .btn, .vr, .dropdown, .modal, footer, .logo-wrapper { 
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
            input[type="number"], input[type="date"] {
                border: none !important;
                background: transparent !important;
                padding: 0 !important;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar glass-navbar fixed-top py-2">
        <div class="container-fluid px-4">
            <div class="d-flex align-items-center gap-3">
                <a href="dashboard.php"
                    class="logo-wrapper bg-accent-light text-accent p-2 rounded-4 shadow-sm text-decoration-none">
                    <i data-lucide="arrow-left" style="width:22px; height:22px;"></i>
                </a>
                <a href="dashboard.php" class="d-flex align-items-center gap-3 text-decoration-none">
                    <img src="assets/images/cba-text.png" alt="Logo CBA" style="height: 35px; width: auto;">
                    <div class="brand-text border-start ps-3 d-none d-sm-block">
                        <h1 class="h6 mb-0 fw-bold tracking-tight text-primary">
                            <?= strtoupper(htmlspecialchars($lokasi['nama_lokasi'])) ?>
                        </h1>
                        <p class="text-secondary tiny mb-0 fw-medium"
                            style="font-size: 0.6rem; letter-spacing: 0.05em;">PIC:
                            <?= htmlspecialchars($lokasi['pic_nama'] ?? $lokasi['pic']) ?>
                        </p>
                    </div>
                </a>
            </div>
            <div class="d-flex align-items-center gap-4">
                <nav class="d-none d-md-flex align-items-center gap-1">
                    <a href="dashboard.php" class="nav-link-premium"><i data-lucide="layout-dashboard"
                            style="width:16px;"></i> Dashboard</a>
                    <?php if ($_SESSION['role'] == 'Admin'): ?>
                        <a href="laporan.php" class="nav-link-premium"><i data-lucide="file-bar-chart"
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
            <a href="laporan.php" class="mobile-nav-item"><i data-lucide="file-bar-chart"></i><span>Laporan</span></a>
            <a href="pengaturan.php" class="mobile-nav-item"><i data-lucide="settings"></i><span>Atur</span></a>
        <?php endif; ?>
        <a href="logout.php" class="mobile-nav-item text-danger"><i data-lucide="log-out"></i><span>Keluar</span></a>
    </nav>

    <div class="container py-3" style="margin-top: 80px; padding-bottom: 100px;">
        <?php if (isset($success_msg)): ?>
            <div class="alert alert-success glass-card border-0 mb-4 fade-in"><i data-lucide="check-circle" class="me-2"
                    style="width:18px;"></i><?= $success_msg ?></div> <?php endif; ?>
        <?php if (isset($error_msg)): ?>
            <div class="alert alert-danger glass-card border-0 mb-4 fade-in"><i data-lucide="x-circle" class="me-2"
                    style="width:18px;"></i><?= $error_msg ?></div> <?php endif; ?>

        <div class="row g-4">
            <div class="col-12">
                <div class="glass-card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="mb-0">Daftar Item</h4>
                        <div class="d-flex gap-2">
                            <button type="submit" name="save_all" form="bulkSaveForm"
                                class="btn btn-outline-primary btn-sm rounded-3 p-2" title="Simpan Semua Perubahan"><i
                                    data-lucide="save" style="width:16px;"></i> Simpan</button>
                            <button class="btn-premium btn-sm" data-bs-toggle="modal" data-bs-target="#addItemModal"><i
                                    data-lucide="plus" style="width:16px;"></i>Tambah</button>
                        </div>
                    </div>
                    <form id="bulkSaveForm" action="" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <div class="table-responsive">
                            <table class="table-premium">
                                <thead>
                                    <tr>
                                        <th style="width: 40%;">NAMA ITEM</th>
                                        <th style="width: 20%;">JUMLAH STOK</th>
                                        <th class="mobile-hide" style="width: 15%;">MIN. STOK</th>
                                        <th style="width: 15%;">STATUS</th>
                                        <th class="text-end" style="width: 10%;">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $item_list = [];
                                    while ($item = mysqli_fetch_assoc($items)) {
                                        $item_list[] = $item;
                                    }
                                    foreach ($item_list as $item):
                                        $is_expired = ($item['tgl_kadaluarsa'] <= $next_month);
                                        $is_low_stock = ($item['stok'] <= $item['min_stok']);
                                        ?>
                                        <tr>
                                            <input type="hidden" name="item_ids[]" value="<?= $item['id'] ?>">
                                            <td>
                                                <span class="row-title"><?= htmlspecialchars($item['nama_item']) ?></span>
                                                <div class="d-flex align-items-center gap-1 mt-1">
                                                    <span class="text-secondary tiny">Exp:</span>
                                                    <input type="date" name="tgl_kadaluarsas[]"
                                                        class="form-control form-control-sm border-0 bg-transparent p-0 tiny fw-medium text-secondary"
                                                        value="<?= $item['tgl_kadaluarsa'] ?>"
                                                        style="width: auto; font-size: 0.75rem;">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <input type="number" name="stoks[]"
                                                        class="form-control form-control-sm border-0 bg-light-subtle fw-bold text-primary"
                                                        value="<?= $item['stok'] ?>" min="0"
                                                        style="width: 70px; padding: 0.25rem 0.5rem;" required>
                                                    <span
                                                        class="text-secondary small"><?= htmlspecialchars($item['satuan']) ?></span>
                                                </div>
                                            </td>
                                            <td class="mobile-hide">
                                                <div class="d-flex align-items-center gap-2">
                                                    <input type="number" name="min_stoks[]"
                                                        class="form-control form-control-sm border-0 bg-transparent text-secondary"
                                                        value="<?= $item['min_stok'] ?>" min="0"
                                                        style="width: 60px; padding: 0.25rem 0.5rem;" required>
                                                    <span
                                                        class="text-secondary small"><?= htmlspecialchars($item['satuan']) ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if ($is_low_stock): ?> <span class="badge-status badge-critical"><i
                                                            data-lucide="alert-triangle" style="width:12px;"></i> Rendah</span>
                                                <?php elseif ($is_expired): ?> <span class="badge-status badge-warning"><i
                                                            data-lucide="clock" style="width:12px;"></i> Kadaluarsa</span>
                                                <?php else: ?> <span class="badge-status badge-success"><i
                                                            data-lucide="check" style="width:12px;"></i> Aman</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex justify-content-end align-items-center gap-1">
                                                    <button type="submit" name="adjust_stock"
                                                        class="btn btn-sm btn-accent-light text-accent rounded-3 p-2"
                                                        title="Simpan Baris Ini"
                                                        onclick="setSingleUpdate(<?= $item['id'] ?>, <?= $item['stok'] ?>, <?= $item['min_stok'] ?>, '<?= $item['tgl_kadaluarsa'] ?>', '<?= addslashes($item['nama_item']) ?>', event)">
                                                        <i data-lucide="check-circle" style="width:18px;"></i>
                                                    </button>
                                                    <button type="button"
                                                        onclick="confirmDeleteItem(<?= $item['id'] ?>, '<?= htmlspecialchars($item['nama_item']) ?>')"
                                                        class="btn btn-sm btn-outline-danger border-0 rounded-3 p-2"><i
                                                            data-lucide="trash-2" style="width:18px;"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Add Item -->
    <div class="modal fade" id="addItemModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content glass-card border-0">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title">Tambah Item Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <div class="modal-body p-4">
                        <div class="mb-3"><label class="form-label small fw-bold">Nama Item</label><input type="text"
                                name="nama_item" class="form-control" placeholder="Contoh: Kasa" required></div>
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label small fw-bold">Stok Awal</label><input
                                    type="number" name="stok" class="form-control" value="0" min="0" required></div>
                            <div class="col-md-6"><label class="form-label small fw-bold">Min. Stok</label><input
                                    type="number" name="min_stok" class="form-control" value="0" min="0" required></div>
                        </div>
                        <div class="mb-3 mt-3"><label class="form-label small fw-bold">Satuan</label><input type="text"
                                name="satuan" class="form-control" placeholder="pcs, botol, box" required></div>
                        <div class="mb-0"><label class="form-label small fw-bold">Tanggal Kadaluarsa</label><input
                                type="date" name="tgl_kadaluarsa" class="form-control" required></div>
                    </div>
                    <div class="modal-footer border-0 pt-0"><button type="button" class="btn btn-light rounded-3"
                            data-bs-toggle="modal">Batal</button><button type="submit" name="add_item"
                            class="btn-premium">Simpan Item</button></div>
                </form>
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

    <div id="deleteFormsContainer">
        <?php foreach ($item_list as $item): ?>
            <form id="deleteItemForm<?= $item['id'] ?>" action="" method="POST" class="mb-0">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                <input type="hidden" name="item_name" value="<?= htmlspecialchars($item['nama_item']) ?>">
                <input type="hidden" name="delete_item" value="1">
            </form>
        <?php endforeach; ?>
    </div>

    <!-- Hidden form for single update -->
    <form id="singleUpdateForm" action="" method="POST">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <input type="hidden" name="item_id" id="single_item_id">
        <input type="hidden" name="stok" id="single_stok">
        <input type="hidden" name="min_stok" id="single_min_stok">
        <input type="hidden" name="tgl_kadaluarsa" id="single_exp">
        <input type="hidden" name="item_name" id="single_item_name">
        <input type="hidden" name="adjust_stock" value="1">
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        lucide.createIcons();

        function setSingleUpdate(id, stok, min, exp, name, event) {
            // Find the current values in the bulk form
            const row = event.target.closest('tr');
            const currentStok = row.querySelector('input[name="stoks[]"]').value;
            const currentMin = row.querySelector('input[name="min_stoks[]"]').value;
            const currentExp = row.querySelector('input[name="tgl_kadaluarsas[]"]').value;

            document.getElementById('single_item_id').value = id;
            document.getElementById('single_stok').value = currentStok;
            document.getElementById('single_min_stok').value = currentMin;
            document.getElementById('single_exp').value = currentExp;
            document.getElementById('single_item_name').value = name;

            // The button is type="submit" inside bulkSaveForm, 
            // but we want it to trigger singleUpdateForm instead
            event.preventDefault();
            document.getElementById('singleUpdateForm').submit();
        }

        function confirmDeleteItem(id, name) {
            Swal.fire({ title: 'Hapus Item?', text: "Anda yakin ingin menghapus " + name + " dari inventaris ini?", icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#64748b', confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal', background: 'rgba(255, 255, 255, 0.95)', customClass: { popup: 'glass-card border-0 shadow-lg', confirmButton: 'btn-premium bg-danger border-0 px-4 py-2 rounded-3', cancelButton: 'btn btn-light border-0 px-4 py-2 rounded-3 ms-2' }, buttonsStyling: false }).then((result) => { if (result.isConfirmed) { document.getElementById('deleteItemForm' + id).submit(); } })
        }
        <?php if (isset($success_msg)): ?> Swal.fire({ icon: 'success', title: 'Berhasil!', text: '<?= $success_msg ?>', timer: 2000, showConfirmButton: false, background: 'rgba(255, 255, 255, 0.95)', customClass: { popup: 'glass-card border-0 shadow-lg' } }); <?php endif; ?>
        <?php if (isset($error_msg)): ?> Swal.fire({ icon: 'error', title: 'Gagal!', text: '<?= $error_msg ?>', timer: 2000, showConfirmButton: false, background: 'rgba(255, 255, 255, 0.95)', customClass: { popup: 'glass-card border-0 shadow-lg' } }); <?php endif; ?>
    </script>
</body>

</html>