<?php
include "config/koneksi.php";
include "login.php";
include "login_session.php";

if (isset($_POST['ubah_checklist'])) {
    
    if (isset($_GET['id'])) {
        $tanggal = $_POST['tanggal'];
        $users_id = $_POST['users_id'];
        $apar_id = $_POST['apar_id'];
        $kloset = $_POST['kloset'];
        $wastafel = $_POST['wastafel'];
        $lantai = $_POST['lantai'];
        $dinding = $_POST['dinding'];
        $kaca = $_POST['kaca'];
        $bau = $_POST['bau'];
        $sabun = $_POST['sabun'];
    
        $query = "UPDATE checklist SET tanggal = '$tanggal', users_id = '$users_id', apar_id = '$apar_id', kloset = '$kloset', wastafel = '$wastafel', lantai = '$lantai', dinding = '$dinding', kaca = '$kaca', bau = '$bau', sabun = '$sabun' WHERE id = '$_GET[id]'";
        $result = mysqli_query($conn, $query);

        if ($result) {
            echo "<script>window.location.href='home.php';</script>";
        } else {
            echo "<script>alert('Data gagal diubah!');</script>";
            echo "<script>window.location.href='home.php';</script>";
        }
    }
}

$users_id = "";
$apar_id = "";
$kloset = "";
$wastafel = "";
$lantai = "";
$dinding = "";
$kaca = "";
$bau = "";
$sabun = "";
$tanggal = "";

