-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 26, 2026 at 04:39 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `chatbot_ai`
--

-- --------------------------------------------------------

--
-- Table structure for table `conversations`
--

CREATE TABLE `conversations` (
  `conversation_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `assigned_cs_id` int(11) DEFAULT NULL,
  `current_status` enum('active','waiting_cs','closed') DEFAULT 'active',
  `started_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `ended_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `conversations`
--

INSERT INTO `conversations` (`conversation_id`, `customer_id`, `assigned_cs_id`, `current_status`, `started_at`, `ended_at`, `updated_at`) VALUES
(1, 7, 3, 'waiting_cs', '2026-05-25 02:00:00', NULL, '2026-05-25 02:10:00'),
(2, 8, 4, 'active', '2026-05-25 03:15:00', NULL, '2026-05-25 03:35:00'),
(3, 9, 3, 'closed', '2026-05-24 06:00:00', '2026-05-24 06:45:00', '2026-05-24 06:45:00'),
(4, 10, NULL, 'active', '2026-05-25 08:20:00', NULL, '2026-05-25 08:22:00');

-- --------------------------------------------------------

--
-- Table structure for table `conversation_state`
--

CREATE TABLE `conversation_state` (
  `state_id` int(11) NOT NULL,
  `conversation_id` int(11) NOT NULL,
  `current_step` varchar(100) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `conversation_state`
--

INSERT INTO `conversation_state` (`state_id`, `conversation_id`, `current_step`, `updated_at`) VALUES
(1, 1, 'waiting_for_customer_service', '2026-05-25 02:10:00'),
(2, 2, 'collecting_project_detail', '2026-05-25 03:35:00'),
(3, 3, 'conversation_closed', '2026-05-24 06:45:00'),
(4, 4, 'asking_service_type', '2026-05-25 08:22:00');

-- --------------------------------------------------------

--
-- Table structure for table `customer_forms`
--

CREATE TABLE `customer_forms` (
  `form_id` int(11) NOT NULL,
  `conversation_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `project_name` varchar(150) DEFAULT NULL,
  `project_description` text DEFAULT NULL,
  `budget` decimal(12,2) DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer_forms`
--

INSERT INTO `customer_forms` (`form_id`, `conversation_id`, `customer_id`, `service_id`, `project_name`, `project_description`, `budget`, `deadline`, `created_at`) VALUES
(1, 2, 8, 2, 'Lestari Florist Website', 'Website company profile dengan halaman katalog bunga, galeri, kontak WhatsApp, dan informasi toko.', 3500000.00, '2026-06-20', '2026-05-25 03:25:00'),
(2, 3, 9, 3, 'Aplikasi Kasir Toko Budi', 'Aplikasi kasir sederhana untuk mencatat transaksi, stok barang, laporan penjualan, dan manajemen user.', 7000000.00, '2026-07-15', '2026-05-24 06:15:00'),
(3, 4, 10, 5, 'Hosting Clara Portfolio', 'Paket hosting dan domain untuk website portfolio pribadi dengan email bisnis.', 800000.00, '2026-06-05', '2026-05-25 08:25:00');

-- --------------------------------------------------------

--
-- Table structure for table `escalations`
--

CREATE TABLE `escalations` (
  `escalation_id` int(11) NOT NULL,
  `conversation_id` int(11) DEFAULT NULL,
  `from_ai` tinyint(1) DEFAULT 1,
  `assigned_cs_id` int(11) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `status` enum('open','handled','closed') DEFAULT 'open',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `escalations`
--

INSERT INTO `escalations` (`escalation_id`, `conversation_id`, `from_ai`, `assigned_cs_id`, `reason`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 3, 'Customer mengalami website down dan membutuhkan pengecekan teknis manual.', 'open', '2026-05-25 02:04:00', '2026-05-25 02:10:00'),
(2, 2, 1, 4, 'Customer sudah memberikan detail proyek website dan perlu follow-up CS.', 'handled', '2026-05-25 03:30:00', '2026-05-25 03:35:00'),
(3, 3, 1, 3, 'Customer meminta estimasi aplikasi kasir dan sudah ditangani CS.', 'closed', '2026-05-24 06:12:00', '2026-05-24 06:45:00');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedback_id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `intents`
--

CREATE TABLE `intents` (
  `intent_id` int(11) NOT NULL,
  `intent_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `intents`
--

INSERT INTO `intents` (`intent_id`, `intent_name`, `description`) VALUES
(1, 'greeting', 'Customer menyapa chatbot atau membuka percakapan.'),
(2, 'ask_service', 'Customer bertanya tentang layanan yang tersedia.'),
(3, 'ask_price', 'Customer menanyakan harga layanan.'),
(4, 'website_request', 'Customer ingin membuat website.'),
(5, 'app_request', 'Customer ingin membuat aplikasi.'),
(6, 'server_issue', 'Customer mengalami masalah server atau hosting.'),
(7, 'handoff_to_cs', 'Percakapan perlu dialihkan ke customer service.'),
(8, 'closing', 'Customer mengakhiri percakapan.');

-- --------------------------------------------------------

--
-- Table structure for table `knowledge_base`
--

CREATE TABLE `knowledge_base` (
  `kb_id` int(11) NOT NULL,
  `intent_id` int(11) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `question_pattern` text DEFAULT NULL,
  `answer` text DEFAULT NULL,
  `keywords` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `knowledge_base`
--

INSERT INTO `knowledge_base` (`kb_id`, `intent_id`, `category`, `question_pattern`, `answer`, `keywords`, `updated_at`) VALUES
(1, 1, 'general', 'halo|hai|selamat pagi|selamat siang', 'Halo, selamat datang di Disty Teknologi. Ada yang bisa kami bantu?', 'halo,hai,salam,selamat pagi,selamat siang', '2026-05-01 02:00:00'),
(2, 2, 'services', 'layanan apa saja|jasa apa saja|bisa bantu apa', 'Kami menyediakan konsultasi IT, pembuatan website, pembuatan aplikasi, perbaikan server, serta hosting dan domain.', 'layanan,jasa,website,aplikasi,server,hosting', '2026-05-01 02:05:00'),
(3, 3, 'pricing', 'berapa harga|biaya|price|budget', 'Harga bergantung pada kebutuhan proyek. Website mulai dari Rp2.500.000, aplikasi mulai dari Rp5.000.000, dan perbaikan server mulai dari Rp750.000.', 'harga,biaya,price,budget,tarif', '2026-05-01 02:10:00'),
(4, 4, 'website', 'buat website|website company profile|landing page', 'Baik, untuk pembuatan website kami perlu mengetahui nama proyek, fitur yang dibutuhkan, budget, dan deadline.', 'website,company profile,landing page,katalog', '2026-05-01 02:15:00'),
(5, 6, 'server', 'server error|website down|database error|hosting bermasalah', 'Untuk masalah server, mohon kirimkan detail error, akses hosting bila diperlukan, dan waktu terakhir server normal.', 'server,error,hosting,database,down,ssl', '2026-05-01 02:20:00');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `conversation_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `intent_id` int(11) DEFAULT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`message_id`, `conversation_id`, `sender_id`, `intent_id`, `content`, `created_at`, `is_deleted`, `deleted_at`) VALUES
(1, 1, 7, 1, 'Halo, saya butuh bantuan.', '2026-05-25 02:00:00', 0, NULL),
(2, 1, 1, 1, 'Halo, selamat datang di Disty Teknologi. Ada yang bisa kami bantu?', '2026-05-25 02:00:10', 0, NULL),
(3, 1, 7, 6, 'Website saya tidak bisa dibuka sejak pagi.', '2026-05-25 02:02:00', 0, NULL),
(4, 1, 1, 7, 'Baik, masalah server akan kami teruskan ke customer service.', '2026-05-25 02:03:00', 0, NULL),
(5, 2, 8, 4, 'Saya ingin membuat website company profile.', '2026-05-25 03:15:00', 0, NULL),
(6, 2, 1, 4, 'Baik, boleh informasikan nama proyek, fitur yang diinginkan, budget, dan deadline?', '2026-05-25 03:15:30', 0, NULL),
(7, 2, 8, 4, 'Nama proyeknya Lestari Florist. Butuh halaman katalog, kontak WhatsApp, dan galeri.', '2026-05-25 03:18:00', 0, NULL),
(8, 2, 4, NULL, 'Halo Ibu Maria, saya Gus dari tim Disty. Saya akan bantu proses kebutuhan websitenya.', '2026-05-25 03:35:00', 0, NULL),
(9, 3, 9, 3, 'Berapa biaya pembuatan aplikasi kasir?', '2026-05-24 06:00:00', 0, NULL),
(10, 3, 1, 3, 'Untuk aplikasi, harga mulai dari Rp5.000.000 tergantung fitur dan kompleksitas.', '2026-05-24 06:01:00', 0, NULL),
(11, 3, 3, NULL, 'Estimasi awal untuk aplikasi kasir sederhana sekitar 30 hari pengerjaan.', '2026-05-24 06:20:00', 0, NULL),
(12, 3, 9, 8, 'Baik, terima kasih. Saya akan diskusikan dulu.', '2026-05-24 06:43:00', 0, NULL),
(13, 4, 10, 2, 'Saya mau tahu layanan hosting dan domain.', '2026-05-25 08:20:00', 0, NULL),
(14, 4, 1, 2, 'Kami menyediakan layanan hosting, domain, email bisnis, dan deployment website.', '2026-05-25 08:20:30', 0, NULL),
(15, 4, 10, 3, 'Kalau untuk domain dan hosting setahun berapa?', '2026-05-25 08:22:00', 0, NULL),
(16, 2, 8, NULL, 'Pesan salah ketik ini dihapus.', '2026-05-25 03:19:00', 1, '2026-05-25 03:19:30');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `service_id` int(11) NOT NULL,
  `service_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price_start` decimal(12,2) DEFAULT NULL,
  `estimated_duration` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`service_id`, `service_name`, `description`, `price_start`, `estimated_duration`) VALUES
(1, 'Konsultasi IT', 'Konsultasi kebutuhan website, aplikasi, server, dan sistem digital.', 150000.00, '1-2 hari'),
(2, 'Pembuatan Website', 'Pembuatan website company profile, landing page, katalog, dan sistem informasi.', 2500000.00, '14-30 hari'),
(3, 'Pembuatan Aplikasi', 'Pengembangan aplikasi web atau mobile sesuai kebutuhan bisnis.', 5000000.00, '30-60 hari'),
(4, 'Perbaikan Server', 'Troubleshooting server, hosting, database, SSL, dan performa website.', 750000.00, '1-7 hari'),
(5, 'Hosting dan Domain', 'Pendaftaran domain, setup hosting, email bisnis, dan deployment website.', 500000.00, '1-3 hari');

-- --------------------------------------------------------

--
-- Table structure for table `service_requests`
--

CREATE TABLE `service_requests` (
  `request_id` int(11) NOT NULL,
  `conversation_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `request_status` enum('pending','processing','done','cancelled') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_requests`
--

INSERT INTO `service_requests` (`request_id`, `conversation_id`, `customer_id`, `service_id`, `request_status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 7, 4, 'pending', 'Customer melaporkan website tidak bisa dibuka sejak pagi. Perlu pengecekan server dan hosting.', '2026-05-25 02:05:00', '2026-05-25 02:10:00'),
(2, 2, 8, 2, 'processing', 'Customer ingin membuat website company profile untuk Lestari Florist dengan katalog, galeri, dan kontak WhatsApp.', '2026-05-25 03:20:00', '2026-05-25 03:35:00'),
(3, 3, 9, 3, 'done', 'Customer bertanya estimasi aplikasi kasir. Informasi awal sudah diberikan oleh CS.', '2026-05-24 06:10:00', '2026-05-24 06:45:00'),
(4, 4, 10, 5, 'pending', 'Customer menanyakan paket hosting dan domain tahunan.', '2026-05-25 08:23:00', '2026-05-25 08:23:00');

-- --------------------------------------------------------

--
-- Table structure for table `system_logs`
--

CREATE TABLE `system_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `conversation_id` int(11) DEFAULT NULL,
  `event_type` varchar(100) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_logs`
--

INSERT INTO `system_logs` (`log_id`, `user_id`, `conversation_id`, `event_type`, `message`, `created_at`) VALUES
(1, 1, 1, 'bot_reply', 'AI membalas greeting customer Andi.', '2026-05-25 02:00:10'),
(2, 1, 1, 'escalation_created', 'AI membuat escalation untuk masalah server customer Andi.', '2026-05-25 02:04:00'),
(3, 3, 1, 'cs_assigned', 'John Yono ditugaskan untuk menangani conversation #1.', '2026-05-25 02:10:00'),
(4, 1, 2, 'form_requested', 'AI meminta detail proyek website kepada Maria.', '2026-05-25 03:15:30'),
(5, 4, 2, 'cs_reply', 'Gus Aritonang membalas conversation Maria.', '2026-05-25 03:35:00'),
(6, 3, 3, 'conversation_closed', 'Conversation Budi ditutup setelah estimasi aplikasi diberikan.', '2026-05-24 06:45:00'),
(7, NULL, NULL, 'system_health', 'Dummy seed database berhasil dijalankan.', '2026-05-25 09:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `role` enum('customer','ai','cs','admin') NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `availability_status` enum('available','unavailable','busy') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `email`, `phone`, `password_hash`, `role`, `status`, `availability_status`, `created_at`) VALUES
(1, 'Disty AI Assistant', 'ai@distyteknologi.test', '0000000000', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ai', 'active', 'available', '2026-05-01 01:00:00'),
(2, 'Admin Disty', 'admin@distyteknologi.test', '081200000001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active', 'available', '2026-05-01 01:10:00'),
(3, 'John Yono', 'john.yono@distyteknologi.test', '081200000002', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cs', 'active', 'available', '2026-05-02 02:00:00'),
(4, 'Gus Aritonang', 'gus.aritonang@distyteknologi.test', '081200000003', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cs', 'active', 'available', '2026-05-02 02:10:00'),
(5, 'Byron Yunus', 'byron.yunus@distyteknologi.test', '081200000004', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cs', 'inactive', 'available', '2026-05-02 02:20:00'),
(6, 'Selina Hutabarat', 'selina.hutabarat@distyteknologi.test', '081200000005', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cs', 'active', 'available', '2026-05-02 02:30:00'),
(7, 'Andi Saputra', 'andi.customer@test.com', '6283116721112', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', 'active', 'available', '2026-05-10 03:00:00'),
(8, 'Maria Lestari', 'maria.customer@test.com', '6281281799122', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', 'active', 'available', '2026-05-12 04:30:00'),
(9, 'Budi Pratama', 'budi.customer@test.com', '6285733124500', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', 'active', 'available', '2026-05-15 07:20:00'),
(10, 'Clara Wijaya', 'clara.customer@test.com', '6289644556677', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', 'active', 'available', '2026-05-20 09:45:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`conversation_id`),
  ADD KEY `idx_conversations_customer` (`customer_id`),
  ADD KEY `idx_conversations_cs` (`assigned_cs_id`),
  ADD KEY `idx_conversations_status` (`current_status`);

--
-- Indexes for table `conversation_state`
--
ALTER TABLE `conversation_state`
  ADD PRIMARY KEY (`state_id`),
  ADD KEY `idx_conv_state_conversation` (`conversation_id`);

--
-- Indexes for table `customer_forms`
--
ALTER TABLE `customer_forms`
  ADD PRIMARY KEY (`form_id`),
  ADD KEY `idx_customer_forms_conv` (`conversation_id`),
  ADD KEY `idx_customer_forms_customer` (`customer_id`),
  ADD KEY `idx_customer_forms_service` (`service_id`);

--
-- Indexes for table `escalations`
--
ALTER TABLE `escalations`
  ADD PRIMARY KEY (`escalation_id`),
  ADD KEY `idx_escalations_conversation` (`conversation_id`),
  ADD KEY `idx_escalations_cs` (`assigned_cs_id`),
  ADD KEY `idx_escalations_status` (`status`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `idx_feedback_message` (`message_id`);

--
-- Indexes for table `intents`
--
ALTER TABLE `intents`
  ADD PRIMARY KEY (`intent_id`);

--
-- Indexes for table `knowledge_base`
--
ALTER TABLE `knowledge_base`
  ADD PRIMARY KEY (`kb_id`),
  ADD KEY `idx_knowledge_base_intent` (`intent_id`),
  ADD KEY `idx_knowledge_base_category` (`category`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `idx_messages_conversation` (`conversation_id`),
  ADD KEY `idx_messages_sender` (`sender_id`),
  ADD KEY `idx_messages_intent` (`intent_id`),
  ADD KEY `idx_messages_is_deleted` (`is_deleted`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`service_id`);

--
-- Indexes for table `service_requests`
--
ALTER TABLE `service_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `idx_service_requests_conv` (`conversation_id`),
  ADD KEY `idx_service_requests_cust` (`customer_id`),
  ADD KEY `idx_service_requests_service` (`service_id`),
  ADD KEY `idx_service_requests_status` (`request_status`);

--
-- Indexes for table `system_logs`
--
ALTER TABLE `system_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_system_logs_user` (`user_id`),
  ADD KEY `idx_system_logs_conversation` (`conversation_id`),
  ADD KEY `idx_system_logs_event_type` (`event_type`),
  ADD KEY `idx_system_logs_created_at` (`created_at`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `conversations`
--
ALTER TABLE `conversations`
  MODIFY `conversation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `conversation_state`
--
ALTER TABLE `conversation_state`
  MODIFY `state_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `customer_forms`
--
ALTER TABLE `customer_forms`
  MODIFY `form_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `escalations`
--
ALTER TABLE `escalations`
  MODIFY `escalation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `intents`
--
ALTER TABLE `intents`
  MODIFY `intent_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `knowledge_base`
--
ALTER TABLE `knowledge_base`
  MODIFY `kb_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `service_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `service_requests`
--
ALTER TABLE `service_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `system_logs`
--
ALTER TABLE `system_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `conversations`
--
ALTER TABLE `conversations`
  ADD CONSTRAINT `conversations_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `conversations_ibfk_2` FOREIGN KEY (`assigned_cs_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `fk_conversations_assigned_cs` FOREIGN KEY (`assigned_cs_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_conversations_customer` FOREIGN KEY (`customer_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `conversation_state`
--
ALTER TABLE `conversation_state`
  ADD CONSTRAINT `conversation_state_ibfk_1` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`conversation_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_conversation_state_conversation` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`conversation_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `customer_forms`
--
ALTER TABLE `customer_forms`
  ADD CONSTRAINT `customer_forms_ibfk_1` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`conversation_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `customer_forms_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `customer_forms_ibfk_3` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`),
  ADD CONSTRAINT `fk_customer_forms_conversation` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`conversation_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_customer_forms_customer` FOREIGN KEY (`customer_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_customer_forms_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `escalations`
--
ALTER TABLE `escalations`
  ADD CONSTRAINT `escalations_ibfk_1` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`conversation_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `escalations_ibfk_2` FOREIGN KEY (`assigned_cs_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `fk_escalations_assigned_cs` FOREIGN KEY (`assigned_cs_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_escalations_conversation` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`conversation_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `messages` (`message_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_feedback_message` FOREIGN KEY (`message_id`) REFERENCES `messages` (`message_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `knowledge_base`
--
ALTER TABLE `knowledge_base`
  ADD CONSTRAINT `fk_kb_intent` FOREIGN KEY (`intent_id`) REFERENCES `intents` (`intent_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_knowledge_base_intent` FOREIGN KEY (`intent_id`) REFERENCES `intents` (`intent_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `fk_messages_conversation` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`conversation_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_messages_intent` FOREIGN KEY (`intent_id`) REFERENCES `intents` (`intent_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_messages_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`conversation_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `messages_ibfk_3` FOREIGN KEY (`intent_id`) REFERENCES `intents` (`intent_id`);

--
-- Constraints for table `service_requests`
--
ALTER TABLE `service_requests`
  ADD CONSTRAINT `fk_service_requests_conversation` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`conversation_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_service_requests_customer` FOREIGN KEY (`customer_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_service_requests_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `service_requests_ibfk_1` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`conversation_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `service_requests_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `service_requests_ibfk_3` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`);

--
-- Constraints for table `system_logs`
--
ALTER TABLE `system_logs`
  ADD CONSTRAINT `fk_logs_conversation` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`conversation_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_system_logs_conversation` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`conversation_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_system_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
