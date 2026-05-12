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

// Handle Add User
if (isset($_POST['add_user'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }
    $username = $_POST['username'];
    $pass = password_hash($_POST['pass'], PASSWORD_DEFAULT);
    $nama = $_POST['nama'];
    $role = $_POST['role'];
    $stmt = $conn->prepare("INSERT INTO users (username, pass, nama, role, status) VALUES (?, ?, ?, ?, 'Aktif')");
    $stmt->bind_param("ssss", $username, $pass, $nama, $role);
    if ($stmt->execute()) {
        $success_msg = "User baru berhasil ditambahkan!";
    }
    $stmt->close();
}

// Handle Update User
if (isset($_POST['update_user'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }
    $user_id = $_POST['target_user_id'];
    $username = $_POST['username'];
    $nama = $_POST['nama'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("UPDATE users SET username = ?, nama = ?, role = ? WHERE id = ?");
    $stmt->bind_param("sssi", $username, $nama, $role, $user_id);
    if ($stmt->execute()) {
        $success_msg = "Data user berhasil diperbarui!";
    }
    $stmt->close();
}

// Handle Delete User
if (isset($_POST['delete_user_id'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }
    $user_id = $_POST['delete_user_id'];
    if ($user_id != $_SESSION['id']) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $success_msg = "User berhasil dihapus!";
        }
        $stmt->close();
    }
}

// Handle Admin Change User Password
if (isset($_POST['admin_change_pass'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }
    $target_user_id = $_POST['target_user_id'];
    $new_pass = password_hash($_POST['new_pass'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET pass = ? WHERE id = ?");
    $stmt->bind_param("si", $new_pass, $target_user_id);
    if ($stmt->execute()) {
        $success_msg = "Password user berhasil diperbarui!";
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

// Fetch users and store in array to reuse for modals
$user_query = mysqli_query($conn, "SELECT id, username, nama, role, status, created_at FROM users ORDER BY role ASC, nama ASC");
$user_list = [];
while ($row = mysqli_fetch_assoc($user_query)) {
    $user_list[] = $row;
}
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
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
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
                                <?php foreach ($user_list as $u): ?>
                                    <tr>
                                        <td>
                                            <span class="row-title"><?= htmlspecialchars($u['nama']) ?></span>
                                            <span class="row-subtitle"><?= htmlspecialchars($u['username']) ?></span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge-status <?= $u['role'] == 'Admin' ? 'badge-critical' : 'badge-success' ?>">
                                                <i data-lucide="<?= $u['role'] == 'Admin' ? 'shield-check' : 'user' ?>"
                                                    style="width:12px;"></i>
                                                <?= $u['role'] ?>
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-1">
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-warning border-0 rounded-3 p-2"
                                                    data-bs-toggle="modal" data-bs-target="#editUserModal<?= $u['id'] ?>">
                                                    <i data-lucide="edit-3" style="width:18px;"></i>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-primary border-0 rounded-3 p-2"
                                                    data-bs-toggle="modal" data-bs-target="#resetPassModal<?= $u['id'] ?>">
                                                    <i data-lucide="key" style="width:18px;"></i>
                                                </button>
                                                <?php if ($u['id'] != $_SESSION['id']): ?>
                                                    <form id="deleteUserForm<?= $u['id'] ?>" action="" method="POST"
                                                        class="mb-0">
                                                        <input type="hidden" name="csrf_token"
                                                            value="<?= $_SESSION['csrf_token'] ?>">
                                                        <input type="hidden" name="delete_user_id" value="<?= $u['id'] ?>">
                                                        <button type="button"
                                                            onclick="confirmDeleteUser(<?= $u['id'] ?>, '<?= htmlspecialchars($u['nama']) ?>')"
                                                            class="btn btn-sm btn-outline-danger border-0 rounded-3 p-2">
                                                            <i data-lucide="trash-2" style="width:18px;"></i>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals (Moved outside table to fix flickering) -->
    <?php foreach ($user_list as $u): ?>
        <div class="modal fade" id="resetPassModal<?= $u['id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content glass-card border-0">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title">Reset Password</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="target_user_id" value="<?= $u['id'] ?>">
                        <div class="modal-body text-start">
                            <p class="small text-secondary mb-3">Reset password untuk
                                <strong><?= htmlspecialchars($u['nama']) ?></strong></p>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Password Baru</label>
                                <input type="password" name="new_pass" class="form-control" required>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="submit" name="admin_change_pass"
                                class="btn-premium w-100 justify-content-center">Update Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editUserModal<?= $u['id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content glass-card border-0">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title">Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="target_user_id" value="<?= $u['id'] ?>">
                        <div class="modal-body text-start">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Username</label>
                                <input type="text" name="username" class="form-control"
                                    value="<?= htmlspecialchars($u['username']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control"
                                    value="<?= htmlspecialchars($u['nama']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Role</label>
                                <select name="role" class="form-control" required>
                                    <option value="User" <?= $u['role'] == 'User' ? 'selected' : '' ?>>PIC / User</option>
                                    <option value="Admin" <?= $u['role'] == 'Admin' ? 'selected' : '' ?>>Administrator</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="submit" name="update_user" class="btn-premium w-100 justify-content-center">Update
                                User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <!-- Modal Self Change Password -->
    <div class="modal fade" id="selfChangePassModal" tabindex="-1" aria-hidden="true">
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
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: '<?= $success_msg ?>', timer: 2000, showConfirmButton: false, background: 'rgba(255, 255, 255, 0.9)', customClass: { popup: 'glass-card border-0 shadow-lg' } });
        <?php endif; ?>
        <?php if (isset($error_msg)): ?>
            Swal.fire({ icon: 'error', title: 'Gagal!', text: '<?= $error_msg ?>', timer: 2000, showConfirmButton: false, background: 'rgba(255, 255, 255, 0.9)', customClass: { popup: 'glass-card border-0 shadow-lg' } });
        <?php endif; ?>
    </script>
    <footer class="py-4 mt-5 border-top border-light text-center">
        <div class="container-fluid px-4">
            <p class="text-secondary small mb-0">&copy; <?= date('Y') ?> <span class="fw-bold text-primary">PT CBA
                    Chemical Industry</span> | Monitoring Kotak P3K - Team IT Pabrik</p>
        </div>
    </footer>
</body>

</html>