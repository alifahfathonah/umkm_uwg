-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 01, 2021 at 08:35 PM
-- Server version: 10.3.30-MariaDB-log-cll-lve
-- PHP Version: 7.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `umkm_db`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/`umkm_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;

USE `umkm_db`;

-- --------------------------------------------------------

--
-- Table structure for table `kategori_produk`
--

CREATE TABLE `kategori_produk` (
  `id` int(11) NOT NULL,
  `kategori` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kategori_produk`
--

INSERT INTO `kategori_produk` (`id`, `kategori`) VALUES
(3, 'Kuliner'),
(4, 'Fashion'),
(5, 'Agribisnis'),
(6, 'Otomotif'),
(7, 'Produk Kreatif'),
(8, 'Teknologi Internet'),
(9, 'Kecantikan');

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `telepon` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`id`, `nama`, `alamat`, `telepon`, `keterangan`) VALUES
(3, 'Ibnu Khalim', 'l. Kopral Usman, Sukoharjo, Kec. Klojen, Kota Malang, Jawa Timur 65117', '085713994548', 'Pelanggan'),
(4, 'Raihan Abdu Sani', 'JL Lembah Dieng G1-10, Perum Bumi Mas Dieng Gi, Kalisongo, Kec. Dau, Malang, Jawa Timur 65151', '085713876567', 'Pelanggan'),
(5, 'robbi Keren', 'Margonda Depok', '082151565865', 'Reseller');

-- --------------------------------------------------------

--
-- Table structure for table `pengguna`
--

