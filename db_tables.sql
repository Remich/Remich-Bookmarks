-- phpMyAdmin SQL Dump
-- version 4.2.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 17, 2016 at 10:01 PM
-- Server version: 5.6.30-0ubuntu0.14.04.1
-- PHP Version: 7.0.8-4+deb.sury.org~trusty+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `remich_bookmarks_scheme`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookmark_items`
--

CREATE TABLE IF NOT EXISTS `bookmark_items` (
`id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '',
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `hits` int(11) NOT NULL DEFAULT '0',
  `trashed` tinyint(1) NOT NULL DEFAULT '0',
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  `last_hit` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `thumbnail` varchar(255) NOT NULL,
  `thumbnail_update` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bookmark_tags`
--

CREATE TABLE IF NOT EXISTS `bookmark_tags` (
`id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `hits` int(11) NOT NULL DEFAULT '0',
  `parent` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `bookmark_tags`
--

INSERT INTO `bookmark_tags` (`id`, `uid`, `name`, `hits`, `parent`) VALUES
(0, 0, 'Not Tagged', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `rel_bookmarks_tags`
--

CREATE TABLE IF NOT EXISTS `rel_bookmarks_tags` (
`id` int(11) NOT NULL,
  `id_a` int(11) NOT NULL,
  `id_b` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
`id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `userhash` varchar(255) NOT NULL,
  `last_login_attempt` datetime NOT NULL,
  `mail` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookmark_items`
--
ALTER TABLE `bookmark_items`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bookmark_tags`
--
ALTER TABLE `bookmark_tags`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rel_bookmarks_tags`
--
ALTER TABLE `rel_bookmarks_tags`
 ADD PRIMARY KEY (`id`), ADD KEY `id_a` (`id_a`), ADD KEY `id_b` (`id_b`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookmark_items`
--
ALTER TABLE `bookmark_items`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `bookmark_tags`
--
ALTER TABLE `bookmark_tags`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `rel_bookmarks_tags`
--
ALTER TABLE `rel_bookmarks_tags`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `rel_bookmarks_tags`
--
ALTER TABLE `rel_bookmarks_tags`
ADD CONSTRAINT `bookmark_item` FOREIGN KEY (`id_a`) REFERENCES `bookmark_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `bookmark_tag` FOREIGN KEY (`id_b`) REFERENCES `bookmark_tags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
