-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 23, 2026 at 01:55 PM
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
-- Database: `readquest`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookmarks`
--

CREATE TABLE `bookmarks` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `story_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chapters`
--

CREATE TABLE `chapters` (
  `id` int(11) NOT NULL,
  `story_id` int(11) NOT NULL,
  `chapter_number` decimal(5,1) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `pages` int(11) DEFAULT 0,
  `views` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chapters`
--

INSERT INTO `chapters` (`id`, `story_id`, `chapter_number`, `title`, `pages`, `views`, `created_at`, `updated_at`) VALUES
(3, 5, 1.1, '', 4, 17, '2026-02-22 20:04:31', '2026-02-22 21:29:56'),
(4, 5, 1.2, '', 4, 2, '2026-02-22 20:04:59', '2026-02-22 20:13:15'),
(5, 5, 2.1, '', 4, 5, '2026-02-22 20:05:20', '2026-02-22 20:51:59'),
(6, 5, 2.2, '', 3, 0, '2026-02-22 20:05:36', '2026-02-22 20:05:36'),
(7, 6, 1.0, '', 2, 3, '2026-02-22 20:36:02', '2026-02-22 21:29:43'),
(8, 6, 2.0, '', 3, 2, '2026-02-22 20:36:20', '2026-02-22 20:56:53'),
(9, 9, 1.0, '', 3, 0, '2026-02-22 20:36:51', '2026-02-22 20:36:51'),
(10, 12, 1.0, '', 2, 0, '2026-02-22 20:37:28', '2026-02-22 20:37:28'),
(11, 26, 1.0, '', 1, 0, '2026-02-22 20:37:49', '2026-02-22 20:37:49'),
(12, 8, 1.0, '', 4, 0, '2026-02-22 20:45:06', '2026-02-22 20:45:06'),
(13, 14, 1.0, '', 3, 0, '2026-02-22 20:45:43', '2026-02-22 20:45:43'),
(14, 11, 1.0, '', 2, 4, '2026-02-22 20:46:17', '2026-02-22 20:56:50'),
(15, 11, 2.0, '', 3, 1, '2026-02-22 20:46:32', '2026-02-22 20:52:46'),
(16, 15, 1.0, '', 3, 0, '2026-02-22 20:47:02', '2026-02-22 20:47:02'),
(17, 15, 2.0, '', 2, 0, '2026-02-22 20:47:13', '2026-02-22 20:47:13'),
(18, 16, 1.0, '', 3, 0, '2026-02-22 20:47:36', '2026-02-22 20:47:36'),
(19, 16, 2.0, '', 2, 0, '2026-02-22 20:47:48', '2026-02-22 20:47:48'),
(20, 13, 1.0, '', 2, 0, '2026-02-22 20:48:18', '2026-02-22 20:48:18'),
(21, 13, 2.0, '', 2, 0, '2026-02-22 20:48:31', '2026-02-22 20:48:31'),
(22, 17, 1.0, '', 3, 0, '2026-02-22 20:49:13', '2026-02-22 20:49:13'),
(23, 17, 2.0, '', 4, 0, '2026-02-22 20:49:26', '2026-02-22 20:49:26'),
(24, 18, 1.0, '', 2, 0, '2026-02-22 20:50:00', '2026-02-22 20:50:00'),
(25, 18, 2.0, '', 4, 0, '2026-02-22 20:51:18', '2026-02-22 20:51:18'),
(26, 20, 1.0, '', 1, 0, '2026-02-22 20:59:29', '2026-02-22 20:59:29'),
(27, 20, 2.0, '', 1, 0, '2026-02-22 20:59:39', '2026-02-22 20:59:39'),
(28, 20, 3.0, '', 1, 0, '2026-02-22 20:59:49', '2026-02-22 20:59:49'),
(29, 21, 1.0, '', 2, 0, '2026-02-22 21:00:20', '2026-02-22 21:00:20'),
(30, 22, 1.0, '', 2, 0, '2026-02-22 21:00:51', '2026-02-22 21:00:51'),
(31, 10, 1.0, '', 6, 0, '2026-02-22 21:01:40', '2026-02-22 21:01:40'),
(32, 10, 2.0, '', 6, 0, '2026-02-22 21:02:07', '2026-02-22 21:02:07'),
(33, 23, 1.0, '', 3, 0, '2026-02-22 21:02:35', '2026-02-22 21:02:35'),
(34, 24, 1.0, '', 5, 0, '2026-02-22 21:03:56', '2026-02-22 21:03:56'),
(35, 24, 2.0, '', 4, 0, '2026-02-22 21:04:11', '2026-02-22 21:04:11'),
(36, 19, 1.0, '', 5, 0, '2026-02-22 21:05:49', '2026-02-22 21:05:49'),
(37, 7, 1.0, '', 3, 0, '2026-02-22 21:06:19', '2026-02-22 21:06:19'),
(38, 7, 2.0, '', 2, 0, '2026-02-22 21:06:38', '2026-02-22 21:06:38');

--
-- Triggers `chapters`
--
DELIMITER $$
CREATE TRIGGER `update_chapter_count_delete` AFTER DELETE ON `chapters` FOR EACH ROW BEGIN
    UPDATE stories 
    SET chapters = (SELECT COUNT(*) FROM chapters WHERE story_id = OLD.story_id)
    WHERE id = OLD.story_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_chapter_count_insert` AFTER INSERT ON `chapters` FOR EACH ROW BEGIN
    UPDATE stories 
    SET chapters = (SELECT COUNT(*) FROM chapters WHERE story_id = NEW.story_id)
    WHERE id = NEW.story_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `story_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `comment_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_edited` tinyint(1) DEFAULT 0,
  `likes` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `story_id`, `user_id`, `parent_id`, `comment_text`, `created_at`, `updated_at`, `is_edited`, `likes`) VALUES
