-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Oct 12, 2018 at 01:47 PM
-- Server version: 5.7.23
-- PHP Version: 7.2.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `ngisweden`
--

-- --------------------------------------------------------

--
-- Table structure for table `affiliations`
--

CREATE TABLE `affiliations` (
  `id` varchar(20) NOT NULL DEFAULT '',
  `name` varchar(100) DEFAULT NULL,
  `search` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` int(11) UNSIGNED NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `clarity_lab_uri` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `labs`
--

CREATE TABLE `labs` (
  `lab_clarity_uri` varchar(100) NOT NULL DEFAULT '',
  `lab_status` varchar(10) DEFAULT NULL,
  `lab_name` varchar(100) NOT NULL DEFAULT '',
  `lab_affiliation` varchar(50) DEFAULT NULL,
  `lab_pi` varchar(100) DEFAULT NULL,
  `log` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) UNSIGNED NOT NULL,
  `email` varchar(100) NOT NULL DEFAULT '',
  `time` int(11) NOT NULL,
  `ip` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `publications`
--

CREATE TABLE `publications` (
  `id` int(11) UNSIGNED NOT NULL,
  `pmid` int(11) DEFAULT NULL,
  `doi` varchar(100) DEFAULT NULL,
  `score` int(11) DEFAULT NULL,
  `keywords` text,
  `submitted` date DEFAULT NULL,
  `pubdate` date DEFAULT NULL,
  `journal` varchar(100) DEFAULT NULL,
  `volume` varchar(10) DEFAULT NULL,
  `issue` varchar(10) DEFAULT NULL,
  `pages` varchar(20) DEFAULT NULL,
  `title` text,
  `abstract` text,
  `authors` text,
  `status` varchar(20) DEFAULT NULL,
  `comments` text,
  `log` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `publications_text`
--

CREATE TABLE `publications_text` (
  `id` int(11) UNSIGNED NOT NULL,
  `publication_id` int(11) UNSIGNED NOT NULL,
  `status` varchar(11) DEFAULT NULL,
  `text` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `publications_xref`
--

CREATE TABLE `publications_xref` (
  `id` int(11) UNSIGNED NOT NULL,
  `publication_id` int(11) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `researchers`
--

CREATE TABLE `researchers` (
  `email` varchar(100) NOT NULL DEFAULT '',
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `query_name` varchar(100) DEFAULT NULL,
  `log` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `uid` int(11) NOT NULL,
  `user_email` varchar(100) NOT NULL DEFAULT '',
  `user_fname` varchar(100) DEFAULT '',
  `user_lname` varchar(100) DEFAULT NULL,
  `user_hash` varchar(128) NOT NULL,
  `user_auth` int(1) NOT NULL DEFAULT '0',
  `user_status` int(1) NOT NULL DEFAULT '0',
  `log` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `affiliations`
--
ALTER TABLE `affiliations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `labs`
--
ALTER TABLE `labs`
  ADD PRIMARY KEY (`lab_clarity_uri`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `publications`
--
ALTER TABLE `publications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `publications_text`
--
ALTER TABLE `publications_text`
  ADD PRIMARY KEY (`id`),
  ADD KEY `publication_id` (`publication_id`);

--
-- Indexes for table `publications_xref`
--
ALTER TABLE `publications_xref`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `researchers`
--
ALTER TABLE `researchers`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`uid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `publications`
--
ALTER TABLE `publications`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `publications_text`
--
ALTER TABLE `publications_text`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `publications_xref`
--
ALTER TABLE `publications_xref`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT;

