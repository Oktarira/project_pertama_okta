-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 18, 2023 at 05:12 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `test_budgetbuddy`
--

-- --------------------------------------------------------

--
-- Table structure for table `anggaran`
--

CREATE TABLE `anggaran` (
  `id_anggaran` int(11) NOT NULL,
  `jumlah_anggaran` int(11) DEFAULT NULL,
  `kategori_id` int(11) DEFAULT NULL,
  `bulan` int(2) NOT NULL,
  `tahun` int(4) NOT NULL,
  `total_anggaran` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `anggaran`
--

INSERT INTO `anggaran` (`id_anggaran`, `jumlah_anggaran`, `kategori_id`, `bulan`, `tahun`, `total_anggaran`, `user_id`) VALUES
(114, 250000, 1, 11, 2023, 250000, 30),
(115, 250000000, 12, 12, 2023, 250000000, 32),
(116, 222000, 1, 12, 2023, 222000, 30),
(118, 350000, 11, 1, 2024, 600000, 38),
(119, 700000, 10, 1, 2024, 700000, 38);

-- --------------------------------------------------------

--
-- Table structure for table `kategori_anggaran`
--

CREATE TABLE `kategori_anggaran` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori_anggaran`
--

INSERT INTO `kategori_anggaran` (`id_kategori`, `nama_kategori`) VALUES
(1, 'Makan dan Minum'),
(2, 'Bahan Makanan'),
(3, 'Belanja'),
(4, 'Bensin'),
(5, 'Transportasi'),
(6, 'Tagihan'),
(7, 'Hiburan'),
(8, 'Edukasi'),
(9, 'Kesehatan'),
(10, 'Kecantikan'),
(11, 'Pakaian'),
(12, 'Liburan'),
(13, 'Olahhraga'),
(14, 'Peliharaan');

-- --------------------------------------------------------

--
-- Table structure for table `pengeluaran`
--

CREATE TABLE `pengeluaran` (
  `id_pengeluaran` int(11) NOT NULL,
  `jumlah_pengeluaran` int(11) DEFAULT NULL,
  `kategori_pengeluaran` varchar(255) DEFAULT NULL,
  `tanggal_pengeluaran` date DEFAULT NULL,
  `user_id` int(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengeluaran`
--

INSERT INTO `pengeluaran` (`id_pengeluaran`, `jumlah_pengeluaran`, `kategori_pengeluaran`, `tanggal_pengeluaran`, `user_id`) VALUES
(77, 21000, '1', '2023-11-09', 30),
(78, 5000000, '12', '2023-12-07', 32),
(79, 1500000, '12', '2023-12-18', 32),
(80, 250000, '11', '2024-01-04', 38);

-- --------------------------------------------------------

--
-- Table structure for table `user_form`
--

CREATE TABLE `user_form` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_form`
--

INSERT INTO `user_form` (`id`, `name`, `email`, `password`) VALUES
(30, 'khabib', 'khabib@gmail.com', '$2y$10$Bs4JW7ObEof/qOhZA/JOkOjMcmNWEzZzG0cAptX2h.W.SnSc7QCTm'),
(32, 'ina', 'ina@gmail.com', '$2y$10$YQhUs74IEciX6bSKOGtIcuNWlq3x2O5HPLThJm/JpY55px2AZhZCS'),
(38, 'nita', 'nita@gmail.com', '$2y$10$IaTl0oj2uBG1j68r4o9upemHUvG8WM1aFgTbB0eE8KlppT4yLxyhi');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `anggaran`
--
ALTER TABLE `anggaran`
  ADD PRIMARY KEY (`id_anggaran`),
  ADD KEY `fk_anggaran_kategori` (`kategori_id`),
  ADD KEY `fk_anggaran_user` (`user_id`);

--
-- Indexes for table `kategori_anggaran`
--
ALTER TABLE `kategori_anggaran`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `pengeluaran`
--
ALTER TABLE `pengeluaran`
  ADD PRIMARY KEY (`id_pengeluaran`),
  ADD KEY `fk_pengeluaran_user` (`user_id`);

--
-- Indexes for table `user_form`
--
ALTER TABLE `user_form`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `anggaran`
--
ALTER TABLE `anggaran`
  MODIFY `id_anggaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;

--
-- AUTO_INCREMENT for table `pengeluaran`
--
ALTER TABLE `pengeluaran`
  MODIFY `id_pengeluaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `user_form`
--
ALTER TABLE `user_form`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `anggaran`
--
ALTER TABLE `anggaran`
  ADD CONSTRAINT `fk_anggaran_kategori` FOREIGN KEY (`kategori_id`) REFERENCES `kategori_anggaran` (`id_kategori`),
  ADD CONSTRAINT `fk_anggaran_user` FOREIGN KEY (`user_id`) REFERENCES `user_form` (`id`);

--
-- Constraints for table `pengeluaran`
--
ALTER TABLE `pengeluaran`
  ADD CONSTRAINT `fk_pengeluaran_user` FOREIGN KEY (`user_id`) REFERENCES `user_form` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