(1, 5, 2, NULL, 'test', '2026-02-22 20:27:33', '2026-02-22 20:27:33', 0, 0),
(2, 5, 2, NULL, 'test', '2026-02-22 20:32:27', '2026-02-22 20:32:27', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `genres`
--

CREATE TABLE `genres` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `color` varchar(7) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `genres`
--

INSERT INTO `genres` (`id`, `name`, `slug`, `description`, `icon`, `color`, `created_at`) VALUES
(1, 'Action', 'action', 'Fast-paced stories with intense battles and adventures', 'fa-fist-raised', '#e74c3c', '2026-02-21 07:56:13'),
(2, 'Romance', 'romance', 'Stories focused on love and relationships', 'fa-heart', '#e91e63', '2026-02-21 07:56:13'),
(3, 'Fantasy', 'fantasy', 'Magical worlds and supernatural elements', 'fa-hat-wizard', '#9b59b6', '2026-02-21 07:56:13'),
(4, 'Sci-Fi', 'scifi', 'Science fiction and futuristic stories', 'fa-rocket', '#3498db', '2026-02-21 07:56:13'),
(5, 'Mystery', 'mystery', 'Detective stories and puzzles to solve', 'fa-search', '#1abc9c', '2026-02-21 07:56:13'),
(6, 'Comedy', 'comedy', 'Funny and lighthearted stories', 'fa-laugh', '#f39c12', '2026-02-21 07:56:13'),
(7, 'Horror', 'horror', 'Scary and suspenseful stories', 'fa-skull', '#34495e', '2026-02-21 07:56:13'),
(8, 'Drama', 'drama', 'Emotional and character-driven stories', 'fa-theater-masks', '#95a5a6', '2026-02-21 07:56:13'),
(9, 'Adventure', 'adventure', 'Exciting journeys and exploration', 'fa-compass', '#16a085', '2026-02-21 07:56:13'),
(10, 'Slice of Life', 'slice-of-life', 'Everyday life and realistic stories', 'fa-coffee', '#d35400', '2026-02-21 07:56:13');

-- --------------------------------------------------------

--
-- Stand-in structure for view `latest_updates`
-- (See below for the actual view)
--
CREATE TABLE `latest_updates` (
`id` int(11)
,`title` varchar(255)
,`cover` varchar(255)
,`author` varchar(100)
,`chapter_number` decimal(5,1)
,`chapter_title` varchar(255)
,`updated_at` timestamp
);

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` int(11) NOT NULL,
  `chapter_id` int(11) NOT NULL,
  `page_number` int(11) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `chapter_id`, `page_number`, `image_url`, `created_at`) VALUES
(6, 3, 1, 'assets/chapters/story_5/chapter_3/page_1.png', '2026-02-22 20:04:31'),
(7, 3, 2, 'assets/chapters/story_5/chapter_3/page_2.png', '2026-02-22 20:04:31'),
(8, 3, 3, 'assets/chapters/story_5/chapter_3/page_3.png', '2026-02-22 20:04:31'),
(9, 3, 4, 'assets/chapters/story_5/chapter_3/page_4.png', '2026-02-22 20:04:31'),
(10, 4, 1, 'assets/chapters/story_5/chapter_4/page_1.png', '2026-02-22 20:04:59'),
(11, 4, 2, 'assets/chapters/story_5/chapter_4/page_2.png', '2026-02-22 20:04:59'),
(12, 4, 3, 'assets/chapters/story_5/chapter_4/page_3.png', '2026-02-22 20:04:59'),
(13, 4, 4, 'assets/chapters/story_5/chapter_4/page_4.png', '2026-02-22 20:04:59'),
(14, 5, 1, 'assets/chapters/story_5/chapter_5/page_1.png', '2026-02-22 20:05:20'),
(15, 5, 2, 'assets/chapters/story_5/chapter_5/page_2.png', '2026-02-22 20:05:20'),
(16, 5, 3, 'assets/chapters/story_5/chapter_5/page_3.png', '2026-02-22 20:05:20'),
(17, 5, 4, 'assets/chapters/story_5/chapter_5/page_4.png', '2026-02-22 20:05:20'),
(18, 6, 1, 'assets/chapters/story_5/chapter_6/page_1.png', '2026-02-22 20:05:36'),
(19, 6, 2, 'assets/chapters/story_5/chapter_6/page_2.png', '2026-02-22 20:05:36'),
(20, 6, 3, 'assets/chapters/story_5/chapter_6/page_3.png', '2026-02-22 20:05:36'),
(21, 7, 1, 'assets/chapters/story_6/chapter_7/page_1.png', '2026-02-22 20:36:02'),
(22, 7, 2, 'assets/chapters/story_6/chapter_7/page_2.png', '2026-02-22 20:36:02'),
(23, 8, 1, 'assets/chapters/story_6/chapter_8/page_1.png', '2026-02-22 20:36:20'),
(24, 8, 2, 'assets/chapters/story_6/chapter_8/page_2.png', '2026-02-22 20:36:20'),
(25, 8, 3, 'assets/chapters/story_6/chapter_8/page_3.png', '2026-02-22 20:36:20'),
(26, 9, 1, 'assets/chapters/story_9/chapter_9/page_1.png', '2026-02-22 20:36:51'),
(27, 9, 2, 'assets/chapters/story_9/chapter_9/page_2.png', '2026-02-22 20:36:51'),
(28, 9, 3, 'assets/chapters/story_9/chapter_9/page_3.png', '2026-02-22 20:36:51'),
(29, 10, 1, 'assets/chapters/story_12/chapter_10/page_1.png', '2026-02-22 20:37:28'),
(30, 10, 2, 'assets/chapters/story_12/chapter_10/page_2.png', '2026-02-22 20:37:28'),
(31, 11, 1, 'assets/chapters/story_26/chapter_11/page_1.png', '2026-02-22 20:37:49'),
(32, 12, 1, 'assets/chapters/story_8/chapter_12/page_1.png', '2026-02-22 20:45:06'),
(33, 12, 2, 'assets/chapters/story_8/chapter_12/page_2.png', '2026-02-22 20:45:06'),
(34, 12, 3, 'assets/chapters/story_8/chapter_12/page_3.png', '2026-02-22 20:45:06'),
(35, 12, 4, 'assets/chapters/story_8/chapter_12/page_4.png', '2026-02-22 20:45:06'),
(36, 13, 1, 'assets/chapters/story_14/chapter_13/page_1.png', '2026-02-22 20:45:43'),
(37, 13, 2, 'assets/chapters/story_14/chapter_13/page_2.png', '2026-02-22 20:45:43'),
(38, 13, 3, 'assets/chapters/story_14/chapter_13/page_3.png', '2026-02-22 20:45:43'),
(39, 14, 1, 'assets/chapters/story_11/chapter_14/page_1.png', '2026-02-22 20:46:17'),
(40, 14, 2, 'assets/chapters/story_11/chapter_14/page_2.png', '2026-02-22 20:46:17'),
(41, 15, 1, 'assets/chapters/story_11/chapter_15/page_1.png', '2026-02-22 20:46:32'),
(42, 15, 2, 'assets/chapters/story_11/chapter_15/page_2.png', '2026-02-22 20:46:32'),
(43, 15, 3, 'assets/chapters/story_11/chapter_15/page_3.png', '2026-02-22 20:46:32'),
(44, 16, 1, 'assets/chapters/story_15/chapter_16/page_1.png', '2026-02-22 20:47:02'),
(45, 16, 2, 'assets/chapters/story_15/chapter_16/page_2.png', '2026-02-22 20:47:02'),
(46, 16, 3, 'assets/chapters/story_15/chapter_16/page_3.png', '2026-02-22 20:47:02'),
(47, 17, 1, 'assets/chapters/story_15/chapter_17/page_1.png', '2026-02-22 20:47:13'),
(48, 17, 2, 'assets/chapters/story_15/chapter_17/page_2.png', '2026-02-22 20:47:13'),
(49, 18, 1, 'assets/chapters/story_16/chapter_18/page_1.png', '2026-02-22 20:47:36'),
(50, 18, 2, 'assets/chapters/story_16/chapter_18/page_2.png', '2026-02-22 20:47:36'),
(51, 18, 3, 'assets/chapters/story_16/chapter_18/page_3.png', '2026-02-22 20:47:36'),
(52, 19, 1, 'assets/chapters/story_16/chapter_19/page_1.png', '2026-02-22 20:47:48'),
(53, 19, 2, 'assets/chapters/story_16/chapter_19/page_2.png', '2026-02-22 20:47:48'),
(54, 20, 1, 'assets/chapters/story_13/chapter_20/page_1.png', '2026-02-22 20:48:18'),
(55, 20, 2, 'assets/chapters/story_13/chapter_20/page_2.png', '2026-02-22 20:48:18'),
(56, 21, 1, 'assets/chapters/story_13/chapter_21/page_1.png', '2026-02-22 20:48:31'),
(57, 21, 2, 'assets/chapters/story_13/chapter_21/page_2.png', '2026-02-22 20:48:31'),
(58, 22, 1, 'assets/chapters/story_17/chapter_22/page_1.png', '2026-02-22 20:49:13'),
(59, 22, 2, 'assets/chapters/story_17/chapter_22/page_2.png', '2026-02-22 20:49:13'),
(60, 22, 3, 'assets/chapters/story_17/chapter_22/page_3.png', '2026-02-22 20:49:13'),
(61, 23, 1, 'assets/chapters/story_17/chapter_23/page_1.png', '2026-02-22 20:49:26'),
(62, 23, 2, 'assets/chapters/story_17/chapter_23/page_2.png', '2026-02-22 20:49:26'),
(63, 23, 3, 'assets/chapters/story_17/chapter_23/page_3.png', '2026-02-22 20:49:26'),
(64, 23, 4, 'assets/chapters/story_17/chapter_23/page_4.png', '2026-02-22 20:49:26'),
(65, 24, 1, 'assets/chapters/story_18/chapter_24/page_1.png', '2026-02-22 20:50:00'),
(66, 24, 2, 'assets/chapters/story_18/chapter_24/page_2.png', '2026-02-22 20:50:00'),
(67, 25, 1, 'assets/chapters/story_18/chapter_25/page_1.png', '2026-02-22 20:51:18'),
(68, 25, 2, 'assets/chapters/story_18/chapter_25/page_2.png', '2026-02-22 20:51:18'),
(69, 25, 3, 'assets/chapters/story_18/chapter_25/page_3.png', '2026-02-22 20:51:18'),
(70, 25, 4, 'assets/chapters/story_18/chapter_25/page_4.png', '2026-02-22 20:51:18'),
(71, 26, 1, 'assets/chapters/story_20/chapter_26/page_1.png', '2026-02-22 20:59:29'),
(72, 27, 1, 'assets/chapters/story_20/chapter_27/page_1.png', '2026-02-22 20:59:39'),
(73, 28, 1, 'assets/chapters/story_20/chapter_28/page_1.png', '2026-02-22 20:59:49'),
(74, 29, 1, 'assets/chapters/story_21/chapter_29/page_1.png', '2026-02-22 21:00:20'),
(75, 29, 2, 'assets/chapters/story_21/chapter_29/page_2.png', '2026-02-22 21:00:20'),
(76, 30, 1, 'assets/chapters/story_22/chapter_30/page_1.png', '2026-02-22 21:00:51'),
(77, 30, 2, 'assets/chapters/story_22/chapter_30/page_2.png', '2026-02-22 21:00:51'),
(78, 31, 1, 'assets/chapters/story_10/chapter_31/page_1.png', '2026-02-22 21:01:40'),
(79, 31, 2, 'assets/chapters/story_10/chapter_31/page_2.png', '2026-02-22 21:01:40'),
(80, 31, 3, 'assets/chapters/story_10/chapter_31/page_3.png', '2026-02-22 21:01:40'),
(81, 31, 4, 'assets/chapters/story_10/chapter_31/page_4.png', '2026-02-22 21:01:40'),
(82, 31, 5, 'assets/chapters/story_10/chapter_31/page_5.png', '2026-02-22 21:01:40'),
(83, 31, 6, 'assets/chapters/story_10/chapter_31/page_6.png', '2026-02-22 21:01:40'),
(84, 32, 1, 'assets/chapters/story_10/chapter_32/page_1.png', '2026-02-22 21:02:07'),
(85, 32, 2, 'assets/chapters/story_10/chapter_32/page_2.png', '2026-02-22 21:02:07'),
(86, 32, 3, 'assets/chapters/story_10/chapter_32/page_3.png', '2026-02-22 21:02:07'),
(87, 32, 4, 'assets/chapters/story_10/chapter_32/page_4.png', '2026-02-22 21:02:07'),
(88, 32, 5, 'assets/chapters/story_10/chapter_32/page_5.png', '2026-02-22 21:02:07'),
(89, 32, 6, 'assets/chapters/story_10/chapter_32/page_6.png', '2026-02-22 21:02:07'),
(90, 33, 1, 'assets/chapters/story_23/chapter_33/page_1.png', '2026-02-22 21:02:35'),
(91, 33, 2, 'assets/chapters/story_23/chapter_33/page_2.png', '2026-02-22 21:02:35'),
(92, 33, 3, 'assets/chapters/story_23/chapter_33/page_3.png', '2026-02-22 21:02:35'),
(93, 34, 1, 'assets/chapters/story_24/chapter_34/page_1.png', '2026-02-22 21:03:56'),
(94, 34, 2, 'assets/chapters/story_24/chapter_34/page_2.png', '2026-02-22 21:03:56'),
(95, 34, 3, 'assets/chapters/story_24/chapter_34/page_3.png', '2026-02-22 21:03:56'),
(96, 34, 4, 'assets/chapters/story_24/chapter_34/page_4.png', '2026-02-22 21:03:56'),
(97, 34, 5, 'assets/chapters/story_24/chapter_34/page_5.png', '2026-02-22 21:03:56'),
(98, 35, 1, 'assets/chapters/story_24/chapter_35/page_1.png', '2026-02-22 21:04:11'),
(99, 35, 2, 'assets/chapters/story_24/chapter_35/page_2.png', '2026-02-22 21:04:11'),
(100, 35, 3, 'assets/chapters/story_24/chapter_35/page_3.png', '2026-02-22 21:04:11'),
(101, 35, 4, 'assets/chapters/story_24/chapter_35/page_4.png', '2026-02-22 21:04:11'),
(102, 36, 1, 'assets/chapters/story_19/chapter_36/page_1.png', '2026-02-22 21:05:49'),
(103, 36, 2, 'assets/chapters/story_19/chapter_36/page_2.png', '2026-02-22 21:05:49'),
(104, 36, 3, 'assets/chapters/story_19/chapter_36/page_3.png', '2026-02-22 21:05:49'),
(105, 36, 4, 'assets/chapters/story_19/chapter_36/page_4.png', '2026-02-22 21:05:49'),
(106, 36, 5, 'assets/chapters/story_19/chapter_36/page_5.png', '2026-02-22 21:05:49'),
(107, 37, 1, 'assets/chapters/story_7/chapter_37/page_1.png', '2026-02-22 21:06:19'),
(108, 37, 2, 'assets/chapters/story_7/chapter_37/page_2.png', '2026-02-22 21:06:19'),
(109, 37, 3, 'assets/chapters/story_7/chapter_37/page_3.png', '2026-02-22 21:06:19'),
(110, 38, 1, 'assets/chapters/story_7/chapter_38/page_1.png', '2026-02-22 21:06:38'),
(111, 38, 2, 'assets/chapters/story_7/chapter_38/page_2.png', '2026-02-22 21:06:38');

--
-- Triggers `pages`
--
DELIMITER $$
CREATE TRIGGER `update_page_count_delete` AFTER DELETE ON `pages` FOR EACH ROW BEGIN
    UPDATE chapters 
    SET pages = (SELECT COUNT(*) FROM pages WHERE chapter_id = OLD.chapter_id)
    WHERE id = OLD.chapter_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_page_count_insert` AFTER INSERT ON `pages` FOR EACH ROW BEGIN
    UPDATE chapters 
    SET pages = (SELECT COUNT(*) FROM pages WHERE chapter_id = NEW.chapter_id)
    WHERE id = NEW.chapter_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `popular_stories`
-- (See below for the actual view)
--
CREATE TABLE `popular_stories` (
`id` int(11)
,`title` varchar(255)
,`author` varchar(100)
,`cover` varchar(255)
,`description` text
,`genre` varchar(100)
,`status` enum('ongoing','completed','hiatus','cancelled')
,`chapters` int(11)
,`rating` decimal(3,2)
,`views` int(11)
,`follows` int(11)
,`featured` tinyint(1)
,`trending` tinyint(1)
,`is_new` tinyint(1)
,`created_at` timestamp
,`updated_at` timestamp
,`bookmark_count` bigint(21)
,`avg_rating` decimal(6,5)
,`rating_count` bigint(21)
);

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `story_id` int(11) NOT NULL,
  `rating` decimal(2,1) NOT NULL CHECK (`rating` >= 0 and `rating` <= 5),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `ratings`
--
DELIMITER $$
CREATE TRIGGER `update_story_rating_insert` AFTER INSERT ON `ratings` FOR EACH ROW BEGIN
    UPDATE stories 
    SET rating = (SELECT COALESCE(AVG(rating), 0) FROM ratings WHERE story_id = NEW.story_id)
    WHERE id = NEW.story_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_story_rating_update` AFTER UPDATE ON `ratings` FOR EACH ROW BEGIN
    UPDATE stories 
    SET rating = (SELECT COALESCE(AVG(rating), 0) FROM ratings WHERE story_id = NEW.story_id)
    WHERE id = NEW.story_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `reading_history`
--

CREATE TABLE `reading_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `story_id` int(11) NOT NULL,
  `chapter_id` int(11) NOT NULL,
  `page_number` int(11) DEFAULT 1,
  `last_read` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reading_sessions`
--

CREATE TABLE `reading_sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `story_id` int(11) NOT NULL,
  `chapter_id` int(11) DEFAULT NULL,
  `started_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `ended_at` timestamp NULL DEFAULT NULL,
  `duration_seconds` int(11) DEFAULT NULL,
  `pages_read` int(11) DEFAULT 0,
  `scroll_depth_percent` int(11) DEFAULT NULL,
  `pauses_count` int(11) DEFAULT 0,
  `revisit_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `story_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `review_text` text DEFAULT NULL,
  `comprehension_score` int(11) DEFAULT NULL CHECK (`comprehension_score` >= 1 and `comprehension_score` <= 5),
  `ease_of_reading` int(11) DEFAULT NULL CHECK (`ease_of_reading` >= 1 and `rating` <= 5),
  `engagement_level` int(11) DEFAULT NULL CHECK (`engagement_level` >= 1 and `engagement_level` <= 5),
  `would_recommend` tinyint(1) DEFAULT 1,
  `reading_time_minutes` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_verified` tinyint(1) DEFAULT 0,
  `helpful_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `story_id`, `user_id`, `rating`, `review_text`, `comprehension_score`, `ease_of_reading`, `engagement_level`, `would_recommend`, `reading_time_minutes`, `created_at`, `updated_at`, `is_verified`, `helpful_count`) VALUES
(1, 5, 2, 4, 'sdasdasdas', 5, 5, 5, 1, NULL, '2026-02-22 20:32:45', '2026-02-22 20:32:45', 0, 0);

--
-- Triggers `reviews`
--
DELIMITER $$
CREATE TRIGGER `update_story_rating_after_review` AFTER INSERT ON `reviews` FOR EACH ROW BEGIN
    UPDATE stories 
    SET rating = (
        SELECT AVG(rating) 
        FROM reviews 
        WHERE story_id = NEW.story_id
    )
    WHERE id = NEW.story_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_story_rating_after_review_delete` AFTER DELETE ON `reviews` FOR EACH ROW BEGIN
    UPDATE stories 
    SET rating = COALESCE((
        SELECT AVG(rating) 
        FROM reviews 
        WHERE story_id = OLD.story_id
    ), 0)
    WHERE id = OLD.story_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_story_rating_after_review_update` AFTER UPDATE ON `reviews` FOR EACH ROW BEGIN
    UPDATE stories 
    SET rating = (
        SELECT AVG(rating) 
        FROM reviews 
        WHERE story_id = NEW.story_id
    )
    WHERE id = NEW.story_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `stories`
--

CREATE TABLE `stories` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(100) NOT NULL,
  `cover` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `genre` varchar(100) NOT NULL,
  `status` enum('ongoing','completed','hiatus','cancelled') DEFAULT 'ongoing',
  `chapters` int(11) DEFAULT 0,
  `rating` decimal(3,2) DEFAULT 0.00,
  `views` int(11) DEFAULT 0,
  `follows` int(11) DEFAULT 0,
  `featured` tinyint(1) DEFAULT 0,
  `trending` tinyint(1) DEFAULT 0,
  `is_new` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stories`
--

INSERT INTO `stories` (`id`, `title`, `author`, `cover`, `description`, `genre`, `status`, `chapters`, `rating`, `views`, `follows`, `featured`, `trending`, `is_new`, `created_at`, `updated_at`) VALUES
(5, 'A Study In Scarlet', 'Conan, Doyle A.', 'assets/covers/cover_699b5251de479.jpg', 'When Dr. John Watson returns from war, he never expects to share a flat with the brilliant and eccentric Sherlock Holmes. But a mysterious murder in London soon draws them into a case unlike any other. With no clear motive and only a few strange clues, Holmes must rely on pure logic and sharp observation to uncover the truth. This is the unforgettable beginning of the legendary detective partnership.', 'Mystery', 'completed', 4, 4.00, 11, 0, 1, 0, 1, '2026-02-22 19:00:33', '2026-02-23 10:50:20'),
(6, 'A Tale of Two Cities', 'Charles Dickens', 'assets/covers/cover_699b53b57a42a.jpg', 'Set during the French Revolution, this powerful novel follows lives caught between London and Paris in a time of fear and uprising. Love, sacrifice, and redemption take center stage as ordinary people face extraordinary choices. A deeply moving historical drama about second chances and selfless devotion.', 'Historical Fiction', 'ongoing', 2, 0.00, 6, 0, 1, 1, 1, '2026-02-22 19:06:29', '2026-02-22 21:29:41'),
(7, 'Alice’s Adventures in Wonderland', 'Lewis Carroll', 'assets/covers/cover_699b53ff38c03.jpg', 'After following a white rabbit down a rabbit hole, young Alice enters a strange world filled with curious creatures and impossible logic. From the Mad Hatter to the Queen of Hearts, every encounter is both puzzling and delightful. A timeless fantasy that celebrates imagination and curiosity.', 'Adventure', 'ongoing', 2, 0.00, 0, 0, 0, 1, 1, '2026-02-22 19:07:43', '2026-02-22 21:06:38'),
(8, 'Anne of Green Gables', 'L.M. Montgomery', 'assets/covers/cover_699b54c0a1e6b.jpg', 'Anne Shirley, a spirited orphan with a vivid imagination, is mistakenly sent to live with siblings who expected a boy. Her charm, intelligence, and dramatic personality slowly win over the quiet town of Avonlea. A heartwarming story about belonging, friendship, and growing up.', 'Slice of Life', 'hiatus', 1, 0.00, 1, 0, 1, 0, 1, '2026-02-22 19:10:56', '2026-02-23 10:47:18'),
(9, 'A Thorn-to-Soothe a Dragon’s Throat', 'Iron Stardust', 'assets/covers/cover_699b570948019.png', 'In a land where dragons are feared and hunted, one unexpected encounter changes everything. When a young soul dares to approach a wounded dragon instead of fleeing in terror, a fragile bond begins to form between human and beast. But as tension rises and old conflicts threaten to reignite, both must face a world that refuses to believe in peace.\r\n\r\nA richly imagined fantasy about courage, compassion, and the power of understanding what others fear.', 'Fantasy', 'completed', 1, 0.00, 0, 0, 1, 0, 1, '2026-02-22 19:20:41', '2026-02-22 20:36:51'),
(10, 'Dracula', 'Bram Stoker', 'assets/covers/cover_699b574420bad.jpg', 'When Jonathan Harker travels to Transylvania, he encounters the mysterious Count Dracula. Soon, terrifying events begin to unfold in England as darkness spreads. A chilling Gothic horror classic that shaped the legend of vampires forever.', 'Horror', 'completed', 2, 0.00, 0, 0, 1, 1, 1, '2026-02-22 19:21:40', '2026-02-22 21:02:07'),
(11, 'Frankenstein', 'Mary Shelley', 'assets/covers/cover_699b576677e20.jpg', 'Victor Frankenstein dares to create life through science, but his experiment leads to tragic consequences. Rejected and alone, the creature struggles to understand humanity while seeking acceptance. A haunting story about ambition, responsibility, and the meaning of being human.', 'Science Fiction', 'ongoing', 2, 0.00, 3, 0, 1, 1, 1, '2026-02-22 19:22:14', '2026-02-22 20:56:50'),
(12, 'It All Started When I Was a Child', 'Iron Stardust', 'assets/covers/cover_699b57c13308e.jpeg', 'A reflective narrative that traces life’s journey from childhood memories to defining moments of growth. Through challenges and discoveries, the story explores how early experiences shape identity and purpose.', 'Fantasy', 'ongoing', 1, 0.00, 0, 0, 1, 0, 1, '2026-02-22 19:23:45', '2026-02-22 20:37:28'),
(13, 'Pride and Prejudice', 'Jane Austen', 'assets/covers/cover_699b588cef8c8.jpg', 'Elizabeth Bennet is intelligent and independent, while Mr. Darcy appears proud and distant. Misunderstandings and social expectations create tension between them, but as truths are revealed, both must confront their own flaws. A beloved romantic classic filled with wit and emotional depth.', 'Romance', 'ongoing', 2, 0.00, 0, 0, 1, 0, 1, '2026-02-22 19:27:08', '2026-02-22 20:48:31'),
(14, 'The Necklace', 'Guy de Maupassant', 'assets/covers/cover_699b58b9094d6.png', 'Mathilde Loisel longs for wealth and luxury beyond her modest life. After borrowing a necklace for a grand event, one mistake leads to years of hardship. A powerful short story about pride, desire, and unexpected consequences.', 'Slice of Life', 'ongoing', 1, 0.00, 0, 0, 1, 0, 1, '2026-02-22 19:27:53', '2026-02-22 20:45:43'),
(15, 'The Time Machine', 'H.G. Wells', 'assets/covers/cover_699b5912f2d86.jpg', 'A scientist invents a machine that allows him to travel into the distant future. There, he discovers a divided society and the surprising fate of humanity. A groundbreaking science fiction novel exploring time, evolution, and progress.', 'Science Fiction', 'hiatus', 2, 0.00, 0, 0, 1, 0, 1, '2026-02-22 19:29:22', '2026-02-22 20:47:13'),
(16, 'The War of the Worlds', 'H.G. Wells', 'assets/covers/cover_699b593ab45f5.jpg', 'When mysterious cylinders crash into Earth, advanced Martians launch a devastating invasion. As cities fall and chaos spreads, humanity struggles to survive. A gripping science fiction story about fear, resilience, and survival.', 'Science Fiction', 'completed', 2, 0.00, 0, 0, 1, 1, 1, '2026-02-22 19:30:02', '2026-02-22 20:47:48'),
(17, 'Wuthering Heights', 'Emily Brontë', 'assets/covers/cover_699b597b1ca4d.jpg', 'On the wild Yorkshire moors, the passionate and destructive love between Heathcliff and Catherine shapes generations. Their intense emotions leave lasting consequences for everyone around them. A dark and unforgettable story of obsession and fate.', 'Romance', 'completed', 2, 0.00, 0, 0, 1, 0, 1, '2026-02-22 19:31:07', '2026-02-22 20:49:26'),
(18, 'The Woman in White', 'Wilkie Collins', 'assets/covers/cover_699b59b745f38.jpg', 'A mysterious woman dressed in white appears unexpectedly, setting off a chain of secrets and conspiracies. As hidden identities unravel, danger lurks behind every discovery. A suspenseful Victorian mystery filled with intrigue.', 'Mystery', 'ongoing', 2, 0.00, 0, 0, 1, 0, 1, '2026-02-22 19:32:07', '2026-02-22 20:51:18'),
(19, 'Three Men in a Boat', 'Jerome K. Jerome', 'assets/covers/cover_699b59f9de724.jpg', 'Three friends set off on what should be a peaceful boating holiday along the Thames. Instead, they encounter a series of humorous mishaps and witty observations. A charming comedy about friendship and travel.', 'Comedy', 'ongoing', 1, 0.00, 0, 0, 1, 0, 1, '2026-02-22 19:33:13', '2026-02-22 21:05:49'),
(20, 'The Little Prince', 'Antoine de Saint-Exupéry', 'assets/covers/cover_699b5a278a3b8.jpg', 'A stranded pilot meets a young prince who has traveled across planets. Through gentle conversations, the prince shares meaningful lessons about love, loneliness, and what truly matters. A simple yet deeply philosophical story treasured by readers of all ages.', 'Inspirational', 'hiatus', 3, 0.00, 0, 0, 1, 0, 1, '2026-02-22 19:33:59', '2026-02-22 20:59:49'),
(21, 'The Woman Whose Truth Was Inconvenient', 'Charlene Martyn', 'assets/covers/cover_699b5ac883799.jpg', 'In a society where silence is safer than honesty, one woman dares to speak a truth others would rather ignore. As her words challenge powerful institutions and long-held beliefs, she finds herself isolated, questioned, and misunderstood. But the more resistance she faces, the stronger her resolve becomes.\r\n\r\nA compelling story about courage, integrity, and the cost of standing firm when the truth makes others uncomfortable.', 'Inspirational', 'ongoing', 1, 0.00, 0, 0, 1, 0, 1, '2026-02-22 19:36:40', '2026-02-22 21:00:20'),
(22, 'Tuned to Grace: A Rehoboth Story', 'Marelyn Gardner', 'assets/covers/cover_699b5b21bc3ed.jpg', 'In the quiet town of Rehoboth, broken notes and broken lives slowly begin to find harmony. As faith, forgiveness, and second chances intertwine, the characters discover that grace often arrives in the most unexpected moments. Through struggles, relationships, and personal trials, each journey becomes a reminder that even the most discordant life can be tuned back into something beautiful.\r\n\r\nA heartfelt story about redemption, healing, and the quiet strength found in faith and community.', 'Inspirational', 'hiatus', 1, 0.00, 0, 0, 1, 0, 1, '2026-02-22 19:38:09', '2026-02-22 21:00:51'),
(23, 'The Strange Case of Dr. Jekyll and Mr. Hyde', 'Robert Louis Stevenson', 'assets/covers/cover_699b5bad557b1.jpg', 'Dr. Henry Jekyll believes he can separate good and evil within himself through science. But his transformation into the violent Mr. Hyde reveals the darkness hidden inside human nature. A suspenseful psychological classic about dual identity.', 'Horror', 'completed', 1, 0.00, 0, 0, 1, 0, 1, '2026-02-22 19:40:29', '2026-02-22 21:02:35'),
(24, 'The Count of Monte Cristo', 'Alexandre Dumas', 'assets/covers/cover_699b5c3105aba.jpg', 'Wrongfully imprisoned, Edmond Dantès escapes after years of suffering and transforms himself into the wealthy Count of Monte Cristo. Determined to confront those who betrayed him, he carefully plans his revenge. An epic tale of justice, patience, and redemption.', 'Historical Fiction', 'completed', 2, 0.00, 0, 0, 1, 0, 1, '2026-02-22 19:42:41', '2026-02-22 21:04:11'),
(26, 'The Wonderful Wizard of Oz', 'L. Frank Baum', 'assets/covers/cover_699b5f098b6e5.jpg', 'After a cyclone carries Dorothy to the magical land of Oz, she begins a journey to find the Wizard who can send her home. Along the way, she befriends companions searching for courage, heart, and wisdom. A timeless fantasy about friendship and believing in oneself.', 'Fantasy', 'completed', 1, 0.00, 0, 0, 0, 1, 1, '2026-02-22 19:54:49', '2026-02-22 20:37:49'),
(27, 'Kidnapped', 'Robert Louis Stevenson', 'assets/covers/cover_699be9c2daeb5.jpg', 'After the death of his father, young David Balfour expects to claim his rightful inheritance. Instead, he is betrayed by his own uncle and sold into captivity. Stranded in the Scottish Highlands, David escapes and begins a dangerous journey filled with political conflict, shifting loyalties, and survival against the odds.\r\n\r\nA fast-paced adventure about courage, justice, and the resilience of a young man determined to reclaim his future.', 'Mystery', '', 0, 0.00, 3, 0, 0, 0, 1, '2026-02-23 05:46:42', '2026-02-23 10:50:09'),
(28, 'Jane Eyre', 'Charlotte Brontë', 'assets/covers/cover_699bea4627974.jpg', 'Orphaned and mistreated as a child, Jane Eyre grows into a strong, intelligent, and principled young woman. When she becomes a governess at the mysterious Thornfield Hall, she develops deep feelings for her employer, Mr. Rochester. But hidden secrets within the estate threaten her happiness and test her integrity.\r\n\r\nA timeless romance that explores love, independence, morality, and the strength of a woman who refuses to compromise her values.', 'Romance', '', 0, 0.00, 0, 0, 0, 0, 1, '2026-02-23 05:48:54', '2026-02-23 05:48:54');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `area` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `profile_picture` varchar(255) DEFAULT 'default.png',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `contact_number`, `area`, `password`, `role`, `profile_picture`, `last_login`, `created_at`) VALUES
(1, 'ana conchada', 'anaconchada@gmail.com', '09684538672', 'antipolo city', '$2y$10$Kkl3k.cnIruVlypTcVBIY.pvZzB3kU7Evc6KX/z6NFp5pKZptrEVG', 'user', 'assets/images/profile_picture.png', '2026-02-22 21:36:50', '2026-01-12 13:50:18'),
(2, 'Admin User', 'admin@readquest.com', '09123456789', 'Admin Office', '$2y$10$SFdXobrsXRaD3wr/d2ONBuvQpzjTqyvkpBmgLuVpQnITOnNuSLscC', 'admin', 'assets/images/profile_picture.png', '2026-02-23 05:08:44', '2026-02-21 07:54:07'),
(3, 'test', 'test@gmail.com', '0986749265447', 'antipolo city', '$2y$10$PzQR5SpPNRBi.jveCoWQA.u9mvl5nuf36nuvpCofhIN.6mNgtdVWe', 'user', 'assets/images/profile_picture.png', '2026-02-21 14:41:48', '2026-02-21 09:11:30');

