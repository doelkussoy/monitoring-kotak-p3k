<?php
session_start();
include "db.php";

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: dashboard.php");
    exit;
}

// Fetch Location Details
$q_lokasi = mysqli_query($conn, "SELECT l.*, u.nama as pic_nama FROM p3k_lokasi l LEFT JOIN users u ON l.pic = u.username WHERE l.id = '$id'");
$lokasi = mysqli_fetch_assoc($q_lokasi);

if (!$lokasi) {
    header("Location: dashboard.php");
    exit;
}

// Access Control
if ($_SESSION['role'] != 'Admin' && $_SESSION['username'] != $lokasi['pic']) {
    echo "<script>alert('Akses Ditolak! Anda bukan penanggung jawab lokasi ini.'); window.location='dashboard.php';</script>";
    exit;
}

// Handle Stock Adjustment
if (isset($_POST['adjust_stock'])) {
    $item_id = $_POST['item_id'];
    $new_stok = $_POST['stok'];
    $new_min_stok = $_POST['min_stok'];
    $new_expired = $_POST['tgl_kadaluarsa'];
    $item_name = $_POST['item_name'];

    $update = mysqli_query($conn, "UPDATE p3k_items SET stok = '$new_stok', min_stok = '$new_min_stok', tgl_kadaluarsa = '$new_expired' WHERE id = '$item_id'");

    if ($update) {
        $msg = "Update $item_name (Stok: $new_stok, Min: $new_min_stok, Exp: $new_expired) oleh " . $_SESSION['username'];
        mysqli_query($conn, "INSERT INTO p3k_activity (lokasi_id, activity_type, message) VALUES ('$id', 'UPDATE', '$msg')");
        $success_msg = "Perubahan berhasil disimpan!";
    }
}

