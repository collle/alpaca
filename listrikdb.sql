-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 13, 2026 at 09:15 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE TABLE `level` (
  `id_level` int(11) NOT NULL,
  `nama_level` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `level` (`id_level`, `nama_level`) VALUES
(1, 'Administrator'),
(2, 'Agen'),
(3, 'Pencatat');

CREATE TABLE `pelanggan` (
  `id_pelanggan` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `nomor_kwh` varchar(20) DEFAULT NULL,
  `nama_pelanggan` varchar(50) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `id_tarif` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `pelanggan` (`id_pelanggan`, `username`, `password`, `nomor_kwh`, `nama_pelanggan`, `alamat`, `id_tarif`) VALUES
(501, 'ichsan', '$2y$10$HhlnNGBdBZz3zDhjkkCFLe/dnqVUMBJHItJeP8iTPBjW0m0gPvfjK', '123456', 'Ichsan Abdul Sany', 'Jl. Daan Mogot KM 15,6', 1),
(502, 'arsakha', '$2y$10$HhlnNGBdBZz3zDhjkkCFLe/dnqVUMBJHItJeP8iTPBjW0m0gPvfjK', '654321', 'Arsakha Fathul Keenandra', 'Jl. Sawah Dalam 1 RT.002 RW.005', 2),
(503, 'arkatama', '$2y$10$HhlnNGBdBZz3zDhjkkCFLe/dnqVUMBJHItJeP8iTPBjW0m0gPvfjK', '135792468', 'Arkatama Zayan Keenandra', 'Jl. Kalianyar RT.004 RW.010', 3);

CREATE TABLE `pembayaran` (
  `id_pembayaran` int(11) NOT NULL,
  `id_tagihan` int(11) DEFAULT NULL,
  `id_pelanggan` int(11) DEFAULT NULL,
  `tanggal_pembayaran` date DEFAULT NULL,
  `bulan_bayar` varchar(20) DEFAULT NULL,
  `biaya_admin` float DEFAULT NULL,
  `total_bayar` float DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `pembayaran` (`id_pembayaran`, `id_tagihan`, `id_pelanggan`, `tanggal_pembayaran`, `bulan_bayar`, `biaya_admin`, `total_bayar`, `id_user`) VALUES
(1, 1, 501, '2026-01-11', 'January', 2500, 70000, 101),
(2, 4, 503, '2026-01-11', 'January', 2500, 152500, 101),
(3, 5, 503, '2026-01-11', 'January', 2500, 38500, 1),
(6, 12, 503, '2026-01-12', 'January', 2500, 32500, 101),
(7, 2, 501, '2026-01-12', 'January', 2500, 137500, 101),
(8, 14, 503, '2026-01-12', 'January', 2500, 47500, 1),
(9, 15, 503, '2026-01-12', 'January', 2500, 47500, 1),
(10, 16, 503, '2026-01-13', 'January', 2500, 79000, 1),
(11, 17, 501, '2026-01-13', 'January', 2500, 130750, 1);


CREATE TABLE `penggunaan` (
  `id_penggunaan` int(11) NOT NULL,
  `id_pelanggan` int(11) DEFAULT NULL,
  `bulan` varchar(20) DEFAULT NULL,
  `tahun` varchar(4) DEFAULT NULL,
  `meter_awal` int(11) DEFAULT NULL,
  `meter_akhir` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `penggunaan` (`id_penggunaan`, `id_pelanggan`, `bulan`, `tahun`, `meter_awal`, `meter_akhir`) VALUES
(1, 501, 'Januari', '2026', 150, 200),
(901, 501, 'Januari', '2026', 100, 150),
(902, 501, 'Januari', '2026', 200, 300),
(903, 502, 'Januari', '2026', 0, 123),
(904, 503, 'Januari', '2025', 0, 100),
(905, 503, 'Februari', '2025', 100, 124),
(906, 502, 'Januari', '2026', 123, 200),
(907, 503, 'Maret', '2025', 124, 200),
(908, 501, 'Januari', '2025', 300, 305),
(909, 503, 'April', '2025', 200, 250),
(910, 503, 'Mei', '2025', 250, 270),
(911, 503, 'Juni', '2025', 270, 300),
(912, 503, 'Juli', '2025', 300, 351),
(913, 501, 'Februari', '2026', 305, 400);

CREATE TABLE `tagihan` (
  `id_tagihan` int(11) NOT NULL,
  `id_penggunaan` int(11) DEFAULT NULL,
  `id_pelanggan` int(11) DEFAULT NULL,
  `bulan` varchar(20) DEFAULT NULL,
  `tahun` varchar(4) DEFAULT NULL,
  `jumlah_meter` int(11) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tagihan` (`id_tagihan`, `id_penggunaan`, `id_pelanggan`, `bulan`, `tahun`, `jumlah_meter`, `status`) VALUES
(1, 0, 501, 'Januari', '2026', 50, 'Lunas'),
(2, 902, 501, 'Januari', '2026', 100, 'Lunas'),
(3, 903, 0, 'Januari', '2026', 123, 'Belum Bayar'),
(4, 904, 503, 'Januari', '2025', 100, 'Lunas'),
(5, 905, 503, 'Februari', '2025', 24, 'Lunas'),
(6, 906, 502, 'Januari', '2026', 77, 'Belum Bayar'),
(7, 907, 503, 'Maret', '2025', 76, 'Lunas'),
(10, 909, 503, 'April', '2025', 50, 'Lunas'),
(11, 909, 503, 'April', '2025', 50, 'Lunas'),
(12, 910, 503, 'Mei', '2025', 20, 'Lunas'),
(13, 910, 503, 'Mei', '2025', 20, 'Lunas'),
(14, 911, 503, 'Juni', '2025', 30, 'Lunas'),
(15, 911, 503, 'Juni', '2025', 30, 'Lunas'),
(16, 912, 503, 'Juli', '2025', 51, 'Lunas'),
(17, 913, 501, 'Februari', '2026', 95, 'Lunas');

CREATE TABLE `tarif` (
  `id_tarif` int(11) NOT NULL,
  `daya` int(11) DEFAULT NULL,
  `tarifperkwh` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tarif` (`id_tarif`, `daya`, `tarifperkwh`) VALUES
(1, 900, 1350),
(2, 1300, 1440),
(3, 2200, 1500);

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `nama_admin` varchar(50) DEFAULT NULL,
  `id_level` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `user` (`id_user`, `username`, `password`, `nama_admin`, `id_level`) VALUES
(0, 'pencatat', '$2y$10$AAOJmvVvrsU2vAMOgwS28OZwhkhYTAYEoYIjG/9m/tsVjs7WmpGRm', 'Petugas Pencatat Meter', 3),
(1, 'agen', '$2y$10$HhlnNGBdBZz3zDhjkkCFLe/dnqVUMBJHItJeP8iTPBjW0m0gPvfjK', 'Mitra Pembayaran', 2),
(101, 'admin', '$2y$10$x5OFhQ6YCCRNMYLXuuzZY.yTb7m2apdZXGzzuPMJhu77DhIg7e20i', 'Administrator', 1);


CREATE TABLE `view_penggunaan_listrik` (
`id_pelanggan` int(11)
,`nama_pelanggan` varchar(50)
,`bulan` varchar(20)
,`tahun` varchar(4)
,`total_meter` bigint(12)
);

DROP TABLE IF EXISTS `view_penggunaan_listrik`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_penggunaan_listrik`  AS SELECT `p`.`id_pelanggan` AS `id_pelanggan`, `p`.`nama_pelanggan` AS `nama_pelanggan`, `u`.`bulan` AS `bulan`, `u`.`tahun` AS `tahun`, `u`.`meter_akhir`- `u`.`meter_awal` AS `total_meter` FROM (`pelanggan` `p` join `penggunaan` `u` on(`p`.`id_pelanggan` = `u`.`id_pelanggan`)) ;

ALTER TABLE `level`
  ADD PRIMARY KEY (`id_level`);


ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id_pelanggan`),
  ADD KEY `id_tarif` (`id_tarif`),
  ADD KEY `idx_pelanggan_nama` (`nama_pelanggan`);


ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `id_tagihan` (`id_tagihan`),
  ADD KEY `id_pelanggan` (`id_pelanggan`),
  ADD KEY `id_user` (`id_user`);


ALTER TABLE `penggunaan`
  ADD PRIMARY KEY (`id_penggunaan`),
  ADD KEY `id_pelanggan` (`id_pelanggan`);


ALTER TABLE `tagihan`
  ADD PRIMARY KEY (`id_tagihan`),
  ADD KEY `id_penggunaan` (`id_penggunaan`),
  ADD KEY `id_pelanggan` (`id_pelanggan`),
  ADD KEY `idx_tagihan_status` (`status`);


ALTER TABLE `tarif`
  ADD PRIMARY KEY (`id_tarif`);


ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD KEY `id_level` (`id_level`);


ALTER TABLE `level`
  MODIFY `id_level` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;


ALTER TABLE `pelanggan`
  MODIFY `id_pelanggan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=504;


ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;


ALTER TABLE `penggunaan`
  MODIFY `id_penggunaan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=914;


ALTER TABLE `tagihan`
  MODIFY `id_tagihan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;


ALTER TABLE `pelanggan`
  ADD CONSTRAINT `pelanggan_ibfk_1` FOREIGN KEY (`id_tarif`) REFERENCES `tarif` (`id_tarif`);


ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`id_tagihan`) REFERENCES `tagihan` (`id_tagihan`),
  ADD CONSTRAINT `pembayaran_ibfk_2` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`),
  ADD CONSTRAINT `pembayaran_ibfk_3` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);


ALTER TABLE `penggunaan`
  ADD CONSTRAINT `penggunaan_ibfk_1` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`);


ALTER TABLE `tagihan`
  ADD CONSTRAINT `tagihan_ibfk_1` FOREIGN KEY (`id_penggunaan`) REFERENCES `penggunaan` (`id_penggunaan`),
  ADD CONSTRAINT `tagihan_ibfk_2` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`);

ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`id_level`) REFERENCES `level` (`id_level`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
