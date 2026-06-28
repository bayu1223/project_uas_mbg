-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 03, 2026 at 02:45 PM
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
-- Database: `db_project_mbg`
--

-- --------------------------------------------------------

--
-- Table structure for table `detail_stok`
--

CREATE TABLE `detail_stok` (
  `id_detail_stok` int(11) NOT NULL,
  `id_stok` int(11) NOT NULL,
  `jenis_transaksi` enum('masuk','keluar') NOT NULL,
  `jumlah` int(11) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `tanggal_transaksi` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_users` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_stok`
--

INSERT INTO `detail_stok` (`id_detail_stok`, `id_stok`, `jenis_transaksi`, `jumlah`, `keterangan`, `tanggal_transaksi`, `id_users`) VALUES
(2, 8, 'keluar', 200, '', '2026-06-03 11:18:54', 1),
(3, 5, 'masuk', 60, '', '2026-06-03 11:19:37', 1);

--
-- Triggers `detail_stok`
--
DELIMITER $$
CREATE TRIGGER `after_detail_stok_insert` AFTER INSERT ON `detail_stok` FOR EACH ROW BEGIN
    IF NEW.jenis_transaksi = 'masuk' THEN
        UPDATE stok_bahan 
        SET jumlah = jumlah + NEW.jumlah,
            tanggal_update = NOW()
        WHERE id_stok = NEW.id_stok;
    ELSEIF NEW.jenis_transaksi = 'keluar' THEN
        -- Validasi pencegahan stok minus di layer DB
        IF (SELECT jumlah FROM stok_bahan WHERE id_stok = NEW.id_stok) < NEW.jumlah THEN
            SIGNAL SQLSTATE '45000' 
            SET MESSAGE_TEXT = 'Kesalahan: Jumlah stok tidak mencukupi untuk transaksi keluar!';
        ELSE
            UPDATE stok_bahan 
            SET jumlah = jumlah - NEW.jumlah,
                tanggal_update = NOW()
            WHERE id_stok = NEW.id_stok;
        END IF;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `distribusi`
--

CREATE TABLE `distribusi` (
  `id_distribusi` int(11) NOT NULL,
  `id_sekolah` int(11) NOT NULL,
  `id_menu` int(11) NOT NULL,
  `tanggal_distribusi` date NOT NULL,
  `jumlah_porsi` int(11) NOT NULL,
  `id_users` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `distribusi`
--

INSERT INTO `distribusi` (`id_distribusi`, `id_sekolah`, `id_menu`, `tanggal_distribusi`, `jumlah_porsi`, `id_users`) VALUES
(3, 4, 1, '2026-06-03', 500, 4);

-- --------------------------------------------------------

--
-- Table structure for table `menu_makanan`
--

CREATE TABLE `menu_makanan` (
  `id_menu` int(11) NOT NULL,
  `nama_menu` varchar(100) NOT NULL,
  `kalori` int(11) NOT NULL,
  `protein` decimal(5,2) NOT NULL,
  `status` enum('aktif','nonaktif') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_makanan`
--

INSERT INTO `menu_makanan` (`id_menu`, `nama_menu`, `kalori`, `protein`, `status`) VALUES
(1, 'Nasi Putih + Ayam Kusus + Jagung Manis', 12, 0.71, 'aktif'),
(2, 'Nasi Putih + Ayam Goreng + Kentang Sambal', 42, 0.71, 'nonaktif'),
(4, 'Nasi Putih + Telur Sambal', 10, 0.19, 'aktif');

-- --------------------------------------------------------

--
-- Table structure for table `sekolah`
--

CREATE TABLE `sekolah` (
  `id_sekolah` int(11) NOT NULL,
  `nama_sekolah` varchar(150) NOT NULL,
  `alamat` text NOT NULL,
  `kepala_sekolah` varchar(100) NOT NULL,
  `jumlah_siswa` int(11) NOT NULL,
  `no_telp` varchar(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sekolah`
--

INSERT INTO `sekolah` (`id_sekolah`, `nama_sekolah`, `alamat`, `kepala_sekolah`, `jumlah_siswa`, `no_telp`) VALUES
(4, 'SDN 01 PEMUDA BANGSA', 'Jln. Sukatani Kec. Awan Putih', 'Hermansyah, S.Pd', 700, '084678567867'),
(5, 'SD Negeri Harapan Bangsa 03', 'Jl. Pemuda Kencana No. 45, Kec. Bogor Timur, Bogor', 'Siti Rahmawati, S.Pd.', 280, '081345678002'),
(6, 'SD Negeri Harapan Bangsa 03', 'Jl. Pemuda Kencana No. 45, Kec. Bogor Timur, Bogor', 'Siti Rahmawati, S.Pd.', 280, '081345678002'),
(7, 'SMP Negeri 1 Cerdas Mulia', 'Jl. Pendidikan Tinggi Blok B/12, Sukabumi', 'Dr. Drs. H. Ahmad Subarjo', 520, '081567890003'),
(8, 'SMA Negeri 2 Unggul Bersama', 'Jl. Raya Khatulistiwa No. 7, Cianjur', 'Irwan Wijaya, M.Si.', 640, '085290123005');

-- --------------------------------------------------------

--
-- Table structure for table `stok_bahan`
--

CREATE TABLE `stok_bahan` (
  `id_stok` int(11) NOT NULL,
  `nama_bahan` varchar(100) NOT NULL,
  `jumlah` int(11) NOT NULL COMMENT 'Total stok real-time saat ini',
  `satuan` varchar(20) NOT NULL,
  `tanggal_update` date NOT NULL,
  `id_supplier` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stok_bahan`
--

INSERT INTO `stok_bahan` (`id_stok`, `nama_bahan`, `jumlah`, `satuan`, `tanggal_update`, `id_supplier`) VALUES
(5, 'Beras Payung', 110, 'kg', '2026-06-03', 3),
(6, 'Telur Ayam Omega', 1200, 'butir', '2026-06-03', 3),
(8, 'Susu Sapi Pasteur', 300, 'kotak', '2026-06-03', 5);

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE `supplier` (
  `id_supplier` int(11) NOT NULL,
  `nama_supplier` varchar(100) NOT NULL,
  `alamat` text NOT NULL,
  `no_telp` varchar(12) NOT NULL,
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`id_supplier`, `nama_supplier`, `alamat`, `no_telp`, `email`) VALUES
(3, 'PT. Pangan Makmur', 'Jln. Anggrek No. 7 Yogyakarta', '986789786789', 'pangan@gmail.com'),
(4, 'CV. Agro Tani Sejahtera', 'Jl. Raya Sawah Hijau No. 45, Cianjur', '081234567890', 'agro@gmail.com'),
(5, 'Koperasi Susu Segar Utama', 'Jl. Peternakan Lembang No. 8, Bandung', '081399887766', 'kssu@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_users` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','petugas') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_users`, `nama`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'Anwari', 'admin1', 'admin123', 'admin', '2026-06-03 11:03:01'),
(4, 'Supratman', 'petugas', '$2y$10$KB.KSGl8kgxKW00XztuAO.k634yF0QBElNarxnWpGUzIaQbpmeley', 'petugas', '2026-06-03 11:01:55'),
(5, 'Syahputra Efendi', 'petugas1', '$2y$10$Bn/9uBOBtgkaRBxetk/CfOuAzZg/nAURHrumUirLElG51Z3a1ESwG', 'petugas', '2026-06-03 11:32:06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `detail_stok`
--
ALTER TABLE `detail_stok`
  ADD PRIMARY KEY (`id_detail_stok`),
  ADD KEY `id_stok` (`id_stok`),
  ADD KEY `id_users` (`id_users`);

--
-- Indexes for table `distribusi`
--
ALTER TABLE `distribusi`
  ADD PRIMARY KEY (`id_distribusi`),
  ADD KEY `id_sekolah` (`id_sekolah`),
  ADD KEY `id_menu` (`id_menu`),
  ADD KEY `id_users` (`id_users`);

--
-- Indexes for table `menu_makanan`
--
ALTER TABLE `menu_makanan`
  ADD PRIMARY KEY (`id_menu`);

--
-- Indexes for table `sekolah`
--
ALTER TABLE `sekolah`
  ADD PRIMARY KEY (`id_sekolah`);

--
-- Indexes for table `stok_bahan`
--
ALTER TABLE `stok_bahan`
  ADD PRIMARY KEY (`id_stok`),
  ADD KEY `id_supplier` (`id_supplier`);

--
-- Indexes for table `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`id_supplier`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_users`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `detail_stok`
--
ALTER TABLE `detail_stok`
  MODIFY `id_detail_stok` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `distribusi`
--
ALTER TABLE `distribusi`
  MODIFY `id_distribusi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `menu_makanan`
--
ALTER TABLE `menu_makanan`
  MODIFY `id_menu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `sekolah`
--
ALTER TABLE `sekolah`
  MODIFY `id_sekolah` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `stok_bahan`
--
ALTER TABLE `stok_bahan`
  MODIFY `id_stok` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `id_supplier` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_users` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_stok`
--
ALTER TABLE `detail_stok`
  ADD CONSTRAINT `detail_stok_ibfk_1` FOREIGN KEY (`id_stok`) REFERENCES `stok_bahan` (`id_stok`) ON DELETE CASCADE,
  ADD CONSTRAINT `detail_stok_ibfk_2` FOREIGN KEY (`id_users`) REFERENCES `users` (`id_users`) ON DELETE CASCADE;

--
-- Constraints for table `distribusi`
--
ALTER TABLE `distribusi`
  ADD CONSTRAINT `distribusi_ibfk_1` FOREIGN KEY (`id_sekolah`) REFERENCES `sekolah` (`id_sekolah`) ON DELETE CASCADE,
  ADD CONSTRAINT `distribusi_ibfk_2` FOREIGN KEY (`id_menu`) REFERENCES `menu_makanan` (`id_menu`) ON DELETE CASCADE,
  ADD CONSTRAINT `distribusi_ibfk_3` FOREIGN KEY (`id_users`) REFERENCES `users` (`id_users`) ON DELETE CASCADE;

--
-- Constraints for table `stok_bahan`
--
ALTER TABLE `stok_bahan`
  ADD CONSTRAINT `stok_bahan_ibfk_1` FOREIGN KEY (`id_supplier`) REFERENCES `supplier` (`id_supplier`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
