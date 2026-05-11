<?php
session_start();
include "db.php";

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'Admin') {
    header("Location: dashboard.php");
    exit;
}

// Handle Add User
if (isset($_POST['add_user'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $pass = mysqli_real_escape_string($conn, $_POST['pass']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $role = $_POST['role'];

    $insert = mysqli_query($conn, "INSERT INTO users (username, pass, nama, role, status) VALUES ('$username', '$pass', '$nama', '$role', 'Aktif')");
    if ($insert) {
        $success_msg = "User baru berhasil ditambahkan!";
    }
}

// Handle Delete User
if (isset($_POST['delete_user_id'])) {
    $user_id = $_POST['delete_user_id'];
    // Prevent deleting self
    if ($user_id != $_SESSION['id']) {
        mysqli_query($conn, "DELETE FROM users WHERE id = '$user_id'");
        $success_msg = "User berhasil dihapus!";
    }
}

$users = mysqli_query($conn, "SELECT * FROM users ORDER BY role ASC, nama ASC");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan - MONITORING KOTAK P3K</title>
    <link rel="shortcut icon" href="assets/images/cba-text.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <nav class="navbar glass-navbar fixed-top py-2">
        <div class="container-fluid px-4">
            <a href="dashboard.php" class="d-flex align-items-center gap-3 text-decoration-none">
                <img src="assets/images/cba-text.png" alt="Logo CBA" style="height: 35px; width: auto;">
                <div class="brand-text border-start ps-3 d-none d-sm-block">
                    <h1 class="h6 mb-0 fw-bold tracking-tight text-primary">MONITORING KOTAK P3K</h1>
                    <p class="text-secondary tiny mb-0 fw-medium" style="font-size: 0.6rem; letter-spacing: 0.05em;">DIGITAL MONITORING SYSTEM</p>
                </div>
            </a>

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
                        <a href="pengaturan.php" class="nav-link-premium active">
                            <i data-lucide="settings" style="width:16px;"></i>
                            Pengaturan
                        </a>
                    <?php endif; ?>
                </nav>
                
                <div class="vr opacity-10 d-none d-md-block" style="height: 24px;"></div>
                
                <div class="dropdown">
                    <div class="user-profile-trigger d-flex align-items-center gap-2" data-bs-toggle="dropdown" role="button">
                        <div class="avatar-circle bg-accent-light text-accent rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
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
                            <a class="dropdown-item rounded-3 small d-flex align-items-center gap-2 py-2" href="logout.php">
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
    <nav class="mobile-bottom-nav desktop-hide shadow-lg">
        <a href="dashboard.php" class="mobile-nav-item">
            <i data-lucide="layout-dashboard"></i>
            <span>Dashboard</span>
        </a>
        <?php if ($_SESSION['role'] == 'Admin'): ?>
            <a href="laporan.php" class="mobile-nav-item">
                <i data-lucide="file-bar-chart"></i>
                <span>Laporan</span>
            </a>
            <a href="pengaturan.php" class="mobile-nav-item active">
                <i data-lucide="settings"></i>
                <span>Atur</span>
            </a>
        <?php endif; ?>
        <a href="logout.php" class="mobile-nav-item text-danger">
            <i data-lucide="log-out"></i>
            <span>Keluar</span>
        </a>
    </nav>

    <div class="container py-3" style="margin-top: 100px; padding-bottom: 100px;">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="glass-card p-4">
                    <h4 class="h5 mb-4">Tambah User Baru</h4>
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Password</label>
                            <input type="password" name="pass" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Role</label>
                            <select name="role" class="form-control" required>
                                <option value="User">PIC / User</option>
                                <option value="Admin">Administrator</option>
                            </select>
                        </div>
                        <button type="submit" name="add_user" class="btn-premium w-100 justify-content-center">Simpan
                            User</button>
                    </form>
                </div>
            </div>
            <div class="col-md-8">
                <div class="glass-card overflow-hidden">
                    <div class="table-responsive">
                        <table class="table-premium">
                            <thead>
                                <tr>
                                    <th>NAMA & USERNAME</th>
                                    <th>HAK AKSES</th>
                                    <th class="text-end">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($u = mysqli_fetch_assoc($users)): ?>
                                    <tr>
                                        <td>
                                            <span class="row-title"><?= $u['nama'] ?></span>
                                            <span class="row-subtitle"><?= $u['username'] ?></span>
                                        </td>
                                        <td>
                                            <span class="badge-status <?= $u['role'] == 'Admin' ? 'badge-critical' : 'badge-success' ?>">
                                                <i data-lucide="<?= $u['role'] == 'Admin' ? 'shield-check' : 'user' ?>" style="width:12px;"></i>
                                                <?= $u['role'] ?>
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <?php if ($u['id'] != $_SESSION['id']): ?>
                                                <form id="deleteUserForm<?= $u['id'] ?>" action="" method="POST" class="mb-0">
                                                    <input type="hidden" name="delete_user_id" value="<?= $u['id'] ?>">
                                                    <button type="button"
                                                        onclick="confirmDeleteUser(<?= $u['id'] ?>, '<?= $u['nama'] ?>')"
                                                        class="btn btn-sm btn-outline-danger border-0 rounded-3 p-2">
                                                        <i data-lucide="trash-2" style="width:18px;"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        lucide.createIcons();

        function confirmDeleteUser(id, name) {
            Swal.fire({
                title: 'Hapus User?',
                text: "Akses login untuk " + name + " akan dicabut permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                background: 'rgba(255, 255, 255, 0.9)',
                backdrop: `rgba(15, 23, 42, 0.1)`,
                customClass: {
                    popup: 'glass-card border-0 shadow-lg',
                    confirmButton: 'btn-premium bg-danger border-0 px-4 py-2 rounded-3',
                    cancelButton: 'btn btn-light border-0 px-4 py-2 rounded-3 ms-2'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('deleteUserForm' + id).submit();
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
                background: 'rgba(255, 255, 255, 0.9)',
                customClass: {
                    popup: 'glass-card border-0 shadow-lg'
                }
            });
        <?php endif; ?>
    </script>
    <!-- Footer -->
    <footer class="py-4 mt-5 border-top border-light">
        <div class="container-fluid px-4 text-center">
            <p class="text-secondary small mb-0">
                &copy; <?= date('Y') ?> <span class="fw-bold text-primary">PT CBA Chemical Industry</span> | Monitoring Kotak P3K - Team IT Pabrik
            </p>
        </div>
    </footer>
</body>

</html>