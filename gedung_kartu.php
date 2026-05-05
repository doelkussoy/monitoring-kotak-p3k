<?php
include "config/koneksi.php";
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: index.php");
  exit;
}

$gedung_id = isset($_GET['gedung_id']) ? (int) $_GET['gedung_id'] : 0;
$tahun = isset($_GET['tahun']) ? (int) $_GET['tahun'] : (int) date('Y');

// Ambil data Gedung
$gedungRow = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM gedung WHERE id=$gedung_id"));
if (!$gedungRow) {
  header("Location: gedung_home.php");
  exit;
}

// Handle simpan perawatan
if (isset($_POST['simpan_checklist'])) {
  $bulan = (int) $_POST['bulan'];
  $tgl = mysqli_real_escape_string($conn, $_POST['tanggal_cek']);
  $fields = ['dinding', 'atap_talang', 'lantai', 'wastafel', 'pintu_kaca', 'toilet', 'lain_lain'];
  $vals = [];
  foreach ($fields as $f) {
    $vals[$f] = isset($_POST[$f]) ? mysqli_real_escape_string($conn, $_POST[$f]) : null;
  }
  $paraf = mysqli_real_escape_string($conn, $_POST['paraf'] ?? '');
  $catatan = mysqli_real_escape_string($conn, $_POST['catatan'] ?? '');
  $uid = (int) $_SESSION['id'];

  // Cek sudah ada?
  $cek = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM checklist_gedung WHERE gedung_id=$gedung_id AND tahun=$tahun AND bulan=$bulan"));
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
    mysqli_query($conn, "UPDATE checklist_gedung SET $set WHERE id={$cek['id']}");
  } else {
    $fStr = "gedung_id,tahun,bulan,tanggal_cek,dinding,atap_talang,lantai,wastafel,pintu_kaca,toilet,lain_lain,paraf,catatan,users_id";
    $v1 = $vals['dinding'] ? "'{$vals['dinding']}'" : 'NULL';
    $v2 = $vals['atap_talang'] ? "'{$vals['atap_talang']}'" : 'NULL';
    $v3 = $vals['lantai'] ? "'{$vals['lantai']}'" : 'NULL';
    $v4 = $vals['wastafel'] ? "'{$vals['wastafel']}'" : 'NULL';
    $v5 = $vals['pintu_kaca'] ? "'{$vals['pintu_kaca']}'" : 'NULL';
    $v6 = $vals['toilet'] ? "'{$vals['toilet']}'" : 'NULL';
    $v7 = $vals['lain_lain'] ? "'{$vals['lain_lain']}'" : 'NULL';
    mysqli_query($conn, "INSERT INTO checklist_gedung ($fStr) VALUES ($gedung_id,$tahun,$bulan,'$tgl',$v1,$v2,$v3,$v4,$v5,$v6,$v7,'$paraf','$catatan',$uid)");
  }
  header("Location: gedung_kartu.php?gedung_id=$gedung_id&tahun=$tahun");
  exit;
}

// Handle hapus baris
if (isset($_GET['hapus'])) {
  mysqli_query($conn, "DELETE FROM checklist_gedung WHERE id=" . (int) $_GET['hapus'] . " AND gedung_id=$gedung_id");
  header("Location: gedung_kartu.php?gedung_id=$gedung_id&tahun=$tahun");
  exit;
}

// Ambil semua perawatan tahun ini
$rows = [];
$res = mysqli_query($conn, "SELECT * FROM checklist_gedung WHERE gedung_id=$gedung_id AND tahun=$tahun ORDER BY bulan ASC");
while ($r = mysqli_fetch_assoc($res)) {
  $rows[$r['bulan']] = $r;
}

$bulanNama = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
$items = [
  'dinding' => 'Dinding',
  'atap_talang' => 'Atap/Talang',
  'lantai' => 'Lantai',
  'wastafel' => 'Wastafel',
  'pintu_kaca' => 'Pintu/Kaca',
  'toilet' => 'Toilet',
  'lain_lain' => 'Lain-lain',
];
$editBulan = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$editData = $editBulan && isset($rows[$editBulan]) ? $rows[$editBulan] : null;
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kartu Riwayat Gedung - <?= htmlspecialchars($gedungRow['no_kode']) ?></title>
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

    .navbar-gedung {
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
    }
  </style>
