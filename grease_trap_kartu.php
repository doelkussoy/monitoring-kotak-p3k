<?php
include "config/koneksi.php";
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: index.php");
  exit;
}

$grease_trap_id = isset($_GET['grease_trap_id']) ? (int) $_GET['grease_trap_id'] : 0;
$tahun = isset($_GET['tahun']) ? (int) $_GET['tahun'] : (int) date('Y');

// Ambil data Grease Trap
$grease_trapRow = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM grease_trap WHERE id=$grease_trap_id"));
if (!$grease_trapRow) {
  header("Location: grease_trap_home.php");
  exit;
}

// Handle simpan perawatan
if (isset($_POST['simpan_checklist'])) {
  $bulan = (int) $_POST['bulan'];
  $minggu = (int) ($_POST['minggu'] ?? 1);
  $tgl = mysqli_real_escape_string($conn, $_POST['tanggal_cek']);
  $fields = ['kondisi_fisik', 'kebersihan_internal', 'pemisahan_lemak', 'saluran_in_out', 'bau_kontaminasi'];
  $vals = [];
  foreach ($fields as $f) {
    $vals[$f] = isset($_POST[$f]) ? mysqli_real_escape_string($conn, $_POST[$f]) : null;
  }
  $paraf = mysqli_real_escape_string($conn, $_POST['paraf'] ?? '');
  $catatan = mysqli_real_escape_string($conn, $_POST['catatan'] ?? '');
  $uid = (int) $_SESSION['id'];

  // Cek sudah ada?
  $cek = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM checklist_grease_trap WHERE grease_trap_id=$grease_trap_id AND tahun=$tahun AND bulan=$bulan AND minggu=$minggu"));
  if ($cek) {
    $setArr = [];
    foreach ($fields as $f) {
      $v = $vals[$f];
      $setArr[] = "`$f`=" . ($v ? "'$v'" : "NULL");
    }
    $setArr[] = "`tanggal_cek`='$tgl'";
    $setArr[] = "`paraf`='$paraf'";
    $setArr[] = "`catatan`='$catatan'";
    $set = implode(',', $setArr);
    mysqli_query($conn, "UPDATE checklist_grease_trap SET $set WHERE id={$cek['id']}");
  } else {
    $fStr = "grease_trap_id,tahun,bulan,minggu,tanggal_cek,kondisi_fisik,kebersihan_internal,pemisahan_lemak,saluran_in_out,bau_kontaminasi,paraf,catatan,users_id";
    $v1 = $vals['kondisi_fisik'] ? "'{$vals['kondisi_fisik']}'" : 'NULL';
    $v2 = $vals['kebersihan_internal'] ? "'{$vals['kebersihan_internal']}'" : 'NULL';
    $v3 = $vals['pemisahan_lemak'] ? "'{$vals['pemisahan_lemak']}'" : 'NULL';
    $v4 = $vals['saluran_in_out'] ? "'{$vals['saluran_in_out']}'" : 'NULL';
    $v5 = $vals['bau_kontaminasi'] ? "'{$vals['bau_kontaminasi']}'" : 'NULL';
    mysqli_query($conn, "INSERT INTO checklist_grease_trap ($fStr) VALUES ($grease_trap_id,$tahun,$bulan,$minggu,'$tgl',$v1,$v2,$v3,$v4,$v5,'$paraf','$catatan',$uid)");
  }
  header("Location: grease_trap_kartu.php?grease_trap_id=$grease_trap_id&tahun=$tahun");
  exit;
}

// Handle hapus baris
if (isset($_GET['hapus'])) {
  mysqli_query($conn, "DELETE FROM checklist_grease_trap WHERE id=" . (int) $_GET['hapus'] . " AND grease_trap_id=$grease_trap_id");
  header("Location: grease_trap_kartu.php?grease_trap_id=$grease_trap_id&tahun=$tahun");
  exit;
}

