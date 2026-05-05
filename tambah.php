<?php
include "config/koneksi.php";

if (isset($_POST['tambah_checklist'])) {
    date_default_timezone_set('Asia/Jakarta');

    $apar_id = $_POST['apar_id'];
    $kloset = $_POST['kloset'];
    $wastafel = $_POST['wastafel'];
    $lantai = $_POST['lantai'];
    $dinding = $_POST['dinding'];
    $kaca = $_POST['kaca'];
    $bau = $_POST['bau'];
    $sabun = $_POST['sabun'];
    $users_id = $_POST['users_id'];

    $query = "INSERT INTO checklist (tanggal, apar_id, kloset, wastafel, lantai, dinding, kaca, bau,
    sabun, users_id) VALUES (NOW(), '$apar_id', '$kloset', '$wastafel', '$lantai', '$dinding', '$kaca', '$bau', '$sabun', '$users_id')";
    $result = mysqli_query($conn, $query);

    if ($result) {
        echo "<script>window.location='home.php'</script>";
    } else {
        die("Maaf, data gagal disimpan: " . mysqli_error($conn));
    }
}
elseif (isset($_POST['tambah_akun'])) {
    $username = $_POST['username'];
    $pass = $_POST['password'];
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $status = $_POST['status'];
    $role = $_POST['role'];

    $query = "INSERT INTO users (username, pass, nama, email, status, role)
              VALUES ('$username', '$pass', '$nama', '$email', '$status', '$role')";
    $result = mysqli_query($conn, $query);

    if ($result) {
        echo "<script>window.location='index.php'</script>";
    } else {
        die("Maaf data gagal disimpan: " . mysqli_error($conn));
    }
}

elseif (isset($_POST['tambah_apar'])) {
    $apar_id = $_POST['apar_id'];
    $lokasi = $_POST['lokasi'];
    $keterangan = $_POST['keterangan'];

    $query = "INSERT INTO apar_data (id, lokasi, keterangan)
              VALUES ('$apar_id', '$lokasi', '$keterangan')";
    $result = mysqli_query($conn, $query);

    if ($result) {
        echo "<script>window.location='data_apar.php'</script>";
    } else {
        die("Maaf data gagal disimpan: " . mysqli_error($conn));
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Login</title>
    <!-- Eskternal CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/mobile_view.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/a404219d80.js" crossorigin="anonymous"></script>
</head>

<body>
    <div class="container-fluid">
        <!-- Navbar -->
        <nav class="navbar bg-primary fixed-top navbar-expand-lg py-3 shadow">
            <div class="container-fluid">
                <div class="navbar-brand d-inline-block align-text-top">
                    <i class="fa-solid fa-check-to-slot me-2" style="color: #ffffff;"></i>
                    <span class="text-white fw-bold h5" style="letter-spacing: 1.2px;">SISTEM PERAWATAN APAR</span>
                </div>
                <span class="navbar-text text-white">
                    <?php
                    date_default_timezone_set('Asia/Jakarta');
                    $hari = date('l');
                    $tanggal = date('d F Y');
                    echo $hari . ', ' . $tanggal;
                    ?>
                </span>
            </div>
        </nav>

        <!-- Form Login -->
        <div class="card mx-auto rounded-4 shadow" style="max-width: 600px; margin-top: 150px;">
            <div class="card-body p-5">
                <div class="card-title h2 text-center mb-3">Tambah Akun</div>
                <p class="card-text text-center text-secondary mb-4" style="font-size: 14px;">Silahkan Isi Data dengan Benar!</p>
                <hr>
                <form method="POST" enctype="multipart/form-data" class="form-akun">
                    <a href="index.php" class="d-flex justify-content-center" style="font-size: 14px; cursor: pointer;">Sudah punya akun</a>
                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <div class="input-group mt-4 mb-4 input-user">
                                <label class="input-group-text"><i class="fa-solid fa-user"></i></label>
                                <input type="text" class="form-control" name="username" placeholder="Username" aria-label="Username" required>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="input-group mt-4 mb-4 input-nama">
                                <label class="input-group-text"><i class="fa-solid fa-id-badge"></i></label>
                                <input type="text" class="form-control" name="nama" placeholder="Nama" aria-label="Nama" required>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-4">
                        <label class="input-group-text"><i class="fa-solid fa-envelope"></i></label>
                        <input type="email" class="form-control" name="email" placeholder="Email" aria-label="Email" required>
                    </div>
                    <div class="input-group mb-4">
                        <label class="input-group-text"><i class="fa-solid fa-key"></i></label>
                        <input type="password" class="form-control" name="password" placeholder="Password" aria-label="Password" required>
                    </div>
                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <div class="input-group mb-4 input-status">
                                <label class="input-group-text"><i class="fa-solid fa-chart-simple"></i></label>
                                <select class="form-select" name="status">
                                    <option selected>Pilih status</option>
                                    <option value="Aktif">Aktif</option>
                                    <option value="Non-Aktif">Non-Aktif</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="input-group mb-4 input-role">
                                <label class="input-group-text"><i class="fa-solid fa-dice-d6"></i></label>
                                <select class="form-select" name="role">
                                    <option selected>Pilih role</option>
                                    <option value="User">User</option>
                                    <option value="Admin">Admin</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <button type="submit" name="tambah_akun" class="btn btn-primary fw-semibold form-control text-uppercase"
                        style="letter-spacing: 1px;">Simpan</button>
                </form>
            </div>
        </div>
    </div>

    <footer class="bg-primary text-white py-3" style="margin-top: 68px;">
        <div class="container-fluid text-center">
            &copy; <?php echo date('Y'); ?> - Sistem Perawatan APAR
        </div>
    </footer>

    <!-- Bootstrap Javascript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
        crossorigin="anonymous"></script>
</body>

</html>