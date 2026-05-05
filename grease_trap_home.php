<?php
include "config/koneksi.php";
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: index.php");
  exit;
}

// Setup tabel jika belum ada
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `grease_trap` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no_kode` varchar(50) NOT NULL,
  `nama_sarana` varchar(100) DEFAULT NULL,
  `lokasi` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `checklist_grease_trap` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `grease_trap_id` int(11) NOT NULL,
  `tahun` int(4) NOT NULL,
  `bulan` tinyint(2) NOT NULL,
  `tanggal_cek` date DEFAULT NULL,
  `valve_handle` enum('Ok','Nok') DEFAULT NULL,
  `hose_coupling_conect` enum('Ok','Nok') DEFAULT NULL,
  `baut_valve_handle` enum('Ok','Nok') DEFAULT NULL,
  `fire_hose` enum('Ok','Nok') DEFAULT NULL,
  `slang_grease_trap` enum('Ok','Nok') DEFAULT NULL,
  `nozzle` enum('Ok','Nok') DEFAULT NULL,
  `box_grease_trap` enum('Ok','Nok') DEFAULT NULL,
  `paraf` varchar(100) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `users_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Cek apakah ada data Grease Trap, jika tidak insert sample
$cekGreaseTrap = mysqli_query($conn, "SELECT COUNT(*) as total FROM grease_trap");
$rowCek = mysqli_fetch_assoc($cekGreaseTrap);
// if ($rowCek['total'] == 0) {
//   mysqli_query($conn, "INSERT INTO `grease_trap` (`no_kode`, `nama_sarana`, `lokasi`) VALUES
//     ('KNR-1','Grease Trap 9 KG','GREASE TRAP UTAMA LANTAI 1'),
//     ('KNR-2','Grease Trap 6 KG','GREASE TRAP UTAMA LANTAI 2'),
//     ('KNR-3','Grease Trap 3 KG','RUANG SERVER')");
// }

// Handle tambah Grease Trap
if (isset($_POST['tambah_grease_trap'])) {
  $kode = mysqli_real_escape_string($conn, $_POST['no_kode']);
  $nama = mysqli_real_escape_string($conn, $_POST['nama_sarana']);
  $lok = mysqli_real_escape_string($conn, $_POST['lokasi']);
  mysqli_query($conn, "INSERT INTO grease_trap (no_kode, nama_sarana, lokasi) VALUES ('$kode','$nama','$lok')");
  header("Location: grease_trap_home.php");
  exit;
}

$keyword = isset($_GET['keyword']) ? mysqli_real_escape_string($conn, $_GET['keyword']) : '';
$where = "";
if ($keyword != '') {
  $where = "WHERE no_kode LIKE '%$keyword%' OR nama_sarana LIKE '%$keyword%' OR lokasi LIKE '%$keyword%'";
}

$listGreaseTrap = mysqli_query($conn, "SELECT * FROM grease_trap $where ORDER BY no_kode ASC");
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sarana Prasarana - Manajemen Grease Trap</title>
  <link rel="icon" type="image/png" href="assets/images/cba.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a404219d80.js" crossorigin="anonymous"></script>
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    :root {
      --blue: #2563eb;
      --blue-dark: #1e3a8a;
      --blue-light: #3b82f6;
    }

    body {
      background: #f4f6f9;
      font-family: 'Segoe UI', sans-serif;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .navbar-grease_trap {
      background: linear-gradient(135deg, var(--blue-dark), var(--blue-light));
    }

    .card-grease_trap {
      border: none;
      border-radius: 16px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, .1);
    }

    .card-grease_trap-header {
      background: #fff;
      color: #333;
      border-radius: 16px 16px 0 0;
      padding: 20px 24px;
    }

    .btn-grease_trap {
      background: var(--blue);
      border: none;
      color: #fff;
    }

    .btn-grease_trap:hover {
      background: var(--blue-dark);
      color: #fff;
    }

    .grease_trap-badge {
      background: rgba(37, 99, 235, 0.1);
      color: var(--blue);
      font-weight: 600;
      padding: 4px 10px;
      border-radius: 6px;
      font-size: 13px;
    }

    .table thead th {
      background: var(--blue);
      color: #fff;
      border: none;
    }

    .btn-kartu {
      background: var(--blue);
      color: #fff;
      border: none;
      border-radius: 8px;
    }

    .btn-kartu:hover {
      background: var(--blue-dark);
      color: #fff;
    }

    .modal-overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, .5);
      z-index: 1050;
      align-items: center;
      justify-content: center;
    }

    .modal-overlay.active {
      display: flex;
    }

    .modal-box {
      background: #fff;
      border-radius: 16px;
      padding: 32px;
      width: 90%;
      max-width: 500px;
    }

    footer {
      background: linear-gradient(135deg, var(--blue-dark), var(--blue-light));
      color: #fff;
      margin-top: 40px;
    }
  </style>
</head>

