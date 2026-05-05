<?php
include "config/koneksi.php";
include "login.php";
include "login_session.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Utama</title>
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

        <!-- Form Login -->
        <div class="card mx-auto rounded-4 shadow" style="margin-top: 120px;">
            <div class="card-body p-5">
                <!-- Title -->
                <div class="col"><div class="h2 mb-3">Data APAR</div></div>
                <hr style="margin-top: 0px; margin-bottom: 40px;">
                
                <div class="row">
                    <div class="col">
                        <!-- Menu Tambah -->
                        <button onclick="tambahAPAR()" class="btn btn-success px-4 me-2"><i class="fa-solid fa-plus me-2"></i>Tambah APAR</button>
                        <a href="home.php" class="btn btn-secondary px-4 me-2"><i class="fa-solid fa-table me-2"></i>Daftar Checklist</a>
                        <button onclick="laporanAPAR()" class="btn btn-secondary px-4"><i class="fa-solid fa-file-lines me-2"></i>Laporan</button>
                    </div>
                    <div class="col-sm-4">
                        <!-- Search box -->
                        <form method="GET" class="mb-4">
                            <div class="input-group">
                                <input type="text" class="form-control rounded-end me-2" name="keyword" placeholder="Masukkan pencarian...">
                                <button type="submit" class="btn btn-primary rounded-start px-4"><i class="fa-solid fa-magnifying-glass me-2"></i>Cari</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Form Tambah APAR -->
                <div id="tambahAPAR" class="modal">
                    <div class="modal-content">
                        <div class="h3">Tambah Data APAR</div><hr style="margin-top: 0px; margin-bottom: 30px;">
                        <form action="tambah.php" method="POST" enctype="multipart/form-data">
                            <div class="input-group mb-4">
                                <label class="input-group-text"><i class="fa-solid fa-hashtag"></i></label>
                                <input type="text" class="form-control" name="apar_id"
                                    placeholder="ID APAR" required>
                            </div>
                            <div class="input-group mb-4">
                                <label class="input-group-text"><i class="fa-solid fa-location-dot"></i></label>
                                <input type="text" class="form-control" name="lokasi"
                                    placeholder="Lokasi" required>
                            </div>
                            <div class="input-group mb-4">
                                <label class="input-group-text"><i class="fa-solid fa-info"></i></label>
                                <select class="form-select" name="keterangan">
                                    <option selected>Pilih keterangan</option>
                                    <option value="Sudah">Sudah</option>
                                    <option value="Belum">Belum</option>
                                </select>
                            </div>
                            <div class="row g-3">
                                <div class="col-6 col-sm-2 col-md-2">
                                    <button type="button" class="btn btn-secondary form-control px-4 me-2 mt-3" onclick="closeAPAR()"><i class="fa-solid fa-xmark me-2"></i>Batal</button>
                                </div>
                                <div class="col-6 col-sm-4 col-md-10">
                                    <button type="submit" name="tambah_apar" class="btn btn-primary form-control text-uppercase mt-3">Tambah</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tabel daftar APAR -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover table-fit text-center">
                        <thead>
                            <tr>
                                <th class="py-3">No</th>
                                <th class="py-3">ID APAR</th>
                                <th class="py-3">Lokasi</th>
                                <th class="py-3">Keterangan</th>
                                <th class="py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (isset($_GET['keyword'])) {
                                $keyword = $_GET['keyword'];
                                $query = "SELECT * FROM apar_data WHERE id LIKE '%$keyword%' OR lokasi LIKE '%$keyword%' OR keterangan LIKE '%$keyword%'"; 
                                $result = mysqli_query($conn, $query);
                                echo "<div style='margin-bottom: 16px;'>Hasil pencarian: $keyword</div>"; 
                            } else {
                                $result = mysqli_query($conn, "SELECT * FROM apar_data ORDER BY id ASC");
                            }
    
                            $no = 1;
                            // Looping untuk menampilkan data
                            while ($row = mysqli_fetch_array($result)):
                            ?>
                                <tr>
                                    <td class="py-3"><?= $no++ ?></td>
                                    <td class="py-3"><?= $row['id'] ?></td>
                                    <td class="py-3"><?= $row['lokasi'] ?></td>
                                    <td class="py-3"><?= $row['keterangan'] ?></td>
                                    <td class="py-3">
                                        <a href="ubah_apar.php?id=<?= $row['id'] ?>"><i class="fa-solid fa-pen btn-primary me-3"></i></a>
                                        <a href="hapus.php?apar&id=<?= $row['id'] ?>"><i class="fa-solid fa-trash btn-danger"></i></a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pilih laporan -->
                <div id="laporanAPAR" class="modal">
                    <div class="modal-content">
                        <div class="d-flex justify-content-between">
                            <div class="h3">Laporan</div>
                            <div class="btn btn-close" onclick="closeLaporan()"></div>
                        </div>
                        <hr style="margin-top: 0px; margin-bottom: 30px;">
                        <div class="card-text mb-4">Buat laporan berdasarkan:</div>
                        <div class="row">
                            <div class="col">
                                <a href="laporan.php?apar" class="btn btn-primary px-4 me-2">Semua Data</a>
                                <button class="btn btn-secondary px-4 me-2" onclick="pilihKeterangan()">Sudah/Belum</button>
                            </div>
                        </div>
                        <form id="keteranganForm" method="GET" action="laporan.php" class="mt-4" style="display: none;">
                            <div class="input-group">
                                <select name="keterangan" class="form-select rounded-end me-2" required>
                                    <option selected>Pilih</option>
                                    <option value="Sudah">Sudah</option>
                                    <option value="Belum">Belum</option>
                                </select>
                                <button type="submit" class="btn btn-primary rounded-start px-4">Buat</button>
                            </div>
                        </form>
                    </div>
                </div>
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

        function tambahAPAR() {
            var modal = document.getElementById('tambahAPAR');
            modal.style.display = 'block';
        }

        function closeAPAR() {
            var modal = document.getElementById('tambahAPAR');
            modal.style.display = 'none';
        }

        function laporanAPAR() {
            var modal = document.getElementById('laporanAPAR');
            modal.style.display = 'block';
        }

        function closeLaporan() {
            var modal = document.getElementById('laporanAPAR');
            modal.style.display = 'none';
        }

        function pilihKeterangan() {
            var ketInput = document.getElementById('keteranganForm');
            if (ketInput.style.display === 'none') {
                ketInput.style.display = 'block';
            } else {
                ketInput.style.display = 'none';
            }
        }
    </script>

    <!-- Bootstrap Javascript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
        crossorigin="anonymous"></script>
</body>

</html>