// Ambil semua perawatan tahun ini
$rows = [];
$res = mysqli_query($conn, "SELECT * FROM checklist_grease_trap WHERE grease_trap_id=$grease_trap_id AND tahun=$tahun ORDER BY bulan ASC, minggu ASC");
while ($r = mysqli_fetch_assoc($res)) {
  $rows[$r['bulan']][$r['minggu']] = $r;
}

$bulanNama = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
$bulanNamaFull = ['', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
$items = [
  'kondisi_fisik' => 'Kondisi fisik grease trap',
  'kebersihan_internal' => 'Kebersihan internal',
  'pemisahan_lemak' => 'Fungsi pemisahan lemak',
  'saluran_in_out' => 'Saluran masuk dan keluar',
  'bau_kontaminasi' => 'Bau atau kontaminasi',
];
$editBulan = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$editData = $editBulan && isset($rows[$editBulan]) ? $rows[$editBulan] : null;
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kartu Riwayat Grease Trap - <?= htmlspecialchars($grease_trapRow['no_kode']) ?></title>
  <link rel="icon" type="image/png" href="assets/images/cba.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a404219d80.js" crossorigin="anonymous"></script>
  <style>
    :root {
      --blue: #2563eb;
      --blue-dark: #1e3a8a;
      --blue-light: #3b82f6;
    }

    body {
      background: #f0f2f5;
      font-family: 'Segoe UI', sans-serif;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .navbar-grease_trap {
      background: linear-gradient(135deg, var(--blue-dark), var(--blue-light));
    }

    .kartu-header {
      background: linear-gradient(135deg, var(--blue-dark), var(--blue-light));
      color: #fff;
      border-radius: 16px 16px 0 0;
      padding: 20px 28px;
    }

    .kartu-body {
      background: #fff;
      border-radius: 0 0 16px 16px;
      padding: 24px;
    }

    .info-box {
      background: #fff8f8;
      border: 1px solid #fcc;
      border-radius: 10px;
      padding: 14px 20px;
      margin-bottom: 20px;
    }

    .info-label {
      font-size: 12px;
      color: #888;
      font-weight: 600;
      text-transform: uppercase;
    }

    .info-val {
      font-size: 15px;
      font-weight: 700;
      color: #222;
    }

    .tbl-kartu {
      font-size: 12px;
      border-collapse: collapse;
      width: 100%;
    }

    .tbl-kartu th,
    .tbl-kartu td {
      border: 1px solid #ddd;
      padding: 5px 7px;
      text-align: center;
      vertical-align: middle;
    }

    .tbl-kartu thead th {
      background: var(--blue);
      color: #fff;
    }

    .tbl-kartu .th-item {
      background: var(--blue);
      color: #fff;
      text-align: left;
      padding-left: 10px;
      white-space: nowrap;
    }

    .tbl-kartu .th-bulan {
      background: var(--blue-dark);
      color: #fff;
    }

    .ok-cell {
      background: #d4edda;
      color: #155724;
      font-weight: 700;
    }

    .nok-cell {
      background: #f8d7da;
      color: #721c24;
      font-weight: 700;
    }

    .empty-cell {
      color: #ccc;
    }

    .badge-kode {
      background: var(--blue);
      color: #fff;
      border-radius: 8px;
      padding: 2px 12px;
      font-size: 13px;
    }

    .btn-isi {
      background: var(--blue);
      color: #fff;
      border: none;
      border-radius: 6px;
      font-size: 11px;
      padding: 2px 8px;
    }

    .btn-isi:hover {
      background: var(--blue-dark);
      color: #fff;
    }

    .modal-overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, .55);
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
      padding: 28px;
      width: 95%;
      max-width: 580px;
      max-height: 90vh;
      overflow-y: auto;
    }

    .form-check-label {
      font-size: 13px;
    }

    footer {
      background: linear-gradient(135deg, var(--blue-dark), var(--blue-light));
      color: #fff;
      margin-top: 40px;
    }

    .print-only-header {
      display: none;
    }

    @media print {
      @page {
        size: landscape;
        margin: 0;
        /* Menghilangkan header URL dan footer halaman browser */
      }

      body {
        background: #fff;
        padding: 1.5cm;
        /* Memberikan ruang aman untuk print */
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
      }

      .no-print {
        display: none !important;
      }

      .container-fluid {
        margin-top: 0 !important;
        padding: 0 !important;
        max-width: 100% !important;
      }

      .kartu-body,
      .kartu-header {
        box-shadow: none !important;
        border-radius: 0 !important;
      }

      .kartu-header {
        padding: 15px !important;
      }

      .kartu-body {
        padding: 15px 0 0 0 !important;
      }

      .tbl-kartu th,
      .tbl-kartu td {
        border: 1px solid #666 !important;
        /* Memperjelas garis tabel saat di-print */
      }

      .info-box {
        border: 1px solid #aaa !important;
        background: #fff !important;
        margin-bottom: 15px !important;
      }

      .print-only-header {
        display: flex !important;
        justify-content: space-between;
        font-size: 12px;
        color: #000;
        margin-bottom: 25px;
        font-family: Arial, sans-serif;
      }

      .semester-2-wrapper {
        page-break-before: always;
        margin-top: 2cm;
      }

      .print-only-header-info {
        display: block !important;
      }

      .print-only {
        display: block !important;
      }

      .info-box {
        display: none !important;
      }

      .kartu-header {
        background: #fff !important;
        color: #000 !important;
        border: 1px solid #000 !important;
        padding: 10px !important;
      }
      
      .semester-title {
        color: #000 !important;
        margin-top: 10px !important;
      }
    }

    .print-only-header-info, .print-only {
      display: none;
    }
  </style>
