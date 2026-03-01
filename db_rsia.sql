-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 01 Mar 2026 pada 15.58
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_rsia`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `accreditations`
--

CREATE TABLE `accreditations` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `achievements`
--

CREATE TABLE `achievements` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `count` int(11) NOT NULL,
  `icon` varchar(50) DEFAULT 'fas fa-hospital'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `achievements`
--

INSERT INTO `achievements` (`id`, `title`, `count`, `icon`) VALUES
(1, 'Rata-rata Kunjungan Rawat Jalan/Tahun', 15000, 'fas fa-user-md'),
(2, 'Rata-rata Kunjungan Rawat Inap/Tahun', 5000, 'fas fa-bed'),
(3, 'Jumlah Tenaga Medis', 120, 'fas fa-users');

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(3, 'admin', '$2y$10$jjCTehU1eEk7zUGk3mvas.z91/h8RgXcxr7Yd6OwSiOretTpjh6q6');

-- --------------------------------------------------------

--
-- Struktur dari tabel `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` longtext DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `content`, `file_path`, `created_at`) VALUES
(1, 'Jam Operasional Libur Tahun Baru', 'Pelayanan rawat jalan akan ditutup sementara pada tanggal 1 Januari.', NULL, '2026-03-01 03:10:03'),
(2, 'Lowongan Dokter Spesialis Anak', 'RSIA Restu Ibu membuka lowongan untuk Dokter Spesialis Anak.', NULL, '2026-03-01 03:10:03');

-- --------------------------------------------------------

--
-- Struktur dari tabel `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`) VALUES
(1, 'Kesehatan Ibu', 'kesehatan-ibu'),
(2, 'Kesehatan Anak', 'kesehatan-anak'),
(3, 'Tips Kesehatan', 'tips-kesehatan'),
(4, 'Kegiatan RS', 'kegiatan-rs');

-- --------------------------------------------------------

--
-- Struktur dari tabel `doctors`
--

CREATE TABLE `doctors` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `specialization` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT 'img/team-1.jpg',
  `bio` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `doctors`
--

INSERT INTO `doctors` (`id`, `name`, `specialization`, `image`, `bio`, `created_at`) VALUES
(1, 'Dr. Andi', 'Spesialis Anak', 'img/team-1.jpg', 'Dokter Anak berpengalaman 10 tahun.', '2026-02-28 03:17:27'),
(2, 'Dr. Budi', 'Spesialis Kandungan', 'img/team-1.jpg', 'Ahli kandungan dan kehamilan.', '2026-02-28 03:17:27');

-- --------------------------------------------------------

--
-- Struktur dari tabel `documents`
--

CREATE TABLE `documents` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `documents`
--

INSERT INTO `documents` (`id`, `title`, `category`, `file_path`, `created_at`) VALUES
(1, 'Akreditasi RSIA 2023', 'Akreditasi', 'akreditas-2023.pdf', '2026-02-28 12:21:35'),
(2, 'SOP Pelayanan Rawat Inap', 'SOP', 'sop-rawat-inap.pdf', '2026-02-28 12:21:35');

-- --------------------------------------------------------

--
-- Struktur dari tabel `insurances`
--

CREATE TABLE `insurances` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `logo` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `insurances`
--

INSERT INTO `insurances` (`id`, `name`, `logo`, `created_at`) VALUES
(1, 'BPJS Kesehatan', 'img/insurance-1.png', '2026-03-01 04:16:51'),
(2, 'Mandiri Inhealth', 'img/insurance-2.png', '2026-03-01 04:16:51');

-- --------------------------------------------------------

--
-- Struktur dari tabel `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` longtext DEFAULT NULL,
  `image` varchar(255) DEFAULT 'img/blog-1.jpg',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `posts`
--

INSERT INTO `posts` (`id`, `title`, `slug`, `content`, `image`, `created_at`, `category_id`) VALUES
(1, 'Pelayanan 24 Jam RSIA Restu Ibu', 'pelayanan-24-jam', 'Kami menyediakan layanan darurat 24 jam...', 'img/blog-1.jpg', '2026-02-28 12:21:35', 1),
(2, 'Tips Kesehatan Ibu Hamil', 'tips-kesehatan-ibu-hamil', 'Panduan lengkap untuk ibu hamil...', 'img/blog-1.jpg', '2026-02-28 12:21:35', 2);

-- --------------------------------------------------------

--
-- Struktur dari tabel `schedules`
--

CREATE TABLE `schedules` (
  `id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `day` varchar(20) NOT NULL,
  `time` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `schedules`
