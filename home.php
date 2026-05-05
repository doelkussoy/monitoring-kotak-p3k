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
                    <span class="text-white fw-bold h5" style="letter-spacing: 1.2px;">SISTEM PERAWATAN APAR</span>
                </div>
                <div class="d-flex justify-content-end">
                    <div class="navbar-brand">
                        <i class="user fa-solid fa-user text-white"></i>
                        <span class="text-white text-capitalize p-2"><?= $_SESSION["username"]; ?></span>
                    </div>
                    <button onclick="logout()" class="btn navbar-brand text-white px-3" style="margin-right: 0px;"><i
                            class="fa-solid fa-arrow-right-from-bracket me-2"></i>Logout</Button>
                </div>
            </div>
        </nav>

        <!-- Konfirmasi logout -->
        <div class="modal" id="logout">
            <div class="modal-content">
                <div class="h3">Konfirmasi</div>
                <hr style="margin-top: 0px; margin-bottom: 30px;">
                <div class="fs-5 mb-4">Apakah anda yakin ingin logout? </div>
                <div class="d-flex justify-content-end">
                    <button id="btn-ya" class="btn btn-primary px-4 me-2">Ya</button>
                    <button id="btn-tidak" class="btn btn-secondary px-4">Tidak</button>
                </div>
            </div>
        </div>

        <div class="card mx-auto rounded-4 shadow" style="margin-top: 130px;">
            <div class="card-body p-5">
                <!-- Title -->
                <div class="h2 mb-3">Daftar Perawatan APAR</div>
                <hr style="margin-top: 0px; margin-bottom: 40px;">

                <div class="row">
                    <div class="col">
                        <!-- Menu Tambah -->
                        <button onclick="tambahChecklist()" class="btn btn-success rounded-2 px-4 me-2"><i
                                class="fa-solid fa-plus me-2"></i>Tambah Perawatan</button>

                        <!-- Data APAR -->
                        <a href="data_apar.php" class="btn btn-secondary rounded-2 px-4 me-2"><i
                                class="fa-solid fa-table me-2"></i>Data APAR</a>

                        <!-- Laporan -->
                        <button onclick="laporanChecklist()" class="btn btn-secondary rounded-2 px-4"><i
                                class="fa-solid fa-file-lines me-2"></i>Laporan</button>
                    </div>
                    <div class="col-sm-4">
                        <!-- Search box -->
                        <form method="GET" class="mb-4">
                            <div class="input-group">
                                <input type="text" class="form-control rounded-end me-2" name="keyword"
                                    placeholder="Masukkan pencarian...">
                                <button type="submit" class="btn btn-primary rounded-start px-4"><i
                                        class="fa-solid fa-magnifying-glass me-2"></i>Cari</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Form Tambah Checklist -->
                <div id="tambahChecklist" class="modal">
                    <div class="modal-content">
                        <div class="h3">Tambah Perawatan APAR</div>
                        <hr style="margin-top: 0px; margin-bottom: 30px;">
                        <form action="tambah.php" method="POST" enctype="multipart/form-data" class="form-tambah">
                            <div class="input-group mb-4">
                                <label class="input-group-text id-label"><i class="fa-solid fa-id-badge"></i></label>
                                <input type="text" class="form-control" name="users_id" placeholder="ID User" required>
                            </div>
                            <div class="input-group mb-4">
                                <label class="input-group-text apar-label"><i class="fa-solid fa-hashtag"></i></label>
                                <input type="text" class="form-control" name="apar_id" placeholder="ID APAR" required>
                            </div>
                            <div class="row g-3 extra">
                                <div class="col-12 col-sm-4 col-md-6">
                                    <div class="input-group mb-4">
                                        <label class="input-group-text"><i
                                                class="fa-solid fa-fire-extinguisher"></i></label>
                                        <label class="input-group-text">Kloset</label>
                                        <select name="kloset" class="form-select" required>
                                            <option selected>Pilih</option>
                                            <option value="Bersih">Bersih</option>
                                            <option value="Kotor">Kotor</option>
                                            <option value="Rusak">Rusak</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4 col-md-6">
                                    <div class="input-group mb-4">
                                        <label class="input-group-text"><i class="fa-solid fa-sink"></i></label>
                                        <label class="input-group-text">Wastafel</label>
                                        <select name="wastafel" class="form-select" required>
                                            <option selected>Pilih</option>
                                            <option value="Bersih">Bersih</option>
                                            <option value="Kotor">Kotor</option>
                                            <option value="Rusak">Rusak</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12 col-sm-4 col-md-6">
                                    <div class="input-group mb-4">
                                        <label class="input-group-text"><img src="assets/images/floor.svg" width="18"
                                                height="18"></label>
                                        <label class="input-group-text">Lantai</label>
                                        <select name="lantai" class="form-select" required>
                                            <option selected>Pilih</option>
                                            <option value="Bersih">Bersih</option>
                                            <option value="Kotor">Kotor</option>
                                            <option value="Rusak">Rusak</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4 col-md-6">
                                    <div class="input-group mb-4">
                                        <label class="input-group-text"><img src="assets/images/wall.svg" width="18"
                                                height="18"></label>
                                        <label class="input-group-text">Dinding</label>
                                        <select name="dinding" class="form-select" required>
                                            <option selected>Pilih</option>
                                            <option value="Bersih">Bersih</option>
                                            <option value="Kotor">Kotor</option>
                                            <option value="Rusak">Rusak</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12 col-sm-4 col-md-6">
                                    <div class="input-group mb-4">
                                        <label class="input-group-text"><img src="assets/images/mirror.svg" width="20"
                                                height="20"></label>
                                        <label class="input-group-text">Kaca</label>
                                        <select name="kaca" class="form-select" required>
                                            <option selected>Pilih</option>
                                            <option value="Bersih">Bersih</option>
                                            <option value="Kotor">Kotor</option>
                                            <option value="Rusak">Rusak</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4 col-md-6">
                                    <div class="input-group mb-4">
                                        <label class="input-group-text"><i
                                                class="fa-solid fa-head-side-mask"></i></label>
                                        <label class="input-group-text">Bau</label>
                                        <select name="bau" class="form-select" required>
                                            <option selected>Pilih</option>
                                            <option value="Ya">Ya</option>
                                            <option value="Tidak">Tidak</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="input-group mb-4">
                                <label class="input-group-text"><i class="fa-solid fa-soap"></i></label>
                                <label class="input-group-text">Sabun</label>
                                <select name="sabun" class="form-select" required>
                                    <option selected>Pilih</option>
                                    <option value="Ada">Ada</option>
                                    <option value="Habis">Habis</option>
                                    <option value="Hilang">Hilang</option>
                                </select>
                            </div>
                            <div class="row g-3">
                                <div class="col-6 col-sm-3 col-md-2">
                                    <button type="button" class="btn btn-secondary form-control px-4 me-2 mt-3"
                                        onclick="closeChecklist()"><i class="fa-solid fa-xmark me-2"></i>Batal</button>
                                </div>
                                <div class="col-6 col-sm-3 col-md-10">
                                    <button type="submit" name="tambah_checklist"
                                        class="btn btn-primary form-control text-uppercase mt-3">Tambah</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tabel daftar checklist -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover table-fit text-center">
                        <thead>
                            <tr>
                                <th class="py-3">No</th>
                                <th class="py-3">ID User</th>
                                <th class="py-3">ID APAR</th>
                                <th class="py-3">Kloset</th>
                                <th class="py-3">Wastafel</th>
                                <th class="py-3">Lantai</th>
                                <th class="py-3">Dinding</th>
                                <th class="py-3">Kaca</th>
                                <th class="py-3">Bau</th>
                                <th class="py-3">Sabun</th>
                                <th class="py-3">Tanggal</th>
                                <th class="py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (isset($_GET['keyword'])) {
                                $keyword = $_GET['keyword'];
                                $query = "SELECT * FROM checklist WHERE users_id LIKE '%$keyword%' OR apar_id LIKE '%$keyword%'";
                                $result = mysqli_query($conn, $query);
                                echo "<div style='margin-bottom: 16px;'>Hasil pencarian: $keyword</div>";
                            } else {
                                $result = mysqli_query($conn, "SELECT * FROM checklist ORDER BY tanggal DESC");
                            }

                            $no = 1;
                            // Looping untuk menampilkan data
                            while ($row = mysqli_fetch_array($result)):
                                ?>
                                <tr>
                                    <td class="py-3"><?= $no++ ?></td>
                                    <td class="py-3"><?= $row['users_id'] ?></td>
                                    <td class="py-3"><?= $row['apar_id'] ?></td>
                                    <td class="py-3"><?= $row['kloset'] ?></td>
                                    <td class="py-3"><?= $row['wastafel'] ?></td>
                                    <td class="py-3"><?= $row['lantai'] ?></td>
                                    <td class="py-3"><?= $row['dinding'] ?></td>
                                    <td class="py-3"><?= $row['kaca'] ?></td>
                                    <td class="py-3"><?= $row['bau'] ?></td>
                                    <td class="py-3"><?= $row['sabun'] ?></td>
                                    <td class="py-3"><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
                                    <td class="py-3">
                                        <a href="ubah.php?id=<?= $row['id'] ?>"><i
                                                class="fa-solid fa-pen me-3 btn-primary"></i></a>
                                        <a href="hapus.php?checklist&id=<?= $row['id'] ?>"><i
                                                class="fa-solid fa-trash btn-danger"></i></a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pilih laporan -->
                <div id="laporanChecklist" class="modal">
                    <div class="modal-content">
                        <div class="d-flex justify-content-between">
                            <div class="h3">Laporan</div>
                            <div class="btn btn-close" onclick="closeLaporan()"></div>
                        </div>
                        <hr style="margin-top: 0px; margin-bottom: 30px;">
                        <div class="card-text mb-4">Buat laporan berdasarkan:</div>
                        <div class="row">
                            <div class="col">
                                <button class="btn btn-primary px-4 me-2" onclick="inputTanggal()">Tanggal</button>
                                <button class="btn btn-secondary px-4 me-2" onclick="pilihBagian()">Rusak/Kotor</button>
                            </div>
                        </div>
                        <form id="tanggalForm" method="GET" action="laporan.php" class="mt-4" style="display: none;">
                            <div class="input-group">
                                <input type="date" class="form-control rounded-end me-2" name="tanggal">
                                <button type="submit" class="btn btn-primary rounded-start px-4">Buat</button>
                            </div>
                        </form>
                        <form id="statusForm" method="GET" action="laporan.php" class="mt-4" style="display: none;">
                            <div class="input-group">
                                <select name="bagian" class="form-select rounded-end me-2" required>
                                    <option selected>Pilih</option>
                                    <option value="Kloset">Kloset</option>
                                    <option value="Wastafel">Wastafel</option>
                                    <option value="Lantai">Lantai</option>
                                    <option value="Dinding">Dinding</option>
                                    <option value="Kaca">Kaca</option>
                                    <option value="Bau">Bau</option>
                                    <option value="Sabun">Sabun</option>
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
            &copy; <?php echo date('Y'); ?> - Sistem Perawatan APAR
        </div>
    </footer>

    <script>
        function logout() {
            var modal = document.getElementById('logout');
            modal.style.display = 'block';

            var yes = document.getElementById('btn-ya');
            var no = document.getElementById('btn-tidak');

            yes.addEventListener('click', function () {
                window.location.href = 'logout.php';
            });

            no.addEventListener('click', function () {
                modal.style.display = 'none';
            });
        }

        function tambahChecklist() {
            var modal = document.getElementById('tambahChecklist');
            modal.style.display = 'block';
        }

        function closeChecklist() {
            var modal = document.getElementById('tambahChecklist');
            modal.style.display = 'none';
        }

        function laporanChecklist() {
            var modal = document.getElementById('laporanChecklist');
            modal.style.display = 'block';
        }

        function closeLaporan() {
            var modal = document.getElementById('laporanChecklist');
            modal.style.display = 'none';
        }

        function inputTanggal() {
            var dateInput = document.getElementById('tanggalForm');
            if (dateInput.style.display === 'none') {
                dateInput.style.display = 'block';
            } else {
                dateInput.style.display = 'none';
            }
        }

        function pilihBagian() {
            var bagianInput = document.getElementById('statusForm');
            if (bagianInput.style.display === 'none') {
                bagianInput.style.display = 'block';
            } else {
                bagianInput.style.display = 'none';
            }
        }
    </script>

    <!-- Bootstrap Javascript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
        crossorigin="anonymous"></script>
</body>

</html>