<body>
  <nav class="navbar navbar-grease_trap fixed-top py-3 shadow">
    <div class="container-fluid d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center gap-2 text-white" style="cursor:pointer" onclick="window.location.href='dashboard.php'">
        <img src="assets/images/cba.png" alt="Logo CBA" style="height:40px; background:white; padding:4px; border-radius:8px;">
        <span class="fw-bold fs-5" style="letter-spacing:1px">SISTEM SARANA PRASARANA</span>
      </div>
      <div class="d-flex align-items-center gap-3">
        <span class="text-white"><i class="fa-solid fa-user me-1"></i><?= $_SESSION['username'] ?></span>
        <a href="logout.php" class="btn btn-sm btn-outline-light rounded-pill px-3">
          <i class="fa-solid fa-right-from-bracket me-1"></i>Logout
        </a>
      </div>
    </div>
  </nav>

  <div class="container" style="margin-top:100px; padding-bottom:40px; flex:1;">
    <div class="card card-grease_trap">
      <div class="card-grease_trap-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
          <h5 class="fw-bold mb-0 text-primary"><i class="fa-solid fa-sink me-2"></i>Daftar Unit Grease Trap</h5>
          <small class="opacity-75">Kartu Riwayat Pengecekan</small>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
          <form method="GET" class="d-flex m-0">
            <div class="input-group input-group-sm">
              <input type="text" name="keyword" class="form-control" placeholder="Cari Kode/Lokasi..."
                value="<?= htmlspecialchars($keyword) ?>">
              <button type="submit" class="btn btn-light text-primary"><i class="fa-solid fa-search"></i></button>
              <?php if ($keyword != ''): ?>
                <a href="grease_trap_home.php" class="btn btn-secondary"><i class="fa-solid fa-times"></i></a>
              <?php endif; ?>
            </div>
          </form>

          <button class="btn btn-primary btn-sm px-3 rounded-pill fw-bold shadow-sm"
            onclick="document.getElementById('modalTambah').classList.add('active')">
            <i class="fa-solid fa-plus me-1"></i>Tambah Grease Trap
          </button>
        </div>
      </div>
      <div class="card-body p-4">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead>
              <tr>
                <th>No</th>
                <th>No. Kode</th>
                <th>Nama Sarana/Prasarana</th>
                <th>Lokasi</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php $no = 1;
              while ($row = mysqli_fetch_assoc($listGreaseTrap)): ?>
                <tr>
                  <td><?= $no++ ?></td>
                  <td><span class="grease_trap-badge"><?= htmlspecialchars($row['no_kode']) ?></span></td>
                  <td><?= htmlspecialchars($row['nama_sarana']) ?></td>
                  <td><i class="fa-solid fa-location-dot text-danger me-1"></i><?= htmlspecialchars($row['lokasi']) ?>
                  </td>
                  <td>
                    <a href="grease_trap_kartu.php?grease_trap_id=<?= $row['id'] ?>" class="btn btn-kartu btn-sm me-1">
                      <i class="fa-solid fa-table me-1"></i>Kartu Riwayat
                    </a>
                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(<?= $row['id'] ?>)">
                      <i class="fa-solid fa-trash"></i>
                    </button>
                  </td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Tambah Grease Trap -->
  <div class="modal-overlay" id="modalTambah">
    <div class="modal-box">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0 text-primary"><i class="fa-solid fa-sink me-2"></i>Tambah Unit Grease Trap</h5>
        <button class="btn-close" onclick="document.getElementById('modalTambah').classList.remove('active')"></button>
      </div>
      <hr>
      <form method="POST">
        <div class="mb-3">
          <label class="form-label fw-semibold small">No. Kode</label>
          <input type="text" class="form-control" name="no_kode" placeholder="Contoh: GT-01" required>
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold">Nama Sarana/Prasarana</label>
          <input type="text" class="form-control" name="nama_sarana" placeholder="Contoh: Grease Trap A" required>
        </div>
        <div class="mb-4">
          <label class="form-label fw-semibold">Lokasi</label>
          <input type="text" class="form-control" name="lokasi" placeholder="Contoh: LABORATORIUM" required>
        </div>
        <div class="d-flex gap-2">
          <button type="submit" name="tambah_grease_trap" class="btn btn-primary w-100 py-2 fw-bold">
            <i class="fa-solid fa-save me-1"></i>Simpan
          </button>
          <button type="button" class="btn btn-secondary px-4"
            onclick="document.getElementById('modalTambah').classList.remove('active')">Batal</button>
        </div>
      </form>
    </div>
  </div>

  <footer class="py-3 text-center">
    &copy; <?= date('Y') ?> - Sistem Perawatan Grease Trap | Team IT Pabrik
  </footer>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    function confirmDelete(id) {
      Swal.fire({
        title: 'Hapus Data Grease Trap?',
        text: "Semua riwayat perawatan untuk Grease Trap ini akan ikut terhapus secara permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fa-solid fa-trash me-1"></i> Ya, Hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        customClass: {
          confirmButton: 'btn btn-danger mx-2',
          cancelButton: 'btn btn-secondary mx-2'
        },
        buttonsStyling: false
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = 'hapus.php?hapus_grease_trap=1&id=' + id;
        }
      });
    }
  </script>

  <?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'hapus_sukses'): ?>
    <script>
      Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: 'Data Grease Trap beserta perawatan telah dihapus.',
        showConfirmButton: false,
        timer: 2500,
        toast: true,
        position: 'top-end'
      });
    </script>
  <?php endif; ?>
</body>

</html>