CREATE TABLE IF NOT EXISTS `apar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no_kode` varchar(50) NOT NULL,
  `nama_sarana` varchar(100) DEFAULT NULL,
  `lokasi` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `checklist_apar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `apar_id` int(11) NOT NULL,
  `tahun` int(4) NOT NULL,
  `bulan` tinyint(2) NOT NULL,
  `tanggal_cek` date DEFAULT NULL,
  `label_pengisian` enum('Ok','Nok') DEFAULT NULL,
  `tekanan_pressure` enum('Ok','Nok') DEFAULT NULL,
  `safety_pin` enum('Ok','Nok') DEFAULT NULL,
  `handle` enum('Ok','Nok') DEFAULT NULL,
  `selang_nozzle` enum('Ok','Nok') DEFAULT NULL,
  `dry_chemical` enum('Ok','Nok') DEFAULT NULL,
  `tablulan` enum('Ok','Nok') DEFAULT NULL,
  `bambu_petunjuk` enum('Ok','Nok') DEFAULT NULL,
  `paraf` varchar(100) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `users_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `apar` (`no_kode`, `nama_sarana`, `lokasi`) VALUES
('KNR-1', 'APAR 9 KG', 'GEDUNG UTAMA LANTAI 1'),
('KNR-2', 'APAR 6 KG', 'GEDUNG UTAMA LANTAI 2'),
('KNR-3', 'APAR 3 KG', 'RUANG SERVER'),
('KNR-4', 'APAR 9 KG', 'GUDANG UTAMA');

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `pass` varchar(100) NOT NULL,
  `nama` varchar(200) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `status` enum('Aktif','Non-Aktif') DEFAULT 'Aktif',
  `role` enum('Admin','User') DEFAULT 'User',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` (`username`, `pass`, `nama`, `email`, `status`, `role`) VALUES
('admin', 'admin', 'Administrator', 'admin@example.com', 'Aktif', 'Admin'),
('user', 'user', 'User Biasa', 'user@example.com', 'Aktif', 'User');

CREATE TABLE IF NOT EXISTS `gedung` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no_kode` varchar(50) NOT NULL,
  `nama_sarana` varchar(100) DEFAULT NULL,
  `lokasi` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `checklist_gedung` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gedung_id` int(11) NOT NULL,
  `tahun` int(4) NOT NULL,
  `bulan` tinyint(2) NOT NULL,
  `tanggal_cek` date DEFAULT NULL,
  `dinding` enum('Ok','Nok') DEFAULT NULL,
  `atap_talang` enum('Ok','Nok') DEFAULT NULL,
  `lantai` enum('Ok','Nok') DEFAULT NULL,
  `wastafel` enum('Ok','Nok') DEFAULT NULL,
  `pintu_kaca` enum('Ok','Nok') DEFAULT NULL,
  `toilet` enum('Ok','Nok') DEFAULT NULL,
  `lain_lain` enum('Ok','Nok') DEFAULT NULL,
  `paraf` varchar(100) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `users_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `hydrant` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no_kode` varchar(50) NOT NULL,
  `nama_sarana` varchar(100) DEFAULT NULL,
  `lokasi` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `checklist_hydrant` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hydrant_id` int(11) NOT NULL,
  `tahun` int(4) NOT NULL,
  `bulan` tinyint(2) NOT NULL,
  `tanggal_cek` date DEFAULT NULL,
  `valve_handle` enum('Ok','Nok') DEFAULT NULL,
  `hose_coupling_conect` enum('Ok','Nok') DEFAULT NULL,
  `baut_valve_handle` enum('Ok','Nok') DEFAULT NULL,
  `fire_hose` enum('Ok','Nok') DEFAULT NULL,
  `slang_hydrant` enum('Ok','Nok') DEFAULT NULL,
  `nozzle` enum('Ok','Nok') DEFAULT NULL,
  `box_hydrant` enum('Ok','Nok') DEFAULT NULL,
  `paraf` varchar(100) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `users_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `grease_trap` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no_kode` varchar(50) NOT NULL,
  `nama_sarana` varchar(100) DEFAULT NULL,
  `lokasi` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `checklist_grease_trap` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `grease_trap_id` int(11) NOT NULL,
  `tahun` int(4) NOT NULL,
  `bulan` tinyint(2) NOT NULL,
  `minggu` tinyint(1) NOT NULL DEFAULT 1,
  `tanggal_cek` date DEFAULT NULL,
  `kondisi_fisik` enum('Ok','Nok') DEFAULT NULL,
  `kebersihan_internal` enum('Ok','Nok') DEFAULT NULL,
  `pemisahan_lemak` enum('Ok','Nok') DEFAULT NULL,
  `saluran_in_out` enum('Ok','Nok') DEFAULT NULL,
  `bau_kontaminasi` enum('Ok','Nok') DEFAULT NULL,
  `paraf` varchar(100) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `users_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