if (isset($_GET['id'])) {

    $result = mysqli_query($conn, "SELECT * FROM checklist WHERE id = '$_GET[id]'");
    $row = mysqli_fetch_array($result);
        
    if ($row) {
        $users_id = $row['users_id'];
        $apar_id = $row['apar_id'];
        $kloset = $row['kloset'];
        $wastafel = $row['wastafel'];
        $lantai = $row['lantai'];
        $dinding = $row['dinding'];
        $kaca = $row['kaca'];
        $bau = $row['bau'];
        $sabun = $row['sabun'];
        $tanggal = date('Y-m-d', strtotime($row['tanggal'])); 
    } 
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Data</title>
    <style>
        .modal {
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fff;
            margin: 50px auto;
            padding: 40px;
            border: 1px solid #888;
            max-width: 1000px;
            width: 80%;
            border-radius: 10px;
        }
    </style>

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
                    <span class="text-white fw-bold h5" style="letter-spacing: 1.2px;">SISTEM CHECKLIST APAR</span>
                </div>
                <div class="d-flex justify-content-end">
                    <div class="navbar-brand">
                        <i class="user fa-solid fa-user text-white"></i>
                        <span class="text-white text-capitalize p-2"><?= $_SESSION["username"]; ?></span>
                    </div>
                    <button onclick="logout()" class="btn navbar-brand text-white px-3" style="margin-right: 0px;"><i class="fa-solid fa-arrow-right-from-bracket me-2"></i>Logout</Button>
                </div>
            </div>
        </nav>

        <!-- Konfirmasi logout -->
        <div class="modal" id="logout">
            <div class="modal-content">
                <div class="h3">Konfirmasi</div><hr style="margin-top: 0px; margin-bottom: 30px;">
                <div class="fs-5 mb-4">Apakah anda yakin ingin logout? </div>
                <div class="d-flex justify-content-end">
                    <button id="btn-ya" class="btn btn-primary px-4 me-2">Ya</button>
                    <button id="btn-tidak" class="btn btn-secondary px-4">Tidak</button>
                </div>
            </div>
        </div>

        <!-- Form Ubah Checklist -->
        <div class="card mx-auto rounded-4 shadow" style="max-width: 1000px; margin-top: 120px;">
            <div class="card-body p-5">
                <div class="h3 card-title">Ubah Data Checklist</div>
                <hr style="margin-top: 0px; margin-bottom: 30px;">
                <form method="POST" enctype="multipart/form-data" class="form-ubah">
                    <div class="input-group mb-4 input-tanggal">
                        <label class="input-group-text"><i class="fa-solid fa-calendar-days"></i></label>
                        <input type="date" class="form-control" name="tanggal" placeholder="dd/mm/yyyy" value="<?= $tanggal ?>" required>
                    </div>
                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <div class="input-group mb-4 input-id">
                                <label class="input-group-text"><i class="fa-solid fa-id-badge"></i></label>
                                <input type="text" class="form-control" name="users_id" placeholder="ID User" value="<?= $users_id ?>" required>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="input-group mb-4">
                                <label class="input-group-text"><i class="fa-solid fa-hashtag"></i></label>
                                <input type="text" class="form-control" name="apar_id" placeholder="ID APAR" value="<?= $apar_id ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <div class="input-group mb-4">
                                <label class="input-group-text"><i class="fa-solid fa-fire-extinguisher"></i></label>
                                <label class="input-group-text">Kloset</label>
                                <select name="kloset" class="form-select" required>
                                    <option selected>Pilih</option>
                                    <option value="Bersih" <?= ($kloset == 'Bersih') ? 'selected' : '' ?>>Bersih</option>
                                    <option value="Kotor" <?= ($kloset == 'Kotor') ? 'selected' : '' ?>>Kotor</option>
                                    <option value="Rusak" <?= ($kloset == 'Rusak') ? 'selected' : '' ?>>Rusak</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="input-group mb-4">
                                <label class="input-group-text"><i class="fa-solid fa-sink"></i></label>
                                <label class="input-group-text">Wastafel</label>
                                <select name="wastafel" class="form-select" required>
                                    <option selected>Pilih</option>
                                    <option value="Bersih" <?= ($wastafel == 'Bersih') ? 'selected' : '' ?>>Bersih</option>
                                    <option value="Kotor" <?= ($wastafel == 'Kotor') ? 'selected' : '' ?>>Kotor</option>
                                    <option value="Rusak" <?= ($wastafel == 'Rusak') ? 'selected' : '' ?>>Rusak</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <div class="input-group mb-4">
                                <label class="input-group-text"><img src="assets/images/floor.svg" width="18" height="18"></label>
                                <label class="input-group-text">Lantai</label>
                                <select name="lantai" class="form-select" required>
                                    <option selected>Pilih</option>
                                    <option value="Bersih" <?= ($lantai == 'Bersih') ? 'selected' : '' ?>>Bersih</option>
                                    <option value="Kotor" <?= ($lantai == 'Kotor') ? 'selected' : '' ?>>Kotor</option>
                                    <option value="Rusak" <?= ($lantai == 'Rusak') ? 'selected' : '' ?>>Rusak</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="input-group mb-4">
                                <label class="input-group-text"><img src="assets/images/wall.svg" width="18" height="18"></label>
                                <label class="input-group-text">Dinding</label>
                                <select name="dinding" class="form-select" required>
                                    <option selected>Pilih</option>
                                    <option value="Bersih" <?= ($dinding == 'Bersih') ? 'selected' : '' ?>>Bersih</option>
                                    <option value="Kotor" <?= ($dinding == 'Kotor') ? 'selected' : '' ?>>Kotor</option>
                                    <option value="Rusak" <?= ($dinding == 'Rusak') ? 'selected' : '' ?>>Rusak</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-12 col-sm-4">
                            <div class="input-group mb-2">
                                <label class="input-group-text"><img src="assets/images/mirror.svg" width="20" height="20"></label>
                                <label class="input-group-text">Kaca</label>
                                <select name="kaca" class="form-select" required>
                                    <option selected>Pilih</option>
                                    <option value="Bersih" <?= ($kaca == 'Bersih') ? 'selected' : '' ?>>Bersih</option>
                                    <option value="Kotor" <?= ($kaca == 'Kotor') ? 'selected' : '' ?>>Kotor</option>
                                    <option value="Rusak" <?= ($kaca == 'Rusak') ? 'selected' : '' ?>>Rusak</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="input-group mb-2">
                                <label class="input-group-text"><i class="fa-solid fa-head-side-mask"></i></label>
                                <label class="input-group-text">Bau</label>
                                <select name="bau" class="form-select" required>
                                    <option selected>Pilih</option>
                                    <option value="Ya" <?= ($bau == 'Ya') ? 'selected' : '' ?>>Ya</option>
                                    <option value="Tidak" <?= ($bau == 'Tidak') ? 'selected' : '' ?>>Tidak</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="input-group mb-2">
                                <label class="input-group-text"><i class="fa-solid fa-soap"></i></label>
                                <label class="input-group-text">Sabun</label>
                                <select name="sabun" class="form-select" required>
                                    <option selected>Pilih</option>
                                    <option value="Ada" <?= ($sabun == 'Ada') ? 'selected' : '' ?>>Ada</option>
                                    <option value="Habis" <?= ($sabun == 'Habis') ? 'selected' : '' ?>>Habis</option>
                                    <option value="Hilang" <?= ($sabun == 'Hilang') ? 'selected' : '' ?>>Hilang</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-6 col-sm-3 col-md-2 button-ubah">
                            <a href="home.php" type="button" class="btn btn-secondary form-control px-4 me-2 mt-4"><i class="fa-solid fa-xmark me-2"></i>Batal</a>
                        </div>
                        <div class="col-6 col-sm-9 col-md-10 button-ubah">
                            <button type="submit" name="ubah_checklist"
                                class="btn btn-primary form-control text-uppercase mt-4">Ubah Data</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <footer class="bg-primary text-white py-3" style="margin-top: 68px;">
        <div class="container-fluid text-center">
            &copy; <?php echo date('Y'); ?> - Sistem Checklist APAR
        </div>
    </footer>

    <script>
        function logout() {
            var modal = document.getElementById('logout');
            modal.style.display = 'block';

            var yes = document.getElementById('btn-ya');
            var no = document.getElementById('btn-tidak');

            yes.addEventListener('click', function() {
                window.location.href = 'logout.php';
            });

            no.addEventListener('click', function() {
                modal.style.display = 'none';
            });
        }
    </script>

    <!-- Bootstrap Javascript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
        crossorigin="anonymous"></script>
</body>

</html>