<?php
include "config/koneksi.php";
include "login.php";
include "login_session.php";

if (isset($_POST['ubah_apar'])) {
    
    if (isset($_GET['id'])) {
        $apar_id = $_POST['apar_id'];
        $lokasi = $_POST['lokasi'];
        $keterangan = $_POST['keterangan'];
    
        $query = "UPDATE apar_data SET id = '$apar_id', lokasi = '$lokasi', keterangan = '$keterangan'  WHERE id = '$_GET[id]'";
        $result = mysqli_query($conn, $query);

        if ($result) {
            echo "<script>window.location.href='data_apar.php';</script>";
        } else {
            echo "<script>alert('Data gagal diubah!');</script>";
            echo "<script>window.location.href='data_apar.php';</script>";
        }
    }
}

$apar_id = "";
$lokasi = "";
$keterangan = "";

if (isset($_GET['id'])) {

    $result = mysqli_query($conn, "SELECT * FROM apar_data WHERE id = '$_GET[id]'");
    $row = mysqli_fetch_array($result);
        
    if ($row) {
        $apar_id = $row['id'];
        $lokasi = $row['lokasi'];
        $keterangan = $row['keterangan'];
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

        <!-- Form Ubah APAR -->
        <div class="card mx-auto rounded-4 shadow" style="max-width: 1000px; margin-top: 160px;">
            <div class="card-body p-5">
                <div class="h3 card-title">Ubah Data APAR</div>
                <hr style="margin-top: 0px; margin-bottom: 30px;">
                <form method="POST" enctype="multipart/form-data">
                    <div class="input-group mb-4">
                        <label class="input-group-text"><i class="fa-solid fa-hashtag"></i></label>
                        <input type="text" class="form-control" name="apar_id"
                                    placeholder="ID APAR" value="<?= $apar_id ?>" required>
                    </div>
                    <div class="input-group mb-4">
                        <label class="input-group-text"><i class="fa-solid fa-location-dot"></i></label>
                        <input type="text" class="form-control" name="lokasi"
                                    placeholder="Lokasi" value="<?= $lokasi ?>" required>
                    </div>
                    <div class="input-group mb-4">
                        <label class="input-group-text"><i class="fa-solid fa-info"></i></label>
                        <select class="form-select" name="keterangan">
                            <option selected>Pilih keterangan</option>
                            <option value="Sudah" <?= ($keterangan == 'Sudah') ? 'selected' : '' ?>>Sudah</option>
                            <option value="Belum" <?= ($keterangan == 'Belum') ? 'selected' : '' ?>>Belum</option>
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-6 col-sm-2 col-md-2">
                            <a href="data_apar.php" class="btn btn-secondary form-control px-4 me-2 mt-3"><i class="fa-solid fa-xmark me-2"></i>Batal</a>
                        </div>
                        <div class="col-6 col-sm-4 col-md-10">
                            <button type="submit" name="ubah_apar" class="btn btn-primary form-control text-uppercase mt-3">Ubah</button>
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