CREATE TABLE `pengguna` (
  `id` int(11) NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` char(1) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pengguna`
--

INSERT INTO `pengguna` (`id`, `username`, `password`, `nama`, `role`) VALUES
(1, 'admin', '$2y$10$/I7laWi1mlNFxYSv54EUPOH8MuZhmRWxhE.LaddTK9TSmVe.IHP2C', 'Admin', '1'),
(3, 'user', '$2y$10$l8qbyeNfYXOfO6vq/uULtuJZW6S8vNmHdBp0DEwTN6ulMNKScO6By', 'user', '2');

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id` int(11) NOT NULL,
  `barcode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_produk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kategori` int(11) NOT NULL,
  `satuan` int(11) NOT NULL,
  `harga` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stok` int(11) NOT NULL,
  `terjual` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id`, `barcode`, `nama_produk`, `kategori`, `satuan`, `harga`, `stok`, `terjual`) VALUES
(3, '1001', 'Beras', 5, 11, '12000', 9, '2'),
(4, '1002', 'Cabai Keriting Merah', 5, 11, '23000', 66, '1'),
(5, '1003', 'Tomat Lokal', 5, 11, '9800', 42, '2'),
(6, '1004', 'Jeruk', 5, 6, '5000', 15, '3'),
(8, '1006', 'markisa', 5, 6, '3000', 10, '10');

-- --------------------------------------------------------

--
-- Table structure for table `satuan_produk`
--

CREATE TABLE `satuan_produk` (
  `id` int(11) NOT NULL,
  `satuan` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `satuan_produk`
--

INSERT INTO `satuan_produk` (`id`, `satuan`) VALUES
(3, 'Lusin'),
(4, 'Kodi'),
(5, 'Rim'),
(6, 'Buah'),
(7, 'Unit'),
(8, 'Dus'),
(9, 'Lembar'),
(10, 'Keping'),
(11, 'Kg'),
(12, 'Ton'),
(13, 'Karung');

-- --------------------------------------------------------

--
-- Table structure for table `stok_keluar`
--

CREATE TABLE `stok_keluar` (
  `id` int(11) NOT NULL,
  `tanggal` datetime NOT NULL,
  `barcode` int(11) NOT NULL,
  `jumlah` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Keterangan` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stok_keluar`
--

INSERT INTO `stok_keluar` (`id`, `tanggal`, `barcode`, `jumlah`, `Keterangan`) VALUES
(2, '2021-07-12 00:34:41', 3, '2', 'rusak'),
(3, '2021-07-14 11:22:28', 7, '10', 'rusak');

-- --------------------------------------------------------

--
-- Table structure for table `stok_masuk`
--

CREATE TABLE `stok_masuk` (
  `id` int(11) NOT NULL,
  `tanggal` datetime NOT NULL,
  `barcode` int(11) NOT NULL,
  `jumlah` varchar(11) COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stok_masuk`
--

INSERT INTO `stok_masuk` (`id`, `tanggal`, `barcode`, `jumlah`, `keterangan`, `supplier`) VALUES
(4, '2021-07-12 00:34:17', 3, '20', 'penambahan', 4),
(5, '2021-07-12 00:39:43', 4, '50', 'penambahan', 5),
(6, '2021-07-12 00:40:04', 5, '50', 'penambahan', 5),
(7, '2021-07-12 00:46:10', 4, '14', 'penambahan', 5),
(8, '2021-07-12 01:28:48', 4, '5', 'penambahan', 4),
(9, '2021-07-12 02:04:10', 6, '50', 'penambahan', 5),
(10, '2021-07-14 10:43:37', 7, '30', 'penambahan', 5),
(11, '2021-07-14 11:28:39', 8, '30', 'penambahan', 5);

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE `supplier` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telepon` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`id`, `nama`, `alamat`, `telepon`, `keterangan`) VALUES
(4, 'PT Tunas Maju Bersama', 'Jl. Tenaga Baru VI No.4, Blimbing, Kec. Blimbing, Kota Malang, Jawa Timur 65126', '0341474639', 'Supplier'),
(5, 'Istana Sayur', 'Jalan Raya Tlogomas No.93 a RT 5 RW 6, Tlogomas, Kecamatan Lowokwaru, Tlogomas, Kec. Lowokwaru, Kota', '085749559400', 'Supplier');

-- --------------------------------------------------------

--
-- Table structure for table `toko`
--

CREATE TABLE `toko` (
  `id` int(11) NOT NULL,
  `nama` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `toko`
--

INSERT INTO `toko` (`id`, `nama`, `alamat`) VALUES
(1, 'UMKM UWG', 'Jl. Borobudur No.35, Mojolangu, Kec. Lowokwaru, Kota Malang, Jawa Timur 65142');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id` int(11) NOT NULL,
  `tanggal` datetime NOT NULL,
  `barcode` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_bayar` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah_uang` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `diskon` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pelanggan` int(11) DEFAULT NULL,
  `nota` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kasir` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id`, `tanggal`, `barcode`, `qty`, `total_bayar`, `jumlah_uang`, `diskon`, `pelanggan`, `nota`, `kasir`) VALUES
(6, '2021-07-12 00:35:38', '3', '2', '24000', '30000', '2000', 3, '2TGBKMWYJ9RNUWE', 1),
(7, '2021-07-12 00:44:37', '5', '2', '19600', '40000', '', 4, 'Y5M067M6VNYSLYT', 1),
(8, '2021-07-12 00:54:05', '5', '4', '39200', '40000', '', 4, 'ERS1QPCHVNIKIAE', 1),
(9, '2021-07-12 00:55:56', '4', '2', '46000', '50000', '', 3, 'SM18LJ7P3JNZDMY', 1),
(10, '2021-07-15 01:34:36', '3', '2', '24000', '25000', '', 3, '7P9UQ3AC42ABD13', 1),
(11, '2021-07-15 01:43:53', '5', '2', '19600', '20000', '', 3, 'UP4SWRC5AU13F16', 3),
(12, '2021-07-12 02:05:08', '6', '2', '10000', '10000', '', 0, 'O6BOWZHE281IFLC', 1),
(13, '2021-07-13 19:11:12', '3', '3', '36000', '50000', '100', 3, 'B06CHZ4HZQWHA28', 1),
(14, '2021-07-14 10:44:50', '7,6', '30,10', '170000', '200000', '', 4, 'PI4MINEEWZKTGX2', 1),
(15, '2021-07-14 11:31:50', '6,8', '3,10', '45000', '50000', '', 5, 'VXY0R9INLGT5BMP', 1),
(16, '2021-07-14 12:08:51', '8', '10', '30000', '30000', '6000', 5, 'BWV3LVH0HFXGALX', 1),
(17, '2021-07-15 21:09:36', '4', '1', '23000', '100000', '0', 3, '5174WHLVRDP593C', 1),
(18, '2021-07-15 21:12:37', '3', '2', '24000', '2000000', '', 4, 'BDTNFT7F2A2K09J', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kategori_produk`
--
ALTER TABLE `kategori_produk`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `satuan_produk`
--
ALTER TABLE `satuan_produk`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stok_keluar`
--
ALTER TABLE `stok_keluar`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stok_masuk`
--
ALTER TABLE `stok_masuk`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `toko`
--
ALTER TABLE `toko`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kategori_produk`
--
ALTER TABLE `kategori_produk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `satuan_produk`
--
ALTER TABLE `satuan_produk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `stok_keluar`
--
ALTER TABLE `stok_keluar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `stok_masuk`
--
ALTER TABLE `stok_masuk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `toko`
--
ALTER TABLE `toko`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
