-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 30, 2026 at 05:09 PM
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
-- Database: `crop_insurance_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `module` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `description`, `ip_address`, `user_agent`, `created_at`) VALUES
(63, 1, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-18 22:46:40'),
(64, NULL, 'register', 'auth', 'New farmer registered.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-18 22:54:05'),
(65, NULL, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-18 22:54:19'),
(66, NULL, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-18 22:54:43'),
(67, NULL, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-18 22:54:48'),
(68, NULL, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-18 23:01:04'),
(69, NULL, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-18 23:01:14'),
(70, NULL, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-18 23:01:50'),
(71, NULL, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-18 23:02:00'),
(72, NULL, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-18 23:02:37'),
(73, NULL, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-18 23:02:42'),
(74, NULL, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-18 23:05:55'),
(75, NULL, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-18 23:05:57'),
(76, NULL, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-18 23:06:20'),
(77, NULL, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-18 23:06:23'),
(78, NULL, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-18 23:25:26'),
(79, NULL, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-18 23:26:15'),
(80, NULL, 'register', 'auth', 'New farmer registered.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-18 23:27:45'),
(81, 1, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-18 23:28:53'),
(82, 1, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-18 23:29:10'),
(83, NULL, 'register', 'auth', 'New farmer registered.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-27 23:53:04'),
(84, NULL, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-27 23:53:10'),
(85, NULL, 'forgot_password', 'auth', 'Password reset requested.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-27 23:53:28'),
(86, NULL, 'reset_password', 'auth', 'Password was reset.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-27 23:54:23'),
(87, NULL, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-27 23:54:41'),
(88, NULL, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-27 23:56:27'),
(89, NULL, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-28 00:04:10'),
(90, NULL, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-28 00:12:40'),
(91, NULL, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-28 00:14:45'),
(92, NULL, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-28 00:15:25'),
(93, NULL, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-28 00:18:46'),
(94, NULL, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-28 00:18:48'),
(95, NULL, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-28 00:19:07'),
(96, NULL, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-28 00:19:09'),
(97, 1, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-28 00:20:03'),
(98, 1, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-28 00:20:07'),
(99, NULL, 'register', 'auth', 'New farmer registered.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 19:48:07'),
(100, NULL, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 19:48:20'),
(101, NULL, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 19:48:36'),
(102, NULL, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 19:48:40'),
(103, 1, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 19:48:54'),
(104, 1, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 19:48:56'),
(105, NULL, 'register', 'auth', 'New farmer registered.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 19:50:35'),
(106, NULL, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 19:50:48'),
(107, NULL, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 19:51:09'),
(108, NULL, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 19:51:12'),
(109, 1, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 19:51:41'),
(110, 1, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 19:51:50'),
(111, NULL, 'forgot_password', 'auth', 'Password reset requested.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 21:01:34'),
(112, NULL, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 21:03:58'),
(113, NULL, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 21:04:02'),
(114, NULL, 'register', 'auth', 'New farmer registered.', '::1', 'curl/8.7.1', '2026-07-13 21:16:29'),
(115, NULL, 'register', 'auth', 'New farmer registered.', '::1', 'curl/8.7.1', '2026-07-13 21:17:50'),
(116, NULL, 'login', 'auth', 'User logged in.', '::1', 'curl/8.7.1', '2026-07-13 21:17:51'),
(117, NULL, 'reset_password', 'auth', 'Password was reset via security question.', '::1', 'curl/8.7.1', '2026-07-13 21:19:16'),
(118, NULL, 'login', 'auth', 'User logged in.', '::1', 'curl/8.7.1', '2026-07-13 21:19:16'),
(119, NULL, 'login', 'auth', 'User logged in.', '::1', 'curl/8.7.1', '2026-07-13 21:33:27'),
(120, NULL, 'set_security_question', 'auth', 'Security question set/updated.', '::1', 'curl/8.7.1', '2026-07-13 21:36:51'),
(121, NULL, 'reset_password', 'auth', 'Password was reset via security question.', '::1', 'curl/8.7.1', '2026-07-13 21:36:53'),
(122, NULL, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 21:40:02'),
(123, NULL, 'set_security_question', 'auth', 'Security question set/updated.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 21:40:23'),
(124, NULL, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 21:40:25'),
(125, NULL, 'reset_password', 'auth', 'Password was reset via security question.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 21:40:59'),
(126, NULL, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 21:41:17'),
(127, NULL, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 21:41:20'),
(128, NULL, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 21:42:49'),
(129, NULL, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 21:42:51'),
(130, NULL, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 21:49:00'),
(131, NULL, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-13 21:49:03'),
(132, 1, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 16:50:27'),
(133, 1, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-14 19:54:29'),
(134, 1, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-15 20:45:41'),
(135, 1, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-16 01:40:58'),
(136, 1, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-16 01:42:22'),
(137, 1, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-16 01:42:40'),
(138, 1, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-16 01:43:03'),
(139, 1, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-16 01:46:34'),
(140, 1, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 22:56:05'),
(141, 1, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 22:56:33'),
(142, 1, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 22:56:45'),
(143, NULL, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 22:57:25'),
(144, 1, 'create_user', 'users', 'Admin created user #16', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 23:30:54'),
(145, 1, 'update_user_status', 'users', 'Set user #16 status to inactive', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 23:31:00'),
(146, 1, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 23:31:03'),
(147, 1, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 23:31:46'),
(148, 1, 'update_user_status', 'users', 'Set user #16 status to active', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 23:31:54'),
(149, 1, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 23:31:59'),
(150, NULL, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 23:32:13'),
(151, NULL, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-21 23:32:28'),
(152, 1, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-22 20:13:29'),
(153, 1, 'deactivate_user', 'users', 'Deactivated user #16', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-22 20:13:39'),
(154, 1, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-22 20:14:34'),
(155, 1, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-27 21:57:10'),
(156, 1, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-27 21:58:13'),
(157, 1, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-28 20:00:05'),
(158, NULL, 'reset_password', 'auth', 'Password was reset via security question.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-28 20:02:51'),
(159, NULL, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-28 20:02:59'),
(160, NULL, 'create_farm', 'farms', 'Created farm #14', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-28 20:06:09'),
(161, NULL, 'create_policy', 'policies', 'Submitted policy #14', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-28 20:06:09'),
(162, NULL, 'upload_policy_doc', 'policies', 'Uploaded damage_photo document for policy #14', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-28 20:06:09'),
(163, NULL, 'upload_policy_doc', 'policies', 'Uploaded damage_photo document for policy #14', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-28 20:06:09'),
(164, NULL, 'upload_policy_doc', 'policies', 'Uploaded damage_photo document for policy #14', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-28 20:06:09'),
(165, NULL, 'upload_policy_doc', 'policies', 'Uploaded damage_photo document for policy #14', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-28 20:06:09'),
(166, NULL, 'upload_policy_doc', 'policies', 'Uploaded damage_photo document for policy #14', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-28 20:06:09'),
(167, NULL, 'upload_policy_doc', 'policies', 'Uploaded valid_id document for policy #14', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-28 20:06:09'),
(168, 1, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-28 20:13:22'),
(169, 1, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-28 20:13:36'),
(170, NULL, 'register', 'auth', 'New farmer registered (pending approval).', '::1', 'curl/8.7.1', '2026-07-28 20:30:44'),
(171, NULL, 'login', 'auth', 'User logged in.', '::1', 'curl/8.7.1', '2026-07-28 20:31:00'),
(172, NULL, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-28 20:36:45'),
(173, NULL, 'register', 'auth', 'New farmer registered (pending approval).', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-28 20:37:42'),
(174, 1, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-28 20:46:45'),
(175, 1, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 23:32:22'),
(176, 1, 'update_user_status', 'users', 'Set user #18 status to active', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 23:34:12'),
(177, NULL, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 23:34:18'),
(178, NULL, 'create_farm', 'farms', 'Created farm #15', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 23:38:39'),
(179, NULL, 'create_policy', 'policies', 'Submitted policy #15', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 23:38:39'),
(180, NULL, 'upload_policy_doc', 'policies', 'Uploaded damage_photo document for policy #15', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 23:38:39'),
(181, NULL, 'upload_policy_doc', 'policies', 'Uploaded damage_photo document for policy #15', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 23:38:39'),
(182, NULL, 'upload_policy_doc', 'policies', 'Uploaded damage_photo document for policy #15', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 23:38:40'),
(183, NULL, 'upload_policy_doc', 'policies', 'Uploaded damage_photo document for policy #15', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 23:38:40'),
(184, NULL, 'upload_policy_doc', 'policies', 'Uploaded damage_photo document for policy #15', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 23:38:40'),
(185, NULL, 'upload_policy_doc', 'policies', 'Uploaded valid_id document for policy #15', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 23:38:40'),
(186, 1, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-29 23:40:43'),
(187, 1, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-07-31 18:12:01'),
(188, 1, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-07-31 21:52:34'),
(189, 1, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-07-31 21:52:52'),
(190, 1, 'create_user', 'users', 'Admin created user #19', '::1', 'curl/8.7.1', '2026-07-31 22:24:01'),
(191, NULL, 'login', 'auth', 'User logged in.', '::1', 'curl/8.7.1', '2026-07-31 22:24:26'),
(192, NULL, 'change_password', 'auth', 'Password changed by user.', '::1', 'curl/8.7.1', '2026-07-31 22:24:34'),
(193, 1, 'create_user', 'users', 'Admin created user #20', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-07-31 22:27:18'),
(194, 1, 'create_user', 'users', 'Admin created user #21', '::1', 'curl/8.7.1', '2026-07-31 22:31:44'),
(195, 1, 'create_user', 'users', 'Admin created user #22', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-07-31 22:32:58'),
(196, 1, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-07-31 22:34:20'),
(197, 1, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-07-31 22:34:33'),
(198, 1, 'create_user', 'users', 'Admin created user #23', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-07-31 22:39:26'),
(199, 1, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-04 20:29:08'),
(200, 1, 'create_user', 'users', 'Admin created user #24', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-04 20:33:42'),
(201, NULL, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-04 20:33:57'),
(202, 1, 'create_user', 'users', 'Admin created user #25', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-04 20:51:56'),
(204, NULL, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-04 20:52:24'),
(205, 1, 'create_user', 'users', 'Admin created user #26', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-04 21:03:21'),
(207, 26, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-04 21:03:46'),
(208, 26, 'change_password', 'auth', 'Password changed by user.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-04 21:04:37'),
(209, 1, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 21:41:17'),
(210, 1, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 21:52:52'),
(211, 26, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 21:53:05'),
(212, 26, 'create_farm', 'farms', 'Created farm #16', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 21:56:27'),
(213, 26, 'create_policy', 'policies', 'Submitted policy #16', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 21:56:27'),
(214, 26, 'upload_policy_doc', 'policies', 'Uploaded damage_photo document for policy #16', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 21:56:28'),
(215, 26, 'upload_policy_doc', 'policies', 'Uploaded damage_photo document for policy #16', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 21:56:28'),
(216, 26, 'upload_policy_doc', 'policies', 'Uploaded damage_photo document for policy #16', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 21:56:28'),
(217, 26, 'upload_policy_doc', 'policies', 'Uploaded damage_photo document for policy #16', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 21:56:28'),
(218, 26, 'upload_policy_doc', 'policies', 'Uploaded damage_photo document for policy #16', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 21:56:28'),
(219, 26, 'upload_policy_doc', 'policies', 'Uploaded valid_id document for policy #16', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 21:56:28'),
(220, 1, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 21:57:01'),
(221, 1, 'review_policy', 'policies', 'Marked policy #16 as under review', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 22:03:24'),
(222, 1, 'update_policy', 'policies', 'Updated details for policy #16', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 22:03:30'),
(223, 1, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-14 20:14:03'),
(224, 1, 'review_policy', 'policies', 'Marked policy #16 as under review', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-14 20:26:12'),
(225, 1, 'update_policy', 'policies', 'Updated details for policy #16', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-14 20:26:19'),
(226, 1, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 18:24:24'),
(227, 1, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 23:53:26'),
(228, 26, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 23:53:39'),
(229, 1, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 23:54:09'),
(230, 1, 'review_policy', 'policies', 'Marked policy #16 as under review', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 23:55:00'),
(231, 1, 'update_policy', 'policies', 'Updated details for policy #16', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 23:55:03'),
(232, 1, 'review_policy', 'policies', 'Marked policy #16 as under review', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 23:57:00'),
(233, 1, 'approve_policy', 'policies', 'Approved policy #16', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 23:58:09'),
(234, 1, 'update_policy', 'policies', 'Updated details for policy #16', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 23:58:17'),
(235, 1, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 06:48:31'),
(236, 1, 'review_policy', 'policies', 'Marked policy #16 as under review', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 06:48:50'),
(237, 1, 'update_policy', 'policies', 'Updated details for policy #16', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 06:48:55'),
(238, 1, 'review_policy', 'policies', 'Marked policy #16 as under review', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 06:50:38'),
(239, 1, 'approve_policy', 'policies', 'Approved policy #16', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 06:51:20'),
(240, 1, 'update_policy', 'policies', 'Updated details for policy #16', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 06:51:26'),
(241, 26, 'submit_claim', 'claims', 'Submitted claim #4', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 06:52:34'),
(242, 26, 'upload_claim_doc', 'claims', 'Uploaded document for claim #4', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 06:52:34'),
(243, 1, 'update_claim_status', 'claims', 'Set claim #4 to approved', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 06:53:39'),
(244, 26, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 06:54:31'),
(245, 26, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-21 20:38:52'),
(246, 26, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-21 20:41:56'),
(247, 26, 'reset_password', 'auth', 'Password was reset via security question.', '::1', NULL, '2026-08-21 20:47:11'),
(248, 26, 'login', 'auth', 'User logged in.', '::1', NULL, '2026-08-21 20:47:14'),
(249, 26, 'request_reset_otp', 'auth', 'Password reset OTP requested.', '::1', NULL, '2026-08-21 20:52:20'),
(250, 26, 'reset_password_otp', 'auth', 'Password reset successfully via OTP.', '::1', NULL, '2026-08-21 20:52:29'),
(251, 26, 'login', 'auth', 'User logged in.', '::1', NULL, '2026-08-21 20:52:38'),
(252, 26, 'request_reset_otp', 'auth', 'Password reset OTP requested.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-21 20:56:26'),
(253, 26, 'request_reset_otp', 'auth', 'Password reset OTP requested.', '::1', NULL, '2026-08-21 20:58:55'),
(254, 26, 'verify_reset_otp', 'auth', 'Password reset OTP successfully verified.', '::1', NULL, '2026-08-21 20:59:08'),
(255, 26, 'reset_password_completed', 'auth', 'Password reset successfully.', '::1', NULL, '2026-08-21 20:59:13'),
(256, 26, 'login', 'auth', 'User logged in.', '::1', NULL, '2026-08-21 20:59:17'),
(257, 1, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-21 21:17:36'),
(258, 1, 'review_policy', 'policies', 'Marked policy #16 as under review', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-21 21:17:47'),
(259, 1, 'update_claim_status', 'claims', 'Set claim #4 to approved', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-21 21:20:00'),
(260, 1, 'update_policy', 'policies', 'Updated details for policy #16', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-21 21:26:34'),
(261, 1, 'review_policy', 'policies', 'Marked policy #16 as under review', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-21 21:48:12'),
(262, 1, 'update_user', 'users', 'Updated user #26', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-21 22:08:49'),
(263, 1, 'update_policy', 'policies', 'Updated details for policy #16', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-21 22:09:06'),
(264, 1, 'review_policy', 'policies', 'Marked policy #16 as under review', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-21 22:14:20'),
(265, 1, 'update_policy', 'policies', 'Updated details for policy #16', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-21 22:14:34'),
(266, 1, 'reject_policy', 'policies', 'Rejected policy #16', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-21 22:16:44'),
(267, 1, 'update_policy', 'policies', 'Updated details for policy #16', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-21 22:17:14'),
(268, 1, 'update_policy', 'policies', 'Updated details for policy #16', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-21 22:17:25'),
(269, 1, 'update_policy', 'policies', 'Updated details for policy #16', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-21 22:17:35'),
(270, 1, 'review_policy', 'policies', 'Marked policy #16 as under review', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-21 22:19:28'),
(271, 1, 'update_policy', 'policies', 'Updated details for policy #16', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-21 22:19:55'),
(272, 1, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-21 22:40:11'),
(273, 1, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-22 10:07:04'),
(274, 1, 'update_user', 'users', 'Updated user #26', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-22 10:07:26'),
(275, 1, 'approve_policy', 'policies', 'Approved policy #16', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-22 10:07:33'),
(276, 1, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-25 01:33:07'),
(277, 1, 'update_user', 'users', 'Updated user #1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-25 01:33:30'),
(278, 1, 'update_user', 'users', 'Updated user #1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-25 01:33:53'),
(279, 1, 'create_user', 'users', 'Admin created user #27', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-25 01:35:12'),
(280, 1, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-25 01:35:35'),
(281, 27, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-25 01:35:57'),
(282, 27, 'change_password', 'auth', 'Password changed by user.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-25 01:37:12'),
(283, 27, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-25 01:37:54'),
(284, 27, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-25 01:38:12'),
(285, 27, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-25 01:39:02'),
(286, 1, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-25 01:43:18'),
(287, 1, 'resend_sms', 'sms_logs', 'Resent SMS log #5 to 639169751409', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-25 01:43:39'),
(288, 1, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-25 01:43:58'),
(289, 27, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-25 01:44:22'),
(290, 27, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-25 01:48:20'),
(291, 1, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-30 19:46:37'),
(292, 1, 'logout', 'auth', 'User logged out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-30 20:21:35'),
(293, 1, 'login', 'auth', 'User logged in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-30 23:08:35');

-- --------------------------------------------------------

--
-- Table structure for table `claims`
--

CREATE TABLE `claims` (
  `id` int(10) UNSIGNED NOT NULL,
  `claim_number` varchar(50) NOT NULL,
  `policy_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `incident_type` varchar(150) NOT NULL,
  `incident_date` date NOT NULL,
  `description` text NOT NULL,
  `estimated_loss` decimal(15,2) NOT NULL,
  `approved_amount` decimal(15,2) DEFAULT NULL,
  `status` enum('submitted','under_review','approved','rejected','paid') NOT NULL DEFAULT 'submitted',
  `reviewed_by` int(10) UNSIGNED DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `claims`