-- --------------------------------------------------------

--
-- Structure for view `latest_updates`
--
DROP TABLE IF EXISTS `latest_updates`;

CREATE ALGORITHM=UNDEFINED DEFINER=`` SQL SECURITY DEFINER VIEW `latest_updates`  AS SELECT `s`.`id` AS `id`, `s`.`title` AS `title`, `s`.`cover` AS `cover`, `s`.`author` AS `author`, `c`.`chapter_number` AS `chapter_number`, `c`.`title` AS `chapter_title`, `c`.`created_at` AS `updated_at` FROM (`stories` `s` join `chapters` `c` on(`s`.`id` = `c`.`story_id`)) ORDER BY `c`.`created_at` DESC ;

-- --------------------------------------------------------

--
-- Structure for view `popular_stories`
--
DROP TABLE IF EXISTS `popular_stories`;

CREATE ALGORITHM=UNDEFINED DEFINER=`` SQL SECURITY DEFINER VIEW `popular_stories`  AS SELECT `s`.`id` AS `id`, `s`.`title` AS `title`, `s`.`author` AS `author`, `s`.`cover` AS `cover`, `s`.`description` AS `description`, `s`.`genre` AS `genre`, `s`.`status` AS `status`, `s`.`chapters` AS `chapters`, `s`.`rating` AS `rating`, `s`.`views` AS `views`, `s`.`follows` AS `follows`, `s`.`featured` AS `featured`, `s`.`trending` AS `trending`, `s`.`is_new` AS `is_new`, `s`.`created_at` AS `created_at`, `s`.`updated_at` AS `updated_at`, count(distinct `b`.`id`) AS `bookmark_count`, coalesce(avg(`r`.`rating`),0) AS `avg_rating`, count(distinct `r`.`id`) AS `rating_count` FROM ((`stories` `s` left join `bookmarks` `b` on(`s`.`id` = `b`.`story_id`)) left join `ratings` `r` on(`s`.`id` = `r`.`story_id`)) GROUP BY `s`.`id` ORDER BY `s`.`views` DESC ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookmarks`
--
ALTER TABLE `bookmarks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_bookmark` (`user_id`,`story_id`),
  ADD KEY `idx_user_bookmarks` (`user_id`),
  ADD KEY `idx_bookmarks_user` (`user_id`),
  ADD KEY `idx_bookmarks_story` (`story_id`),
  ADD KEY `idx_bookmarks_created` (`created_at`);

--
-- Indexes for table `chapters`
--
ALTER TABLE `chapters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_chapter` (`story_id`,`chapter_number`),
  ADD KEY `idx_story_chapter` (`story_id`,`chapter_number`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_comments_story` (`story_id`),
  ADD KEY `idx_comments_user` (`user_id`),
  ADD KEY `idx_comments_parent` (`parent_id`);