</head>

<body>
  <nav class="navbar navbar-gedung fixed-top py-3 shadow no-print">
    <div class="container-fluid d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center gap-2 text-white" style="cursor:pointer"
        onclick="window.location.href='dashboard.php'">
        <img src="assets/images/cba.png" alt="Logo CBA" style="height:40px; background:white; padding:4px; border-radius:8px;">
        <span class="fw-bold fs-5 d-none d-sm-inline" style="letter-spacing:1px">SISTEM SARANA PRASARANA</span>
      </div>
      <div class="d-flex align-items-center gap-2 gap-md-3">
        <span class="text-white small d-none d-md-inline"><i class="fa-solid fa-user me-1"></i><?= $_SESSION['username'] ?></span>
        <a href="logout.php" class="btn btn-sm btn-outline-light rounded-pill px-2 px-md-3" title="Logout">
          <i class="fa-solid fa-right-from-bracket d-inline d-sm-none"></i>
          <span class="d-none d-sm-inline"><i class="fa-solid fa-right-from-bracket me-1"></i>Logout</span>
        </a>
      </div>
    </div>
  </nav>

  <div class="container-fluid" style="margin-top:90px;padding-bottom:30px;max-width:1400px;flex:1">

    <!-- Print Header Custom (Pengganti bawaan browser) -->
    <div class="print-only-header">
      <div style="width:33%; text-align:left;"><?= date('d/m/Y, H:i') ?></div>
      <div style="width:33%; text-align:center;">Kartu Riwayat Gedung - <?= htmlspecialchars($gedungRow['no_kode']) ?></div>
      <div style="width:33%; text-align:right;"></div>
    </div>

    <!-- Kontrol atas -->
    <div class="d-flex flex-wrap gap-2 align-items-center mb-3 no-print">
      <a href="gedung_home.php" class="btn btn-sm btn-outline-secondary rounded-pill">
        <i class="fa-solid fa-arrow-left me-1"></i>Kembali
      </a>
      <form method="GET" class="d-flex align-items-center gap-2 ms-auto">
        <input type="hidden" name="gedung_id" value="<?= $gedung_id ?>">
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
        <!-- Info Gedung -->
        <div class="info-box">
          <div class="row g-2">
            <div class="col-12 col-md-4">
              <div class="info-label">No. Kode</div>
              <div class="info-val"><span class="badge-kode"><?= htmlspecialchars($gedungRow['no_kode']) ?></span></div>
            </div>
            <div class="col-12 col-md-4">
              <div class="info-label">Nama Sarana/Prasarana</div>
              <div class="info-val"><?= htmlspecialchars($gedungRow['nama_sarana']) ?></div>
            </div>
            <div class="col-12 col-md-4">
              <div class="info-label">Lokasi</div>
              <div class="info-val"><i
                  class="fa-solid fa-location-dot text-danger me-1"></i><?= htmlspecialchars($gedungRow['lokasi']) ?>
              </div>
            </div>
          </div>
        </div>

        <!-- Tabel Perawatan -->
        <div class="table-responsive">
          <table class="tbl-kartu">
            <thead>
              <tr>
                <th rowspan="2" style="width:30px">NO</th>
                <th rowspan="2" class="th-item" style="min-width:180px">PENGECEKAN</th>
                <?php for ($b = 1; $b <= 12; $b++): ?>
                  <th colspan="3" class="th-bulan"><?= $bulanNama[$b] ?></th>
                <?php endfor; ?>
              </tr>
              <tr>
                <?php for ($b = 1; $b <= 12; $b++): ?>
                  <th style="background:var(--blue-dark);color:#fff;font-size:10px">Tgl</th>
                  <th style="background:var(--blue-dark);color:#fff;font-size:10px">Ok</th>
                  <th style="background:var(--blue-dark);color:#fff;font-size:10px">Nok</th>
                <?php endfor; ?>
              </tr>
            </thead>
            <tbody>
              <?php $no = 1;
              foreach ($items as $key => $label): ?>
                <tr>
                  <td><?= $no++ ?></td>
                  <td class="th-item"><?= $label ?></td>
                  <?php for ($b = 1; $b <= 12; $b++):
                    $r = $rows[$b] ?? null;
                    $val = $r ? $r[$key] : null;
                    $tgl = $r ? date('d', strtotime($r['tanggal_cek'])) : '';
                    ?>
                    <td style="font-size:10px"><?= $tgl ?: '<span class="empty-cell">-</span>' ?></td>
                    <td class="<?= $val == 'Ok' ? 'ok-cell' : '' ?>"><?= $val == 'Ok' ? '✓' : '' ?></td>
                    <td class="<?= $val == 'Nok' ? 'nok-cell' : '' ?>"><?= $val == 'Nok' ? '✗' : '' ?></td>
                  <?php endfor; ?>
                </tr>
              <?php endforeach; ?>
              <!-- Baris Paraf -->
              <tr>
                <td colspan="2" class="th-item fw-bold">Pemeriksa</td>
                <?php for ($b = 1; $b <= 12; $b++):
                  $r = $rows[$b] ?? null;
                  $paraf = $r ? htmlspecialchars($r['paraf']) : '';
                  ?>
                  <td colspan="3" style="font-size:10px;font-style:italic">
                    <?= $paraf ?: '<span class="empty-cell">Paraf</span>' ?>
                  </td>
                <?php endfor; ?>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Keterangan/Catatan Tambahan -->
        <div class="mt-4" style="border:1px solid #ddd;border-radius:10px;padding:16px;">
          <div class="fw-bold mb-3" style="color:var(--blue)">Keterangan / Catatan Tambahan :</div>
          <div class="row g-2">
            <?php foreach ($bulanNama as $b => $nm):
              if ($b == 0)
                continue;
              $cat = isset($rows[$b]) ? $rows[$b]['catatan'] : '';
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
          <div class="fw-semibold mb-2 text-secondary small">Kelola Perawatan per Bulan:</div>
          <div class="d-flex flex-wrap gap-2">
            <?php for ($b = 1; $b <= 12; $b++): ?>
              <button class="btn btn-sm <?= isset($rows[$b]) ? 'btn-success' : 'btn-outline-secondary' ?> rounded-pill"
                onclick="bukaEdit(<?= $b ?>)"
                title="<?= isset($rows[$b]) ? 'Edit' : 'Isi' ?> Bulan <?= $bulanNama[$b] ?>">
                <?= $bulanNama[$b] ?>   <?= isset($rows[$b]) ? '<i class=\'fa-solid fa-check\'></i>' : '' ?>
              </button>
            <?php endfor; ?>
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
          <i class="fa-solid fa-building-circle-check me-2"></i>Isi Perawatan Gedung
        </h5>
        <button class="btn-close" onclick="document.getElementById('modalIsian').classList.remove('active')"></button>
      </div>
      <hr class="mt-1">
      <form method="POST" id="formPerawatan">
        <input type="hidden" name="simpan_checklist" value="1">
        <input type="hidden" name="bulan" id="editBulan" value="">

        <div class="row g-3 mb-3">
          <div class="col-6">
            <label class="form-label fw-semibold small">Bulan</label>
            <select class="form-select form-select-sm" name="bulan_select" id="bulanSelect" required
              onchange="document.getElementById('editBulan').value=this.value">
              <option value="">-- Pilih Bulan --</option>
              <?php for ($b = 1; $b <= 12; $b++): ?>
                <option value="<?= $b ?>"><?= $bulanNama[$b] ?></option>
              <?php endfor; ?>
            </select>
          </div>
          <div class="col-6">
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
    &copy; <?= date('Y') ?> - Sistem Perawatan Gedung | Team IT Pabrik
  </footer>

  <script>
    // Data yang sudah ada untuk pre-fill edit
    var existingData = <?= json_encode($rows) ?>;
    var items = <?= json_encode(array_keys($items)) ?>;

    function bukaEdit(bulan) {
      var modal = document.getElementById('modalIsian');
      modal.classList.add('active');
      document.getElementById('editBulan').value = bulan;
      document.getElementById('bulanSelect').value = bulan;

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
      if (existingData[bulan]) {
        var d = existingData[bulan];
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