</head>

<body>
  <nav class="navbar navbar-grease_trap fixed-top py-3 shadow no-print">
    <div class="container-fluid d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center gap-2 text-white" style="cursor:pointer"
        onclick="window.location.href='dashboard.php'">
        <img src="assets/images/cba.png" alt="Logo CBA" style="height:40px; background:white; padding:4px; border-radius:8px;">
        <span class="fw-bold fs-5" style="letter-spacing:1px">SISTEM SARANA PRASARANA</span>
      </div>
      <div class="d-flex align-items-center gap-3">
        <span class="text-white small"><i class="fa-solid fa-user me-1"></i><?= $_SESSION['username'] ?></span>
        <a href="logout.php" class="btn btn-sm btn-outline-light rounded-pill px-3">Logout</a>
      </div>
    </div>
  </nav>

  <div class="container-fluid" style="margin-top:90px;padding-bottom:30px;max-width:1400px;flex:1">

    <!-- Print Header Custom (Pengganti bawaan browser) -->
    <div class="print-only-header">
      <div style="width:33%; text-align:left;"><?= date('d/m/Y, H:i') ?></div>
      <div style="width:33%; text-align:center;">Kartu Riwayat Grease Trap - <?= htmlspecialchars($grease_trapRow['no_kode']) ?></div>
      <div style="width:33%; text-align:right;"></div>
    </div>

    <!-- Kontrol atas -->
    <div class="d-flex flex-wrap gap-2 align-items-center mb-3 no-print">
      <a href="grease_trap_home.php" class="btn btn-sm btn-outline-secondary rounded-pill">
        <i class="fa-solid fa-arrow-left me-1"></i>Kembali
      </a>
      <form method="GET" class="d-flex align-items-center gap-2 ms-auto">
        <input type="hidden" name="grease_trap_id" value="<?= $grease_trap_id ?>">
        <label class="text-secondary fw-semibold small mb-0">Tahun:</label>
        <select name="tahun" class="form-select form-select-sm" style="width:100px" onchange="this.form.submit()">
          <?php for ($y = date('Y'); $y >= 2023; $y--): ?>
            <option value="<?= $y ?>" <?= $y == $tahun ? 'selected' : '' ?>><?= $y ?></option>
          <?php endfor; ?>
        </select>
      </form>
      <button class="btn btn-sm btn-success rounded-pill no-print" onclick="window.print()">
        <i class="fa-solid fa-print me-1"></i>Cetak
      </button>
      <button class="btn btn-sm rounded-pill no-print" style="background:var(--blue);color:#fff"
        onclick="document.getElementById('modalIsian').classList.add('active');document.getElementById('editBulan').value=''">
        <i class="fa-solid fa-plus me-1"></i>Isi Perawatan
      </button>
    </div>

    <!-- Kartu Riwayat -->
    <div style="border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.12)">
      <div class="kartu-header">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
          <div>
            <div class="fw-bold fs-5 mb-1">KARTU RIWAYAT SARANA PRASARANA</div>
            <small class="opacity-80">CBA/GA/E-003 &nbsp;|&nbsp; Rev: 00</small>
          </div>
          <div class="text-end">
            <div class="info-label text-white opacity-75">Tahun</div>
            <div class="fw-bold fs-4"><?= $tahun ?></div>
          </div>
        </div>
      </div>
      <div class="kartu-body">
        <!-- Info Grease Trap -->
        <div class="info-box">
          <div class="row g-2">
            <div class="col-12 col-md-4">
              <div class="info-label">No. Kode</div>
              <div class="info-val"><span class="badge-kode"><?= htmlspecialchars($grease_trapRow['no_kode']) ?></span></div>
            </div>
            <div class="col-12 col-md-4">
              <div class="info-label">Nama Sarana/Prasarana</div>
              <div class="info-val"><?= htmlspecialchars($grease_trapRow['nama_sarana']) ?></div>
            </div>
            <div class="col-12 col-md-4">
              <div class="info-label">Lokasi</div>
              <div class="info-val"><i
                  class="fa-solid fa-location-dot text-danger me-1"></i><?= htmlspecialchars($grease_trapRow['lokasi']) ?>
              </div>
            </div>
          </div>
        </div>

        <!-- Semester 1 -->
        <div class="semester-print-wrapper">
          <div class="print-only-header-info mb-3">
            <div class="row g-2" style="font-size:12px; border:1px solid #000; padding:10px; border-radius:8px;">
               <div class="col-4"><b>No. Kode:</b> <?= htmlspecialchars($grease_trapRow['no_kode']) ?></div>
               <div class="col-4"><b>Nama Sarana:</b> GREASE TRAP</div>
               <div class="col-4"><b>Lokasi:</b> <?= htmlspecialchars($grease_trapRow['lokasi']) ?></div>
            </div>
          </div>
          <div class="fw-bold mb-2 text-primary semester-title">SEMESTER 1 (JANUARI - JUNI)</div>
          <div class="table-responsive">
            <table class="tbl-kartu">
              <thead>
                <tr>
                  <th rowspan="3" style="width:30px">NO</th>
                  <th rowspan="3" class="th-item" style="min-width:180px">PENGECEKAN</th>
                  <?php 
                  $monthWeeks = [1=>5, 2=>4, 3=>4, 4=>5, 5=>4, 6=>4, 7=>5, 8=>4, 9=>4, 10=>5, 11=>4, 12=>5];
                  for ($b = 1; $b <= 6; $b++): ?>
                    <th colspan="<?= $monthWeeks[$b] ?>" class="th-bulan"><?= strtoupper($bulanNamaFull[$b]) ?></th>
                  <?php endfor; ?>
                </tr>
                <tr>
                  <?php for ($b = 1; $b <= 6; $b++): 
                    for ($w = 1; $w <= $monthWeeks[$b]; $w++):
                      $wRom = ['','I','II','III','IV','V'];
                  ?>
                    <th style="background:var(--blue-dark);color:#fff;font-size:10px"><?= $wRom[$w] ?></th>
                  <?php endfor; endfor; ?>
                </tr>
                <tr>
                  <?php for ($b = 1; $b <= 6; $b++): 
                    for ($w = 1; $w <= $monthWeeks[$b]; $w++):
                  ?>
                    <th style="background:var(--blue-dark);color:#fff;font-size:9px">Tgl</th>
                  <?php endfor; endfor; ?>
                </tr>
              </thead>
              <tbody>
                <?php $no = 1;
                foreach ($items as $key => $label): ?>
                  <tr>
                    <td><?= $no++ ?></td>
                    <td class="th-item"><?= $label ?></td>
                    <?php for ($b = 1; $b <= 6; $b++):
                      for ($w = 1; $w <= $monthWeeks[$b]; $w++):
                        $r = $rows[$b][$w] ?? null;
                        $val = $r ? $r[$key] : null;
                        $tgl = $r ? date('d', strtotime($r['tanggal_cek'])) : '';
                      ?>
                        <td class="<?= $val == 'Ok' ? 'ok-cell' : ($val == 'Nok' ? 'nok-cell' : '') ?>" title="<?= $tgl ? "Tgl: $tgl" : '' ?>">
                          <?= $val ? strtoupper($val) : '<span class="empty-cell">-</span>' ?>
                        </td>
                      <?php endfor; ?>
                    <?php endfor; ?>
                  </tr>
                <?php endforeach; ?>
                <!-- Baris Paraf -->
                <tr>
                  <td colspan="2" class="th-item fw-bold">Paraf Pemeriksa</td>
                  <?php for ($b = 1; $b <= 6; $b++):
                    for ($w = 1; $w <= $monthWeeks[$b]; $w++):
                      $r = $rows[$b][$w] ?? null;
                      $paraf = $r ? htmlspecialchars($r['paraf']) : '';
                    ?>
                      <td style="font-size:9px;font-style:italic">
                        <?= $paraf ?: '<span class="empty-cell">...</span>' ?>
                      </td>
                    <?php endfor; ?>
                  <?php endfor; ?>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Keterangan Semester 1 (Print Only) -->
          <div class="mt-3 print-only">
            <div class="fw-bold mb-2 small">Keterangan / Catatan Tambahan :</div>
            <div class="row g-2">
              <?php for ($b = 1; $b <= 6; $b++):
                $catArr = [];
                if (isset($rows[$b])) {
                  foreach ($rows[$b] as $wData) {
                    if (!empty($wData['catatan'])) $catArr[] = $wData['catatan'];
                  }
                }
                $cat = implode(', ', array_unique($catArr));
                ?>
                <div class="col-4">
                  <div class="d-flex gap-2 align-items-start" style="font-size:10px">
                    <span class="fw-bold" style="min-width:30px"><?= $bulanNama[$b] ?>:</span>
                    <span style="border-bottom:1px solid #000; flex:1; min-height:15px"><?= $cat ?></span>
                  </div>
                </div>
              <?php endfor; ?>
            </div>
          </div>
        </div>

        <!-- Semester 2 -->
        <div class="semester-print-wrapper semester-2-wrapper">
          <div class="print-only-header-info mb-3">
            <div class="row g-2" style="font-size:12px; border:1px solid #000; padding:10px; border-radius:8px;">
               <div class="col-4"><b>No. Kode:</b> <?= htmlspecialchars($grease_trapRow['no_kode']) ?></div>
               <div class="col-4"><b>Nama Sarana:</b> GREASE TRAP</div>
               <div class="col-4"><b>Lokasi:</b> <?= htmlspecialchars($grease_trapRow['lokasi']) ?></div>
            </div>
          </div>
          <div class="fw-bold mb-2 text-primary semester-title">SEMESTER 2 (JULI - DESEMBER)</div>
          <div class="table-responsive">
            <table class="tbl-kartu">
              <thead>
                <tr>
                  <th rowspan="3" style="width:30px">NO</th>
                  <th rowspan="3" class="th-item" style="min-width:180px">PENGECEKAN</th>
                  <?php for ($b = 7; $b <= 12; $b++): ?>
                    <th colspan="<?= $monthWeeks[$b] ?>" class="th-bulan"><?= strtoupper($bulanNamaFull[$b]) ?></th>
                  <?php endfor; ?>
                </tr>
                <tr>
                  <?php for ($b = 7; $b <= 12; $b++): 
                    for ($w = 1; $w <= $monthWeeks[$b]; $w++):
                      $wRom = ['','I','II','III','IV','V'];
                  ?>
                    <th style="background:var(--blue-dark);color:#fff;font-size:10px"><?= $wRom[$w] ?></th>
                  <?php endfor; endfor; ?>
                </tr>
                <tr>
                  <?php for ($b = 7; $b <= 12; $b++): 
                    for ($w = 1; $w <= $monthWeeks[$b]; $w++):
                  ?>
                    <th style="background:var(--blue-dark);color:#fff;font-size:9px">Tgl</th>
                  <?php endfor; endfor; ?>
                </tr>
              </thead>
              <tbody>
                <?php $no = 1;
                foreach ($items as $key => $label): ?>
                  <tr>
                    <td><?= $no++ ?></td>
                    <td class="th-item"><?= $label ?></td>
                    <?php for ($b = 7; $b <= 12; $b++):
                      for ($w = 1; $w <= $monthWeeks[$b]; $w++):
                        $r = $rows[$b][$w] ?? null;
                        $val = $r ? $r[$key] : null;
                        $tgl = $r ? date('d', strtotime($r['tanggal_cek'])) : '';
                      ?>
                        <td class="<?= $val == 'Ok' ? 'ok-cell' : ($val == 'Nok' ? 'nok-cell' : '') ?>" title="<?= $tgl ? "Tgl: $tgl" : '' ?>">
                          <?= $val ? strtoupper($val) : '<span class="empty-cell">-</span>' ?>
                        </td>
                      <?php endfor; ?>
                    <?php endfor; ?>
                  </tr>
                <?php endforeach; ?>
                <!-- Baris Paraf -->
                <tr>
                  <td colspan="2" class="th-item fw-bold">Paraf Pemeriksa</td>
                  <?php for ($b = 7; $b <= 12; $b++):
                    for ($w = 1; $w <= $monthWeeks[$b]; $w++):
                      $r = $rows[$b][$w] ?? null;
                      $paraf = $r ? htmlspecialchars($r['paraf']) : '';
                    ?>
                      <td style="font-size:9px;font-style:italic">
                        <?= $paraf ?: '<span class="empty-cell">...</span>' ?>
                      </td>
                    <?php endfor; ?>
                  <?php endfor; ?>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Keterangan Semester 2 (Print Only) -->
          <div class="mt-3 print-only">
            <div class="fw-bold mb-2 small">Keterangan / Catatan Tambahan :</div>
            <div class="row g-2">
              <?php for ($b = 7; $b <= 12; $b++):
                $catArr = [];
                if (isset($rows[$b])) {
                  foreach ($rows[$b] as $wData) {
                    if (!empty($wData['catatan'])) $catArr[] = $wData['catatan'];
                  }
                }
                $cat = implode(', ', array_unique($catArr));
                ?>
                <div class="col-4">
                  <div class="d-flex gap-2 align-items-start" style="font-size:10px">
                    <span class="fw-bold" style="min-width:30px"><?= $bulanNama[$b] ?>:</span>
                    <span style="border-bottom:1px solid #000; flex:1; min-height:15px"><?= $cat ?></span>
                  </div>
                </div>
              <?php endfor; ?>
            </div>
          </div>
        </div>

        <!-- Keterangan/Catatan Tambahan (Screen Only) -->
        <div class="mt-4 no-print" style="border:1px solid #ddd;border-radius:10px;padding:16px;">
          <div class="fw-bold mb-3" style="color:var(--blue)">Keterangan / Catatan Tambahan :</div>
          <div class="row g-2">
            <?php foreach ($bulanNama as $b => $nm):
              if ($b == 0)
                continue;
              $catArr = [];
              if (isset($rows[$b])) {
                foreach ($rows[$b] as $wData) {
                  if (!empty($wData['catatan'])) $catArr[] = $wData['catatan'];
                }
              }
              $cat = implode(', ', array_unique($catArr));
              ?>
              <div class="col-6 col-md-3 col-lg-2">
                <div class="d-flex gap-2 align-items-start">
                  <span class="fw-semibold small" style="min-width:28px"><?= $nm ?> :</span>
                  <span class="small text-secondary" style="border-bottom:1px solid #ccc;flex:1;min-height:20px">
                    <?= $cat ? htmlspecialchars($cat) : '' ?>
                  </span>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Tombol Aksi per Bulan -->
        <div class="mt-3 no-print">
          <div class="fw-semibold mb-2 text-secondary small">Kelola Perawatan per Minggu:</div>
          <div class="row g-2">
            <div class="col-12 col-md-6">
              <?php for ($b = 1; $b <= 6; $b++): ?>
                <div class="d-flex flex-wrap gap-1 mb-2">
                  <span class="small fw-bold me-2" style="width:40px"><?= $bulanNama[$b] ?>:</span>
                  <?php for ($w = 1; $w <= $monthWeeks[$b]; $w++): ?>
                    <button class="btn btn-xs <?= isset($rows[$b][$w]) ? 'btn-success' : 'btn-outline-secondary' ?> py-0 px-2"
                      style="font-size:10px"
                      onclick="bukaEdit(<?= $b ?>, <?= $w ?>)">
                      W<?= $w ?> <?= isset($rows[$b][$w]) ? '✓' : '' ?>
                    </button>
                  <?php endfor; ?>
                </div>
              <?php endfor; ?>
            </div>
            <div class="col-12 col-md-6">
              <?php for ($b = 7; $b <= 12; $b++): ?>
                <div class="d-flex flex-wrap gap-1 mb-2">
                  <span class="small fw-bold me-2" style="width:40px"><?= $bulanNama[$b] ?>:</span>
                  <?php for ($w = 1; $w <= $monthWeeks[$b]; $w++): ?>
                    <button class="btn btn-xs <?= isset($rows[$b][$w]) ? 'btn-success' : 'btn-outline-secondary' ?> py-0 px-2"
                      style="font-size:10px"
                      onclick="bukaEdit(<?= $b ?>, <?= $w ?>)">
                      W<?= $w ?> <?= isset($rows[$b][$w]) ? '✓' : '' ?>
                    </button>
                  <?php endfor; ?>
                </div>
              <?php endfor; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Isian Perawatan -->
  <div class="modal-overlay" id="modalIsian">
    <div class="modal-box">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="fw-bold mb-0" style="color:var(--blue)">
          <i class="fa-solid fa-sink me-2"></i>Isi Perawatan Grease Trap
        </h5>
        <button class="btn-close" onclick="document.getElementById('modalIsian').classList.remove('active')"></button>
      </div>
      <hr class="mt-1">
      <form method="POST" id="formPerawatan">
        <input type="hidden" name="simpan_checklist" value="1">
        <div class="row g-3 mb-3">
          <div class="col-4">
            <label class="form-label fw-semibold small">Bulan</label>
            <select class="form-select form-select-sm" name="bulan" id="bulanSelect" required>
              <option value="">-- Pilih --</option>
              <?php for ($b = 1; $b <= 12; $b++): ?>
                <option value="<?= $b ?>"><?= $bulanNama[$b] ?></option>
              <?php endfor; ?>
            </select>
          </div>
          <div class="col-4">
            <label class="form-label fw-semibold small">Minggu</label>
            <select class="form-select form-select-sm" name="minggu" id="mingguSelect" required>
              <option value="1">I</option>
              <option value="2">II</option>
              <option value="3">III</option>
              <option value="4">IV</option>
              <option value="5">V</option>
            </select>
          </div>
          <div class="col-4">
            <label class="form-label fw-semibold small">Tanggal Cek</label>
            <input type="date" class="form-control form-control-sm" name="tanggal_cek" id="inputTgl" required>
          </div>
        </div>

        <div class="fw-semibold small mb-2" style="color:var(--blue)">Status Pengecekan (Ok / Nok)</div>
        <div class="table-responsive mb-3">
          <table class="table table-sm table-bordered" style="font-size:13px">
            <thead style="background:var(--blue);color:#fff">
              <tr>
                <th>Item Pengecekan</th>
                <th class="text-center">Ok</th>
                <th class="text-center">Nok</th>
              </tr>
            </thead>
            <tbody>
              <?php $no2 = 1;
              foreach ($items as $key => $label): ?>
                <tr>
                  <td><?= $no2++ ?>. <?= $label ?></td>
                  <td class="text-center">
                    <input class="form-check-input" type="radio" name="<?= $key ?>" id="<?= $key ?>_ok" value="Ok">
                  </td>
                  <td class="text-center">
                    <input class="form-check-input" type="radio" name="<?= $key ?>" id="<?= $key ?>_nok" value="Nok">
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <div class="row g-3 mb-3">
          <div class="col-6">
            <label class="form-label fw-semibold small">Paraf Pemeriksa</label>
            <input type="text" class="form-control form-control-sm" name="paraf" id="inputParaf"
              placeholder="Nama / Paraf">
          </div>
          <div class="col-6">
            <label class="form-label fw-semibold small">Catatan</label>
            <input type="text" class="form-control form-control-sm" name="catatan" id="inputCatatan"
              placeholder="Catatan tambahan">
          </div>
        </div>

        <div class="d-flex gap-2">
          <button type="submit" class="btn flex-grow-1" style="background:var(--blue);color:#fff">
            <i class="fa-solid fa-save me-1"></i>Simpan
          </button>
          <button type="button" class="btn btn-secondary"
            onclick="document.getElementById('modalIsian').classList.remove('active')">Batal</button>
        </div>
      </form>
    </div>
  </div>

  <footer class="py-3 text-center no-print">
    &copy; <?= date('Y') ?> - Sistem Perawatan Grease Trap | Team IT Pabrik
  </footer>

  <script>
    // Data yang sudah ada untuk pre-fill edit
    var existingData = <?= json_encode($rows) ?>;
    var items = <?= json_encode(array_keys($items)) ?>;

    function bukaEdit(bulan, minggu = 1) {
      var modal = document.getElementById('modalIsian');
      modal.classList.add('active');
      document.getElementById('bulanSelect').value = bulan;
      document.getElementById('mingguSelect').value = minggu;

      // Reset semua radio
      items.forEach(function (k) {
        var ok = document.getElementById(k + '_ok');
        var nok = document.getElementById(k + '_nok');
        if (ok) ok.checked = false;
        if (nok) nok.checked = false;
      });
      document.getElementById('inputParaf').value = '';
      document.getElementById('inputCatatan').value = '';
      document.getElementById('inputTgl').value = '';

      // Isi dengan data existing jika ada
      if (existingData[bulan] && existingData[bulan][minggu]) {
        var d = existingData[bulan][minggu];
        if (d.tanggal_cek) document.getElementById('inputTgl').value = d.tanggal_cek;
        items.forEach(function (k) {
          if (d[k]) {
            var el = document.getElementById(k + '_' + d[k].toLowerCase());
            if (el) el.checked = true;
          }
        });
        document.getElementById('inputParaf').value = d.paraf || '';
        document.getElementById('inputCatatan').value = d.catatan || '';
      }
    }

    document.getElementById('bulanSelect').addEventListener('change', function () {
      document.getElementById('editBulan').value = this.value;
    });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>