--
-- Indexes for table `genres`
--
ALTER TABLE `genres`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_page` (`chapter_id`,`page_number`),
  ADD KEY `idx_chapter_page` (`chapter_id`,`page_number`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_rating` (`user_id`,`story_id`),
  ADD KEY `story_id` (`story_id`);

--
-- Indexes for table `reading_history`
--
ALTER TABLE `reading_history`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_history` (`user_id`,`story_id`),
  ADD KEY `story_id` (`story_id`),
  ADD KEY `chapter_id` (`chapter_id`),
  ADD KEY `idx_user_history` (`user_id`,`last_read`);

--
-- Indexes for table `reading_sessions`
--
ALTER TABLE `reading_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `story_id` (`story_id`),
  ADD KEY `chapter_id` (`chapter_id`),
  ADD KEY `idx_sessions_user_story` (`user_id`,`story_id`),
  ADD KEY `idx_sessions_created` (`started_at`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_story` (`user_id`,`story_id`),
  ADD KEY `idx_reviews_story` (`story_id`),
  ADD KEY `idx_reviews_user` (`user_id`),
  ADD KEY `idx_reviews_created` (`created_at`);

--
-- Indexes for table `stories`
--
ALTER TABLE `stories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_genre` (`genre`),
  ADD KEY `idx_featured` (`featured`),
  ADD KEY `idx_trending` (`trending`),
  ADD KEY `idx_is_new` (`is_new`),
  ADD KEY `idx_rating` (`rating`),
  ADD KEY `idx_created` (`created_at`);
ALTER TABLE `stories` ADD FULLTEXT KEY `idx_search` (`title`,`author`,`description`);

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
-- AUTO_INCREMENT for table `bookmarks`
--
ALTER TABLE `bookmarks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chapters`
--
ALTER TABLE `chapters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `genres`
--
ALTER TABLE `genres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reading_history`
--
ALTER TABLE `reading_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reading_sessions`
--
ALTER TABLE `reading_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `stories`
--
ALTER TABLE `stories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookmarks`
--
ALTER TABLE `bookmarks`
  ADD CONSTRAINT `bookmarks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookmarks_ibfk_2` FOREIGN KEY (`story_id`) REFERENCES `stories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `chapters`
--
ALTER TABLE `chapters`
  ADD CONSTRAINT `chapters_ibfk_1` FOREIGN KEY (`story_id`) REFERENCES `stories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`story_id`) REFERENCES `stories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pages`
--
ALTER TABLE `pages`
  ADD CONSTRAINT `pages_ibfk_1` FOREIGN KEY (`chapter_id`) REFERENCES `chapters` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`story_id`) REFERENCES `stories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reading_history`
--
ALTER TABLE `reading_history`
  ADD CONSTRAINT `reading_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reading_history_ibfk_2` FOREIGN KEY (`story_id`) REFERENCES `stories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reading_history_ibfk_3` FOREIGN KEY (`chapter_id`) REFERENCES `chapters` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reading_sessions`
--
ALTER TABLE `reading_sessions`
  ADD CONSTRAINT `reading_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reading_sessions_ibfk_2` FOREIGN KEY (`story_id`) REFERENCES `stories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reading_sessions_ibfk_3` FOREIGN KEY (`chapter_id`) REFERENCES `chapters` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`story_id`) REFERENCES `stories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
