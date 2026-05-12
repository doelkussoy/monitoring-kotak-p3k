-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 12, 2026 at 08:55 AM
-- Server version: 10.11.10-MariaDB-log
-- PHP Version: 8.2.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kotakp3k`
--

-- --------------------------------------------------------

--
-- Table structure for table `p3k_activity`
--

CREATE TABLE `p3k_activity` (
  `id` int(11) NOT NULL,
  `lokasi_id` int(11) NOT NULL,
  `activity_type` enum('UPDATE','CHECK','CRITICAL') DEFAULT 'CHECK',
  `message` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `p3k_activity`
--

INSERT INTO `p3k_activity` (`id`, `lokasi_id`, `activity_type`, `message`, `created_at`) VALUES
(3, 5, 'UPDATE', 'Menambahkan item baru: Test oleh admin', '2026-05-08 15:07:48'),
(4, 5, 'UPDATE', 'Update stok Test menjadi 0 oleh admin', '2026-05-08 15:15:37'),
(5, 5, 'UPDATE', 'Menghapus item: Test oleh admin', '2026-05-08 15:17:50'),
(6, 25, 'UPDATE', 'Menambahkan item baru: Kasa oleh sahrudin', '2026-05-08 15:36:00'),
(7, 7, 'UPDATE', 'Menambahkan item baru: Kasa oleh admin', '2026-05-08 16:00:15'),
(8, 7, 'UPDATE', 'Menambahkan item baru: Kasa oleh admin', '2026-05-08 16:00:30'),
(9, 7, 'UPDATE', 'Menghapus item: Kasa oleh admin', '2026-05-08 16:00:53'),
(10, 7, 'UPDATE', 'Menghapus item: Kasa oleh admin', '2026-05-08 16:01:12'),
(11, 7, 'UPDATE', 'Menghapus item: Kasa oleh admin', '2026-05-08 16:01:15'),
(12, 5, 'UPDATE', 'Update stok Alkohol 70% (Stok: 0, Min: 0) oleh admin', '2026-05-08 16:07:47'),
(13, 5, 'UPDATE', 'Update stok Alkohol 70% (Stok: 1, Min: 0) oleh admin', '2026-05-08 16:07:53'),
(14, 28, 'UPDATE', 'Update Alkohol 70% (Stok: 1, Min: 0, Exp: 2027-05-07) oleh asgun', '2026-05-08 16:14:36'),
(15, 28, 'UPDATE', 'Update Alkohol 70% (Stok: 1, Min: 0, Exp: 2027-05-06) oleh asgun', '2026-05-08 16:15:55'),
(16, 28, 'UPDATE', 'Update Alkohol 70% (Stok: 0, Min: 0, Exp: 2027-05-06) oleh asgun', '2026-05-08 16:18:55'),
(17, 28, 'UPDATE', 'Update Alkohol 70% (Stok: 0, Min: 0, Exp: 2026-05-05) oleh asgun', '2026-05-08 16:21:31'),
(18, 28, 'UPDATE', 'Update Aquades (100ml lar. saline) (Stok: 1, Min: 0, Exp: 2025-06-12) oleh asgun', '2026-05-08 16:22:35');

-- --------------------------------------------------------

--
-- Table structure for table `p3k_items`
--

CREATE TABLE `p3k_items` (
  `id` int(11) NOT NULL,
  `lokasi_id` int(11) NOT NULL,
  `nama_item` varchar(100) NOT NULL,
  `stok` int(11) NOT NULL DEFAULT 0,
  `min_stok` int(11) NOT NULL DEFAULT 0,
  `satuan` varchar(20) DEFAULT 'pcs',
  `tgl_kadaluarsa` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `p3k_items`
--

INSERT INTO `p3k_items` (`id`, `lokasi_id`, `nama_item`, `stok`, `min_stok`, `satuan`, `tgl_kadaluarsa`) VALUES
(8, 25, 'Kasa', 3, 0, 'pcs', '2026-08-29'),
(9, 7, 'Kasa', 3, 1, 'pcs', '2026-09-30'),
(11, 5, 'Kasa steril terbungkus', 1, 0, 'box', '2027-05-08'),
(12, 5, 'Perban (lebar 5 cm)', 1, 0, 'roll', '2027-05-08'),
(13, 5, 'Perban (lebar 10 cm)', 1, 0, 'roll', '2027-05-08'),
(14, 5, 'Plester (lebar 1,25 cm)', 1, 0, 'roll', '2027-05-08'),
(15, 5, 'Plester cepat', 1, 0, 'box', '2027-05-08'),
(16, 5, 'Kapas 25 gr', 1, 0, 'bungkus', '2027-05-08'),
(17, 5, 'Kain segitiga (mitella)', 1, 0, 'pcs', '2027-05-08'),
(18, 5, 'Gunting', 1, 0, 'pcs', '2027-05-08'),
(19, 5, 'Peniti', 1, 0, 'set', '2027-05-08'),
(20, 5, 'Sarung tangan sekali pakai', 1, 0, 'pasang', '2027-05-08'),
(21, 5, 'Masker', 1, 0, 'box', '2027-05-08'),
(22, 5, 'Pinset', 1, 0, 'pcs', '2027-05-08'),
(23, 5, 'Lampu senter', 1, 0, 'pcs', '2027-05-08'),
(24, 5, 'Gelas cuci mata', 1, 0, 'pcs', '2027-05-08'),
(25, 5, 'Kantong plastik bersih', 1, 0, 'pack', '2027-05-08'),
(26, 5, 'Aquades (100ml lar. saline)', 1, 0, 'botol', '2027-05-08'),
(27, 5, 'Povidon Iodin (60 ml)', 1, 0, 'botol', '2027-05-08'),
(28, 5, 'Alkohol 70%', 1, 0, 'botol', '2027-05-08'),
(29, 5, 'Buku panduan P3K di tempat kerja', 1, 0, 'pcs', '2027-05-08'),
(30, 5, 'Buku catatan', 1, 0, 'pcs', '2027-05-08'),
(31, 6, 'Kasa steril terbungkus', 1, 0, 'box', '2027-05-08'),
(32, 6, 'Perban (lebar 5 cm)', 1, 0, 'roll', '2027-05-08'),
(33, 6, 'Perban (lebar 10 cm)', 1, 0, 'roll', '2027-05-08'),
(34, 6, 'Plester (lebar 1,25 cm)', 1, 0, 'roll', '2027-05-08'),
(35, 6, 'Plester cepat', 1, 0, 'box', '2027-05-08'),
(36, 6, 'Kapas 25 gr', 1, 0, 'bungkus', '2027-05-08'),
(37, 6, 'Kain segitiga (mitella)', 1, 0, 'pcs', '2027-05-08'),
(38, 6, 'Gunting', 1, 0, 'pcs', '2027-05-08'),
(39, 6, 'Peniti', 1, 0, 'set', '2027-05-08'),
(40, 6, 'Sarung tangan sekali pakai', 1, 0, 'pasang', '2027-05-08'),
(41, 6, 'Masker', 1, 0, 'box', '2027-05-08'),
(42, 6, 'Pinset', 1, 0, 'pcs', '2027-05-08'),
(43, 6, 'Lampu senter', 1, 0, 'pcs', '2027-05-08'),
(44, 6, 'Gelas cuci mata', 1, 0, 'pcs', '2027-05-08'),
(45, 6, 'Kantong plastik bersih', 1, 0, 'pack', '2027-05-08'),
(46, 6, 'Aquades (100ml lar. saline)', 1, 0, 'botol', '2027-05-08'),
(47, 6, 'Povidon Iodin (60 ml)', 1, 0, 'botol', '2027-05-08'),
(48, 6, 'Alkohol 70%', 1, 0, 'botol', '2027-05-08'),
(49, 6, 'Buku panduan P3K di tempat kerja', 1, 0, 'pcs', '2027-05-08'),
(50, 6, 'Buku catatan', 1, 0, 'pcs', '2027-05-08'),
(51, 7, 'Kasa steril terbungkus', 1, 0, 'box', '2027-05-08'),
(52, 7, 'Perban (lebar 5 cm)', 1, 0, 'roll', '2027-05-08'),
(53, 7, 'Perban (lebar 10 cm)', 1, 0, 'roll', '2027-05-08'),
(54, 7, 'Plester (lebar 1,25 cm)', 1, 0, 'roll', '2027-05-08'),
(55, 7, 'Plester cepat', 1, 0, 'box', '2027-05-08'),
(56, 7, 'Kapas 25 gr', 1, 0, 'bungkus', '2027-05-08'),
(57, 7, 'Kain segitiga (mitella)', 1, 0, 'pcs', '2027-05-08'),
(58, 7, 'Gunting', 1, 0, 'pcs', '2027-05-08'),
(59, 7, 'Peniti', 1, 0, 'set', '2027-05-08'),
(60, 7, 'Sarung tangan sekali pakai', 1, 0, 'pasang', '2027-05-08'),
(61, 7, 'Masker', 1, 0, 'box', '2027-05-08'),
(62, 7, 'Pinset', 1, 0, 'pcs', '2027-05-08'),
(63, 7, 'Lampu senter', 1, 0, 'pcs', '2027-05-08'),
(64, 7, 'Gelas cuci mata', 1, 0, 'pcs', '2027-05-08'),
(65, 7, 'Kantong plastik bersih', 1, 0, 'pack', '2027-05-08'),
(66, 7, 'Aquades (100ml lar. saline)', 1, 0, 'botol', '2027-05-08'),
(67, 7, 'Povidon Iodin (60 ml)', 1, 0, 'botol', '2027-05-08'),
(68, 7, 'Alkohol 70%', 1, 0, 'botol', '2027-05-08'),
(69, 7, 'Buku panduan P3K di tempat kerja', 1, 0, 'pcs', '2027-05-08'),
(70, 7, 'Buku catatan', 1, 0, 'pcs', '2027-05-08'),
(71, 8, 'Kasa steril terbungkus', 1, 0, 'box', '2027-05-08'),
(72, 8, 'Perban (lebar 5 cm)', 1, 0, 'roll', '2027-05-08'),
(73, 8, 'Perban (lebar 10 cm)', 1, 0, 'roll', '2027-05-08'),
(74, 8, 'Plester (lebar 1,25 cm)', 1, 0, 'roll', '2027-05-08'),
(75, 8, 'Plester cepat', 1, 0, 'box', '2027-05-08'),
(76, 8, 'Kapas 25 gr', 1, 0, 'bungkus', '2027-05-08'),
(77, 8, 'Kain segitiga (mitella)', 1, 0, 'pcs', '2027-05-08'),
(78, 8, 'Gunting', 1, 0, 'pcs', '2027-05-08'),
(79, 8, 'Peniti', 1, 0, 'set', '2027-05-08'),
(80, 8, 'Sarung tangan sekali pakai', 1, 0, 'pasang', '2027-05-08'),
(81, 8, 'Masker', 1, 0, 'box', '2027-05-08'),
(82, 8, 'Pinset', 1, 0, 'pcs', '2027-05-08'),
(83, 8, 'Lampu senter', 1, 0, 'pcs', '2027-05-08'),
(84, 8, 'Gelas cuci mata', 1, 0, 'pcs', '2027-05-08'),
(85, 8, 'Kantong plastik bersih', 1, 0, 'pack', '2027-05-08'),
(86, 8, 'Aquades (100ml lar. saline)', 1, 0, 'botol', '2027-05-08'),
(87, 8, 'Povidon Iodin (60 ml)', 1, 0, 'botol', '2027-05-08'),
(88, 8, 'Alkohol 70%', 1, 0, 'botol', '2027-05-08'),
(89, 8, 'Buku panduan P3K di tempat kerja', 1, 0, 'pcs', '2027-05-08'),
(90, 8, 'Buku catatan', 1, 0, 'pcs', '2027-05-08'),
(91, 9, 'Kasa steril terbungkus', 1, 0, 'box', '2027-05-08'),
(92, 9, 'Perban (lebar 5 cm)', 1, 0, 'roll', '2027-05-08'),
(93, 9, 'Perban (lebar 10 cm)', 1, 0, 'roll', '2027-05-08'),
(94, 9, 'Plester (lebar 1,25 cm)', 1, 0, 'roll', '2027-05-08'),
(95, 9, 'Plester cepat', 1, 0, 'box', '2027-05-08'),
(96, 9, 'Kapas 25 gr', 1, 0, 'bungkus', '2027-05-08'),
(97, 9, 'Kain segitiga (mitella)', 1, 0, 'pcs', '2027-05-08'),
(98, 9, 'Gunting', 1, 0, 'pcs', '2027-05-08'),
(99, 9, 'Peniti', 1, 0, 'set', '2027-05-08'),
(100, 9, 'Sarung tangan sekali pakai', 1, 0, 'pasang', '2027-05-08'),
(101, 9, 'Masker', 1, 0, 'box', '2027-05-08'),
(102, 9, 'Pinset', 1, 0, 'pcs', '2027-05-08'),
(103, 9, 'Lampu senter', 1, 0, 'pcs', '2027-05-08'),
(104, 9, 'Gelas cuci mata', 1, 0, 'pcs', '2027-05-08'),
(105, 9, 'Kantong plastik bersih', 1, 0, 'pack', '2027-05-08'),
(106, 9, 'Aquades (100ml lar. saline)', 1, 0, 'botol', '2027-05-08'),
(107, 9, 'Povidon Iodin (60 ml)', 1, 0, 'botol', '2027-05-08'),
(108, 9, 'Alkohol 70%', 1, 0, 'botol', '2027-05-08'),
(109, 9, 'Buku panduan P3K di tempat kerja', 1, 0, 'pcs', '2027-05-08'),
(110, 9, 'Buku catatan', 1, 0, 'pcs', '2027-05-08'),
(111, 10, 'Kasa steril terbungkus', 1, 0, 'box', '2027-05-08'),
(112, 10, 'Perban (lebar 5 cm)', 1, 0, 'roll', '2027-05-08'),
(113, 10, 'Perban (lebar 10 cm)', 1, 0, 'roll', '2027-05-08'),
(114, 10, 'Plester (lebar 1,25 cm)', 1, 0, 'roll', '2027-05-08'),
(115, 10, 'Plester cepat', 1, 0, 'box', '2027-05-08'),
(116, 10, 'Kapas 25 gr', 1, 0, 'bungkus', '2027-05-08'),
(117, 10, 'Kain segitiga (mitella)', 1, 0, 'pcs', '2027-05-08'),
(118, 10, 'Gunting', 1, 0, 'pcs', '2027-05-08'),
(119, 10, 'Peniti', 1, 0, 'set', '2027-05-08'),
(120, 10, 'Sarung tangan sekali pakai', 1, 0, 'pasang', '2027-05-08'),
(121, 10, 'Masker', 1, 0, 'box', '2027-05-08'),
(122, 10, 'Pinset', 1, 0, 'pcs', '2027-05-08'),
(123, 10, 'Lampu senter', 1, 0, 'pcs', '2027-05-08'),
(124, 10, 'Gelas cuci mata', 1, 0, 'pcs', '2027-05-08'),
(125, 10, 'Kantong plastik bersih', 1, 0, 'pack', '2027-05-08'),
(126, 10, 'Aquades (100ml lar. saline)', 1, 0, 'botol', '2027-05-08'),
(127, 10, 'Povidon Iodin (60 ml)', 1, 0, 'botol', '2027-05-08'),
(128, 10, 'Alkohol 70%', 1, 0, 'botol', '2027-05-08'),
(129, 10, 'Buku panduan P3K di tempat kerja', 1, 0, 'pcs', '2027-05-08'),
(130, 10, 'Buku catatan', 1, 0, 'pcs', '2027-05-08'),
(131, 11, 'Kasa steril terbungkus', 1, 0, 'box', '2027-05-08'),
(132, 11, 'Perban (lebar 5 cm)', 1, 0, 'roll', '2027-05-08'),
(133, 11, 'Perban (lebar 10 cm)', 1, 0, 'roll', '2027-05-08'),
(134, 11, 'Plester (lebar 1,25 cm)', 1, 0, 'roll', '2027-05-08'),
(135, 11, 'Plester cepat', 1, 0, 'box', '2027-05-08'),
(136, 11, 'Kapas 25 gr', 1, 0, 'bungkus', '2027-05-08'),
(137, 11, 'Kain segitiga (mitella)', 1, 0, 'pcs', '2027-05-08'),
(138, 11, 'Gunting', 1, 0, 'pcs', '2027-05-08'),
(139, 11, 'Peniti', 1, 0, 'set', '2027-05-08'),
(140, 11, 'Sarung tangan sekali pakai', 1, 0, 'pasang', '2027-05-08'),
(141, 11, 'Masker', 1, 0, 'box', '2027-05-08'),
(142, 11, 'Pinset', 1, 0, 'pcs', '2027-05-08'),
(143, 11, 'Lampu senter', 1, 0, 'pcs', '2027-05-08'),
(144, 11, 'Gelas cuci mata', 1, 0, 'pcs', '2027-05-08'),
(145, 11, 'Kantong plastik bersih', 1, 0, 'pack', '2027-05-08'),
(146, 11, 'Aquades (100ml lar. saline)', 1, 0, 'botol', '2027-05-08'),
(147, 11, 'Povidon Iodin (60 ml)', 1, 0, 'botol', '2027-05-08'),
(148, 11, 'Alkohol 70%', 1, 0, 'botol', '2027-05-08'),
(149, 11, 'Buku panduan P3K di tempat kerja', 1, 0, 'pcs', '2027-05-08'),
(150, 11, 'Buku catatan', 1, 0, 'pcs', '2027-05-08'),
(151, 12, 'Kasa steril terbungkus', 1, 0, 'box', '2027-05-08'),
(152, 12, 'Perban (lebar 5 cm)', 1, 0, 'roll', '2027-05-08'),
(153, 12, 'Perban (lebar 10 cm)', 1, 0, 'roll', '2027-05-08'),
(154, 12, 'Plester (lebar 1,25 cm)', 1, 0, 'roll', '2027-05-08'),
(155, 12, 'Plester cepat', 1, 0, 'box', '2027-05-08'),
(156, 12, 'Kapas 25 gr', 1, 0, 'bungkus', '2027-05-08'),
(157, 12, 'Kain segitiga (mitella)', 1, 0, 'pcs', '2027-05-08'),
(158, 12, 'Gunting', 1, 0, 'pcs', '2027-05-08'),
(159, 12, 'Peniti', 1, 0, 'set', '2027-05-08'),
(160, 12, 'Sarung tangan sekali pakai', 1, 0, 'pasang', '2027-05-08'),
(161, 12, 'Masker', 1, 0, 'box', '2027-05-08'),
(162, 12, 'Pinset', 1, 0, 'pcs', '2027-05-08'),
(163, 12, 'Lampu senter', 1, 0, 'pcs', '2027-05-08'),
(164, 12, 'Gelas cuci mata', 1, 0, 'pcs', '2027-05-08'),
(165, 12, 'Kantong plastik bersih', 1, 0, 'pack', '2027-05-08'),
(166, 12, 'Aquades (100ml lar. saline)', 1, 0, 'botol', '2027-05-08'),
(167, 12, 'Povidon Iodin (60 ml)', 1, 0, 'botol', '2027-05-08'),
(168, 12, 'Alkohol 70%', 1, 0, 'botol', '2027-05-08'),
(169, 12, 'Buku panduan P3K di tempat kerja', 1, 0, 'pcs', '2027-05-08'),
(170, 12, 'Buku catatan', 1, 0, 'pcs', '2027-05-08'),
(171, 13, 'Kasa steril terbungkus', 1, 0, 'box', '2027-05-08'),
(172, 13, 'Perban (lebar 5 cm)', 1, 0, 'roll', '2027-05-08'),
(173, 13, 'Perban (lebar 10 cm)', 1, 0, 'roll', '2027-05-08'),
(174, 13, 'Plester (lebar 1,25 cm)', 1, 0, 'roll', '2027-05-08'),
(175, 13, 'Plester cepat', 1, 0, 'box', '2027-05-08'),
(176, 13, 'Kapas 25 gr', 1, 0, 'bungkus', '2027-05-08'),
(177, 13, 'Kain segitiga (mitella)', 1, 0, 'pcs', '2027-05-08'),
(178, 13, 'Gunting', 1, 0, 'pcs', '2027-05-08'),
(179, 13, 'Peniti', 1, 0, 'set', '2027-05-08'),
(180, 13, 'Sarung tangan sekali pakai', 1, 0, 'pasang', '2027-05-08'),
(181, 13, 'Masker', 1, 0, 'box', '2027-05-08'),
(182, 13, 'Pinset', 1, 0, 'pcs', '2027-05-08'),
(183, 13, 'Lampu senter', 1, 0, 'pcs', '2027-05-08'),
(184, 13, 'Gelas cuci mata', 1, 0, 'pcs', '2027-05-08'),
(185, 13, 'Kantong plastik bersih', 1, 0, 'pack', '2027-05-08'),
(186, 13, 'Aquades (100ml lar. saline)', 1, 0, 'botol', '2027-05-08'),
(187, 13, 'Povidon Iodin (60 ml)', 1, 0, 'botol', '2027-05-08'),
(188, 13, 'Alkohol 70%', 1, 0, 'botol', '2027-05-08'),
(189, 13, 'Buku panduan P3K di tempat kerja', 1, 0, 'pcs', '2027-05-08'),
(190, 13, 'Buku catatan', 1, 0, 'pcs', '2027-05-08'),
(191, 14, 'Kasa steril terbungkus', 1, 0, 'box', '2027-05-08'),
(192, 14, 'Perban (lebar 5 cm)', 1, 0, 'roll', '2027-05-08'),
(193, 14, 'Perban (lebar 10 cm)', 1, 0, 'roll', '2027-05-08'),
(194, 14, 'Plester (lebar 1,25 cm)', 1, 0, 'roll', '2027-05-08'),
(195, 14, 'Plester cepat', 1, 0, 'box', '2027-05-08'),
(196, 14, 'Kapas 25 gr', 1, 0, 'bungkus', '2027-05-08'),
(197, 14, 'Kain segitiga (mitella)', 1, 0, 'pcs', '2027-05-08'),
(198, 14, 'Gunting', 1, 0, 'pcs', '2027-05-08'),
(199, 14, 'Peniti', 1, 0, 'set', '2027-05-08'),
(200, 14, 'Sarung tangan sekali pakai', 1, 0, 'pasang', '2027-05-08'),
(201, 14, 'Masker', 1, 0, 'box', '2027-05-08'),
(202, 14, 'Pinset', 1, 0, 'pcs', '2027-05-08'),
(203, 14, 'Lampu senter', 1, 0, 'pcs', '2027-05-08'),
(204, 14, 'Gelas cuci mata', 1, 0, 'pcs', '2027-05-08'),
(205, 14, 'Kantong plastik bersih', 1, 0, 'pack', '2027-05-08'),
(206, 14, 'Aquades (100ml lar. saline)', 1, 0, 'botol', '2027-05-08'),
(207, 14, 'Povidon Iodin (60 ml)', 1, 0, 'botol', '2027-05-08'),
(208, 14, 'Alkohol 70%', 1, 0, 'botol', '2027-05-08'),
(209, 14, 'Buku panduan P3K di tempat kerja', 1, 0, 'pcs', '2027-05-08'),
(210, 14, 'Buku catatan', 1, 0, 'pcs', '2027-05-08'),
(211, 15, 'Kasa steril terbungkus', 1, 0, 'box', '2027-05-08'),
(212, 15, 'Perban (lebar 5 cm)', 1, 0, 'roll', '2027-05-08'),
(213, 15, 'Perban (lebar 10 cm)', 1, 0, 'roll', '2027-05-08'),
(214, 15, 'Plester (lebar 1,25 cm)', 1, 0, 'roll', '2027-05-08'),
(215, 15, 'Plester cepat', 1, 0, 'box', '2027-05-08'),
(216, 15, 'Kapas 25 gr', 1, 0, 'bungkus', '2027-05-08'),
(217, 15, 'Kain segitiga (mitella)', 1, 0, 'pcs', '2027-05-08'),
(218, 15, 'Gunting', 1, 0, 'pcs', '2027-05-08'),
(219, 15, 'Peniti', 1, 0, 'set', '2027-05-08'),
(220, 15, 'Sarung tangan sekali pakai', 1, 0, 'pasang', '2027-05-08'),
(221, 15, 'Masker', 1, 0, 'box', '2027-05-08'),
(222, 15, 'Pinset', 1, 0, 'pcs', '2027-05-08'),
(223, 15, 'Lampu senter', 1, 0, 'pcs', '2027-05-08'),
(224, 15, 'Gelas cuci mata', 1, 0, 'pcs', '2027-05-08'),
(225, 15, 'Kantong plastik bersih', 1, 0, 'pack', '2027-05-08'),
(226, 15, 'Aquades (100ml lar. saline)', 1, 0, 'botol', '2027-05-08'),
(227, 15, 'Povidon Iodin (60 ml)', 1, 0, 'botol', '2027-05-08'),
(228, 15, 'Alkohol 70%', 1, 0, 'botol', '2027-05-08'),
(229, 15, 'Buku panduan P3K di tempat kerja', 1, 0, 'pcs', '2027-05-08'),
(230, 15, 'Buku catatan', 1, 0, 'pcs', '2027-05-08'),
(231, 16, 'Kasa steril terbungkus', 1, 0, 'box', '2027-05-08'),
(232, 16, 'Perban (lebar 5 cm)', 1, 0, 'roll', '2027-05-08'),
(233, 16, 'Perban (lebar 10 cm)', 1, 0, 'roll', '2027-05-08'),
(234, 16, 'Plester (lebar 1,25 cm)', 1, 0, 'roll', '2027-05-08'),
(235, 16, 'Plester cepat', 1, 0, 'box', '2027-05-08'),
(236, 16, 'Kapas 25 gr', 1, 0, 'bungkus', '2027-05-08'),
(237, 16, 'Kain segitiga (mitella)', 1, 0, 'pcs', '2027-05-08'),
(238, 16, 'Gunting', 1, 0, 'pcs', '2027-05-08'),
(239, 16, 'Peniti', 1, 0, 'set', '2027-05-08'),
(240, 16, 'Sarung tangan sekali pakai', 1, 0, 'pasang', '2027-05-08'),
(241, 16, 'Masker', 1, 0, 'box', '2027-05-08'),
(242, 16, 'Pinset', 1, 0, 'pcs', '2027-05-08'),
(243, 16, 'Lampu senter', 1, 0, 'pcs', '2027-05-08'),
(244, 16, 'Gelas cuci mata', 1, 0, 'pcs', '2027-05-08'),
(245, 16, 'Kantong plastik bersih', 1, 0, 'pack', '2027-05-08'),
(246, 16, 'Aquades (100ml lar. saline)', 1, 0, 'botol', '2027-05-08'),
(247, 16, 'Povidon Iodin (60 ml)', 1, 0, 'botol', '2027-05-08'),
(248, 16, 'Alkohol 70%', 1, 0, 'botol', '2027-05-08'),
(249, 16, 'Buku panduan P3K di tempat kerja', 1, 0, 'pcs', '2027-05-08'),
(250, 16, 'Buku catatan', 1, 0, 'pcs', '2027-05-08'),
(251, 17, 'Kasa steril terbungkus', 1, 0, 'box', '2027-05-08'),
(252, 17, 'Perban (lebar 5 cm)', 1, 0, 'roll', '2027-05-08'),
(253, 17, 'Perban (lebar 10 cm)', 1, 0, 'roll', '2027-05-08'),
(254, 17, 'Plester (lebar 1,25 cm)', 1, 0, 'roll', '2027-05-08'),
(255, 17, 'Plester cepat', 1, 0, 'box', '2027-05-08'),
(256, 17, 'Kapas 25 gr', 1, 0, 'bungkus', '2027-05-08'),
(257, 17, 'Kain segitiga (mitella)', 1, 0, 'pcs', '2027-05-08'),
(258, 17, 'Gunting', 1, 0, 'pcs', '2027-05-08'),
(259, 17, 'Peniti', 1, 0, 'set', '2027-05-08'),
(260, 17, 'Sarung tangan sekali pakai', 1, 0, 'pasang', '2027-05-08'),
(261, 17, 'Masker', 1, 0, 'box', '2027-05-08'),
(262, 17, 'Pinset', 1, 0, 'pcs', '2027-05-08'),
(263, 17, 'Lampu senter', 1, 0, 'pcs', '2027-05-08'),
(264, 17, 'Gelas cuci mata', 1, 0, 'pcs', '2027-05-08'),
(265, 17, 'Kantong plastik bersih', 1, 0, 'pack', '2027-05-08'),
(266, 17, 'Aquades (100ml lar. saline)', 1, 0, 'botol', '2027-05-08'),
(267, 17, 'Povidon Iodin (60 ml)', 1, 0, 'botol', '2027-05-08'),
(268, 17, 'Alkohol 70%', 1, 0, 'botol', '2027-05-08'),
(269, 17, 'Buku panduan P3K di tempat kerja', 1, 0, 'pcs', '2027-05-08'),
(270, 17, 'Buku catatan', 1, 0, 'pcs', '2027-05-08'),
(271, 18, 'Kasa steril terbungkus', 1, 0, 'box', '2027-05-08'),
(272, 18, 'Perban (lebar 5 cm)', 1, 0, 'roll', '2027-05-08'),
(273, 18, 'Perban (lebar 10 cm)', 1, 0, 'roll', '2027-05-08'),
(274, 18, 'Plester (lebar 1,25 cm)', 1, 0, 'roll', '2027-05-08'),
(275, 18, 'Plester cepat', 1, 0, 'box', '2027-05-08'),
(276, 18, 'Kapas 25 gr', 1, 0, 'bungkus', '2027-05-08'),
(277, 18, 'Kain segitiga (mitella)', 1, 0, 'pcs', '2027-05-08'),
(278, 18, 'Gunting', 1, 0, 'pcs', '2027-05-08'),
(279, 18, 'Peniti', 1, 0, 'set', '2027-05-08'),
(280, 18, 'Sarung tangan sekali pakai', 1, 0, 'pasang', '2027-05-08'),
(281, 18, 'Masker', 1, 0, 'box', '2027-05-08'),
(282, 18, 'Pinset', 1, 0, 'pcs', '2027-05-08'),
(283, 18, 'Lampu senter', 1, 0, 'pcs', '2027-05-08'),
(284, 18, 'Gelas cuci mata', 1, 0, 'pcs', '2027-05-08'),
(285, 18, 'Kantong plastik bersih', 1, 0, 'pack', '2027-05-08'),
(286, 18, 'Aquades (100ml lar. saline)', 1, 0, 'botol', '2027-05-08'),
(287, 18, 'Povidon Iodin (60 ml)', 1, 0, 'botol', '2027-05-08'),
(288, 18, 'Alkohol 70%', 1, 0, 'botol', '2027-05-08'),
(289, 18, 'Buku panduan P3K di tempat kerja', 1, 0, 'pcs', '2027-05-08'),
(290, 18, 'Buku catatan', 1, 0, 'pcs', '2027-05-08'),
(291, 19, 'Kasa steril terbungkus', 1, 0, 'box', '2027-05-08'),
(292, 19, 'Perban (lebar 5 cm)', 1, 0, 'roll', '2027-05-08'),
(293, 19, 'Perban (lebar 10 cm)', 1, 0, 'roll', '2027-05-08'),
(294, 19, 'Plester (lebar 1,25 cm)', 1, 0, 'roll', '2027-05-08'),
(295, 19, 'Plester cepat', 1, 0, 'box', '2027-05-08'),
(296, 19, 'Kapas 25 gr', 1, 0, 'bungkus', '2027-05-08'),
(297, 19, 'Kain segitiga (mitella)', 1, 0, 'pcs', '2027-05-08'),
(298, 19, 'Gunting', 1, 0, 'pcs', '2027-05-08'),
(299, 19, 'Peniti', 1, 0, 'set', '2027-05-08'),
(300, 19, 'Sarung tangan sekali pakai', 1, 0, 'pasang', '2027-05-08'),
(301, 19, 'Masker', 1, 0, 'box', '2027-05-08'),
(302, 19, 'Pinset', 1, 0, 'pcs', '2027-05-08'),
(303, 19, 'Lampu senter', 1, 0, 'pcs', '2027-05-08'),
(304, 19, 'Gelas cuci mata', 1, 0, 'pcs', '2027-05-08'),
(305, 19, 'Kantong plastik bersih', 1, 0, 'pack', '2027-05-08'),
(306, 19, 'Aquades (100ml lar. saline)', 1, 0, 'botol', '2027-05-08'),
(307, 19, 'Povidon Iodin (60 ml)', 1, 0, 'botol', '2027-05-08'),
(308, 19, 'Alkohol 70%', 1, 0, 'botol', '2027-05-08'),
(309, 19, 'Buku panduan P3K di tempat kerja', 1, 0, 'pcs', '2027-05-08'),
(310, 19, 'Buku catatan', 1, 0, 'pcs', '2027-05-08'),
(311, 20, 'Kasa steril terbungkus', 1, 0, 'box', '2027-05-08'),
(312, 20, 'Perban (lebar 5 cm)', 1, 0, 'roll', '2027-05-08'),
(313, 20, 'Perban (lebar 10 cm)', 1, 0, 'roll', '2027-05-08'),
(314, 20, 'Plester (lebar 1,25 cm)', 1, 0, 'roll', '2027-05-08'),
(315, 20, 'Plester cepat', 1, 0, 'box', '2027-05-08'),
(316, 20, 'Kapas 25 gr', 1, 0, 'bungkus', '2027-05-08'),
(317, 20, 'Kain segitiga (mitella)', 1, 0, 'pcs', '2027-05-08'),
(318, 20, 'Gunting', 1, 0, 'pcs', '2027-05-08'),
(319, 20, 'Peniti', 1, 0, 'set', '2027-05-08'),
(320, 20, 'Sarung tangan sekali pakai', 1, 0, 'pasang', '2027-05-08'),
(321, 20, 'Masker', 1, 0, 'box', '2027-05-08'),
(322, 20, 'Pinset', 1, 0, 'pcs', '2027-05-08'),
(323, 20, 'Lampu senter', 1, 0, 'pcs', '2027-05-08'),
(324, 20, 'Gelas cuci mata', 1, 0, 'pcs', '2027-05-08'),
(325, 20, 'Kantong plastik bersih', 1, 0, 'pack', '2027-05-08'),
(326, 20, 'Aquades (100ml lar. saline)', 1, 0, 'botol', '2027-05-08'),
(327, 20, 'Povidon Iodin (60 ml)', 1, 0, 'botol', '2027-05-08'),
(328, 20, 'Alkohol 70%', 1, 0, 'botol', '2027-05-08'),
(329, 20, 'Buku panduan P3K di tempat kerja', 1, 0, 'pcs', '2027-05-08'),
(330, 20, 'Buku catatan', 1, 0, 'pcs', '2027-05-08'),
(331, 21, 'Kasa steril terbungkus', 1, 0, 'box', '2027-05-08'),
(332, 21, 'Perban (lebar 5 cm)', 1, 0, 'roll', '2027-05-08'),
(333, 21, 'Perban (lebar 10 cm)', 1, 0, 'roll', '2027-05-08'),
(334, 21, 'Plester (lebar 1,25 cm)', 1, 0, 'roll', '2027-05-08'),
(335, 21, 'Plester cepat', 1, 0, 'box', '2027-05-08'),
(336, 21, 'Kapas 25 gr', 1, 0, 'bungkus', '2027-05-08'),
(337, 21, 'Kain segitiga (mitella)', 1, 0, 'pcs', '2027-05-08'),
(338, 21, 'Gunting', 1, 0, 'pcs', '2027-05-08'),
(339, 21, 'Peniti', 1, 0, 'set', '2027-05-08'),
(340, 21, 'Sarung tangan sekali pakai', 1, 0, 'pasang', '2027-05-08'),
(341, 21, 'Masker', 1, 0, 'box', '2027-05-08'),
(342, 21, 'Pinset', 1, 0, 'pcs', '2027-05-08'),
(343, 21, 'Lampu senter', 1, 0, 'pcs', '2027-05-08'),
(344, 21, 'Gelas cuci mata', 1, 0, 'pcs', '2027-05-08'),
(345, 21, 'Kantong plastik bersih', 1, 0, 'pack', '2027-05-08'),
(346, 21, 'Aquades (100ml lar. saline)', 1, 0, 'botol', '2027-05-08'),
(347, 21, 'Povidon Iodin (60 ml)', 1, 0, 'botol', '2027-05-08'),
(348, 21, 'Alkohol 70%', 1, 0, 'botol', '2027-05-08'),
(349, 21, 'Buku panduan P3K di tempat kerja', 1, 0, 'pcs', '2027-05-08'),
(350, 21, 'Buku catatan', 1, 0, 'pcs', '2027-05-08'),
(351, 22, 'Kasa steril terbungkus', 1, 0, 'box', '2027-05-08'),
(352, 22, 'Perban (lebar 5 cm)', 1, 0, 'roll', '2027-05-08'),
(353, 22, 'Perban (lebar 10 cm)', 1, 0, 'roll', '2027-05-08'),
(354, 22, 'Plester (lebar 1,25 cm)', 1, 0, 'roll', '2027-05-08'),
(355, 22, 'Plester cepat', 1, 0, 'box', '2027-05-08'),
(356, 22, 'Kapas 25 gr', 1, 0, 'bungkus', '2027-05-08'),
(357, 22, 'Kain segitiga (mitella)', 1, 0, 'pcs', '2027-05-08'),
(358, 22, 'Gunting', 1, 0, 'pcs', '2027-05-08'),
(359, 22, 'Peniti', 1, 0, 'set', '2027-05-08'),
(360, 22, 'Sarung tangan sekali pakai', 1, 0, 'pasang', '2027-05-08'),
(361, 22, 'Masker', 1, 0, 'box', '2027-05-08'),
(362, 22, 'Pinset', 1, 0, 'pcs', '2027-05-08'),
(363, 22, 'Lampu senter', 1, 0, 'pcs', '2027-05-08'),
(364, 22, 'Gelas cuci mata', 1, 0, 'pcs', '2027-05-08'),
(365, 22, 'Kantong plastik bersih', 1, 0, 'pack', '2027-05-08'),
(366, 22, 'Aquades (100ml lar. saline)', 1, 0, 'botol', '2027-05-08'),
(367, 22, 'Povidon Iodin (60 ml)', 1, 0, 'botol', '2027-05-08'),
(368, 22, 'Alkohol 70%', 1, 0, 'botol', '2027-05-08'),
(369, 22, 'Buku panduan P3K di tempat kerja', 1, 0, 'pcs', '2027-05-08'),
(370, 22, 'Buku catatan', 1, 0, 'pcs', '2027-05-08'),
(371, 23, 'Kasa steril terbungkus', 1, 0, 'box', '2027-05-08'),
(372, 23, 'Perban (lebar 5 cm)', 1, 0, 'roll', '2027-05-08'),
(373, 23, 'Perban (lebar 10 cm)', 1, 0, 'roll', '2027-05-08'),
(374, 23, 'Plester (lebar 1,25 cm)', 1, 0, 'roll', '2027-05-08'),
(375, 23, 'Plester cepat', 1, 0, 'box', '2027-05-08'),
(376, 23, 'Kapas 25 gr', 1, 0, 'bungkus', '2027-05-08'),
(377, 23, 'Kain segitiga (mitella)', 1, 0, 'pcs', '2027-05-08'),
(378, 23, 'Gunting', 1, 0, 'pcs', '2027-05-08'),
(379, 23, 'Peniti', 1, 0, 'set', '2027-05-08'),
(380, 23, 'Sarung tangan sekali pakai', 1, 0, 'pasang', '2027-05-08'),
(381, 23, 'Masker', 1, 0, 'box', '2027-05-08'),
(382, 23, 'Pinset', 1, 0, 'pcs', '2027-05-08'),
(383, 23, 'Lampu senter', 1, 0, 'pcs', '2027-05-08'),
(384, 23, 'Gelas cuci mata', 1, 0, 'pcs', '2027-05-08'),
(385, 23, 'Kantong plastik bersih', 1, 0, 'pack', '2027-05-08'),
(386, 23, 'Aquades (100ml lar. saline)', 1, 0, 'botol', '2027-05-08'),
(387, 23, 'Povidon Iodin (60 ml)', 1, 0, 'botol', '2027-05-08'),
(388, 23, 'Alkohol 70%', 1, 0, 'botol', '2027-05-08'),
(389, 23, 'Buku panduan P3K di tempat kerja', 1, 0, 'pcs', '2027-05-08'),
(390, 23, 'Buku catatan', 1, 0, 'pcs', '2027-05-08'),
(391, 24, 'Kasa steril terbungkus', 1, 0, 'box', '2027-05-08'),
(392, 24, 'Perban (lebar 5 cm)', 1, 0, 'roll', '2027-05-08'),
(393, 24, 'Perban (lebar 10 cm)', 1, 0, 'roll', '2027-05-08'),
(394, 24, 'Plester (lebar 1,25 cm)', 1, 0, 'roll', '2027-05-08'),
(395, 24, 'Plester cepat', 1, 0, 'box', '2027-05-08'),
(396, 24, 'Kapas 25 gr', 1, 0, 'bungkus', '2027-05-08'),
(397, 24, 'Kain segitiga (mitella)', 1, 0, 'pcs', '2027-05-08'),
(398, 24, 'Gunting', 1, 0, 'pcs', '2027-05-08'),
(399, 24, 'Peniti', 1, 0, 'set', '2027-05-08'),
(400, 24, 'Sarung tangan sekali pakai', 1, 0, 'pasang', '2027-05-08'),
(401, 24, 'Masker', 1, 0, 'box', '2027-05-08'),
(402, 24, 'Pinset', 1, 0, 'pcs', '2027-05-08'),
(403, 24, 'Lampu senter', 1, 0, 'pcs', '2027-05-08'),
(404, 24, 'Gelas cuci mata', 1, 0, 'pcs', '2027-05-08'),
(405, 24, 'Kantong plastik bersih', 1, 0, 'pack', '2027-05-08'),
(406, 24, 'Aquades (100ml lar. saline)', 1, 0, 'botol', '2027-05-08'),
(407, 24, 'Povidon Iodin (60 ml)', 1, 0, 'botol', '2027-05-08'),
(408, 24, 'Alkohol 70%', 1, 0, 'botol', '2027-05-08'),
(409, 24, 'Buku panduan P3K di tempat kerja', 1, 0, 'pcs', '2027-05-08'),
(410, 24, 'Buku catatan', 1, 0, 'pcs', '2027-05-08'),
(411, 25, 'Kasa steril terbungkus', 1, 0, 'box', '2027-05-08'),
(412, 25, 'Perban (lebar 5 cm)', 1, 0, 'roll', '2027-05-08'),
(413, 25, 'Perban (lebar 10 cm)', 1, 0, 'roll', '2027-05-08'),
(414, 25, 'Plester (lebar 1,25 cm)', 1, 0, 'roll', '2027-05-08'),
(415, 25, 'Plester cepat', 1, 0, 'box', '2027-05-08'),
(416, 25, 'Kapas 25 gr', 1, 0, 'bungkus', '2027-05-08'),
(417, 25, 'Kain segitiga (mitella)', 1, 0, 'pcs', '2027-05-08'),
(418, 25, 'Gunting', 1, 0, 'pcs', '2027-05-08'),
(419, 25, 'Peniti', 1, 0, 'set', '2027-05-08'),
(420, 25, 'Sarung tangan sekali pakai', 1, 0, 'pasang', '2027-05-08'),
(421, 25, 'Masker', 1, 0, 'box', '2027-05-08'),
(422, 25, 'Pinset', 1, 0, 'pcs', '2027-05-08'),
(423, 25, 'Lampu senter', 1, 0, 'pcs', '2027-05-08'),
(424, 25, 'Gelas cuci mata', 1, 0, 'pcs', '2027-05-08'),
(425, 25, 'Kantong plastik bersih', 1, 0, 'pack', '2027-05-08'),
(426, 25, 'Aquades (100ml lar. saline)', 1, 0, 'botol', '2027-05-08'),
(427, 25, 'Povidon Iodin (60 ml)', 1, 0, 'botol', '2027-05-08'),
(428, 25, 'Alkohol 70%', 1, 0, 'botol', '2027-05-08'),
(429, 25, 'Buku panduan P3K di tempat kerja', 1, 0, 'pcs', '2027-05-08'),
(430, 25, 'Buku catatan', 1, 0, 'pcs', '2027-05-08'),
(431, 26, 'Kasa steril terbungkus', 1, 0, 'box', '2027-05-08'),
(432, 26, 'Perban (lebar 5 cm)', 1, 0, 'roll', '2027-05-08'),
(433, 26, 'Perban (lebar 10 cm)', 1, 0, 'roll', '2027-05-08'),
(434, 26, 'Plester (lebar 1,25 cm)', 1, 0, 'roll', '2027-05-08'),
(435, 26, 'Plester cepat', 1, 0, 'box', '2027-05-08'),
(436, 26, 'Kapas 25 gr', 1, 0, 'bungkus', '2027-05-08'),
(437, 26, 'Kain segitiga (mitella)', 1, 0, 'pcs', '2027-05-08'),
(438, 26, 'Gunting', 1, 0, 'pcs', '2027-05-08'),
(439, 26, 'Peniti', 1, 0, 'set', '2027-05-08'),
(440, 26, 'Sarung tangan sekali pakai', 1, 0, 'pasang', '2027-05-08'),
(441, 26, 'Masker', 1, 0, 'box', '2027-05-08'),
(442, 26, 'Pinset', 1, 0, 'pcs', '2027-05-08'),
(443, 26, 'Lampu senter', 1, 0, 'pcs', '2027-05-08'),
(444, 26, 'Gelas cuci mata', 1, 0, 'pcs', '2027-05-08'),
(445, 26, 'Kantong plastik bersih', 1, 0, 'pack', '2027-05-08'),
(446, 26, 'Aquades (100ml lar. saline)', 1, 0, 'botol', '2027-05-08'),
(447, 26, 'Povidon Iodin (60 ml)', 1, 0, 'botol', '2027-05-08'),
(448, 26, 'Alkohol 70%', 1, 0, 'botol', '2027-05-08'),
(449, 26, 'Buku panduan P3K di tempat kerja', 1, 0, 'pcs', '2027-05-08'),
(450, 26, 'Buku catatan', 1, 0, 'pcs', '2027-05-08'),
(451, 27, 'Kasa steril terbungkus', 1, 0, 'box', '2027-05-08'),
(452, 27, 'Perban (lebar 5 cm)', 1, 0, 'roll', '2027-05-08'),
(453, 27, 'Perban (lebar 10 cm)', 1, 0, 'roll', '2027-05-08'),
(454, 27, 'Plester (lebar 1,25 cm)', 1, 0, 'roll', '2027-05-08'),
(455, 27, 'Plester cepat', 1, 0, 'box', '2027-05-08'),
(456, 27, 'Kapas 25 gr', 1, 0, 'bungkus', '2027-05-08'),
(457, 27, 'Kain segitiga (mitella)', 1, 0, 'pcs', '2027-05-08'),
(458, 27, 'Gunting', 1, 0, 'pcs', '2027-05-08'),
(459, 27, 'Peniti', 1, 0, 'set', '2027-05-08'),
(460, 27, 'Sarung tangan sekali pakai', 1, 0, 'pasang', '2027-05-08'),
(461, 27, 'Masker', 1, 0, 'box', '2027-05-08'),
(462, 27, 'Pinset', 1, 0, 'pcs', '2027-05-08'),
(463, 27, 'Lampu senter', 1, 0, 'pcs', '2027-05-08'),
(464, 27, 'Gelas cuci mata', 1, 0, 'pcs', '2027-05-08'),
(465, 27, 'Kantong plastik bersih', 1, 0, 'pack', '2027-05-08'),
(466, 27, 'Aquades (100ml lar. saline)', 1, 0, 'botol', '2027-05-08'),
(467, 27, 'Povidon Iodin (60 ml)', 1, 0, 'botol', '2027-05-08'),
(468, 27, 'Alkohol 70%', 1, 0, 'botol', '2027-05-08'),
(469, 27, 'Buku panduan P3K di tempat kerja', 1, 0, 'pcs', '2027-05-08'),
(470, 27, 'Buku catatan', 1, 0, 'pcs', '2027-05-08'),
(471, 28, 'Kasa steril terbungkus', 1, 0, 'box', '2027-05-08'),
(472, 28, 'Perban (lebar 5 cm)', 1, 0, 'roll', '2027-05-08'),
(473, 28, 'Perban (lebar 10 cm)', 1, 0, 'roll', '2027-05-08'),
(474, 28, 'Plester (lebar 1,25 cm)', 1, 0, 'roll', '2027-05-08'),
(475, 28, 'Plester cepat', 1, 0, 'box', '2027-05-08'),
(476, 28, 'Kapas 25 gr', 1, 0, 'bungkus', '2027-05-08'),
(477, 28, 'Kain segitiga (mitella)', 1, 0, 'pcs', '2027-05-08'),
(478, 28, 'Gunting', 1, 0, 'pcs', '2027-05-08'),
(479, 28, 'Peniti', 1, 0, 'set', '2027-05-08'),
(480, 28, 'Sarung tangan sekali pakai', 1, 0, 'pasang', '2027-05-08'),
(481, 28, 'Masker', 1, 0, 'box', '2027-05-08'),
(482, 28, 'Pinset', 1, 0, 'pcs', '2027-05-08'),
(483, 28, 'Lampu senter', 1, 0, 'pcs', '2027-05-08'),
(484, 28, 'Gelas cuci mata', 1, 0, 'pcs', '2027-05-08'),
(485, 28, 'Kantong plastik bersih', 1, 0, 'pack', '2027-05-08'),
(486, 28, 'Aquades (100ml lar. saline)', 1, 0, 'botol', '2025-06-12'),
(487, 28, 'Povidon Iodin (60 ml)', 1, 0, 'botol', '2027-05-08'),
(488, 28, 'Alkohol 70%', 0, 0, 'botol', '2026-05-05'),
(489, 28, 'Buku panduan P3K di tempat kerja', 1, 0, 'pcs', '2027-05-08'),
(490, 28, 'Buku catatan', 1, 0, 'pcs', '2027-05-08'),
(491, 29, 'Kasa steril terbungkus', 1, 0, 'box', '2027-05-08'),
(492, 29, 'Perban (lebar 5 cm)', 1, 0, 'roll', '2027-05-08'),
(493, 29, 'Perban (lebar 10 cm)', 1, 0, 'roll', '2027-05-08'),
(494, 29, 'Plester (lebar 1,25 cm)', 1, 0, 'roll', '2027-05-08'),
(495, 29, 'Plester cepat', 1, 0, 'box', '2027-05-08'),
(496, 29, 'Kapas 25 gr', 1, 0, 'bungkus', '2027-05-08'),
(497, 29, 'Kain segitiga (mitella)', 1, 0, 'pcs', '2027-05-08'),
(498, 29, 'Gunting', 1, 0, 'pcs', '2027-05-08'),
(499, 29, 'Peniti', 1, 0, 'set', '2027-05-08'),
(500, 29, 'Sarung tangan sekali pakai', 1, 0, 'pasang', '2027-05-08'),
(501, 29, 'Masker', 1, 0, 'box', '2027-05-08'),
(502, 29, 'Pinset', 1, 0, 'pcs', '2027-05-08'),
(503, 29, 'Lampu senter', 1, 0, 'pcs', '2027-05-08'),
(504, 29, 'Gelas cuci mata', 1, 0, 'pcs', '2027-05-08'),
(505, 29, 'Kantong plastik bersih', 1, 0, 'pack', '2027-05-08'),
(506, 29, 'Aquades (100ml lar. saline)', 1, 0, 'botol', '2027-05-08'),
(507, 29, 'Povidon Iodin (60 ml)', 1, 0, 'botol', '2027-05-08'),
(508, 29, 'Alkohol 70%', 1, 0, 'botol', '2027-05-08'),
(509, 29, 'Buku panduan P3K di tempat kerja', 1, 0, 'pcs', '2027-05-08'),
(510, 29, 'Buku catatan', 1, 0, 'pcs', '2027-05-08'),
(511, 30, 'Kasa steril terbungkus', 1, 0, 'box', '2027-05-08'),
(512, 30, 'Perban (lebar 5 cm)', 1, 0, 'roll', '2027-05-08'),
(513, 30, 'Perban (lebar 10 cm)', 1, 0, 'roll', '2027-05-08'),
(514, 30, 'Plester (lebar 1,25 cm)', 1, 0, 'roll', '2027-05-08'),
(515, 30, 'Plester cepat', 1, 0, 'box', '2027-05-08'),
(516, 30, 'Kapas 25 gr', 1, 0, 'bungkus', '2027-05-08'),
(517, 30, 'Kain segitiga (mitella)', 1, 0, 'pcs', '2027-05-08'),
(518, 30, 'Gunting', 1, 0, 'pcs', '2027-05-08'),
(519, 30, 'Peniti', 1, 0, 'set', '2027-05-08'),
(520, 30, 'Sarung tangan sekali pakai', 1, 0, 'pasang', '2027-05-08'),
(521, 30, 'Masker', 1, 0, 'box', '2027-05-08'),
(522, 30, 'Pinset', 1, 0, 'pcs', '2027-05-08'),
(523, 30, 'Lampu senter', 1, 0, 'pcs', '2027-05-08'),
(524, 30, 'Gelas cuci mata', 1, 0, 'pcs', '2027-05-08'),
(525, 30, 'Kantong plastik bersih', 1, 0, 'pack', '2027-05-08'),
(526, 30, 'Aquades (100ml lar. saline)', 1, 0, 'botol', '2027-05-08'),
(527, 30, 'Povidon Iodin (60 ml)', 1, 0, 'botol', '2027-05-08'),
(528, 30, 'Alkohol 70%', 1, 0, 'botol', '2027-05-08'),
(529, 30, 'Buku panduan P3K di tempat kerja', 1, 0, 'pcs', '2027-05-08'),
(530, 30, 'Buku catatan', 1, 0, 'pcs', '2027-05-08');

-- --------------------------------------------------------

--
-- Table structure for table `p3k_lokasi`
--

CREATE TABLE `p3k_lokasi` (
  `id` int(11) NOT NULL,
  `nama_lokasi` varchar(100) NOT NULL,
  `last_update` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `pic` varchar(100) DEFAULT 'Admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `p3k_lokasi`
--

INSERT INTO `p3k_lokasi` (`id`, `nama_lokasi`, `last_update`, `pic`) VALUES
(5, 'Kotak P3K Gedung Utama Lantai 1', '2026-05-08 15:03:33', 'maesaroh'),
(6, 'Kotak P3K Gedung Laboratorium Lantai 1', '2026-05-08 15:03:33', 'loka'),
(7, 'Kotak P3K Gudang Barang Jadi A3', '2026-05-08 15:03:33', 'nizar'),
(8, 'Kotak P3K Gudang RMT B1', '2026-05-08 15:03:33', 'aseptsauri'),
(9, 'Kotak P3K CF B2', '2026-05-08 15:03:33', 'atmajaya'),
(10, 'Kotak P3K Mini Lab B2', '2026-05-08 15:03:33', 'loka'),
(11, 'Kotak P3K Kantor Maintenance Gedung B3', '2026-05-08 15:03:33', 'dianti'),
(12, 'Kotak P3K Insekfungi B4', '2026-05-08 15:03:33', 'mardi_suhalimah'),
(13, 'Kotak P3K Ruang Maintenance Gedung B5', '2026-05-08 15:03:33', 'novadi'),
(14, 'Kotak P3K Gudang RMT C1', '2026-05-08 15:03:33', 'ulfa'),
(15, 'Kotak P3K Produksi Mulsa C2', '2026-05-08 15:03:33', 'evi'),
(16, 'Kotak P3K Produksi Botol D2', '2026-05-08 15:03:33', 'ajirobin'),
(17, 'Kotak P3K Maintenance Plastik D2', '2026-05-08 15:03:33', 'aminudin'),
(18, 'Kotak P3K Ruang Officer Gudang Botol D4', '2026-05-08 15:03:33', 'novi'),
(19, 'Kotak P3K Ruang Workshop Utility', '2026-05-08 15:03:33', 'arifs'),
(20, 'Kotak P3K Ruang Kepala Sift Filling E1', '2026-05-08 15:03:33', 'dediw'),
(21, 'Kotak P3K Produksi Reaktor C13 E3', '2026-05-08 15:03:33', 'ayu'),
(22, 'Kotak P3K Gudang RMT F2', '2026-05-08 15:03:33', 'dewo'),
(23, 'Kotak P3K Gudang RMT F3', '2026-05-08 15:03:33', 'ilyas'),
(24, 'Kotak P3K Gudang Barang Jadi F5', '2026-05-08 15:03:33', 'arismangsur'),
(25, 'Kotak P3K Gudang Barang Jadi Mulsa G1', '2026-05-08 15:03:33', 'sahrudin'),
(26, 'Kotak P3K TPS B3 Gedung G2', '2026-05-08 15:03:33', 'fuad'),
(27, 'Kotak P3K Produksi Assembling Gedung H2', '2026-05-08 15:03:33', 'indra_ardo'),
(28, 'Kotak P3K Gudang BJ Sprayer H3', '2026-05-08 15:03:33', 'asgun'),
(29, 'Kotak P3K Gudang RMT Sparepart I3', '2026-05-08 15:03:33', 'ginta'),
(30, 'Kotak P3K Gudang BJ Paraquat Gedung J', '2026-05-08 15:03:33', 'endig');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `pass` varchar(100) NOT NULL,
  `nama` varchar(200) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `status` enum('Aktif','Non-Aktif') DEFAULT 'Aktif',
  `role` enum('Admin','User') DEFAULT 'User',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `pass`, `nama`, `email`, `status`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$8EpPc/5alVra3MLDBBQIwefegn3I0NYfaavtZfHz/INEqjo.OLEz6', 'Administrator', 'admin@example.com', 'Aktif', 'Admin', '2026-05-04 10:28:55'),
(5, 'maesaroh', 'maesaroh', 'Maesaroh', NULL, 'Aktif', 'User', '2026-05-08 15:03:33'),
(6, 'loka', 'loka', 'Loka', NULL, 'Aktif', 'User', '2026-05-08 15:03:33'),
(7, 'nizar', 'nizar', 'Nizar', NULL, 'Aktif', 'User', '2026-05-08 15:03:33'),
(8, 'aseptsauri', 'aseptsauri', 'Asep Tsauri', NULL, 'Aktif', 'User', '2026-05-08 15:03:33'),
(9, 'atmajaya', 'atmajaya', 'Atmajaya', NULL, 'Aktif', 'User', '2026-05-08 15:03:33'),
(10, 'dianti', 'dianti', 'Dianti', NULL, 'Aktif', 'User', '2026-05-08 15:03:33'),
(11, 'mardi_suhalimah', 'mardi_suhalimah', 'Mardi & Suhalimah', NULL, 'Aktif', 'User', '2026-05-08 15:03:33'),
(12, 'novadi', 'novadi', 'Novadi', NULL, 'Aktif', 'User', '2026-05-08 15:03:33'),
(13, 'ulfa', 'ulfa', 'Ulfa', NULL, 'Aktif', 'User', '2026-05-08 15:03:33'),
(14, 'evi', 'evi', 'Evi', NULL, 'Aktif', 'User', '2026-05-08 15:03:33'),
(15, 'ajirobin', 'ajirobin', 'Aji Robin', NULL, 'Aktif', 'User', '2026-05-08 15:03:33'),
(16, 'aminudin', 'aminudin', 'Aminudin', NULL, 'Aktif', 'User', '2026-05-08 15:03:33'),
(17, 'novi', 'novi', 'Novi', NULL, 'Aktif', 'User', '2026-05-08 15:03:33'),
(18, 'arifs', 'arifs', 'Arif S.', NULL, 'Aktif', 'User', '2026-05-08 15:03:33'),
(19, 'dediw', 'dediw', 'Dedi W.', NULL, 'Aktif', 'User', '2026-05-08 15:03:33'),
(20, 'ayu', 'ayu', 'Ayu', NULL, 'Aktif', 'User', '2026-05-08 15:03:33'),
(21, 'dewo', 'dewo', 'Dewo', NULL, 'Aktif', 'User', '2026-05-08 15:03:33'),
(22, 'ilyas', 'ilyas', 'Ilyas', NULL, 'Aktif', 'User', '2026-05-08 15:03:33'),
(23, 'arismangsur', 'arismangsur', 'Aris Mangsur', NULL, 'Aktif', 'User', '2026-05-08 15:03:33'),
(24, 'sahrudin', 'sahrudin', 'Sahrudin', NULL, 'Aktif', 'User', '2026-05-08 15:03:33'),
(25, 'fuad', 'fuad', 'Fuad', NULL, 'Aktif', 'User', '2026-05-08 15:03:33'),
(26, 'indra_ardo', 'indra_ardo', 'Indra & Ardo', NULL, 'Aktif', 'User', '2026-05-08 15:03:33'),
(27, 'asgun', 'asgun', 'Asgun', NULL, 'Aktif', 'User', '2026-05-08 15:03:33'),
(28, 'ginta', 'ginta', 'Ginta', NULL, 'Aktif', 'User', '2026-05-08 15:03:33'),
(29, 'endig', 'endig', 'Endi G.', NULL, 'Aktif', 'User', '2026-05-08 15:03:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `p3k_activity`
--
ALTER TABLE `p3k_activity`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lokasi_id` (`lokasi_id`);

--
-- Indexes for table `p3k_items`
--
ALTER TABLE `p3k_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lokasi_id` (`lokasi_id`);

--
-- Indexes for table `p3k_lokasi`
--
ALTER TABLE `p3k_lokasi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `p3k_activity`
--
ALTER TABLE `p3k_activity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `p3k_items`
--
ALTER TABLE `p3k_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=531;

--
-- AUTO_INCREMENT for table `p3k_lokasi`
--
ALTER TABLE `p3k_lokasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `p3k_activity`
--
ALTER TABLE `p3k_activity`
  ADD CONSTRAINT `p3k_activity_ibfk_1` FOREIGN KEY (`lokasi_id`) REFERENCES `p3k_lokasi` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `p3k_items`
--
ALTER TABLE `p3k_items`
  ADD CONSTRAINT `p3k_items_ibfk_1` FOREIGN KEY (`lokasi_id`) REFERENCES `p3k_lokasi` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