--

INSERT INTO `claims` (`id`, `claim_number`, `policy_id`, `user_id`, `incident_type`, `incident_date`, `description`, `estimated_loss`, `approved_amount`, `status`, `reviewed_by`, `reviewed_at`, `remarks`, `created_at`, `updated_at`) VALUES
(4, 'CLM-2026-000001', 16, 26, 'Flood', '2026-08-08', 'Permi nasisingo taypoon', 17500.00, 17500.00, 'approved', 1, '2026-08-21 21:20:00', '', '2026-08-19 06:52:34', '2026-08-21 21:20:00');

-- --------------------------------------------------------

--
-- Table structure for table `claim_documents`
--

CREATE TABLE `claim_documents` (
  `id` int(10) UNSIGNED NOT NULL,
  `claim_id` int(10) UNSIGNED NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `file_size` int(10) UNSIGNED DEFAULT NULL COMMENT 'Size in bytes',
  `uploaded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `claim_documents`
--

INSERT INTO `claim_documents` (`id`, `claim_id`, `file_name`, `file_path`, `file_type`, `file_size`, `uploaded_at`) VALUES
(2, 4, 'file_6a84e2327edfc1.81765159.jpg', 'claims/file_6a84e2327edfc1.81765159.jpg', 'image/jpeg', 618138, '2026-08-19 06:52:34');

-- --------------------------------------------------------

--
-- Table structure for table `coverage_plans`
--

CREATE TABLE `coverage_plans` (
  `id` int(10) UNSIGNED NOT NULL,
  `plan_name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `coverage_type` enum('natural_disaster','pest_disease','drought','flood','comprehensive') NOT NULL,
  `coverage_percent` decimal(5,2) NOT NULL COMMENT 'Percentage of loss covered (e.g. 80.00)',
  `premium_rate` decimal(5,4) NOT NULL COMMENT 'Rate per hectare per season (e.g. 0.0500)',
  `max_coverage_amount` decimal(15,2) NOT NULL,
  `duration_months` int(11) NOT NULL DEFAULT 6,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `coverage_plans`
--

INSERT INTO `coverage_plans` (`id`, `plan_name`, `description`, `coverage_type`, `coverage_percent`, `premium_rate`, `max_coverage_amount`, `duration_months`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Basic Flood Cover', 'Covers crop loss due to flooding', 'flood', 70.00, 0.0500, 50000.00, 6, 1, '2026-06-08 14:25:12', '2026-06-08 14:25:12'),
(2, 'Natural Disaster Plan', 'Covers typhoons, earthquakes, and other natural events', 'natural_disaster', 80.00, 0.0650, 100000.00, 6, 1, '2026-06-08 14:25:12', '2026-06-08 14:25:12'),
(3, 'Pest & Disease Shield', 'Covers losses from pest infestations and crop diseases', 'pest_disease', 75.00, 0.0450, 75000.00, 6, 1, '2026-06-08 14:25:12', '2026-06-08 14:25:12'),
(4, 'Drought Protection', 'Covers crop loss due to prolonged drought', 'drought', 70.00, 0.0550, 60000.00, 6, 1, '2026-06-08 14:25:12', '2026-06-08 14:25:12'),
(5, 'Comprehensive Plan', 'Full coverage for all insurable risks', 'comprehensive', 90.00, 0.0850, 200000.00, 12, 1, '2026-06-08 14:25:12', '2026-06-08 14:25:12');

-- --------------------------------------------------------

--
-- Table structure for table `crop_types`
--

CREATE TABLE `crop_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `crop_types`
--

INSERT INTO `crop_types` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Rice', 'Palay / rice farming', '2026-06-08 14:25:12'),
(2, 'Corn', 'White and yellow corn', '2026-06-08 14:25:12'),
(3, 'Sugarcane', 'Sugar cane production', '2026-06-08 14:25:12'),
(4, 'Banana', 'Banana plantation', '2026-06-08 14:25:12'),
(5, 'Coconut', 'Coconut/copra farming', '2026-06-08 14:25:12'),
(6, 'Vegetables', 'Various vegetable crops', '2026-06-08 14:25:12'),
(7, 'Cassava', 'Cassava/root crops', '2026-06-08 14:25:12'),
(8, 'Coffee', 'Coffee bean farming', '2026-06-08 14:25:12'),
(9, 'Mango', 'Mango orchards', '2026-06-08 14:25:12'),
(10, 'Tobacco', 'Tobacco leaf farming', '2026-06-08 14:25:12');

-- --------------------------------------------------------

--
-- Table structure for table `farms`
--

CREATE TABLE `farms` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `farm_name` varchar(150) NOT NULL,
  `application_type` varchar(50) DEFAULT NULL,
  `farmer_category` varchar(100) DEFAULT NULL,
  `location` varchar(255) NOT NULL,
  `province` varchar(100) DEFAULT NULL,
  `municipality` varchar(100) DEFAULT NULL,
  `barangay` varchar(100) DEFAULT NULL,
  `area_hectares` decimal(10,4) NOT NULL,
  `crop_type_id` int(10) UNSIGNED NOT NULL,
  `soil_type` varchar(100) DEFAULT NULL,
  `irrigation` tinyint(1) DEFAULT 0,
  `tenurial_status` varchar(50) DEFAULT NULL,
  `planting_method` varchar(50) DEFAULT NULL,
  `planting_date` date DEFAULT NULL,
  `harvest_date` date DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `farms`
--

INSERT INTO `farms` (`id`, `user_id`, `farm_name`, `application_type`, `farmer_category`, `location`, `province`, `municipality`, `barangay`, `area_hectares`, `crop_type_id`, `soil_type`, `irrigation`, `tenurial_status`, `planting_method`, `planting_date`, `harvest_date`, `created_at`, `updated_at`, `latitude`, `longitude`) VALUES
(16, 26, 'Juan Tamad Farm', 'New', 'Small Farmer', 'Centro Sur', '', '', '', 3.5000, 1, 'Irrigated', 0, 'Owner', 'Direct Seeding', '2026-07-01', '2026-10-10', '2026-08-10 21:56:27', '2026-08-10 21:56:27', 17.88626800, 121.57250600);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('info','success','warning','error') DEFAULT 'info',
  `is_read` tinyint(1) DEFAULT 0,
  `link` varchar(500) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `type`, `is_read`, `link`, `created_at`) VALUES
(10, 26, 'Policy Approved ✅', 'Your policy <strong>POL-2026-000001</strong> has been approved and is now active.', 'success', 1, '/web-based-crop-insurance/views/user/my-applications.php', '2026-08-18 23:58:09'),
(11, 26, 'Policy Approved ✅', 'Your policy <strong>POL-2026-000001</strong> has been approved and is now active.', 'success', 1, '/web-based-crop-insurance/views/user/my-applications.php', '2026-08-19 06:51:20'),
(12, 26, 'Claim Approved ✅', 'Your claim <strong>CLM-2026-000001</strong> status has been updated to: <strong>approved</strong>.', 'success', 1, '/web-based-crop-insurance/views/user/application-status.php', '2026-08-19 06:53:39'),
(13, 26, 'Claim Approved ✅', 'Your claim <strong>CLM-2026-000001</strong> status has been updated to: <strong>approved</strong>.', 'success', 0, '/web-based-crop-insurance/views/user/application-status.php', '2026-08-21 21:20:00'),
(14, 26, 'Policy Set to Pending ⏳', 'Your policy application <strong>POL-2026-000001</strong> status is set to pending. Remarks: Set to pending from table actions.', 'warning', 0, '/views/user/my-applications.php', '2026-08-21 21:26:30'),
(15, 26, 'Policy Under Review 🔍', 'Your policy application <strong>POL-2026-000001</strong> is currently under review by the Municipal Agriculture Office. Remarks: Set to pending from table actions.', 'info', 0, '/views/user/my-applications.php', '2026-08-21 21:48:12'),
(16, 26, 'Policy Set to Pending ⏳', 'Your policy application <strong>POL-2026-000001</strong> status is set to pending. Remarks: Set to pending from table actions.', 'warning', 0, '/views/user/my-applications.php', '2026-08-21 22:08:59'),
(17, 26, 'Policy Under Review 🔍', 'Your policy application <strong>POL-2026-000001</strong> is currently under review by the Municipal Agriculture Office. Remarks: Set to pending from table actions.', 'info', 0, '/views/user/my-applications.php', '2026-08-21 22:14:20'),
(18, 26, 'Policy Application Rejected ❌', 'Your application for policy <strong>POL-2026-000001</strong> was rejected. Reason: Set to pending from table actions.', 'error', 0, '/views/user/my-applications.php', '2026-08-21 22:16:44'),
(19, 26, 'Policy Set to Pending ⏳', 'Your policy application <strong>POL-2026-000001</strong> status is set to pending.', 'warning', 0, '/views/user/my-applications.php', '2026-08-21 22:17:19'),
(20, 26, 'Policy Under Review 🔍', 'Your policy application <strong>POL-2026-000001</strong> is currently under review by the Municipal Agriculture Office.', 'info', 0, '/views/user/my-applications.php', '2026-08-21 22:19:28'),
(21, 26, 'Policy Approved ✅', 'Your policy <strong>POL-2026-000001</strong> has been approved and is now active. Remarks: Approved from table actions.', 'success', 0, '/views/user/my-applications.php', '2026-08-22 10:07:33');

-- --------------------------------------------------------

--
-- Table structure for table `otp_verifications`
--

CREATE TABLE `otp_verifications` (
  `id` int(11) NOT NULL,
  `email` varchar(191) NOT NULL,
  `otp_hash` varchar(255) NOT NULL,
  `purpose` varchar(50) NOT NULL DEFAULT 'admin_create_user',
  `attempts` tinyint(1) NOT NULL DEFAULT 0,
  `used` tinyint(1) NOT NULL DEFAULT 0,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `otp_verifications`
--

INSERT INTO `otp_verifications` (`id`, `email`, `otp_hash`, `purpose`, `attempts`, `used`, `expires_at`, `created_at`) VALUES
(2, 'reyan050880@gmail.com', '$2y$10$ZHJc6rGVvq5Tepwi0UAvceeK6m2qJB44jHQ593zjrwHOSjHsq7vQS', 'admin_create_user', 0, 0, '2026-07-31 22:05:03', '2026-07-31 13:55:03'),
(3, 'reyann050880@gmail.com', '$2y$10$gwL.69EvvPDuE3mfuQwTo.S5edhDi2w9MscRkyxam3L2lV49kFgFO', 'admin_create_user', 0, 1, '2026-07-31 22:05:43', '2026-07-31 13:55:43'),
(4, 'reyann050880@gmail.com', '$2y$10$R4l915Hev3ZcYwa2FxDCyOZr/XF./QWE4qyi2Zu7uty35fEfXDKZ6', 'admin_create_user', 0, 1, '2026-07-31 22:06:11', '2026-07-31 13:56:11'),
(5, 'reyann050880@gmail.com', '$2y$10$nwpIaZQfK0yOOTwvDdGrH.AHD1dShnX3yjy7WlFNSrnlkuuNkAa5S', 'admin_create_user', 0, 1, '2026-07-31 22:06:26', '2026-07-31 13:56:26'),
(9, 'reyann050880@gmail.com', '$2y$10$zXFAtYymzPCAKKodf/8O2.i3fN7uP9KApU5Mua/pn6604Li0NaOEG', 'admin_create_user', 0, 1, '2026-07-31 22:36:44', '2026-07-31 14:26:44'),
(11, 'reyann050880@gmail.com', '$2y$10$7e2m6opl6TKFHJqT5P9b8u157HJOmotr11jOp7Kyx.Q9BzaHWRzMK', 'admin_create_user', 0, 1, '2026-07-31 22:42:24', '2026-07-31 14:32:24'),
(12, 'reyann050880@gmail.com', '$2y$10$SUhmj.uBmPZaJzt20N/gceHQayKi.IiP6x4KLqzOYiUqFx5y/sGPC', 'admin_create_user', 1, 1, '2026-07-31 22:48:38', '2026-07-31 14:38:38'),
(13, 'reyann050880@gmail.com', '$2y$10$5M.OiUSBtmcVOUTNfZcWvexOeIBcruepc//ZQTNv889aWaLuKyMXS', 'admin_create_user', 0, 1, '2026-08-04 20:43:01', '2026-08-04 12:33:01'),
(14, 'reyann050880@gmail.com', '$2y$10$d27WnSicnDoXV6QLUEKE6OpAVI9a2FpWrS6vxlI89nPQbWqMOI.4i', 'admin_create_user', 0, 1, '2026-08-04 20:57:58', '2026-08-04 12:47:58'),
(15, 'reyann050880@gmail.com', '$2y$10$P9HSN/W2GudvhRAUJTUe9eWDLLTkIYtlpAhKRdBzV/NgbV6la/thG', 'admin_create_user', 0, 1, '2026-08-04 21:01:18', '2026-08-04 12:51:18'),
(16, 'reyann050880@gmail.com', '$2y$10$gdwwzUqBgrRredfOLJAmquhki8cF7.lF8oSBwVnlKvaZ10tnOM/72', 'admin_create_user', 0, 1, '2026-08-04 21:12:38', '2026-08-04 13:02:38'),
(17, 'reyann050880@gmail.com', '$2y$10$pQHQh/er0XBIqEi/iZ.z1uI2yu5CiU5/NnUZO3uInrNnlP8f2zmaW', 'password_reset', 1, 1, '2026-08-21 21:02:16', '2026-08-21 12:52:16'),
(18, 'reyann050880@gmail.com', '$2y$10$uYM.6dcl.jQ4V0qc3Mu9UeGA2ur/sJLiXO/IhAb2Y8MLxE2HCIFn2', 'password_reset', 0, 1, '2026-08-21 21:06:22', '2026-08-21 12:56:22'),
(19, 'reyann050880@gmail.com', '$2y$10$24FhjPlmKIk252fDplnO9.gD9Q4v3SM2FQ.nbMonGl.nBbXmwhO2.', 'password_reset', 1, 1, '2026-08-21 21:08:52', '2026-08-21 12:58:52'),
(20, 'glenard2308@gmail.com', '$2y$10$RtZdxQkvUquhoU/z2WSGJO3xs9kImTCy6dzGHN98MbgmR1GWmVa/W', 'admin_create_user', 1, 1, '2026-08-25 01:44:32', '2026-08-24 17:34:32');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(10) UNSIGNED NOT NULL,
  `reference_number` varchar(100) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `policy_id` int(10) UNSIGNED DEFAULT NULL,
  `claim_id` int(10) UNSIGNED DEFAULT NULL,
  `type` enum('premium','payout') NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `method` enum('cash','bank_transfer','gcash','maya','check') NOT NULL DEFAULT 'cash',
  `status` enum('pending','completed','failed','refunded') NOT NULL DEFAULT 'pending',
  `transaction_date` datetime DEFAULT current_timestamp(),
  `processed_by` int(10) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `policies`
--

CREATE TABLE `policies` (
  `id` int(10) UNSIGNED NOT NULL,
  `policy_number` varchar(50) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `farm_id` int(10) UNSIGNED NOT NULL,
  `plan_id` int(10) UNSIGNED NOT NULL,
  `agent_id` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('pending','under_review','active','expired','cancelled','rejected') NOT NULL DEFAULT 'pending',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `total_premium` decimal(15,2) NOT NULL,
  `coverage_amount` decimal(15,2) NOT NULL,
  `remarks` text DEFAULT NULL,
  `cause_of_damage` varchar(100) DEFAULT NULL,
  `percent_damage` decimal(5,2) DEFAULT NULL,
  `financial_damage` decimal(15,2) DEFAULT NULL,
  `damage_description` text DEFAULT NULL,
  `date_of_loss` date DEFAULT NULL,
  `farm_verification` enum('Pending','Verified','Rejected') NOT NULL DEFAULT 'Pending',
  `damage_verification` enum('Pending','Verified','Rejected') NOT NULL DEFAULT 'Pending',
  `coverage_verification` enum('Pending','Verified','Rejected') NOT NULL DEFAULT 'Pending',
  `approved_at` datetime DEFAULT NULL,
  `approved_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `policies`
--

INSERT INTO `policies` (`id`, `policy_number`, `user_id`, `farm_id`, `plan_id`, `agent_id`, `status`, `start_date`, `end_date`, `total_premium`, `coverage_amount`, `remarks`, `cause_of_damage`, `percent_damage`, `financial_damage`, `damage_description`, `date_of_loss`, `farm_verification`, `damage_verification`, `coverage_verification`, `approved_at`, `approved_by`, `created_at`, `updated_at`) VALUES
(16, 'POL-2026-000001', 26, 16, 2, 1, 'active', '2026-07-01', '2027-01-01', 11375.00, 50000.00, 'Approved from table actions.', 'Flood', 35.00, 102900.00, 'Permi nasisingo taypoon', '2026-08-08', 'Verified', 'Verified', 'Verified', '2026-08-22 10:07:33', 1, '2026-08-10 21:56:27', '2026-08-22 10:07:33');

-- --------------------------------------------------------

--
-- Table structure for table `policy_documents`
--

CREATE TABLE `policy_documents` (
  `id` int(10) UNSIGNED NOT NULL,
  `policy_id` int(10) UNSIGNED NOT NULL,
  `document_type` varchar(50) NOT NULL DEFAULT 'damage_photo',
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `file_size` int(10) UNSIGNED DEFAULT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `policy_documents`
--

INSERT INTO `policy_documents` (`id`, `policy_id`, `document_type`, `file_name`, `file_path`, `file_type`, `file_size`, `uploaded_at`) VALUES
(7, 14, 'damage_photo', 'file_6a689b317026a8.81372599.png', 'policies/file_6a689b317026a8.81372599.png', 'image/png', 413709, '2026-07-28 20:06:09'),
(8, 14, 'damage_photo', 'file_6a689b3183be02.75336280.jpg', 'policies/file_6a689b3183be02.75336280.jpg', 'image/jpeg', 54977, '2026-07-28 20:06:09'),
(9, 14, 'damage_photo', 'file_6a689b31997b87.25914767.jpg', 'policies/file_6a689b31997b87.25914767.jpg', 'image/jpeg', 171331, '2026-07-28 20:06:09'),
(10, 14, 'damage_photo', 'file_6a689b31ac83e0.64373998.png', 'policies/file_6a689b31ac83e0.64373998.png', 'image/png', 769390, '2026-07-28 20:06:09'),
(11, 14, 'damage_photo', 'file_6a689b31c0a0b2.34629576.png', 'policies/file_6a689b31c0a0b2.34629576.png', 'image/png', 256483, '2026-07-28 20:06:09'),
(12, 14, 'valid_id', 'file_6a689b31d20568.01056968.png', 'policies/file_6a689b31d20568.01056968.png', 'image/png', 413709, '2026-07-28 20:06:09'),
(13, 15, 'damage_photo', 'file_6a6a1e7fca20a5.42588209.png', 'policies/file_6a6a1e7fca20a5.42588209.png', 'image/png', 413709, '2026-07-29 23:38:39'),
(14, 15, 'damage_photo', 'file_6a6a1e7fdd1e00.60645308.jpg', 'policies/file_6a6a1e7fdd1e00.60645308.jpg', 'image/jpeg', 54977, '2026-07-29 23:38:39'),
(15, 15, 'damage_photo', 'file_6a6a1e800f58b0.04444446.png', 'policies/file_6a6a1e800f58b0.04444446.png', 'image/png', 2042712, '2026-07-29 23:38:40'),
(16, 15, 'damage_photo', 'file_6a6a1e802737f7.01691121.jpg', 'policies/file_6a6a1e802737f7.01691121.jpg', 'image/jpeg', 1127672, '2026-07-29 23:38:40'),
(17, 15, 'damage_photo', 'file_6a6a1e8047d405.77272711.png', 'policies/file_6a6a1e8047d405.77272711.png', 'image/png', 2376743, '2026-07-29 23:38:40'),
(18, 15, 'valid_id', 'file_6a6a1e806761e3.07245073.png', 'policies/file_6a6a1e806761e3.07245073.png', 'image/png', 413709, '2026-07-29 23:38:40'),
(19, 16, 'damage_photo', 'file_6a79d88bf38cf9.63668119.png', 'policies/file_6a79d88bf38cf9.63668119.png', 'image/png', 413709, '2026-08-10 21:56:28'),
(20, 16, 'damage_photo', 'file_6a79d88c1126a9.77663977.jpg', 'policies/file_6a79d88c1126a9.77663977.jpg', 'image/jpeg', 54977, '2026-08-10 21:56:28'),
(21, 16, 'damage_photo', 'file_6a79d88c2b5265.42538214.png', 'policies/file_6a79d88c2b5265.42538214.png', 'image/png', 2264400, '2026-08-10 21:56:28'),
(22, 16, 'damage_photo', 'file_6a79d88c47e419.76773295.png', 'policies/file_6a79d88c47e419.76773295.png', 'image/png', 1642947, '2026-08-10 21:56:28'),
(23, 16, 'damage_photo', 'file_6a79d88c5a3dd8.16113586.png', 'policies/file_6a79d88c5a3dd8.16113586.png', 'image/png', 1029117, '2026-08-10 21:56:28'),
(24, 16, 'valid_id', 'file_6a79d88c6c0f89.12848975.jpg', 'policies/file_6a79d88c6c0f89.12848975.jpg', 'image/jpeg', 59217, '2026-08-10 21:56:28');

-- --------------------------------------------------------

--
-- Table structure for table `sms_logs`
--

CREATE TABLE `sms_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `recipient` varchar(20) NOT NULL,
  `message` text NOT NULL,
  `status` enum('sent','failed','simulated') NOT NULL DEFAULT 'sent',
  `http_code` int(10) UNSIGNED DEFAULT NULL,
  `response_body` text DEFAULT NULL,
  `error_message` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sms_logs`
--

INSERT INTO `sms_logs` (`id`, `recipient`, `message`, `status`, `http_code`, `response_body`, `error_message`, `created_at`) VALUES
(1, '639171234567', 'Test SMS log entry for audit page', 'sent', 200, '{\"status\":\"success\",\"message\":\"Your message was successfully delivered\",\"data\":{\"uid\":\"6a885ca6bf7b3\",\"to\":\"639171234567\",\"from\":\"PhilSMS\",\"message\":\"Test SMS log entry for audit page\",\"status\":\"Delivered\",\"cost\":\"1\",\"sms_count\":1}}', NULL, '2026-08-21 22:11:45'),
(2, '639169751409', 'Dear Juan Tamad, your Crop Insurance application (POL-2026-000001) is now UNDER REVIEW by the Municipal Agriculture Office. Note: Set to pending from table actions.. We will update you once processed. - Sto. Nino Crop Insurance', 'sent', 200, '{\"status\":\"success\",\"message\":\"Your message was successfully delivered\",\"data\":{\"uid\":\"6a885d498db9f\",\"to\":\"639169751409\",\"from\":\"PhilSMS\",\"message\":\"Dear Juan Tamad, your Crop Insurance application (POL-2026-000001) is now UNDER REVIEW by the Municipal Agriculture Office. Note: Set to pending from table actions.. We will update you once processed. - Sto. Nino Crop Insurance\",\"status\":\"Delivered\",\"cost\":\"2\",\"sms_count\":2}}', NULL, '2026-08-21 22:14:28'),
(3, '639169751409', 'Dear Juan Tamad, your Crop Insurance application (POL-2026-000001) was REJECTED. Reason: Set to pending from table actions.. Please visit the Municipal Agriculture Office for inquiries. - Sto. Nino Crop Insurance', 'sent', 200, '{\"status\":\"success\",\"message\":\"Your message was successfully delivered\",\"data\":{\"uid\":\"6a885dd927456\",\"to\":\"639169751409\",\"from\":\"PhilSMS\",\"message\":\"Dear Juan Tamad, your Crop Insurance application (POL-2026-000001) was REJECTED. Reason: Set to pending from table actions.. Please visit the Municipal Agriculture Office for inquiries. - Sto. Nino Crop Insurance\",\"status\":\"Delivered\",\"cost\":\"2\",\"sms_count\":2}}', NULL, '2026-08-21 22:16:51'),
(4, '639169751409', 'Dear Juan Tamad, your Crop Insurance application (POL-2026-000001) status has been set to PENDING. Please monitor your account for updates. - Sto. Nino Crop Insurance', 'failed', 404, '{\"status\":\"error\",\"message\":\"Your message contains spam words.\"}', 'Your message contains spam words.', '2026-08-21 22:17:25'),
(5, '639169751409', 'Dear Juan Tamad, your Crop Insurance application (POL-2026-000001) is now UNDER REVIEW by the Municipal Agriculture Office. We will update you once processed. - Sto. Nino Crop Insurance', 'sent', 200, '{\"status\":\"success\",\"message\":\"Your message was successfully delivered\",\"data\":{\"uid\":\"6a885e7d82ba6\",\"to\":\"639169751409\",\"from\":\"PhilSMS\",\"message\":\"Dear Juan Tamad, your Crop Insurance application (POL-2026-000001) is now UNDER REVIEW by the Municipal Agriculture Office. We will update you once processed. - Sto. Nino Crop Insurance\",\"status\":\"Delivered\",\"cost\":\"2\",\"sms_count\":2}}', NULL, '2026-08-21 22:19:36'),
(6, '639452855007', 'Dear Juan Tamad, your Crop Insurance application (POL-2026-000001) has been APPROVED. Your policy is now active. Thank you! - Sto. Nino Crop Insurance', 'sent', 200, '{\"status\":\"success\",\"message\":\"Your message was successfully delivered\",\"data\":{\"uid\":\"6a8904737f64d\",\"to\":\"639452855007\",\"from\":\"PhilSMS\",\"message\":\"Dear Juan Tamad, your Crop Insurance application (POL-2026-000001) has been APPROVED. Your policy is now active. Thank you! - Sto. Nino Crop Insurance\",\"status\":\"Delivered\",\"cost\":\"1\",\"sms_count\":1}}', NULL, '2026-08-22 10:07:41'),
(7, '639169751409', 'Dear Juan Tamad, your Crop Insurance application (POL-2026-000001) is now UNDER REVIEW by the Municipal Agriculture Office. We will update you once processed. - Sto. Nino Crop Insurance', 'sent', 200, '{\"status\":\"success\",\"message\":\"Your message was successfully delivered\",\"data\":{\"uid\":\"6a8c82cc95607\",\"to\":\"639169751409\",\"from\":\"PhilSMS\",\"message\":\"Dear Juan Tamad, your Crop Insurance application (POL-2026-000001) is now UNDER REVIEW by the Municipal Agriculture Office. We will update you once processed. - Sto. Nino Crop Insurance\",\"status\":\"Delivered\",\"cost\":\"2\",\"sms_count\":2}}', NULL, '2026-08-25 01:43:39');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(191) NOT NULL,
  `password` varchar(255) NOT NULL,
  `must_change_password` tinyint(1) NOT NULL DEFAULT 0,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `farmer_type` varchar(100) DEFAULT NULL,
  `role` enum('admin','agent','farmer') NOT NULL DEFAULT 'farmer',
  `status` enum('pending','active','inactive','suspended') NOT NULL DEFAULT 'active',
  `profile_photo` varchar(255) DEFAULT NULL,
  `email_verified` tinyint(1) DEFAULT 0,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `failed_attempts` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `locked_until` datetime DEFAULT NULL,
  `security_question` varchar(255) DEFAULT NULL,
  `security_answer_hash` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `must_change_password`, `phone`, `address`, `farmer_type`, `role`, `status`, `profile_photo`, `email_verified`, `reset_token`, `reset_expires`, `failed_attempts`, `locked_until`, `security_question`, `security_answer_hash`, `created_at`, `updated_at`) VALUES
(1, 'System', 'Admin', 'admin@cropinsurance.ph', '$2y$12$qiO.RL0hIP058jiNNT2ayevLJ/YONbPIJUFN34HGexj5gYtDo29pO', 0, '09000000001', '', '', 'admin', 'active', NULL, 1, NULL, NULL, 0, NULL, 'What was your childhood nickname?', '$2y$10$ydXv9VxmrX0yMTvKGUrl5.AGaUrXecnLxnFvgUToiUIqIvfZKJfKS', '2026-06-08 14:25:12', '2026-08-30 23:08:35'),
(26, 'Juan', 'Tamad', 'reyann050880@gmail.com', '$2y$12$2zDBe.Hum6v6lI8//E7xlulI.FQ0wbZlSqXh0YTdrPu1X.peb56/2', 0, '09452855007', '', '', 'farmer', 'active', NULL, 1, NULL, NULL, 1, '2026-08-21 21:14:13', 'What is your mother\'s maiden name?', '$2y$10$NhEcaapwgOg20PGrF2e6surjKaM2bFi/R6eCoN4/colBsNAgE3UOu', '2026-08-04 21:03:12', '2026-08-25 01:44:10'),
(27, 'Glenard', 'Pagurayan', 'glenard2308@gmail.com', '$2y$12$5Kqx9J.lDNf4F5xdWj3.4ulsg4pMf9vh36PWjlWtZma70dLS1sFzu', 0, '09557997409', NULL, NULL, 'farmer', 'active', NULL, 1, NULL, NULL, 0, NULL, NULL, NULL, '2026-08-25 01:35:05', '2026-08-25 01:37:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_audit_user` (`user_id`);

--
-- Indexes for table `claims`
--
ALTER TABLE `claims`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `claim_number` (`claim_number`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `reviewed_by` (`reviewed_by`),
  ADD KEY `idx_claims_policy` (`policy_id`),
  ADD KEY `idx_claims_status` (`status`);

--
-- Indexes for table `claim_documents`
--
ALTER TABLE `claim_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `claim_id` (`claim_id`);

--
-- Indexes for table `coverage_plans`
--
ALTER TABLE `coverage_plans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `crop_types`
--
ALTER TABLE `crop_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `farms`
--
ALTER TABLE `farms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `crop_type_id` (`crop_type_id`),
  ADD KEY `idx_farms_user` (`user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notifications_user` (`user_id`,`is_read`);

--
-- Indexes for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_email_purpose` (`email`,`purpose`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reference_number` (`reference_number`),
  ADD KEY `policy_id` (`policy_id`),
  ADD KEY `claim_id` (`claim_id`),
  ADD KEY `processed_by` (`processed_by`),
  ADD KEY `idx_payments_user` (`user_id`);

--
-- Indexes for table `policies`
--
ALTER TABLE `policies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `policy_number` (`policy_number`),
  ADD KEY `farm_id` (`farm_id`),
  ADD KEY `plan_id` (`plan_id`),
  ADD KEY `agent_id` (`agent_id`),
  ADD KEY `approved_by` (`approved_by`),
  ADD KEY `idx_policies_user` (`user_id`),
  ADD KEY `idx_policies_status` (`status`);

--
-- Indexes for table `policy_documents`
--
ALTER TABLE `policy_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_policy_id` (`policy_id`);

--
-- Indexes for table `sms_logs`
--
ALTER TABLE `sms_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sms_recipient` (`recipient`),
  ADD KEY `idx_sms_status` (`status`),
  ADD KEY `idx_sms_created_at` (`created_at`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=294;

--
-- AUTO_INCREMENT for table `claims`
--
ALTER TABLE `claims`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `claim_documents`
--
ALTER TABLE `claim_documents`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `coverage_plans`
--
ALTER TABLE `coverage_plans`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `crop_types`
--
ALTER TABLE `crop_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `farms`
--
ALTER TABLE `farms`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `policies`
--
ALTER TABLE `policies`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `policy_documents`
--
ALTER TABLE `policy_documents`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `sms_logs`
--
ALTER TABLE `sms_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `claims`
--
ALTER TABLE `claims`
  ADD CONSTRAINT `claims_ibfk_1` FOREIGN KEY (`policy_id`) REFERENCES `policies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `claims_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `claims_ibfk_3` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `claim_documents`
--
ALTER TABLE `claim_documents`
  ADD CONSTRAINT `claim_documents_ibfk_1` FOREIGN KEY (`claim_id`) REFERENCES `claims` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `farms`
--
ALTER TABLE `farms`
  ADD CONSTRAINT `farms_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `farms_ibfk_2` FOREIGN KEY (`crop_type_id`) REFERENCES `crop_types` (`id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`policy_id`) REFERENCES `policies` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payments_ibfk_3` FOREIGN KEY (`claim_id`) REFERENCES `claims` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payments_ibfk_4` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `policies`
--
ALTER TABLE `policies`
  ADD CONSTRAINT `policies_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `policies_ibfk_2` FOREIGN KEY (`farm_id`) REFERENCES `farms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `policies_ibfk_3` FOREIGN KEY (`plan_id`) REFERENCES `coverage_plans` (`id`),
  ADD CONSTRAINT `policies_ibfk_4` FOREIGN KEY (`agent_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `policies_ibfk_5` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
