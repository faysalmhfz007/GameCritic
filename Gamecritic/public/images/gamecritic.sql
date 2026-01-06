-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 07, 2025 at 09:35 PM
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
-- Database: `gamecritic`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `email`, `password`) VALUES
(1, '', 'admin@gamecritic.com', 'admin123');

-- --------------------------------------------------------

--
-- Table structure for table `comment_votes`
--

CREATE TABLE `comment_votes` (
  `id` int(11) NOT NULL,
  `comment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vote_type` enum('up','down') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comment_votes`
--

INSERT INTO `comment_votes` (`id`, `comment_id`, `user_id`, `vote_type`, `created_at`) VALUES
(4, 14, 3, 'down', '2025-09-07 18:16:41'),
(5, 16, 3, 'up', '2025-09-07 18:39:40');

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

CREATE TABLE `games` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `genre` varchar(100) DEFAULT NULL,
  `platform` varchar(100) DEFAULT NULL,
  `release_year` int(11) DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `review` text DEFAULT NULL,
  `pos_count` int(11) DEFAULT 0,
  `neg_count` int(11) DEFAULT 0,
  `overall_score` decimal(4,1) DEFAULT 0.0,
  `comments` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `games`
--

INSERT INTO `games` (`id`, `title`, `genre`, `platform`, `release_year`, `cover_image`, `description`, `review`, `pos_count`, `neg_count`, `overall_score`, `comments`) VALUES
(1, 'The Witcher 3: Wild Hunt', 'RPG', 'PC, PS4, Xbox One, Switch', 2015, '/images/Witcher_3_cover_art.jpg', 'An epic RPG following Geralt on his quest to find Ciri. Rich story, deep characters, and huge open world.', 'One of the best RPGs ever. Excellent writing, meaningful choices, and immersive gameplay.', 1, 0, 10.0, '[2025-09-07 20:39] Mrinu_4789: Best rpg game ever\n'),
(2, 'God of War (2018)', 'Action-Adventure', 'PS4, PS5', 2018, '/images/68bdd97e5b444.jpg', 'A reimagining of the God of War franchise with a more grounded, emotional journey.', 'Visually stunning, emotionally gripping, and full of brutal combat mechanics.', 2, 0, 10.0, NULL),
(3, 'Red Dead Redemption 1', 'Action-Adventure', 'PC, PS4, Xbox One', 2018, '/images/68bdd8976c5ba.jpg', 'A massive Western epic set in 1899, following outlaw Arthur Morgan.', 'Rockstar delivers a masterclass in storytelling, immersion, and world-building.', 1, 0, 10.0, NULL),
(4, 'Battlefield 6', 'War, fps, ', 'PC, Xbox, Playstation', 2025, '/images/default.jpg', 'Join an elite squad of Marine Raiders fighting relentlessly to save a world on the edge of collapse. From classic modes like Conquest and Breakthrough to new fast-paced, close-quarters experiences like Escalation, Battlefield 6 is bringing you more ways to battle than ever before.', NULL, 1, 1, 5.0, NULL),
(6, 'God of War: Ragnarök', 'Action', 'PlayStation', 2022, '/images/godofwar.jpg', 'Epic Norse adventure continues', NULL, 0, 0, 0.0, NULL),
(7, 'Elden Ring', 'RPG', 'Multi-platform', 2022, '/images/eldenring.jpg', 'FromSoftware masterpiece of exploration', NULL, 0, 0, 0.0, NULL),
(8, 'Zelda: Tears of the Kingdom', 'Adventure', 'Nintendo Switch', 2023, '/images/zelda.jpg', 'A magical return to Hyrule', NULL, 0, 0, 0.0, NULL),
(9, 'Spider-Man 2', 'Action', 'PlayStation', 2023, '/images/68bddaa5bf96f.jpg', 'Swing into action with Peter & Miles', NULL, 1, 1, 5.0, '[2025-09-07 20:16] Mrinu_4789: THis needs a remaster\n'),
(11, 'Hogwarts Legacy', 'RPG', 'Multi-platform', 2023, '/images/hogwarts.jpg', 'Live the wizarding dream at Hogwarts', NULL, 1, 0, 10.0, NULL),
(12, 'GTA V', ' Action-adventure.', 'PC, Playstation, Xbox', 2013, '/images/default.jpg', '\r\nGrand Theft Auto V is a 2013 action-adventure game developed by Rockstar North and published by Rockstar Games. It is the seventh main entry in the Grand Theft Auto series, following 2008\'s Grand Theft Auto IV, and the fifteenth instalment overall. ', NULL, 1, 0, 10.0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `poll_games`
--

CREATE TABLE `poll_games` (
  `id` int(11) NOT NULL,
  `game_name` varchar(255) NOT NULL,
  `game_picture` varchar(255) NOT NULL,
  `votes` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `poll_games`
--

INSERT INTO `poll_games` (`id`, `game_name`, `game_picture`, `votes`) VALUES
(1, 'Elden Ring 3', '/images/default.jpg', 0),
(2, 'GTA 6', '/images/default.jpg', 1),
(3, '007 First light', '/images/default.jpg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` decimal(3,1) NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `upvotes` int(11) DEFAULT 0,
  `downvotes` int(11) DEFAULT 0,
  `vote_score` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `game_id`, `user_id`, `rating`, `comment`, `created_at`, `upvotes`, `downvotes`, `vote_score`) VALUES