// Handle Add Item
if (isset($_POST['add_item'])) {
    $nama_item = mysqli_real_escape_string($conn, $_POST['nama_item']);
    $stok = $_POST['stok'];
    $min_stok = $_POST['min_stok'];
    $satuan = mysqli_real_escape_string($conn, $_POST['satuan']);
    $tgl_kadaluarsa = $_POST['tgl_kadaluarsa'];

    $insert = mysqli_query($conn, "INSERT INTO p3k_items (lokasi_id, nama_item, stok, min_stok, satuan, tgl_kadaluarsa) 
        VALUES ('$id', '$nama_item', '$stok', '$min_stok', '$satuan', '$tgl_kadaluarsa')");

    if ($insert) {
        $msg = "Menambahkan item baru: $nama_item oleh " . $_SESSION['username'];
        mysqli_query($conn, "INSERT INTO p3k_activity (lokasi_id, activity_type, message) VALUES ('$id', 'UPDATE', '$msg')");
        $success_msg = "Item baru berhasil ditambahkan!";
        // Refresh items
        $items = mysqli_query($conn, "SELECT * FROM p3k_items WHERE lokasi_id = '$id' ORDER BY nama_item ASC");
    }
}

// Handle Delete Item
if (isset($_POST['delete_item'])) {
    $item_id = $_POST['item_id'];
    $item_name = $_POST['item_name'];

    $delete = mysqli_query($conn, "DELETE FROM p3k_items WHERE id = '$item_id'");
    if ($delete) {
        $msg = "Menghapus item: $item_name oleh " . $_SESSION['username'];
        mysqli_query($conn, "INSERT INTO p3k_activity (lokasi_id, activity_type, message) VALUES ('$id', 'UPDATE', '$msg')");
        $success_msg = "Item berhasil dihapus!";
        // Refresh items
        $items = mysqli_query($conn, "SELECT * FROM p3k_items WHERE lokasi_id = '$id' ORDER BY nama_item ASC");
    }
}
$items = mysqli_query($conn, "SELECT * FROM p3k_items WHERE lokasi_id = '$id' ORDER BY nama_item ASC");

// Threshold for expiration alert (30 days)
$next_month = date('Y-m-d', strtotime('+30 days'));
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Lokasi - <?= $lokasi['nama_lokasi'] ?> | MONITORING KOTAK P3K</title>
    <link rel="shortcut icon" href="assets/images/cba-text.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <!-- Navbar (simplified) -->
    <nav class="navbar glass-navbar fixed-top py-2">
        <div class="container-fluid px-4">
            <div class="d-flex align-items-center gap-3">
                <a href="dashboard.php"
                    class="logo-wrapper bg-accent-light text-accent p-2 rounded-4 shadow-sm text-decoration-none">
                    <i data-lucide="arrow-left" style="width:22px; height:22px;"></i>
                </a>
                <a href="dashboard.php" class="d-flex align-items-center gap-3 text-decoration-none">
                    <img src="assets/images/cba-text.png" alt="Logo CBA" style="height: 35px; width: auto;">
                    <div class="brand-text border-start ps-3">
                        <h1 class="h6 mb-0 fw-bold tracking-tight text-primary">
                            <?= strtoupper($lokasi['nama_lokasi']) ?>
                        </h1>
                        <p class="text-secondary tiny mb-0 fw-medium"
                            style="font-size: 0.6rem; letter-spacing: 0.05em;">PIC:
                            <?= $lokasi['pic_nama'] ?? $lokasi['pic'] ?>
                        </p>
                    </div>
                </a>
            </div>

            <div class="d-flex align-items-center gap-4">
                <nav class="d-none d-md-flex align-items-center gap-1">
                    <a href="dashboard.php" class="nav-link-premium">
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

                <!-- Mobile Bottom Nav -->
                <nav class="mobile-bottom-nav desktop-hide">
                    <a href="dashboard.php" class="mobile-nav-item">
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

    <div class="container" style="margin-top: 100px; padding-bottom: 50px;">
        <?php if (isset($success_msg)): ?>
            <div class="alert alert-success glass-card border-0 mb-4 fade-in">
                <i data-lucide="check-circle" class="me-2" style="width:18px;"></i>
                <?= $success_msg ?>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-12">
                <div class="glass-card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="mb-0">Daftar Item</h4>
                        <button class="btn-premium btn-sm" data-bs-toggle="modal" data-bs-target="#addItemModal">
                            <i data-lucide="plus" style="width:16px;"></i>
                            Tambah Item
                        </button>
                    </div>

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
                            <?php $modals = []; ?>
                            <tbody>
                                <?php while ($item = mysqli_fetch_assoc($items)):
                                    $is_expired = ($item['tgl_kadaluarsa'] <= $next_month);
                                    $is_low_stock = ($item['stok'] <= $item['min_stok']);
                                    ?>
                                    <tr>
                                        <form action="" method="POST">
                                            <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                            <input type="hidden" name="item_name" value="<?= $item['nama_item'] ?>">
                                            <td>
                                                <span class="row-title"><?= $item['nama_item'] ?></span>
                                                <div class="d-flex align-items-center gap-1 mt-1">
                                                    <span class="text-secondary tiny">Exp:</span>
                                                    <input type="date" name="tgl_kadaluarsa"
                                                        class="form-control form-control-sm border-0 bg-transparent p-0 tiny fw-medium text-secondary"
                                                        value="<?= $item['tgl_kadaluarsa'] ?>"
                                                        style="width: auto; font-size: 0.75rem;">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <input type="number" name="stok"
                                                        class="form-control form-control-sm border-0 bg-light-subtle fw-bold text-primary"
                                                        value="<?= $item['stok'] ?>" min="0"
                                                        style="width: 70px; padding: 0.25rem 0.5rem;" required>
                                                    <span class="text-secondary small"><?= $item['satuan'] ?></span>
                                                </div>
                                            </td>
                                            <td class="mobile-hide">
                                                <div class="d-flex align-items-center gap-2">
                                                    <input type="number" name="min_stok"
                                                        class="form-control form-control-sm border-0 bg-transparent text-secondary"
                                                        value="<?= $item['min_stok'] ?>" min="0"
                                                        style="width: 60px; padding: 0.25rem 0.5rem;" required>
                                                    <span class="text-secondary small"><?= $item['satuan'] ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if ($is_low_stock): ?>
                                                    <span class="badge-status badge-critical">
                                                        <i data-lucide="alert-triangle" style="width:12px;"></i>
                                                        Rendah
                                                    </span>
                                                <?php elseif ($is_expired): ?>
                                                    <span class="badge-status badge-warning">
                                                        <i data-lucide="clock" style="width:12px;"></i>
                                                        Kadaluarsa
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge-status badge-success">
                                                        <i data-lucide="check" style="width:12px;"></i>
                                                        Aman
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex justify-content-end align-items-center gap-1">
                                                    <button type="submit" name="adjust_stock"
                                                        class="btn btn-sm btn-accent-light text-accent rounded-3 p-2"
                                                        title="Simpan Perubahan">
                                                        <i data-lucide="check-circle" style="width:18px;"></i>
                                                    </button>
                                        </form>
                                        <form id="deleteItemForm<?= $item['id'] ?>" action="" method="POST" class="mb-0">
                                            <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                            <input type="hidden" name="item_name" value="<?= $item['nama_item'] ?>">
                                            <input type="hidden" name="delete_item" value="1">
                                            <button type="button"
                                                onclick="confirmDeleteItem(<?= $item['id'] ?>, '<?= $item['nama_item'] ?>')"
                                                class="btn btn-sm btn-outline-danger border-0 rounded-3 p-2">
                                                <i data-lucide="trash-2" style="width:18px;"></i>
                                            </button>
                                        </form>
                        </div>
                        </td>
                        </tr>

                        <!-- Modal Adjust Stock -->
                        <div class="modal fade" id="adjustModal<?= $item['id'] ?>" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content glass-card border-0">
                                    <div class="modal-header border-0 pb-0">
                                        <h5 class="modal-title">Sesuaikan Stok</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="" method="POST">
                                        <div class="modal-body p-4">
                                            <p class="text-secondary small mb-3">Update jumlah stok untuk
                                                <strong><?= $item['nama_item'] ?></strong>.
                                            </p>
                                            <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                            <input type="hidden" name="item_name" value="<?= $item['nama_item'] ?>">
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Jumlah Stok Baru</label>
                                                <div class="input-group">
                                                    <input type="number" name="stok" class="form-control"
                                                        value="<?= $item['stok'] ?>" min="0" required>
                                                    <span class="input-group-text bg-light"><?= $item['satuan'] ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 pt-0">
                                            <button type="button" class="btn btn-light rounded-3"
                                                data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" name="adjust_stock" class="btn-premium">Simpan
                                                Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php
                        // Collect modals to render after the table
                        $modals[] = $item;
                                endwhile; ?>
                    </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php foreach ($modals as $item):
        $is_low_stock = ($item['stok'] <= $item['min_stok']);
        ?>
        <!-- Modal Adjust Stock -->
        <div class="modal fade" id="adjustModal<?= $item['id'] ?>" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content glass-card border-0">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title">Sesuaikan Stok</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="" method="POST">
                        <div class="modal-body p-4">
                            <p class="text-secondary small mb-3">Update jumlah stok untuk
                                <strong><?= $item['nama_item'] ?></strong>.
                            </p>
                            <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                            <input type="hidden" name="item_name" value="<?= $item['nama_item'] ?>">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Jumlah Stok Baru</label>
                                <div class="input-group">
                                    <input type="number" name="stok" class="form-control" value="<?= $item['stok'] ?>"
                                        min="0" required>
                                    <span class="input-group-text bg-light"><?= $item['satuan'] ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-light rounded-3" data-bs-toggle="modal"
                                data-bs-target="#adjustModal<?= $item['id'] ?>">Batal</button>
                            <button type="submit" name="adjust_stock" class="btn-premium">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
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
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nama Item</label>
                            <input type="text" name="nama_item" class="form-control" placeholder="Contoh: Kasa"
                                required>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Stok Awal</label>
                                <input type="number" name="stok" class="form-control" value="0" min="0" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Min. Stok</label>
                                <input type="number" name="min_stok" class="form-control" value="0" min="0" required>
                            </div>
                        </div>
                        <div class="mb-3 mt-3">
                            <label class="form-label small fw-bold">Satuan</label>
                            <input type="text" name="satuan" class="form-control" placeholder="pcs, botol, box"
                                required>
                        </div>
                        <div class="mb-0">
                            <label class="form-label small fw-bold">Tanggal Kadaluarsa</label>
                            <input type="date" name="tgl_kadaluarsa" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="add_item" class="btn-premium">Simpan Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="py-4 mt-5 border-top border-light">
        <div class="container-fluid px-4 text-center">
            <p class="text-secondary small mb-0">
                &copy; <?= date('Y') ?> <span class="fw-bold text-primary">PT CBA Chemical Industry</span> | Monitoring
                Kotak P3K - Team IT Pabrik
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        lucide.createIcons();

        function confirmDeleteItem(id, name) {
            Swal.fire({
                title: 'Hapus Item?',
                text: "Anda yakin ingin menghapus " + name + " dari inventaris ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                background: 'rgba(255, 255, 255, 0.95)',
                customClass: {
                    popup: 'glass-card border-0 shadow-lg',
                    confirmButton: 'btn-premium bg-danger border-0 px-4 py-2 rounded-3',
                    cancelButton: 'btn btn-light border-0 px-4 py-2 rounded-3 ms-2'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('deleteItemForm' + id).submit();
                }
            })
        }

        <?php if (isset($success_msg)): ?>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '<?= $success_msg ?>',
                timer: 2000,
                showConfirmButton: false,
                background: 'rgba(255, 255, 255, 0.95)',
                customClass: { popup: 'glass-card border-0 shadow-lg' }
            });
        <?php endif; ?>
    </script>
</body>

</html>