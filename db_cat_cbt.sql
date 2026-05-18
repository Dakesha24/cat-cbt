-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 13, 2026 at 02:33 AM
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
-- Database: `db_cat_cbt`
--

-- --------------------------------------------------------

--
-- Table structure for table `bank_ujian`
--

CREATE TABLE `bank_ujian` (
  `bank_ujian_id` int(11) NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `jenis_ujian_id` int(11) NOT NULL,
  `nama_ujian` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bank_ujian`
--

INSERT INTO `bank_ujian` (`bank_ujian_id`, `kategori`, `jenis_ujian_id`, `nama_ujian`, `deskripsi`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'umum', 7, 'UTS 1', 'DFFFF', 4, '2025-06-09 16:52:39', '2025-06-09 16:52:39'),
(2, 'XII IPA', 10, 'Ujian Tengah Semester Ganjil 2025', 'Ini adalah ujian yang sangat menantang jiwa dan raga', 4, '2025-06-15 03:18:18', '2025-06-15 03:18:18'),
(3, 'Kelas TKJ', 8, 'UTS', 'DSKLJFLDSJ', 1, '2025-06-20 13:04:44', '2025-06-20 13:04:44');

-- --------------------------------------------------------

--
-- Table structure for table `guru`
--

CREATE TABLE `guru` (
  `guru_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `sekolah_id` int(11) NOT NULL,
  `nip` varchar(20) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `mata_pelajaran` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guru`
--

INSERT INTO `guru` (`guru_id`, `user_id`, `sekolah_id`, `nip`, `nama_lengkap`, `mata_pelajaran`) VALUES
(1, 4, 1, '222222', 'Guru Test', 'Fisika'),
(5, 42, 1, '12345', 'guru 2w', 'Indonesia'),
(6, 45, 1, '12345678910', 'guru 4 tes', 'Fisika'),
(7, 46, 1, '123456', 'guru 3', 'Fisika'),
(8, 47, 2, '123456789', 'guru21', 'informatika');

-- --------------------------------------------------------

--
-- Table structure for table `hasil_ujian`
--

CREATE TABLE `hasil_ujian` (
  `jawaban_id` int(11) NOT NULL,
  `peserta_ujian_id` int(11) NOT NULL,
  `soal_id` int(11) NOT NULL,
  `jawaban_siswa` enum('A','B','C','D','E') NOT NULL,
  `is_correct` tinyint(1) DEFAULT NULL,
  `waktu_menjawab` timestamp NOT NULL DEFAULT current_timestamp(),
  `theta_saat_ini` decimal(5,3) DEFAULT NULL,
  `se_saat_ini` decimal(5,3) DEFAULT NULL,
  `delta_se_saat_ini` decimal(5,3) NOT NULL,
  `pi_saat_ini` decimal(5,3) DEFAULT NULL,
  `qi_saat_ini` decimal(5,3) DEFAULT NULL,
  `ii_saat_ini` decimal(5,3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hasil_ujian`
--

INSERT INTO `hasil_ujian` (`jawaban_id`, `peserta_ujian_id`, `soal_id`, `jawaban_siswa`, `is_correct`, `waktu_menjawab`, `theta_saat_ini`, `se_saat_ini`, `delta_se_saat_ini`, `pi_saat_ini`, `qi_saat_ini`, `ii_saat_ini`) VALUES
(249, 68, 26, 'A', 0, '2025-01-13 13:12:56', -0.234, 2.014, -1.014, NULL, NULL, NULL),
(250, 68, 34, 'B', 0, '2025-01-13 13:12:58', -0.432, 1.418, 0.596, NULL, NULL, NULL),
(251, 68, 31, 'C', 0, '2025-01-13 13:12:59', -0.456, 1.157, 0.261, NULL, NULL, NULL),
(252, 68, 51, 'B', 0, '2025-01-13 13:13:01', -0.543, 1.002, 0.155, NULL, NULL, NULL),
(253, 68, 25, 'B', 0, '2025-01-13 13:13:02', -0.876, 0.899, 0.102, NULL, NULL, NULL),
(254, 68, 37, 'A', 0, '2025-01-13 13:13:04', -0.987, 0.832, 0.068, NULL, NULL, NULL),
(255, 68, 42, 'C', 0, '2025-01-13 13:13:06', -1.234, 0.775, 0.057, NULL, NULL, NULL),
(256, 68, 24, 'A', 1, '2025-01-13 13:13:07', -1.245, 0.738, 0.037, NULL, NULL, NULL),
(257, 68, 79, 'B', 0, '2025-01-13 13:13:09', -0.987, 0.694, 0.044, NULL, NULL, NULL),
(258, 68, 82, 'A', 0, '2025-01-13 13:13:11', -1.432, 0.646, 0.048, NULL, NULL, NULL),
(259, 68, 54, 'B', 1, '2025-01-13 13:13:13', -1.543, 0.635, 0.011, NULL, NULL, NULL),
(260, 68, 46, 'A', 0, '2025-01-13 13:13:14', -0.876, 0.616, 0.019, NULL, NULL, NULL),
(261, 68, 72, 'A', 0, '2025-01-13 13:13:20', -1.876, 0.570, 0.046, NULL, NULL, NULL),
(262, 69, 26, 'A', 0, '2025-01-13 13:22:56', -0.234, 2.014, -1.014, NULL, NULL, NULL),
(263, 69, 34, 'B', 0, '2025-01-13 13:22:57', -0.432, 1.418, 0.596, NULL, NULL, NULL),
(264, 69, 31, 'B', 0, '2025-01-13 13:22:58', -0.456, 1.157, 0.261, NULL, NULL, NULL),
(265, 69, 51, 'B', 0, '2025-01-13 13:23:00', -0.543, 1.002, 0.155, NULL, NULL, NULL),
(266, 69, 25, 'B', 0, '2025-01-13 13:23:01', -0.876, 0.899, 0.102, NULL, NULL, NULL),
(267, 69, 37, 'A', 0, '2025-01-13 13:23:03', -0.987, 0.832, 0.068, NULL, NULL, NULL),
(268, 69, 42, 'C', 0, '2025-01-13 13:23:05', -1.234, 0.775, 0.057, NULL, NULL, NULL),
(269, 69, 24, 'B', 0, '2025-01-13 13:23:06', -1.245, 0.738, 0.037, NULL, NULL, NULL),
(270, 69, 82, 'B', 1, '2025-01-13 13:23:08', -1.432, 0.693, 0.045, NULL, NULL, NULL),
(271, 69, 79, 'B', 0, '2025-01-13 13:23:10', -0.987, 0.669, 0.024, NULL, NULL, NULL),
(272, 69, 54, 'C', 0, '2025-01-13 13:23:11', -1.543, 0.616, 0.053, NULL, NULL, NULL),
(273, 69, 72, 'C', 0, '2025-01-13 13:23:13', -1.876, 0.613, 0.003, NULL, NULL, NULL),
(295, 72, 26, 'A', 0, '2025-01-13 13:37:18', -0.234, 2.014, -1.014, NULL, NULL, NULL),
(296, 72, 34, 'A', 0, '2025-01-13 13:37:20', -0.432, 1.418, 0.596, NULL, NULL, NULL),
(297, 72, 31, 'B', 0, '2025-01-13 13:37:21', -0.456, 1.157, 0.261, NULL, NULL, NULL),
(298, 72, 51, 'B', 0, '2025-01-13 13:37:22', -0.543, 1.002, 0.155, NULL, NULL, NULL),
(299, 72, 25, 'A', 1, '2025-01-13 13:37:24', -0.876, 0.899, 0.102, NULL, NULL, NULL),
(300, 72, 69, 'B', 0, '2025-01-13 13:37:25', -0.543, 0.833, 0.066, NULL, NULL, NULL),
(301, 72, 46, 'C', 1, '2025-01-13 13:37:27', -0.876, 0.760, 0.073, NULL, NULL, NULL),
(302, 72, 60, 'A', 0, '2025-01-13 13:37:28', -0.432, 0.720, 0.040, NULL, NULL, NULL),
(303, 72, 58, 'B', 0, '2025-01-13 13:37:29', -0.876, 0.673, 0.048, NULL, NULL, NULL),
(304, 72, 37, 'B', 1, '2025-01-13 13:37:31', -0.987, 0.642, 0.031, NULL, NULL, NULL),
(305, 72, 63, 'B', 0, '2025-01-13 13:37:32', -0.876, 0.616, 0.026, NULL, NULL, NULL),
(306, 72, 79, 'B', 0, '2025-01-13 13:37:34', -0.987, 0.585, 0.031, NULL, NULL, NULL),
(307, 72, 42, 'B', 1, '2025-01-13 13:37:35', -1.234, 0.565, 0.020, NULL, NULL, NULL),
(308, 72, 76, 'B', 0, '2025-01-13 13:37:39', -0.432, 0.558, 0.007, NULL, NULL, NULL),
(309, 72, 24, 'B', 0, '2025-01-13 13:37:41', -1.245, 0.528, 0.030, NULL, NULL, NULL),
(310, 72, 82, 'C', 0, '2025-01-13 13:37:45', -1.432, 0.520, 0.008, NULL, NULL, NULL),
(311, 72, 54, 'A', 0, '2025-01-13 13:37:46', -1.543, 0.515, 0.005, NULL, NULL, NULL),
(312, 72, 72, 'A', 0, '2025-01-13 13:37:48', -1.876, 0.506, 0.008, NULL, NULL, NULL),
(313, 73, 26, 'A', 0, '2025-05-08 06:46:28', -0.234, 2.014, -1.014, NULL, NULL, NULL),
(314, 73, 34, 'A', 0, '2025-05-08 06:46:31', -0.432, 1.418, 0.596, NULL, NULL, NULL),
(315, 73, 31, 'A', 1, '2025-05-08 06:46:34', -0.456, 1.157, 0.261, NULL, NULL, NULL),
(316, 73, 60, 'A', 0, '2025-05-08 06:46:36', -0.432, 1.002, 0.155, NULL, NULL, NULL),
(317, 73, 51, 'A', 1, '2025-05-08 06:46:39', -0.543, 0.896, 0.106, NULL, NULL, NULL),
(318, 73, 76, 'A', 0, '2025-05-08 06:46:42', -0.432, 0.819, 0.077, NULL, NULL, NULL),
(319, 73, 69, 'A', 1, '2025-05-08 06:46:44', -0.543, 0.757, 0.062, NULL, NULL, NULL),
(320, 73, 66, 'A', 1, '2025-05-08 06:46:46', -0.234, 0.710, 0.047, NULL, NULL, NULL),
(321, 73, 33, 'A', 0, '2025-05-08 06:46:48', 0.234, 0.672, 0.038, NULL, NULL, NULL),
(322, 73, 25, 'A', 1, '2025-05-08 06:46:50', -0.876, 0.669, 0.003, NULL, NULL, NULL),
(323, 73, 44, 'A', 0, '2025-05-08 06:46:52', 0.234, 0.631, 0.038, NULL, NULL, NULL),
(324, 73, 46, 'A', 0, '2025-05-08 06:46:55', -0.876, 0.612, 0.019, NULL, NULL, NULL),
(325, 73, 37, 'A', 0, '2025-05-08 06:46:57', -0.987, 0.576, 0.036, NULL, NULL, NULL),
(326, 73, 42, 'A', 0, '2025-05-08 06:46:59', -1.234, 0.560, 0.016, NULL, NULL, NULL),
(327, 73, 24, 'A', 1, '2025-05-08 06:47:02', -1.245, 0.556, 0.004, NULL, NULL, NULL),
(328, 73, 79, 'A', 0, '2025-05-08 06:47:04', -0.987, 0.537, 0.019, NULL, NULL, NULL),
(329, 73, 82, 'A', 0, '2025-05-08 06:47:06', -1.432, 0.505, 0.032, NULL, NULL, NULL),
(330, 73, 54, 'A', 0, '2025-05-08 06:47:09', -1.543, 0.516, -0.011, NULL, NULL, NULL),
(331, 73, 72, 'A', 0, '2025-05-08 06:47:11', -1.876, 0.509, 0.007, NULL, NULL, NULL),
(332, 74, 26, 'B', 1, '2025-05-08 07:59:11', -0.234, 2.014, -1.014, NULL, NULL, NULL),
(333, 74, 33, 'A', 0, '2025-05-08 08:00:55', 0.234, 1.433, 0.580, NULL, NULL, NULL),
(334, 74, 66, 'B', 0, '2025-05-08 08:01:36', -0.234, 1.176, 0.258, NULL, NULL, NULL),
(335, 74, 34, 'C', 1, '2025-05-08 08:01:43', -0.432, 1.008, 0.168, NULL, NULL, NULL),
(336, 74, 44, 'A', 0, '2025-05-08 08:01:47', 0.234, 0.915, 0.093, NULL, NULL, NULL),
(337, 74, 60, 'A', 0, '2025-05-08 08:01:51', -0.432, 0.839, 0.077, NULL, NULL, NULL),
(338, 74, 31, 'C', 0, '2025-05-08 08:01:54', -0.456, 0.768, 0.070, NULL, NULL, NULL),
(339, 74, 51, 'B', 0, '2025-05-08 08:01:56', -0.543, 0.718, 0.050, NULL, NULL, NULL),
(340, 74, 25, 'A', 1, '2025-05-08 08:02:00', -0.876, 0.680, 0.038, NULL, NULL, NULL),
(341, 74, 69, 'A', 1, '2025-05-08 08:02:04', -0.543, 0.663, 0.017, NULL, NULL, NULL),
(342, 74, 76, 'B', 0, '2025-05-08 08:02:07', -0.432, 0.613, 0.050, NULL, NULL, NULL),
(343, 74, 46, 'C', 1, '2025-05-08 08:02:19', -0.876, 0.585, 0.028, NULL, NULL, NULL),
(344, 74, 47, 'B', 1, '2025-05-08 08:02:21', 0.432, 0.584, 0.001, NULL, NULL, NULL),
(345, 74, 27, 'B', 1, '2025-05-08 08:02:29', 0.543, 0.577, 0.007, NULL, NULL, NULL),
(346, 74, 29, 'A', 0, '2025-05-08 08:02:32', 0.567, 0.563, 0.013, NULL, NULL, NULL),
(347, 74, 39, 'A', 0, '2025-05-08 08:02:34', 0.543, 0.544, 0.019, NULL, NULL, NULL),
(348, 74, 58, 'B', 0, '2025-05-08 08:02:39', -0.876, 0.530, 0.014, NULL, NULL, NULL),
(349, 74, 37, 'A', 0, '2025-05-08 08:02:42', -0.987, 0.506, 0.024, NULL, NULL, NULL),
(350, 74, 42, 'B', 1, '2025-05-08 08:02:45', -1.234, 0.498, 0.008, NULL, NULL, NULL),
(351, 74, 79, 'C', 1, '2025-05-08 08:02:58', -0.987, 0.502, -0.004, NULL, NULL, NULL),
(352, 75, 26, 'A', 0, '2025-05-19 13:45:16', -0.234, 2.014, -1.014, NULL, NULL, NULL),
(353, 75, 34, 'A', 0, '2025-05-19 13:45:18', -0.432, 1.418, 0.596, NULL, NULL, NULL),
(354, 75, 31, 'A', 1, '2025-05-19 13:45:21', -0.456, 1.157, 0.261, NULL, NULL, NULL),
(355, 75, 60, 'A', 0, '2025-05-19 13:45:25', -0.432, 1.002, 0.155, NULL, NULL, NULL),
(356, 75, 51, 'A', 1, '2025-05-19 13:45:27', -0.543, 0.896, 0.106, NULL, NULL, NULL),
(357, 75, 76, 'A', 0, '2025-05-19 13:45:30', -0.432, 0.819, 0.077, NULL, NULL, NULL),
(358, 75, 69, 'A', 1, '2025-05-19 13:45:32', -0.543, 0.757, 0.062, NULL, NULL, NULL),
(359, 75, 66, 'A', 1, '2025-05-19 13:45:34', -0.234, 0.710, 0.047, NULL, NULL, NULL),
(360, 75, 33, 'A', 0, '2025-05-19 13:45:37', 0.234, 0.672, 0.038, NULL, NULL, NULL),
(361, 75, 25, 'A', 1, '2025-05-19 13:45:39', -0.876, 0.669, 0.003, NULL, NULL, NULL),
(362, 75, 44, 'A', 0, '2025-05-19 13:45:42', 0.234, 0.631, 0.038, NULL, NULL, NULL),
(363, 75, 46, 'A', 0, '2025-05-19 13:45:44', -0.876, 0.612, 0.019, NULL, NULL, NULL),
(364, 75, 37, 'A', 0, '2025-05-19 13:45:46', -0.987, 0.576, 0.036, NULL, NULL, NULL),
(365, 75, 42, 'A', 0, '2025-05-19 13:45:49', -1.234, 0.560, 0.016, NULL, NULL, NULL),
(366, 75, 24, 'A', 1, '2025-05-19 13:45:51', -1.245, 0.556, 0.004, NULL, NULL, NULL),
(367, 75, 79, 'A', 0, '2025-05-19 13:45:53', -0.987, 0.537, 0.019, NULL, NULL, NULL),
(368, 75, 82, 'A', 0, '2025-05-19 13:45:56', -1.432, 0.505, 0.032, NULL, NULL, NULL),
(369, 75, 54, 'A', 0, '2025-05-19 13:45:58', -1.543, 0.516, -0.011, NULL, NULL, NULL),
(370, 75, 72, 'A', 0, '2025-05-19 13:46:00', -1.876, 0.509, 0.007, NULL, NULL, NULL),
(371, 76, 26, '', 0, '2025-05-20 12:00:12', -0.234, 2.014, -1.014, NULL, NULL, NULL),
(372, 76, 34, '', 0, '2025-05-20 12:00:15', -0.432, 1.418, 0.596, NULL, NULL, NULL),
(373, 76, 31, '', 0, '2025-05-20 12:00:18', -0.456, 1.157, 0.261, NULL, NULL, NULL),
(374, 76, 51, '', 0, '2025-05-20 12:00:20', -0.543, 1.002, 0.155, NULL, NULL, NULL),
(375, 76, 25, '', 0, '2025-05-20 12:00:23', -0.876, 0.899, 0.102, NULL, NULL, NULL),
(376, 76, 37, '', 0, '2025-05-20 12:00:26', -0.987, 0.832, 0.068, NULL, NULL, NULL),
(377, 76, 42, '', 0, '2025-05-20 12:00:28', -1.234, 0.775, 0.057, NULL, NULL, NULL),
(378, 76, 24, '', 0, '2025-05-20 12:00:31', -1.245, 0.738, 0.037, NULL, NULL, NULL),
(379, 76, 82, '', 0, '2025-05-20 12:00:34', -1.432, 0.693, 0.045, NULL, NULL, NULL),
(380, 76, 54, '', 0, '2025-05-20 12:00:36', -1.543, 0.668, 0.026, NULL, NULL, NULL),
(381, 76, 72, '', 0, '2025-05-20 12:00:38', -1.876, 0.642, 0.025, NULL, NULL, NULL),
(382, 77, 26, '', 0, '2025-05-20 12:38:16', -0.234, 2.014, -1.014, NULL, NULL, NULL),
(383, 77, 34, '', 0, '2025-05-20 12:38:57', -0.432, 1.418, 0.596, NULL, NULL, NULL),
(384, 77, 31, 'D', 0, '2025-05-20 12:39:07', -0.456, 1.157, 0.261, NULL, NULL, NULL),
(385, 79, 26, '', 0, '2025-05-21 00:43:36', -0.234, 2.014, -1.014, NULL, NULL, NULL),
(386, 79, 34, '', 0, '2025-05-21 00:43:39', -0.432, 1.418, 0.596, NULL, NULL, NULL),
(387, 79, 31, '', 0, '2025-05-21 00:43:42', -0.456, 1.157, 0.261, NULL, NULL, NULL),
(388, 79, 51, '', 0, '2025-05-21 00:43:45', -0.543, 1.002, 0.155, NULL, NULL, NULL),
(389, 79, 25, '', 0, '2025-05-21 00:43:48', -0.876, 0.899, 0.102, NULL, NULL, NULL),
(390, 79, 37, '', 0, '2025-05-21 00:43:51', -0.987, 0.832, 0.068, NULL, NULL, NULL),
(391, 79, 42, '', 0, '2025-05-21 00:43:54', -1.234, 0.775, 0.057, NULL, NULL, NULL),
(392, 79, 24, '', 0, '2025-05-21 00:43:57', -1.245, 0.738, 0.037, NULL, NULL, NULL),
(393, 79, 82, '', 0, '2025-05-21 00:44:00', -1.432, 0.693, 0.045, NULL, NULL, NULL),
(394, 79, 54, '', 0, '2025-05-21 00:44:03', -1.543, 0.668, 0.026, NULL, NULL, NULL),
(395, 79, 72, '', 0, '2025-05-21 00:44:06', -1.876, 0.642, 0.025, NULL, NULL, NULL),
(416, 82, 26, 'E', 0, '2025-05-26 11:14:55', -0.234, 2.014, -1.014, 0.558, 0.442, 0.247),
(417, 82, 34, 'E', 0, '2025-05-26 11:14:57', -0.432, 1.418, 0.596, 0.549, 0.451, 0.248),
(418, 82, 31, 'E', 0, '2025-05-26 11:14:59', -0.456, 1.157, 0.261, 0.506, 0.494, 0.250),
(419, 82, 51, 'E', 0, '2025-05-26 11:15:01', -0.543, 1.002, 0.155, 0.522, 0.478, 0.250),
(420, 82, 25, 'E', 0, '2025-05-26 11:15:02', -0.876, 0.899, 0.102, 0.582, 0.418, 0.243),
(421, 82, 37, 'E', 0, '2025-05-26 11:15:04', -0.987, 0.832, 0.068, 0.528, 0.472, 0.249),
(422, 82, 42, 'E', 0, '2025-05-26 11:15:06', -1.234, 0.775, 0.057, 0.561, 0.439, 0.246),
(423, 82, 24, 'E', 0, '2025-05-26 11:15:08', -1.245, 0.738, 0.037, 0.503, 0.497, 0.250),
(424, 82, 82, 'E', 0, '2025-05-26 11:15:10', -1.432, 0.693, 0.045, 0.547, 0.453, 0.248),
(425, 82, 54, 'E', 0, '2025-05-26 11:15:12', -1.543, 0.668, 0.026, 0.528, 0.472, 0.249),
(426, 82, 72, 'E', 0, '2025-05-26 11:15:13', -1.876, 0.642, 0.025, 0.582, 0.418, 0.243),
(429, 85, 87, 'B', 1, '2025-06-13 00:10:41', 0.002, 2.000, -1.000, 0.500, 0.500, 0.250),
(430, 85, 33, 'C', 0, '2025-06-13 00:10:43', 0.234, 1.419, 0.581, 0.442, 0.558, 0.247),
(431, 85, 26, 'B', 1, '2025-06-13 00:27:23', -0.234, 1.168, 0.251, 0.615, 0.385, 0.237),
(432, 85, 44, 'B', 1, '2025-06-13 00:27:24', 0.234, 1.015, 0.152, 0.385, 0.615, 0.237),
(433, 85, 47, 'B', 1, '2025-06-13 00:27:26', 0.432, 0.901, 0.114, 0.451, 0.549, 0.248),
(434, 85, 92, 'C', 0, '2025-06-13 00:27:28', 0.500, 0.828, 0.073, 0.483, 0.517, 0.250),
(435, 85, 66, 'C', 0, '2025-06-13 00:27:30', -0.234, 0.775, 0.053, 0.676, 0.324, 0.219),
(436, 85, 34, 'B', 0, '2025-06-13 00:27:32', -0.432, 0.723, 0.052, 0.549, 0.451, 0.248),
(437, 85, 31, 'E', 0, '2025-06-13 00:27:34', -0.456, 0.691, 0.032, 0.506, 0.494, 0.250),
(438, 85, 51, 'D', 0, '2025-06-13 00:27:36', -0.543, 0.655, 0.036, 0.522, 0.478, 0.250),
(439, 85, 25, 'C', 0, '2025-06-13 00:27:38', -0.876, 0.628, 0.027, 0.582, 0.418, 0.243),
(440, 85, 37, 'B', 1, '2025-06-13 00:27:41', -0.987, 0.623, 0.005, 0.528, 0.472, 0.249),
(441, 85, 46, 'B', 0, '2025-06-13 00:27:42', -0.876, 0.604, 0.019, 0.472, 0.528, 0.249),
(442, 85, 79, 'B', 0, '2025-06-13 00:27:49', -0.987, 0.570, 0.034, 0.528, 0.472, 0.249),
(443, 85, 42, 'B', 1, '2025-06-13 00:27:51', -1.234, 0.556, 0.014, 0.561, 0.439, 0.246),
(444, 85, 58, 'A', 0, '2025-06-13 00:27:54', -0.876, 0.554, 0.001, 0.411, 0.589, 0.242),
(445, 85, 24, 'B', 0, '2025-06-13 00:27:56', -1.245, 0.512, 0.042, 0.591, 0.409, 0.242),
(446, 85, 82, 'A', 0, '2025-06-13 00:27:58', -1.432, 0.517, -0.005, 0.547, 0.453, 0.248),
(447, 85, 54, 'B', 1, '2025-06-13 00:27:59', -1.543, 0.515, 0.002, 0.528, 0.472, 0.249),
(448, 85, 63, 'C', 1, '2025-06-13 00:28:02', -0.876, 0.509, 0.006, 0.339, 0.661, 0.224),
(449, 85, 69, 'D', 0, '2025-06-13 00:28:03', -0.543, 0.458, 0.051, 0.418, 0.582, 0.243),
(450, 85, 72, 'B', 1, '2025-06-13 00:28:04', -1.876, 0.447, 0.011, 0.791, 0.209, 0.165),
(451, 85, 60, 'B', 1, '2025-06-13 00:28:07', -0.432, 0.505, -0.058, 0.191, 0.809, 0.154),
(452, 85, 27, 'D', 0, '2025-06-13 00:28:10', 0.543, 0.430, 0.076, 0.274, 0.726, 0.199),
(453, 85, 76, 'A', 0, '2025-06-13 00:28:11', -0.432, 0.470, -0.041, 0.726, 0.274, 0.199);

-- --------------------------------------------------------

--
-- Table structure for table `jadwal_ujian`
--

CREATE TABLE `jadwal_ujian` (
  `jadwal_id` int(11) NOT NULL,
  `ujian_id` int(11) NOT NULL,
  `kelas_id` int(11) NOT NULL,
  `guru_id` int(11) NOT NULL,
  `tanggal_mulai` datetime NOT NULL,
  `tanggal_selesai` datetime NOT NULL,
  `durasi_menit` int(11) NOT NULL,
  `kode_akses` varchar(20) NOT NULL,
  `status` enum('belum_mulai','sedang_berlangsung','selesai') NOT NULL DEFAULT 'belum_mulai'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jadwal_ujian`
--

INSERT INTO `jadwal_ujian` (`jadwal_id`, `ujian_id`, `kelas_id`, `guru_id`, `tanggal_mulai`, `tanggal_selesai`, `durasi_menit`, `kode_akses`, `status`) VALUES
(9, 2, 1, 1, '2025-01-09 13:12:00', '2025-06-23 13:12:00', 0, 'utsaja123', 'sedang_berlangsung'),
(11, 2, 2, 1, '2025-05-21 07:30:00', '2025-05-22 07:30:00', 0, 'Baaaaa', 'sedang_berlangsung'),
(14, 3, 1, 1, '2025-06-20 15:23:00', '2025-06-26 15:24:00', 0, 'SMN123', 'sedang_berlangsung');

-- --------------------------------------------------------

--
-- Table structure for table `jenis_ujian`
--

CREATE TABLE `jenis_ujian` (
  `jenis_ujian_id` int(11) NOT NULL,
  `deskripsi` text NOT NULL,
  `nama_jenis` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `kelas_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jenis_ujian`
--

INSERT INTO `jenis_ujian` (`jenis_ujian_id`, `deskripsi`, `nama_jenis`, `created_at`, `updated_at`, `kelas_id`, `created_by`) VALUES
(7, 'Mata Pelajaran Fisika', 'Fisika', '2025-01-07 17:27:02', '2025-06-17 18:47:00', 7, NULL),
(8, 'wdsa', 'UTS kelas 10', '2025-05-08 14:45:15', '2025-05-08 14:45:15', NULL, NULL),
(9, 'DFDFP', 'UAS Fisika 2026', '2025-06-12 07:15:44', '2025-06-12 07:15:44', 1, 4),
(10, 'FFGFG', 'UTS kelas 10', '2025-06-12 07:16:21', '2025-06-12 07:16:21', 1, 4),
(12, 'DFD', 'UAS', '2025-06-12 07:51:53', '2025-06-12 07:51:53', NULL, 45),
(13, 'DFD', 'UAS', '2025-06-12 07:53:01', '2025-06-12 07:53:01', NULL, 45),
(14, 'dffddfs', 'uas', '2025-06-12 07:53:17', '2025-06-12 07:53:17', NULL, 45),
(15, 'ddsfdf', 'quiz', '2025-06-12 07:54:51', '2025-06-12 07:54:51', NULL, 45),
(17, 'DFDF', 'UAS', '2025-06-12 22:01:19', '2025-06-12 22:01:19', 7, 47);

-- --------------------------------------------------------

--
-- Table structure for table `kelas`
--

CREATE TABLE `kelas` (
  `kelas_id` int(11) NOT NULL,
  `sekolah_id` int(11) NOT NULL,
  `nama_kelas` varchar(20) NOT NULL,
  `tahun_ajaran` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kelas`
--

INSERT INTO `kelas` (`kelas_id`, `sekolah_id`, `nama_kelas`, `tahun_ajaran`) VALUES
(1, 1, 'XII IPA', '2024/2025'),
(2, 1, 'B', '2022'),
(5, 1, 'xii ipa 3', '2025/2026'),
(7, 2, 'xi tkj', '2025/2026');

-- --------------------------------------------------------

--
-- Table structure for table `kelas_guru`
--

CREATE TABLE `kelas_guru` (
  `kelas_guru_id` int(11) NOT NULL,
  `kelas_id` int(11) NOT NULL,
  `guru_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kelas_guru`
--

INSERT INTO `kelas_guru` (`kelas_guru_id`, `kelas_id`, `guru_id`, `created_at`, `updated_at`) VALUES
(1, 5, 5, '2025-06-08 15:19:12', '2025-06-08 15:19:12'),
(2, 1, 5, '2025-06-08 15:19:36', '2025-06-08 15:19:36'),
(4, 2, 7, '2025-06-10 11:47:54', '2025-06-10 11:47:54'),
(5, 1, 7, '2025-06-10 11:52:45', '2025-06-10 11:52:45'),
(6, 1, 1, '2025-06-11 09:15:24', '2025-06-11 09:15:24'),
(7, 7, 8, '2025-06-12 15:00:59', '2025-06-12 15:00:59');

-- --------------------------------------------------------

--
-- Table structure for table `pengumuman`
--

CREATE TABLE `pengumuman` (
  `pengumuman_id` int(11) NOT NULL,
  `judul` varchar(200) NOT NULL,
  `isi_pengumuman` text NOT NULL,
  `tanggal_publish` datetime DEFAULT current_timestamp(),
  `tanggal_berakhir` datetime DEFAULT NULL,
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengumuman`
--

INSERT INTO `pengumuman` (`pengumuman_id`, `judul`, `isi_pengumuman`, `tanggal_publish`, `tanggal_berakhir`, `created_by`) VALUES
(1, 'Besok Libur', 'Besok libur ya anak anak', '2025-01-08 08:06:00', '2025-01-09 08:06:00', 4),
(2, 'Besok Ujian Asesmen', 'fghjkl', '2025-05-08 14:43:00', '2025-05-09 14:43:00', 4),
(3, 'Tolong Cek LMS ya', 'Tolong cek LMS', '2025-05-26 08:42:00', '2025-05-27 08:44:00', 4);

-- --------------------------------------------------------

--
-- Table structure for table `peserta_ujian`
--

CREATE TABLE `peserta_ujian` (
  `peserta_ujian_id` int(11) NOT NULL,
  `jadwal_id` int(11) NOT NULL,
  `siswa_id` int(11) NOT NULL,
  `status` enum('belum_mulai','sedang_mengerjakan','selesai') DEFAULT 'belum_mulai',
  `waktu_mulai` datetime DEFAULT NULL,
  `waktu_selesai` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `peserta_ujian`
--

INSERT INTO `peserta_ujian` (`peserta_ujian_id`, `jadwal_id`, `siswa_id`, `status`, `waktu_mulai`, `waktu_selesai`) VALUES
(68, 9, 28, 'selesai', '2025-01-13 20:12:54', '2025-01-13 20:13:20'),
(69, 9, 23, 'selesai', '2025-01-13 20:22:53', '2025-01-13 20:23:13'),
(72, 9, 26, 'selesai', '2025-01-13 20:37:15', '2025-01-13 20:37:48'),
(73, 9, 31, 'selesai', '2025-05-08 13:46:25', '2025-05-08 13:47:11'),
(74, 9, 32, 'selesai', '2025-05-08 14:58:27', '2025-05-08 15:02:58'),
(75, 9, 33, 'selesai', '2025-05-19 20:45:10', '2025-05-19 20:46:00'),
(76, 9, 34, 'selesai', '2025-05-20 19:00:08', '2025-05-20 19:00:39'),
(77, 9, 35, 'selesai', '2025-05-20 19:38:13', '2025-05-21 04:26:38'),
(79, 11, 36, 'selesai', '2025-05-21 07:43:32', '2025-05-21 07:44:06'),
(82, 9, 38, 'selesai', '2025-05-26 18:14:52', '2025-05-26 18:15:13'),
(85, 9, 43, 'selesai', '2025-06-13 07:10:37', '2025-06-13 07:28:11');

-- --------------------------------------------------------

--
-- Table structure for table `sekolah`
--

CREATE TABLE `sekolah` (
  `sekolah_id` int(11) NOT NULL,
  `nama_sekolah` varchar(100) NOT NULL,
  `alamat` text DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sekolah`
--

INSERT INTO `sekolah` (`sekolah_id`, `nama_sekolah`, `alamat`, `telepon`, `email`, `created_at`) VALUES
(1, 'SMKN 4 Bandung', 'Bandung', '09876543299', 'smkn4juara@gmail.com', '2024-12-14 19:14:58'),
(2, 'SMAN 21 Bandung', 'Ciwastra', '1234567898', 'dansdk@gmail.com', '2025-05-26 15:12:26');

-- --------------------------------------------------------

--
-- Table structure for table `siswa`
--

CREATE TABLE `siswa` (
  `siswa_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `kelas_id` int(11) DEFAULT NULL,
  `nomor_peserta` varchar(20) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `siswa`
--

INSERT INTO `siswa` (`siswa_id`, `user_id`, `kelas_id`, `nomor_peserta`, `nama_lengkap`, `jenis_kelamin`, `created_at`, `updated_at`) VALUES
(20, 22, 1, '242312', 'adammm', 'Laki-laki', '2025-01-09 16:53:12', '2025-06-18 00:56:30'),
(22, 24, 1, '098767', 'shin te hyong', NULL, '2025-01-10 11:54:14', '2025-01-10 11:54:14'),
(23, 25, 1, '229183', 'murid pintar\r\n', NULL, '2025-01-10 12:42:44', '2025-01-11 03:57:13'),
(24, 26, 1, '83912', 'beruang besar', NULL, '2025-01-11 02:25:18', '2025-01-11 02:25:18'),
(25, 27, 1, '2312413', 'burung garuda', NULL, '2025-01-11 02:26:26', '2025-01-11 02:26:26'),
(26, 28, 1, '1234123', 'kuda hitam', NULL, '2025-01-11 06:11:47', '2025-01-11 06:11:47'),
(27, 29, 1, '2432412', 'ayam jago', NULL, '2025-01-11 06:30:26', '2025-01-11 06:30:26'),
(28, 30, 1, '232099', 'danis keysara', 'Laki-laki', '2025-01-11 07:44:10', '2025-06-20 08:24:48'),
(29, 31, 1, '343242', 'keysara saputra', NULL, '2025-01-11 07:46:03', '2025-01-11 07:46:03'),
(30, 32, 1, '2132121', 'jess no limit', NULL, '2025-01-11 08:22:16', '2025-01-11 08:22:16'),
(31, 34, 1, '12345', 'Geral Rades', NULL, '2025-05-08 06:42:18', '2025-05-08 06:42:18'),
(32, 35, 1, '12345', 'jauza amalia', NULL, '2025-05-08 07:58:18', '2025-05-08 07:58:18'),
(33, 36, 1, '12345', 'Pradesa Rades', NULL, '2025-05-19 13:44:58', '2025-05-19 13:44:58'),
(34, 37, 1, '666666', 'Ibrahim', NULL, '2025-05-20 11:59:08', '2025-05-20 11:59:08'),
(35, 38, 1, '23434332', 'jefri nikol', NULL, '2025-05-20 12:37:57', '2025-05-20 12:37:57'),
(36, 39, 2, '2134565', 'mufid', NULL, '2025-05-21 00:32:04', '2025-05-21 00:32:04'),
(37, 40, 1, '1234', 'coba baru', NULL, '2025-05-21 08:00:12', '2025-06-08 16:52:29'),
(38, 41, 1, '1234567', 'sahrul mubarok', NULL, '2025-05-26 11:13:57', '2025-05-26 11:13:57'),
(39, 43, 1, '123001', '123 001', 'Laki-laki', '2025-05-26 15:20:27', '2025-06-18 02:34:35'),
(40, 44, 1, '123002', 'TES AJAH', NULL, '2025-05-26 15:20:27', '2025-06-09 01:43:32'),
(41, 48, 7, '1234567890', 'Anak TKJ bgt', NULL, '2025-06-12 15:14:15', '2025-06-12 15:14:15'),
(42, 49, 7, '0333222', 'Joko', NULL, '2025-06-12 15:51:47', '2025-06-12 15:52:05'),
(43, 50, 1, '4343232', 'Mubarok', NULL, '2025-06-13 00:07:58', '2025-06-13 00:07:58'),
(44, 51, 1, '12345', 'Agus Kun', NULL, '2025-06-15 10:39:40', '2025-06-15 10:39:40'),
(45, 52, 1, '1234567', 'mamah', NULL, '2025-06-15 13:46:12', '2025-06-15 13:46:12'),
(46, 53, 1, '12345', 'ikan mas', NULL, '2025-06-15 18:27:37', '2025-06-15 18:27:37'),
(47, 54, 7, '123456', 'apipaja', 'Laki-laki', '2025-06-18 00:57:37', '2025-06-18 00:57:37'),
(48, 55, 2, 'SISWA001', 'SISWA 001', 'Laki-laki', '2025-06-18 00:58:40', '2025-06-18 00:58:40'),
(49, 56, 2, 'SISWA002', 'SISWA 002', 'Laki-laki', '2025-06-18 00:58:40', '2025-06-18 00:58:48'),
(50, 57, 2, '12345678', 'fisika asik', 'Perempuan', '2025-06-18 01:08:18', '2025-06-18 01:08:58'),
(51, 58, 1, 'siswa003', 'siswa 003', 'Laki-laki', '2025-06-18 02:38:00', '2025-06-18 02:38:00'),
(52, 59, 1, 'siswa004', 'siswa 004', 'Laki-laki', '2025-06-18 02:38:00', '2025-06-18 02:38:00'),
(53, 60, 1, 'siswa005', 'siswa 005', 'Laki-laki', '2025-06-18 02:38:00', '2025-06-18 02:38:00'),
(54, 61, 1, 'siswa006', 'siswa 006', 'Laki-laki', '2025-06-18 02:38:00', '2025-06-18 02:38:00'),
(55, 62, 1, 'siswa007', 'siswa 007', 'Laki-laki', '2025-06-18 02:38:00', '2025-06-18 02:38:00'),
(56, 63, 1, 'siswa008', 'siswa 008', 'Laki-laki', '2025-06-18 02:38:00', '2025-06-18 02:38:00'),
(57, 64, 1, 'siswa009', 'siswa 009', 'Laki-laki', '2025-06-18 02:38:00', '2025-06-18 02:38:00'),
(58, 65, 1, 'siswa010', 'siswa 010', 'Laki-laki', '2025-06-18 02:38:00', '2025-06-18 02:38:00');

-- --------------------------------------------------------

--
-- Table structure for table `soal_ujian`
--

CREATE TABLE `soal_ujian` (
  `soal_id` int(11) NOT NULL,
  `kode_soal` varchar(50) DEFAULT NULL,
  `ujian_id` int(11) DEFAULT NULL,
  `bank_ujian_id` int(11) DEFAULT NULL,
  `pertanyaan` text NOT NULL,
  `pilihan_a` text NOT NULL,
  `pilihan_b` text NOT NULL,
  `pilihan_c` text NOT NULL,
  `pilihan_d` text NOT NULL,
  `pilihan_e` text NOT NULL,
  `pembahasan` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `jawaban_benar` enum('A','B','C','D','E') NOT NULL,
  `tingkat_kesulitan` decimal(5,3) NOT NULL DEFAULT 0.000 COMMENT 'parameter b',
  `is_bank_soal` tinyint(1) DEFAULT 0,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `soal_ujian`
--

INSERT INTO `soal_ujian` (`soal_id`, `kode_soal`, `ujian_id`, `bank_ujian_id`, `pertanyaan`, `pilihan_a`, `pilihan_b`, `pilihan_c`, `pilihan_d`, `pilihan_e`, `pembahasan`, `foto`, `jawaban_benar`, `tingkat_kesulitan`, `is_bank_soal`, `created_by`, `created_at`, `updated_at`) VALUES
(24, 'CB1', 2, NULL, '<p>Sebuah balok diukur dengan mistar memiliki panjang 25 cm. Bila dituliskan dalam notasi ilmiah dengan satuan meter, maka hasil pengukuran tersebut adalah...</p>\r\n', '<p>2,5 x 10?1 m</p>\r\n', '<p>2,5 x 10? m</p>\r\n', '<p>2,5 x 101 m</p>\r\n', '<p>2,5 x 10? m</p>\r\n', '<p>1</p>\r\n', '', NULL, 'A', -1.246, 0, NULL, '2025-06-07 04:08:33', '2025-06-19 11:33:48'),
(25, 'CB2', 2, NULL, '<p>Dari kelompok besaran berikut, yang termasuk kelompok besaran pokok dalam SI adalah...</p>\r\n', '<p>Panjang, waktu, suhu, kuat arus</p>\r\n', '<p>Panjang, gaya, massa, waktu</p>\r\n', '<p>Panjang, momentum, suhu, kuat arus</p>\r\n', '<p>Panjang, usaha, massa, waktu</p>\r\n', '<p>1</p>\r\n', '<p><br></p>', NULL, 'A', -0.862, 0, NULL, '2025-06-07 04:08:33', '2025-06-20 08:05:33'),
(26, 'CB3', 2, NULL, '<p>Hasil pengukuran massa sebuah benda adalah 0,543 kg. Banyaknya angka penting dari hasil pengukuran tersebut adalah...</p>\r\n', '<p>2</p>\r\n', '<p>3</p>\r\n', '<p>4</p>\r\n', '<p>5</p>\r\n', '<p>1</p>\r\n', '<p>tes pembahasan</p>\r\n', NULL, 'B', -0.234, 0, NULL, '2025-06-07 04:08:33', '2025-06-17 13:11:57'),
(27, 'CB4', 2, NULL, '<p>Alat ukur yang tepat untuk mengukur diameter dalam sebuah pipa adalah...</p>\r\n', '<p>Mistar</p>\r\n', '<p>Jangka sorong</p>\r\n', '<p>Mikrometer sekrup</p>\r\n', '<p>Meteran</p>\r\n', '<p>1</p>\r\n', '', NULL, 'B', 0.543, 0, NULL, '2025-06-07 04:08:33', '2025-06-15 10:41:38'),
(28, 'CB5', 2, NULL, '<p>Dimensi dari besaran momentum adalah...</p>\r\n', '<p>[MLT?1]</p>\r\n', '<p>[MLT??]</p>\r\n', '<p>[ML?T??]</p>\r\n', '<p>[ML?T?1]</p>\r\n', '<p>1</p>\r\n', '', NULL, 'A', 1.234, 0, NULL, '2025-06-07 04:08:33', '2025-06-15 10:41:47'),
(29, NULL, 2, NULL, 'Dua buah vektor gaya F1 = 3 N dan F2 = 4 N mengapit sudut 90?. Besar resultan kedua gaya tersebut adalah...', '1 N', '3 N', '5 N', '7 N', '1', NULL, NULL, 'C', 0.567, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(30, NULL, 2, NULL, 'Sebuah perahu menyeberangi sungai dengan kecepatan 3 m/s relatif terhadap air. Jika kecepatan arus air 4 m/s, maka kecepatan perahu relatif terhadap tepi sungai adalah...', '1 m/s', '5 m/s', '7 m/s', '9 m/s', '1', NULL, NULL, 'B', 0.890, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(31, NULL, 2, NULL, 'Jika vektor A = 3i + 4j dan vektor B = -i + 2j, maka hasil A + B adalah...', '2i + 6j', '4i + 2j', '2i + 2j', '4i + 6j', '1', NULL, NULL, 'A', -0.456, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(32, NULL, 2, NULL, 'Dua vektor gaya masing-masing 10 N saling mengapit sudut 60?. Besar resultan kedua gaya tersebut adalah...', '10 N', '15 N', '17,3 N', '20 N', '1', NULL, NULL, 'C', 1.678, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(33, NULL, 2, NULL, 'Sebuah vektor memiliki komponen x = 8 dan komponen y = 6. Besar vektor tersebut adalah...', '8 satuan', '10 satuan', '12 satuan', '14 satuan', '1', NULL, NULL, 'B', 0.234, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(34, NULL, 2, NULL, 'Sebuah mobil bergerak dengan kecepatan tetap 72 km/jam. Jarak yang ditempuh mobil selama 15 menit adalah...', '12 km', '15 km', '18 km', '21 km', '1', NULL, NULL, 'C', -0.432, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(35, NULL, 2, NULL, 'Sebuah bola dilempar vertikal ke atas dengan kecepatan awal 20 m/s. Jika percepatan gravitasi 10 m/s?, maka tinggi maksimum yang dicapai bola adalah...', '10 m', '15 m', '20 m', '25 m', '1', NULL, NULL, 'C', 1.234, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(36, NULL, 2, NULL, 'Sebuah benda bergerak lurus dengan percepatan tetap 2 m/s?. Jika kecepatan awal benda 5 m/s, maka kecepatan benda setelah 3 detik adalah...', '11 m/s', '13 m/s', '15 m/s', '17 m/s', '1', NULL, NULL, 'A', 0.765, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(37, NULL, 2, NULL, 'Jarak yang ditempuh oleh sebuah benda yang bergerak lurus beraturan selama 5 detik dengan kecepatan 4 m/s adalah...', '15 m', '20 m', '25 m', '30 m', '1', NULL, NULL, 'B', -0.987, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(38, NULL, 2, NULL, 'Sebuah benda jatuh bebas dari ketinggian 80 meter. Waktu yang diperlukan untuk mencapai tanah adalah... (g = 10 m/s?)', '2 s', '3 s', '4 s', '5 s', '1', NULL, NULL, 'C', 1.543, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(39, NULL, 2, NULL, 'Sebuah benda bermassa 2 kg ditarik dengan gaya 10 N pada bidang datar licin. Percepatan yang dialami benda adalah...', '2 m/s?', '3 m/s?', '4 m/s?', '5 m/s?', '1', NULL, NULL, 'D', 0.543, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(40, NULL, 2, NULL, 'Dua benda bermassa sama dihubungkan dengan tali melalui sebuah katrol licin. Jika sistem dilepaskan, maka percepatan sistem adalah...', '0', 'g/2', 'g', '2g', '1', NULL, NULL, 'B', 1.876, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(41, NULL, 2, NULL, 'Sebuah benda bermassa 4 kg berada pada bidang miring dengan sudut 30?. Jika g = 10 m/s?, maka besar gaya normal pada benda adalah...', '20 N', '25 N', '30 N', '35 N', '1', NULL, NULL, 'C', 2.123, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(42, NULL, 2, NULL, 'Gaya gesek yang bekerja pada sebuah benda dipengaruhi oleh...', 'Masa benda', 'Gaya normal dan koefisien gesek', 'Percepatan benda', 'Kecepatan benda', '1', NULL, NULL, 'B', -1.234, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(43, NULL, 2, NULL, 'Sebuah lift bergerak ke atas dengan percepatan 2 m/s?. Jika massa orang dalam lift 60 kg dan g = 10 m/s?, maka gaya normal yang dialami orang tersebut adalah...', '600 N', '620 N', '720 N', '780 N', '1', NULL, NULL, 'C', 1.987, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(44, NULL, 2, NULL, 'Sebuah roda berputar dengan frekuensi 300 rpm. Periode putaran roda tersebut adalah...', '0,1 s', '0,2 s', '0,3 s', '0,4 s', '1', NULL, NULL, 'B', 0.234, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(45, NULL, 2, NULL, 'Sebuah benda bergerak melingkar dengan jari-jari 2 meter. Jika kecepatan sudutnya 10 rad/s, maka kecepatan liniernya adalah...', '5 m/s', '10 m/s', '15 m/s', '20 m/s', '1', NULL, NULL, 'D', 1.543, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(46, NULL, 2, NULL, 'Pada gerak melingkar beraturan, besaran yang nilainya tetap adalah...', 'Kecepatan linier', 'Percepatan sentripetal', 'Kecepatan sudut', 'Percepatan sudut', '1', NULL, NULL, 'C', -0.876, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(47, NULL, 2, NULL, 'Sebuah benda bergerak melingkar dengan periode 2 detik. Frekuensi putaran benda tersebut adalah...', '0,25 Hz', '0,5 Hz', '1 Hz', '2 Hz', '1', NULL, NULL, 'B', 0.432, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(48, NULL, 2, NULL, 'Percepatan sentripetal pada gerak melingkar beraturan dipengaruhi oleh...', 'Massa dan jari-jari', 'Kecepatan dan periode', 'Kecepatan dan jari-jari', 'Massa dan kecepatan', '1', NULL, NULL, 'C', 1.765, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(49, NULL, 2, NULL, 'Sebuah pegas ditarik dengan gaya 10 N sehingga bertambah panjang 5 cm. Konstanta pegas tersebut adalah...', '100 N/m', '200 N/m', '300 N/m', '400 N/m', '1', NULL, NULL, 'B', 0.876, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(50, NULL, 2, NULL, 'Dua buah pegas identik disusun paralel. Jika konstanta masing-masing pegas 200 N/m, maka konstanta pegas pengganti adalah...', '100 N/m', '200 N/m', '300 N/m', '400 N/m', '1', NULL, NULL, 'D', 1.234, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(51, NULL, 2, NULL, 'Energi potensial elastis pegas dipengaruhi oleh...', 'Konstanta pegas dan pertambahan panjang', 'Massa dan konstanta pegas', 'Gaya dan massa', 'Gaya dan pertambahan panjang', '1', NULL, NULL, 'A', -0.543, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(52, NULL, 2, NULL, 'Sebuah pegas memiliki konstanta 100 N/m. Energi potensial elastis pegas saat pertambahan panjangnya 2 cm adalah...', '0,02 J', '0,04 J', '0,2 J', '0,4 J', '1', NULL, NULL, 'A', 1.876, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(53, NULL, 2, NULL, 'Dua buah pegas identik disusun seri. Jika konstanta masing-masing pegas 100 N/m, maka konstanta pegas pengganti adalah...', '25 N/m', '50 N/m', '100 N/m', '200 N/m', '1', NULL, NULL, 'B', 0.987, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(54, NULL, 2, NULL, 'Sebuah bola bermassa 0,5 kg bergerak dengan kecepatan 4 m/s. Momentum bola tersebut adalah...', '1 kg?m/s', '2 kg?m/s', '3 kg?m/s', '4 kg?m/s', '1', NULL, NULL, 'B', -1.543, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(55, NULL, 2, NULL, 'Dua benda bermassa sama bertumbukan lenting sempurna. Jika salah satu benda mula-mula diam, maka setelah tumbukan...', 'Kedua benda diam', 'Kedua benda bergerak sama cepat', 'Benda yang bergerak menjadi diam', 'Kedua benda bergerak berlawanan arah', '1', NULL, NULL, 'C', 2.123, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(56, NULL, 2, NULL, 'Pada tumbukan lenting sempurna berlaku hukum kekekalan...', 'Momentum saja', 'Energi kinetik saja', 'Momentum dan energi kinetik', 'Energi potensial', '1', NULL, NULL, 'C', 0.765, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(57, NULL, 2, NULL, 'Sebuah bola bermassa 2 kg menumbuk dinding dengan kecepatan 5 m/s dan memantul dengan kecepatan 3 m/s. Impuls yang dialami bola adalah...', '6 N?s', '8 N?s', '12 N?s', '16 N?s', '1', NULL, NULL, 'D', 1.432, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(58, NULL, 2, NULL, 'Koefisien restitusi pada tumbukan lenting sempurna adalah...', '0', '0,5', '1', '2', '1', NULL, NULL, 'C', -0.876, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(59, NULL, 2, NULL, 'Sebuah benda bermassa 2 kg jatuh bebas dari ketinggian 5 meter. Energi potensial benda pada ketinggian 3 meter adalah... (g = 10 m/s?)', '30 J', '40 J', '60 J', '80 J', '1', NULL, NULL, 'C', 0.765, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(60, NULL, 2, NULL, 'Sebuah benda bermassa 4 kg bergerak dengan kecepatan 5 m/s. Energi kinetik benda tersebut adalah...', '25 J', '50 J', '75 J', '100 J', '1', NULL, NULL, 'B', -0.432, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(61, NULL, 2, NULL, 'Usaha yang dilakukan untuk memindahkan sebuah benda bermassa 2 kg sejauh 4 meter pada bidang datar licin dengan gaya 5 N adalah...', '10 J', '15 J', '20 J', '25 J', '1', NULL, NULL, 'C', 0.987, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(62, NULL, 2, NULL, 'Daya yang diperlukan untuk melakukan usaha 600 joule dalam waktu 2 menit adalah...', '5 watt', '10 watt', '15 watt', '20 watt', '1', NULL, NULL, 'A', 1.234, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(63, NULL, 2, NULL, 'Energi mekanik total sebuah benda yang bergerak jatuh bebas adalah...', 'Berkurang', 'Bertambah', 'Tetap', 'Nol', '1', NULL, NULL, 'C', -0.876, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(64, NULL, 2, NULL, 'Suhu 77?F jika dikonversi ke dalam skala Celsius adalah...', '15?C', '20?C', '25?C', '30?C', '1', NULL, NULL, 'C', 0.543, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(65, NULL, 2, NULL, 'Kalor yang diperlukan untuk menaikkan suhu 2 kg air dari 30?C menjadi 80?C adalah... (c air = 4200 J/kg?C)', '210.000 J', '420.000 J', '630.000 J', '840.000 J', '1', NULL, NULL, 'B', 1.876, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(66, NULL, 2, NULL, 'Peristiwa perpindahan kalor melalui zat perantara tanpa disertai perpindahan partikel disebut...', 'Konduksi', 'Konveksi', 'Radiasi', 'Sublimasi', '1', NULL, NULL, 'A', -0.234, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(67, NULL, 2, NULL, 'Banyaknya kalor yang diperlukan untuk mengubah 2 kg es menjadi air pada suhu 0?C adalah... (kalor lebur es = 334 kJ/kg)', '334 kJ', '668 kJ', '836 kJ', '1002 kJ', '1', NULL, NULL, 'B', 1.432, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(68, NULL, 2, NULL, 'Suatu zat yang memiliki kalor jenis tinggi akan mengalami perubahan suhu yang...', 'Cepat saat dipanaskan', 'Lambat saat dipanaskan', 'Tidak terpengaruh pemanasan', 'Tidak dapat ditentukan', '1', NULL, NULL, 'B', 0.876, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(69, NULL, 2, NULL, 'Tekanan hidrostatis pada dasar wadah dipengaruhi oleh...', 'Massa jenis fluida dan kedalaman', 'Luas penampang dan kedalaman', 'Volume fluida dan kedalaman', 'Massa fluida dan kedalaman', '1', NULL, NULL, 'A', -0.543, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(70, NULL, 2, NULL, 'Sebuah benda memiliki massa jenis 800 kg/m3 dimasukkan ke dalam air (? = 1000 kg/m3). Benda tersebut akan...', 'Tenggelam', 'Melayang', 'Terapung', 'Tidak dapat ditentukan', '1', NULL, NULL, 'C', 1.234, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(71, NULL, 2, NULL, 'Prinsip kerja dongkrak hidrolik berdasarkan...', 'Hukum Pascal', 'Hukum Archimedes', 'Hukum Bernoulli', 'Hukum Newton', '1', NULL, NULL, 'A', 0.987, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(72, NULL, 2, NULL, 'Gaya apung yang dialami sebuah benda dalam fluida bergantung pada...', 'Massa benda', 'Volume benda yang tercelup', 'Massa jenis benda', 'Bentuk benda', '1', NULL, NULL, 'B', -1.876, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(73, NULL, 2, NULL, 'Tekanan gauge pada suatu titik di dalam fluida adalah...', 'Tekanan total', 'Tekanan atmosfer', 'Tekanan hidrostatis', 'Selisih tekanan total dengan tekanan atmosfer', '1', NULL, NULL, 'D', 2.123, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(74, NULL, 2, NULL, 'Gelombang yang arah rambatnya sejajar dengan arah getarnya disebut gelombang...', 'Transversal', 'Longitudinal', 'Mekanik', 'Elektromagnetik', '1', NULL, NULL, 'B', 0.765, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(75, NULL, 2, NULL, 'Jika frekuensi gelombang 100 Hz dan panjang gelombangnya 2 meter, maka cepat rambat gelombang tersebut adalah...', '50 m/s', '100 m/s', '150 m/s', '200 m/s', '1', NULL, NULL, 'D', 1.543, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(76, NULL, 2, NULL, 'Peristiwa perpaduan dua gelombang yang memiliki frekuensi hampir sama disebut...', 'Interferensi', 'Difraksi', 'Pelayangan', 'Polarisasi', '1', NULL, NULL, 'C', -0.432, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(77, NULL, 2, NULL, 'Sifat gelombang yang memungkinkan terjadinya pembiasan adalah...', 'Pemantulan', 'Pembiasan', 'Interferensi', 'Difraksi', '1', NULL, NULL, 'B', 0.876, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(78, NULL, 2, NULL, 'Pada gelombang stasioner, jarak antara dua perut yang berurutan adalah...', '? ?', '? ?', '_ ?', '1 ?', '1', NULL, NULL, 'B', 1.234, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(79, NULL, 2, NULL, 'Jika sebuah benda diletakkan pada jarak 15 cm di depan cermin cekung yang fokusnya 10 cm, maka jarak bayangan yang terbentuk adalah...', '20 cm', '25 cm', '30 cm', '35 cm', '1', NULL, NULL, 'C', -0.987, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(80, NULL, 2, NULL, 'Sinar-sinar istimewa pada cermin cekung adalah...', 'Sinar datang sejajar sumbu utama dipantulkan melalui fokus', 'Sinar datang melalui fokus dipantulkan sejajar sumbu utama', 'Sinar datang melalui pusat kelengkungan dipantulkan melalui titik yang sama', 'Semua jawaban benar', '1', NULL, NULL, 'D', 1.765, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(81, NULL, 2, NULL, 'Sebuah benda diletakkan 20 cm di depan lensa cembung yang fokusnya 15 cm. Perbesaran bayangan yang terbentuk adalah...', '2 kali', '3 kali', '4 kali', '5 kali', '1', NULL, NULL, 'B', 0.543, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(82, NULL, 2, NULL, 'Cacat mata hipermetropi dapat ditolong dengan menggunakan kacamata berlensa...', 'Cekung', 'Cembung', 'Silindris', 'Bifokal', '1', NULL, NULL, 'B', -1.432, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(83, NULL, 2, NULL, 'Bayangan yang dibentuk oleh cermin datar bersifat...', 'Maya, tegak, sama besar', 'Nyata, tegak, sama besar', 'Maya, terbalik, sama besar', 'Nyata, terbalik, sama besar', '1', NULL, NULL, 'A', 0.876, 0, NULL, '2025-06-07 04:08:33', '2025-06-07 04:08:33'),
(86, NULL, NULL, NULL, 'apa itu bunga?', 'dssd', 'dssds', 'dsdfdfdsf', 'dsdsddds', 'sdfdfdsf', 'dsfdsf', '1749269359_e39e0a9d1e504a7ae0ef.jpg', 'B', 0.002, 1, 4, '2025-06-07 04:09:19', '2025-06-07 04:09:19'),
(87, NULL, 2, NULL, 'apa itu bunga?', 'dssd', 'dssds', 'dsdfdfdsf', 'dsdsddds', 'sdfdfdsf', 'dsfdsf', '1749269359_e39e0a9d1e504a7ae0ef.jpg', 'B', 0.002, 0, 4, '2025-06-07 04:09:52', '2025-06-07 04:09:52'),
(88, NULL, NULL, NULL, 'apa itu tas?', 'fddfd', 'dsfdfdsf', 'dfds,fmdkljc', 'dfdsfdsf', 'ddsccv', 'cvc', '1749269479_564ccec6d0870c562977.jpg', 'D', 0.033, 1, 4, '2025-06-07 04:11:19', '2025-06-07 04:11:19'),
(89, NULL, 3, NULL, 'apa itu bunga?', 'dssd', 'dssds', 'dsdfdfdsf', 'dsdsddds', 'sdfdfdsf', 'dsfdsf', '1749269359_e39e0a9d1e504a7ae0ef.jpg', 'B', 0.002, 0, 4, '2025-06-07 04:11:34', '2025-06-07 04:11:34'),
(90, NULL, 3, NULL, 'apa itu tas?', 'fddfd', 'dsfdfdsf', 'dfds,fmdkljc', 'dfdsfdsf', 'ddsccv', 'cvc', '1749269479_564ccec6d0870c562977.jpg', 'D', 0.033, 0, 4, '2025-06-07 04:11:34', '2025-06-07 04:11:34'),
(91, 'dsasd2', NULL, 1, '<p>Apa itu kertas?</p>\r\n', '<p>a</p>\r\n', '<p>b</p>\r\n', '<p>c</p>\r\n', '<p>d</p>\r\n', '<p>e</p>\r\n', '<p>kjjk</p>\r\n', '1750336099_6379d98700b80fb3ee7b.png', 'D', 0.500, 1, 4, '2025-06-09 17:19:57', '2025-06-20 11:51:20'),
(92, NULL, 2, NULL, 'Apa itu kertas?', 'a', 'b', 'c', 'd', 'e', 'kjjk', '1749489597_26fffa65270857ea7803.jpg', 'D', 0.500, 0, 4, '2025-06-10 13:54:11', '2025-06-10 13:54:11'),
(97, NULL, NULL, 2, '<p>Apa makanan kesukaan mu?</p>\r\n', '<p>dsfd<strong>ds</strong></p>\r\n', '<p>dfdfd<em>ffd</em>f</p>\r\n', '<p>fdsdff<u>dfddf</u></p>\r\n', '<p>dfdd<sub>ddfdf</sub></p>\r\n', '<p>fdd</p>\r\n', '<p>fdsfsdfd</p>\r\n', '1749957899_396af41431d82eedd25e.png', 'C', 0.004, 1, 4, '2025-06-15 03:24:59', '2025-06-15 03:24:59'),
(98, NULL, 3, NULL, '<p>Apa makanan kesukaan mu?</p>\r\n', '<p>dsfd<strong>ds</strong></p>\r\n', '<p>dfdfd<em>ffd</em>f</p>\r\n', '<p>fdsdff<u>dfddf</u></p>\r\n', '<p>dfdd<sub>ddfdf</sub></p>\r\n', '<p>fdd</p>\r\n', '<p>fdsfsdfd</p>\r\n', '1749957899_396af41431d82eedd25e.png', 'C', 0.004, 0, 4, '2025-06-15 03:25:27', '2025-06-15 03:25:27'),
(99, 'MAT01', 3, NULL, '<p>DFDSFS</p>\r\n', '<p>DDSF</p>\r\n', '<p>DFSSF</p>\r\n', '<p>DFSDSF</p>\r\n', '<p>DSFDSF</p>\r\n', '', '<p>DSFSDFFDS</p>\r\n', '1749969796_08d1afaeeed0eddf6e10.png', 'A', 0.001, 0, 4, '2025-06-15 06:43:16', '2025-06-15 07:36:08'),
(106, 'dsasd', 2, NULL, '<p>Apa itu kertas?</p>\r\n', '<p>a</p>\r\n', '<p>b</p>\r\n', '<p>c</p>\r\n', '<p>d</p>\r\n', '<p>e</p>\r\n', '<p>kjjk</p>\r\n', '1749489597_26fffa65270857ea7803.jpg', 'D', 0.500, 0, 1, '2025-06-19 12:14:57', '2025-06-19 12:14:57'),
(107, 'dsasd', 2, NULL, '<p>Apa itu kertas?</p>\r\n', '<p>a</p>\r\n', '<p>b</p>\r\n', '<p>c</p>\r\n', '<p>d</p>\r\n', '<p>e</p>\r\n', '<p>kjjk</p>\r\n', '1749489597_26fffa65270857ea7803.jpg', 'D', 0.500, 0, 1, '2025-06-19 12:21:22', '2025-06-19 12:21:22'),
(111, 'dsasd', 2, NULL, '<p>Apa itu kertas?</p>\r\n', '<p>a</p>\r\n', '<p>b</p>\r\n', '<p>c</p>\r\n', '<p>d</p>\r\n', '<p>e</p>\r\n', '<p>kjjk</p>\r\n', '1750336099_6379d98700b80fb3ee7b.png', 'D', 0.500, 0, 4, '2025-06-20 04:07:13', '2025-06-20 04:07:13'),
(115, 'TES123', 3, NULL, '<p>APa itu summernote?</p><p><br><br></p>', '<p><img src=\"http://localhost:8080/uploads/editor-images/editor_1750411336_68552848e07b9.png\" class=\"img-fluid\" style=\"display: block; max-width: 100%; height: auto; margin: 10px 0px;\"><br><br></p>', '<p>b</p>', '<p>c</p>', '<p>d</p>', '<p><br></p>', '<p><img src=\"http://localhost:8080/uploads/editor-images/editor_1750411326_6855283eb9aeb.png\" class=\"img-fluid\" style=\"display: block; max-width: 100%; height: auto; margin: 10px 0px;\"><br><br></p>', NULL, 'A', -0.016, 0, 4, '2025-06-20 09:21:23', '2025-06-20 09:22:19');

-- --------------------------------------------------------

--
-- Table structure for table `ujian`
--

CREATE TABLE `ujian` (
  `id_ujian` int(11) NOT NULL,
  `jenis_ujian_id` int(11) NOT NULL,
  `nama_ujian` varchar(100) NOT NULL,
  `kode_ujian` varchar(50) DEFAULT NULL,
  `deskripsi` text NOT NULL,
  `se_awal` decimal(6,4) NOT NULL DEFAULT 1.0000,
  `se_minimum` decimal(6,4) NOT NULL DEFAULT 0.2500,
  `delta_se_minimum` decimal(6,4) NOT NULL DEFAULT 0.0100,
  `maksimal_soal_tampil` int(11) DEFAULT 20,
  `durasi` time NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `kelas_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ujian`
--

INSERT INTO `ujian` (`id_ujian`, `jenis_ujian_id`, `nama_ujian`, `kode_ujian`, `deskripsi`, `se_awal`, `se_minimum`, `delta_se_minimum`, `maksimal_soal_tampil`, `durasi`, `created_at`, `updated_at`, `kelas_id`, `created_by`) VALUES
(2, 9, 'UTS Fisika Dasar', 'apaaja123', 'Tolong Kerjakan dengan sungguh-sungguh', 1.0000, 0.0100, 0.0010, 20, '01:00:00', '2025-01-08 10:58:13', '2025-06-19 18:35:53', NULL, NULL),
(3, 7, 'tes', NULL, 'ds', 1.0000, 0.2500, 0.0100, 20, '01:00:00', '2025-01-13 21:55:38', '2025-01-13 21:55:38', NULL, NULL),
(7, 7, 'UTS Fisika Expert', 'FIS123', 'SDKFDJKDLJDSJFKDJFLDSJFKL', 1.0000, 0.2500, 0.0100, 20, '01:30:00', '2025-06-19 19:35:56', '2025-06-19 19:35:56', 7, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','siswa','guru') NOT NULL DEFAULT 'siswa',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `role`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@gmail.com', '$2y$10$BzOctZLZGFMUeyGscyM8IOD6cbtRJpnMpaVZYDgl90ueKB8QFIEJu', 'admin', 'active', '2024-12-10 21:49:44', '2024-12-10 21:49:44'),
(4, 'guru', 'guru@gmail.com', '$2y$10$.68YR1N.5M2iNtJQ3A5X4uDeehyKCuVcmDxS5PtihUwsNL/YXhMFe', 'guru', 'active', '2024-12-14 19:11:43', '2024-12-14 19:11:43'),
(22, 'adam', 'adam@gmail.com', '$2y$10$GhzZGEq5ccbsAvf9VkO8pOTvA6DlreGGR/tnFe3gxv7jD4EMDg182', 'siswa', 'active', '2025-01-09 09:52:48', '2025-01-09 09:52:48'),
(24, 'shin', 'shin@gmail.com', '$2y$10$JqrroSU4SJWqiAFERrJ8iuTQ4jknxvvW6awWkxWLb96yQsrxfAYl6', 'siswa', 'active', '2025-01-10 04:53:45', '2025-01-10 04:53:45'),
(25, 'pintar', 'pintar@gmail.com', '$2y$10$6VcWAUpTuDiiSSv.srYiuuUcXm6h/YocwXI.dIe0L/MhOmC7u7AMG', 'siswa', 'active', '2025-01-10 05:42:17', '2025-01-10 05:42:17'),
(26, 'beruang', 'beruang@gmail.com', '$2y$10$XhLQPT1xMrAwWdVCZgWRseWufOJMqK35GjCuLEDPlQD37TDNOxLIm', 'siswa', 'active', '2025-01-10 19:24:49', '2025-01-10 19:24:49'),
(27, 'burung', 'burung@gmail.com', '$2y$10$NIcJcPMblRqJ4NvIX.aPru81.D5t/OwVxkza7ZjwnSSLrOGYSwyy.', 'siswa', 'active', '2025-01-10 19:25:57', '2025-01-10 19:25:57'),
(28, 'kuda', 'kuda@gmail.com', '$2y$10$3GHxvlerWNnd8UtmpOI/8uhU3YoeyeD5nZ1ulIlsl3R0HJ.L2asV6', 'siswa', 'active', '2025-01-10 23:11:29', '2025-01-10 23:11:29'),
(29, 'ayam', 'ayam@gmail.com', '$2y$10$CYVFhQDKnK/ES10Yvesy9uv4XIbq4wk6dtGAmv9SX/IJ7pNKyHYMm', 'siswa', 'active', '2025-01-10 23:30:06', '2025-01-10 23:30:06'),
(30, 'danis', 'danis@gmail.com', '$2y$10$qvbUBLVZuBmiUOZQiV14iOH9VtwhJNZUkDvGRWL7nLG.7XCUZrpXy', 'siswa', 'active', '2025-01-11 00:43:49', '2025-01-11 00:43:49'),
(31, 'keysara', 'keysara@gmai.com', '$2y$10$fiaiwtwDaJWcVQd9ZPJM7eUF2yILDp5UScPhrpckhbpHqK2giqjBu', 'siswa', 'active', '2025-01-11 00:45:42', '2025-01-11 00:45:42'),
(32, 'jess', 'jess@gmail.com', '$2y$10$B3qe2cI9HDkobDo2PQQ5N..4kJb4AOKiUlptxaeDEwmrFy.D4cjnS', 'siswa', 'active', '2025-01-11 08:21:52', '2025-01-11 08:21:52'),
(33, 'yogi', 'yogisuardi@gmail.com', '$2y$10$QF22EL7LREmla1fiHpDj..Pqq3uN1e9rl3hY6mZyMLqaTfYbN1nf6', 'siswa', 'active', '2025-05-06 02:00:28', '2025-05-06 02:00:28'),
(34, 'geral', 'geral@gmail.com', '$2y$10$uVJK8Pa4e68TL8Zmxuk.AeyiUbbhtLaiJefs6SPaI6rCAf8564UtG', 'siswa', 'active', '2025-05-08 06:41:56', '2025-05-08 06:41:56'),
(35, 'jauza', 'jauzaamalia14@gmail.com', '$2y$10$rXziiez2bRk.c515wlUWJ.2wDZbCOP4m9oSQfSDB82uP9oglklIKy', 'siswa', 'active', '2025-05-08 07:57:31', '2025-05-08 07:57:31'),
(36, 'pradesa', 'pradesa@gmail.com', '$2y$10$w.u23c2Fm2DzZOb/iwHM6eMvkcC8iVJHsXUWiAaLjiXRR26qhrRRS', 'siswa', 'active', '2025-05-19 13:44:35', '2025-05-19 13:44:35'),
(37, 'ibrahim', 'ibrahim@gmail.com', '$2y$10$PQUkdzw.L9lW9kKH8IFNYOK9z1LbNovpLkr3wGpb2.lthY.rE5I62', 'siswa', 'active', '2025-05-20 11:58:40', '2025-05-20 11:58:40'),
(38, 'jefri', 'jefri@gmail.com', '$2y$10$FgFjWr0Of61CgfE6H1DpveMs46j15ej2fpLZBKN5hFzN7UsbSivLW', 'siswa', 'active', '2025-05-20 12:37:36', '2025-05-20 12:37:36'),
(39, 'mufid', 'mufid@gmail.com', '$2y$10$rOHKoV2rsdzUg4frH2TyC.qRP5utPCwpGyfQSvL9eZvIvNa9yLkS2', 'siswa', 'active', '2025-05-21 00:31:37', '2025-05-21 00:31:37'),
(40, 'cobabaru', 'cobabaru@gmail.com', '$2y$10$k/60aJResxck1/1AfHJiCORf.io.EugqHHxBdjFdauSE5SW5suYLi', 'siswa', 'active', '2025-05-21 07:59:46', '2025-05-21 07:59:46'),
(41, 'sahrul', 'sahrul@gmail.com', '$2y$10$HYYK.xHlEorcemo3d0we8.G/tJTXs08s.91SfGMxttWY2Z33aOizu', 'siswa', 'inactive', '2025-05-26 11:13:31', '2025-05-26 12:23:33'),
(42, 'guru2', 'guruku@gmail.com', '$2y$10$mKIV/JZYZE2N3pd/LdwRmu0dh6VvYT6.LhgOdSlMtzm74hh.YclCi', 'guru', 'active', '2025-05-26 12:45:04', '2025-05-26 12:45:54'),
(43, '123001', '123001@sekolah.com', '$2y$10$izzokXyIMTBOV/wnsuQ1feutnEF5uTYo3/7HL1d34lXl4lnmJ0h.m', 'siswa', 'active', '2025-05-26 15:20:27', '2025-05-26 15:20:27'),
(44, '12300updaten', '123002@sekolah.com', '$2y$10$5.4X/gLS5UBLqINkdlyuxOqChfZw5FZNXj4hELuKWIUt7/GDVe8lC', 'siswa', 'active', '2025-05-26 15:20:27', '2025-05-26 15:20:27'),
(45, 'guru4', 'guru4@gmail.com', '$2y$10$7pgNGh4ubmJIMuE/i6wKAeGLyNH9IYUPW/OCZ.FHXLyLSRTLSX54q', 'guru', 'active', '2025-06-09 01:54:12', '2025-06-09 01:54:12'),
(46, 'guru3', 'guru3@gmail.com', '$2y$10$p5RfSp7CrCO1VGCKGz8L8OMPDX2qk/d3eoQJ1AAzojfWmbdy02HWC', 'guru', 'active', '2025-06-10 11:47:54', '2025-06-10 11:53:49'),
(47, 'guru21', 'guru21@gmail.com', '$2y$10$Zv/xLmEWNlAGRPP5ul124eDIp31d.hvRAC4CdPSnRb6w0.PGnLzmS', 'guru', 'active', '2025-06-12 15:00:22', '2025-06-12 15:00:22'),
(48, 'anaktkj', 'anaktkj@gmail.com', '$2y$10$SocJE/guWUST1Rby.FRN1.YgRH3rjr0lCRCgXCgTtLPfZ0d8YUvp6', 'siswa', 'active', '2025-06-12 15:13:38', '2025-06-12 15:13:38'),
(49, 'joko', 'joko@gmail.com', '$2y$10$ZTKUBcbtellHQL/hThOpiuOSABMax1lp.PBsJEo7OzOYPBJPUtGz.', 'siswa', 'active', '2025-06-12 15:50:38', '2025-06-12 15:50:38'),
(50, 'Mubarok', 'mubarok@gmail.com', '$2y$10$JTWQzjPstL1GDQcEPV2nXO8iiRKFeQf2fs0vp2mgB6JwTYKHiYJgW', 'siswa', 'active', '2025-06-13 00:07:10', '2025-06-13 00:07:10'),
(51, 'agus', 'agus@gmail.com', '$2y$10$G7R.DZMkeq80oRQpxv21fu9xlHRZvevgt4gzvZ0pAMHMtU0wIUWwO', 'siswa', 'active', '2025-06-15 10:39:16', '2025-06-15 10:39:16'),
(52, 'mamah', 'mamah@gmail.com', '$2y$10$2yo30DJBuYYhxG6RXu.RcuC.6zaN6yKJKgOtqCfPh4/hKgvWU4UyC', 'siswa', 'active', '2025-06-15 13:45:54', '2025-06-15 13:45:54'),
(53, 'ikanmas', 'ikanmas@gmail.com', '$2y$10$gr9dwnmtimv4GkHXcXu1geWNNFxbOXxI.Q6oIZ35CJ6do8nv6OfFS', 'siswa', 'active', '2025-06-15 18:26:54', '2025-06-15 18:26:54'),
(54, 'apip', 'apip@gmail.com', '$2y$10$kWpXxFIQnDMmpqxrZxnDcOH64IhpKgDtcgMbOeBwluYixwa0RMu.W', 'siswa', 'active', '2025-06-18 00:57:37', '2025-06-18 00:57:37'),
(55, 'siswa001', 'siswa001@sekolah.com', '$2y$10$UJNgN.Nr84t3X15skDf77uFXjiuiZPzusmV0rz4XyvfXjma5ilB7y', 'siswa', 'active', '2025-06-18 00:58:39', '2025-06-18 00:58:39'),
(56, 'siswa002', 'siswa002@sekolah.com', '$2y$10$C2HgbjOG1.fJsOljvZEFouvTyhnnM4Mp0fOck0tVk9T5y40xa0hru', 'siswa', 'active', '2025-06-18 00:58:40', '2025-06-18 00:58:40'),
(57, 'fisika', 'fisika@gmail.com', '$2y$10$sxZVuM2qIRNksDMgd.vWHOeK75E2ONpl03LB16wgt4FAL2RdRBsA6', 'siswa', 'active', '2025-06-18 01:06:29', '2025-06-18 01:06:29'),
(58, 'siswa003', 'siswa003@sekolah.com', '$2y$10$L2vQtKWSIhyQO7vZbVkvuODdUZsE0bqA/OT6AQc.SgO9FyEy6TXQG', 'siswa', 'active', '2025-06-18 02:38:00', '2025-06-18 02:38:00'),
(59, 'siswa004', 'siswa004@sekolah.com', '$2y$10$4PmVd4NK5hN5jZoGRO6sW.wXc.DhWG7ljx0evo4oQolB2LOZUAqv.', 'siswa', 'active', '2025-06-18 02:38:00', '2025-06-18 02:38:00'),
(60, 'siswa005', 'siswa005@sekolah.com', '$2y$10$n1CDehxI6rC5hvWF15p2Ce5gxj1Nxp19AB6mSFJ.VlSZX0ss5sPuG', 'siswa', 'active', '2025-06-18 02:38:00', '2025-06-18 02:38:00'),
(61, 'siswa006', 'siswa006@sekolah.com', '$2y$10$6SUVqbuLQbHq3bmE1pGEVudArunqbFzjWi8rOx8fiRV.cckqm.EYy', 'siswa', 'active', '2025-06-18 02:38:00', '2025-06-18 02:38:00'),
(62, 'siswa007', 'siswa007@sekolah.com', '$2y$10$GP8HDl7pvof0TbLXAa0i/u92z09P0A6vbGhBl9FKLZcqBRL9MzOaK', 'siswa', 'active', '2025-06-18 02:38:00', '2025-06-18 02:38:00'),
(63, 'siswa008', 'siswa008@sekolah.com', '$2y$10$RjI32X.rnVW8LnwRrK48t.nVO.crNJ0Y9.DAJDGtQZpcgdRx4bYnG', 'siswa', 'active', '2025-06-18 02:38:00', '2025-06-18 02:38:00'),
(64, 'siswa009', 'siswa009@sekolah.com', '$2y$10$pUGnO7xLYDyBD6IBldmlJOb4h25oH/2Pz8BpPEcGFXudPQtOwMFwC', 'siswa', 'active', '2025-06-18 02:38:00', '2025-06-18 02:38:00'),
(65, 'siswa010', 'siswa010@sekolah.com', '$2y$10$ZQtEvYeYXqeL7EhXGZLEpOl..x1XCojGHH8DtD9.qM8PgBwR.DE1u', 'siswa', 'active', '2025-06-18 02:38:00', '2025-06-18 02:38:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bank_ujian`
--
ALTER TABLE `bank_ujian`
  ADD PRIMARY KEY (`bank_ujian_id`),
  ADD UNIQUE KEY `unique_bank_ujian` (`kategori`,`jenis_ujian_id`,`nama_ujian`,`created_by`),
  ADD KEY `jenis_ujian_id` (`jenis_ujian_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `guru`
--
ALTER TABLE `guru`
  ADD PRIMARY KEY (`guru_id`),
  ADD KEY `guru_id` (`user_id`),
  ADD KEY `sekolah_id` (`sekolah_id`);

--
-- Indexes for table `hasil_ujian`
--
ALTER TABLE `hasil_ujian`
  ADD PRIMARY KEY (`jawaban_id`),
  ADD KEY `peserta_ujian_id` (`peserta_ujian_id`),
  ADD KEY `soal_id` (`soal_id`);

--
-- Indexes for table `jadwal_ujian`
--
ALTER TABLE `jadwal_ujian`
  ADD PRIMARY KEY (`jadwal_id`),
  ADD KEY `kelas_id` (`kelas_id`),
  ADD KEY `guru_id` (`guru_id`),
  ADD KEY `jenis_ujian_id` (`ujian_id`);

--
-- Indexes for table `jenis_ujian`
--
ALTER TABLE `jenis_ujian`
  ADD PRIMARY KEY (`jenis_ujian_id`),
  ADD KEY `kelas_id` (`kelas_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `kelas`
--
ALTER TABLE `kelas`
  ADD PRIMARY KEY (`kelas_id`),
  ADD KEY `sekolah_id` (`sekolah_id`);

--
-- Indexes for table `kelas_guru`
--
ALTER TABLE `kelas_guru`
  ADD PRIMARY KEY (`kelas_guru_id`),
  ADD KEY `guru_id` (`guru_id`),
  ADD KEY `kelas_id` (`kelas_id`);

--
-- Indexes for table `pengumuman`
--
ALTER TABLE `pengumuman`
  ADD PRIMARY KEY (`pengumuman_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `peserta_ujian`
--
ALTER TABLE `peserta_ujian`
  ADD PRIMARY KEY (`peserta_ujian_id`),
  ADD KEY `jadwal_id` (`jadwal_id`),
  ADD KEY `siswa_id` (`siswa_id`);

--
-- Indexes for table `sekolah`
--
ALTER TABLE `sekolah`
  ADD PRIMARY KEY (`sekolah_id`);

--
-- Indexes for table `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`siswa_id`),
  ADD KEY `kelas_id` (`kelas_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `soal_ujian`
--
ALTER TABLE `soal_ujian`
  ADD PRIMARY KEY (`soal_id`),
  ADD KEY `ujian_id` (`ujian_id`),
  ADD KEY `bank_ujian_id` (`bank_ujian_id`);

--
-- Indexes for table `ujian`
--
ALTER TABLE `ujian`
  ADD PRIMARY KEY (`id_ujian`),
  ADD KEY `jenis_ujian_id` (`jenis_ujian_id`),
  ADD KEY `kelas_id` (`kelas_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bank_ujian`
--
ALTER TABLE `bank_ujian`
  MODIFY `bank_ujian_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `guru`
--
ALTER TABLE `guru`
  MODIFY `guru_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `hasil_ujian`
--
ALTER TABLE `hasil_ujian`
  MODIFY `jawaban_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=497;

--
-- AUTO_INCREMENT for table `jadwal_ujian`
--
ALTER TABLE `jadwal_ujian`
  MODIFY `jadwal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `jenis_ujian`
--
ALTER TABLE `jenis_ujian`
  MODIFY `jenis_ujian_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `kelas`
--
ALTER TABLE `kelas`
  MODIFY `kelas_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `kelas_guru`
--
ALTER TABLE `kelas_guru`
  MODIFY `kelas_guru_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `pengumuman`
--
ALTER TABLE `pengumuman`
  MODIFY `pengumuman_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `peserta_ujian`
--
ALTER TABLE `peserta_ujian`
  MODIFY `peserta_ujian_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

--
-- AUTO_INCREMENT for table `sekolah`
--
ALTER TABLE `sekolah`
  MODIFY `sekolah_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `siswa`
--
ALTER TABLE `siswa`
  MODIFY `siswa_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `soal_ujian`
--
ALTER TABLE `soal_ujian`
  MODIFY `soal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;

--
-- AUTO_INCREMENT for table `ujian`
--
ALTER TABLE `ujian`
  MODIFY `id_ujian` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bank_ujian`
--
ALTER TABLE `bank_ujian`
  ADD CONSTRAINT `bank_ujian_ibfk_1` FOREIGN KEY (`jenis_ujian_id`) REFERENCES `jenis_ujian` (`jenis_ujian_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bank_ujian_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `guru`
--
ALTER TABLE `guru`
  ADD CONSTRAINT `guru_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `guru_ibfk_2` FOREIGN KEY (`sekolah_id`) REFERENCES `sekolah` (`sekolah_id`);

--
-- Constraints for table `hasil_ujian`
--
ALTER TABLE `hasil_ujian`
  ADD CONSTRAINT `hasil_ujian_ibfk_1` FOREIGN KEY (`peserta_ujian_id`) REFERENCES `peserta_ujian` (`peserta_ujian_id`),
  ADD CONSTRAINT `hasil_ujian_ibfk_2` FOREIGN KEY (`soal_id`) REFERENCES `soal_ujian` (`soal_id`);

--
-- Constraints for table `jadwal_ujian`
--
ALTER TABLE `jadwal_ujian`
  ADD CONSTRAINT `jadwal_ujian_ibfk_2` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`kelas_id`),
  ADD CONSTRAINT `jadwal_ujian_ibfk_3` FOREIGN KEY (`guru_id`) REFERENCES `guru` (`guru_id`),
  ADD CONSTRAINT `jadwal_ujian_ibfk_4` FOREIGN KEY (`ujian_id`) REFERENCES `ujian` (`id_ujian`);

--
-- Constraints for table `jenis_ujian`
--
ALTER TABLE `jenis_ujian`
  ADD CONSTRAINT `jenis_ujian_ibfk_1` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`kelas_id`),
  ADD CONSTRAINT `jenis_ujian_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `kelas`
--
ALTER TABLE `kelas`
  ADD CONSTRAINT `kelas_ibfk_1` FOREIGN KEY (`sekolah_id`) REFERENCES `sekolah` (`sekolah_id`);

--
-- Constraints for table `kelas_guru`
--
ALTER TABLE `kelas_guru`
  ADD CONSTRAINT `kelas_guru_ibfk_1` FOREIGN KEY (`guru_id`) REFERENCES `guru` (`guru_id`),
  ADD CONSTRAINT `kelas_guru_ibfk_2` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`kelas_id`);

--
-- Constraints for table `pengumuman`
--
ALTER TABLE `pengumuman`
  ADD CONSTRAINT `pengumuman_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `peserta_ujian`
--
ALTER TABLE `peserta_ujian`
  ADD CONSTRAINT `peserta_ujian_ibfk_1` FOREIGN KEY (`jadwal_id`) REFERENCES `jadwal_ujian` (`jadwal_id`),
  ADD CONSTRAINT `peserta_ujian_ibfk_2` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`siswa_id`);

--
-- Constraints for table `siswa`
--
ALTER TABLE `siswa`
  ADD CONSTRAINT `siswa_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `siswa_kelas_fk` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`kelas_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `soal_ujian`
--
ALTER TABLE `soal_ujian`
  ADD CONSTRAINT `soal_ujian_ibfk_1` FOREIGN KEY (`ujian_id`) REFERENCES `ujian` (`id_ujian`),
  ADD CONSTRAINT `soal_ujian_ibfk_2` FOREIGN KEY (`bank_ujian_id`) REFERENCES `bank_ujian` (`bank_ujian_id`) ON DELETE CASCADE;

--
-- Constraints for table `ujian`
--
ALTER TABLE `ujian`
  ADD CONSTRAINT `ujian_ibfk_1` FOREIGN KEY (`jenis_ujian_id`) REFERENCES `jenis_ujian` (`jenis_ujian_id`),
  ADD CONSTRAINT `ujian_ibfk_2` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`kelas_id`),
  ADD CONSTRAINT `ujian_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