--

INSERT INTO `schedules` (`id`, `doctor_id`, `day`, `time`) VALUES
(1, 1, 'Senin - Kamis', '08:00 - 12:00'),
(2, 2, 'Senin - Jumat', '13:00 - 17:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT 'fas fa-heartbeat',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `services`
--

INSERT INTO `services` (`id`, `title`, `description`, `icon`, `created_at`) VALUES
(1, 'Poliklinik Anak', 'Pelayanan kesehatan tumbuh kembang anak dan imunisasi.', 'fas fa-baby', '2026-02-28 12:35:36'),
(2, 'Poliklinik Kandungan', 'Pemeriksaan kehamilan, persalinan, dan kandungan.', 'fas fa-baby-carriage', '2026-02-28 12:35:36'),
(3, 'Poliklinik Penyakit Dalam', 'Penanganan penyakit dewasa dan lansia.', 'fas fa-stethoscope', '2026-02-28 12:35:36'),
(4, 'Home Visit', 'Layanan kesehatan di rumah untuk pasien yang tidak bisa datang.', 'fas fa-house-user', '2026-02-28 12:35:36');

-- --------------------------------------------------------

--
-- Struktur dari tabel `site_profile`
--

CREATE TABLE `site_profile` (
  `id` int(11) NOT NULL,
  `logo` varchar(255) DEFAULT 'img/logo-default.png',
  `history` text DEFAULT NULL,
  `vision` text DEFAULT NULL,
  `mission` text DEFAULT NULL,
  `motto` varchar(255) DEFAULT NULL,
  `goals` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `site_profile`
--

INSERT INTO `site_profile` (`id`, `logo`, `history`, `vision`, `mission`, `motto`, `goals`) VALUES
(1, '1772375657_logo_transparan.png', '<p>Sejarah RSIA Restu Ibu...</p>', '<p>Menjadi Rumah Sakit Terpercaya...</p>', '<p>Memberikan pelayanan terbaik...</p>', 'Melayani dengan Kasih Ibu', '<p>Meningkatkan kualitas pelayanan...</p>');

-- --------------------------------------------------------

--
-- Struktur dari tabel `staff`
--

CREATE TABLE `staff` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `position` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `image` varchar(255) DEFAULT 'img/team-1.jpg',
  `bio` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `staff`
--

INSERT INTO `staff` (`id`, `name`, `position`, `category`, `image`, `bio`, `created_at`) VALUES
(1, 'Dr. H. Budi Santoso', 'Direktur Utama', 'Direksi', 'img/team-1.jpg', NULL, '2026-02-28 09:13:57'),
(2, 'Siti Aminah', 'Kepala Perawat', 'Medis', 'img/team-2.jpg', NULL, '2026-02-28 09:13:57'),
(3, 'Rina Wati', 'Staff Administrasi', 'Administrasi', 'img/team-3.jpg', NULL, '2026-02-28 09:13:57');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `accreditations`
--
ALTER TABLE `accreditations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `achievements`
--
ALTER TABLE `achievements`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indeks untuk tabel `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `insurances`
--
ALTER TABLE `insurances`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_post_category` (`category_id`);

--
-- Indeks untuk tabel `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indeks untuk tabel `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `site_profile`
--
ALTER TABLE `site_profile`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `accreditations`
--
ALTER TABLE `accreditations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `achievements`
--
ALTER TABLE `achievements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `insurances`
--
ALTER TABLE `insurances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `fk_post_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Ketidakleluasaan untuk tabel `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