(1, 2, 3, 1.0, 'Thumb Up', '2025-08-22 13:35:16', 0, 0, 0),
(2, 2, 3, 1.0, NULL, '2025-08-22 13:39:57', 0, 0, 0),
(3, 3, 3, 1.0, NULL, '2025-08-22 16:16:32', 0, 0, 0),
(4, 4, 3, 1.0, NULL, '2025-08-22 16:17:07', 0, 0, 0),
(7, 4, 2, 0.0, NULL, '2025-09-06 13:51:04', 0, 0, 0),
(8, 12, 3, 1.0, NULL, '2025-09-06 14:21:40', 0, 0, 0),
(10, 11, 4, 1.0, NULL, '2025-09-07 16:55:59', 0, 0, 0),
(11, 9, 4, 1.0, NULL, '2025-09-07 16:56:25', 0, 0, 0),
(13, 9, 3, 0.0, NULL, '2025-09-07 18:16:07', 0, 0, 0),
(14, 9, 3, 0.0, 'THis needs a remaster', '2025-09-07 18:16:26', 0, 1, -1),
(15, 1, 3, 1.0, NULL, '2025-09-07 18:39:30', 0, 0, 0),
(16, 1, 3, 0.0, 'Best rpg game ever', '2025-09-07 18:39:38', 1, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `is_admin` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `phone`, `password`, `profile_picture`, `is_admin`) VALUES
(1, '', 'user@example.com', NULL, '123456', NULL, 0),
(2, '', 'admin@gamecritic.com', NULL, 'admin123', NULL, 1),
(3, 'Mrinu_4789', 'cars@gmail.com', '', '123457', NULL, 0),
(4, 'SOSB', 'SOSB@bracu.ac.bd', '', 'GOAT123', NULL, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `comment_votes`
--
ALTER TABLE `comment_votes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_comment` (`user_id`,`comment_id`),
  ADD KEY `comment_id` (`comment_id`);

--
-- Indexes for table `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `poll_games`
--
ALTER TABLE `poll_games`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `game_id` (`game_id`),
  ADD KEY `user_id` (`user_id`);

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
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `comment_votes`
--
ALTER TABLE `comment_votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `games`
--
ALTER TABLE `games`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `poll_games`
--
ALTER TABLE `poll_games`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comment_votes`
--
ALTER TABLE `comment_votes`
  ADD CONSTRAINT `comment_votes_ibfk_1` FOREIGN KEY (`comment_id`) REFERENCES `reviews` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comment_votes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
