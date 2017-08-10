-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Ven 28 Juillet 2017 à 21:05
-- Version du serveur :  5.7.19-0ubuntu0.16.04.1
-- Version de PHP :  7.0.18-0ubuntu0.16.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `matcha42`
--
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS pictures;
DROP TABLE IF EXISTS userinterests;
DROP TABLE IF EXISTS userlocation;
DROP TABLE IF EXISTS iplocation;
DROP TABLE IF EXISTS likes;
DROP TABLE IF EXISTS notifications;
-- --------------------------------------------------------

--
-- Structure de la table `likes`
--

CREATE TABLE IF NOT EXISTS `likes` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_user_like` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `likes`
--

INSERT INTO `likes` (`id`, `id_user`, `id_user_like`, `created_at`, `updated_at`) VALUES
(1, 1, 2, '2017-07-28 20:57:51', '2017-07-28 20:57:51'),
(2, 2, 1, '2017-07-28 20:58:25', '2017-07-28 20:58:25'),
(3, 3, 2, '2017-07-28 21:04:00', '2017-07-28 21:04:00'),
(4, 1, 3, '2017-07-28 21:04:14', '2017-07-28 21:04:14');

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_user_dest` int(11) NOT NULL,
  `reading` tinyint(1) NOT NULL DEFAULT '0',
  `message` varchar(255) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `id_user`, `id_user_dest`, `reading`, `message`, `link`, `created_at`, `updated_at`) VALUES
(1, 'like', 1, 2, 1, 'like your profil', '/users/view/1', '2017-07-28 20:57:51', '2017-07-28 20:57:51'),
(2, 'like', 2, 1, 1, 'like your profil', '/users/view/2', '2017-07-28 20:58:25', '2017-07-28 20:58:25'),
(3, 'like', 3, 2, 0, 'like your profil', '/users/view/3', '2017-07-28 21:04:00', '2017-07-28 21:04:00'),
(4, 'like', 1, 3, 1, 'like your profil', '/users/view/1', '2017-07-28 21:04:14', '2017-07-28 21:04:14');

-- --------------------------------------------------------

--
-- Structure de la table `pictures`
--

CREATE TABLE IF NOT EXISTS `pictures` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  `is_profil` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `pictures`
--

INSERT INTO `pictures` (`id`, `id_user`, `url`, `is_profil`, `created_at`, `updated_at`) VALUES
(1, 1, '/image/597b888a69584.jpeg', 1, '2017-07-28 20:55:06', '2017-07-28 20:55:06'),
(2, 2, '/image/597b890309a74.png', 1, '2017-07-28 20:57:07', '2017-07-28 20:57:07'),
(3, 3, '/image/597b8a4968738.png', 1, '2017-07-28 21:02:33', '2017-07-28 21:02:33');

-- --------------------------------------------------------

--
-- Structure de la table `userinterests`
--

CREATE TABLE IF NOT EXISTS `userinterests` (
  `id` int(11) NOT NULL,
  `interest` varchar(140) NOT NULL,
  `id_user` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `userinterests`
--

INSERT INTO `userinterests` (`id`, `interest`, `id_user`, `created_at`, `updated_at`) VALUES
(1, '#cul', 1, '2017-07-28 20:54:56', '2017-07-28 20:54:56'),
(2, '#seins', 1, '2017-07-28 20:54:56', '2017-07-28 20:54:56'),
(3, '#foot', 1, '2017-07-28 20:54:56', '2017-07-28 20:54:56'),
(4, '#hand', 1, '2017-07-28 20:54:56', '2017-07-28 20:54:56'),
(5, '#concombre', 2, '2017-07-28 20:56:32', '2017-07-28 20:56:32'),
(6, '#banane', 2, '2017-07-28 20:56:32', '2017-07-28 20:56:32'),
(7, '#cornet', 2, '2017-07-28 20:56:32', '2017-07-28 20:56:32'),
(8, '#music', 2, '2017-07-28 20:56:32', '2017-07-28 20:56:32'),
(9, '#epaule', 2, '2017-07-28 20:56:47', '2017-07-28 20:56:47');

-- --------------------------------------------------------

--
-- Structure de la table `userlocation`
--

CREATE TABLE IF NOT EXISTS `userlocation` (
  `id` int(11) NOT NULL,
  `country` varchar(140) NOT NULL,
  `region` varchar(140) NOT NULL,
  `zipCode` int(11) DEFAULT NULL,
  `city` varchar(140) NOT NULL,
  `lat` float DEFAULT NULL,
  `lon` float DEFAULT NULL,
  `id_user` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `userlocation`
--

INSERT INTO `userlocation` (`id`, `country`, `region`, `zipCode`, `city`, `lat`, `lon`, `id_user`, `created_at`, `updated_at`) VALUES
(1, 'France', 'Île-de-France', 75001, 'Paris', 48.8582, 2.3387, 2, '2017-07-28 12:49:12', '2017-07-28 20:59:46'),
(2, 'France', 'Ile-de-France', 95210, 'Saint-Gratien', 48.964, 2.28643, 1, '2017-07-28 12:49:30', '2017-07-28 21:04:14'),
(3, 'France', 'Ile-de-France', 95210, 'Saint-Gratien', 48.964, 2.28644, 3, '2017-07-28 21:01:21', '2017-07-28 21:04:39');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `mail` varchar(255) NOT NULL,
  `password` varchar(512) NOT NULL,
  `age` int(11) NOT NULL,
  `gender` varchar(6) DEFAULT NULL,
  `orientation` varchar(255) DEFAULT NULL,
  `popularity` int(11) NOT NULL DEFAULT '0',
  `resume` varchar(140) DEFAULT NULL,
  `interests` varchar(8000) DEFAULT NULL,
  `last_seen` varchar(255) DEFAULT NULL,
  `is_connected` tinyint(1) NOT NULL DEFAULT '0',
  `token` varchar(255) NOT NULL,
  `verified` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `users`
--

INSERT INTO `users` (`id`, `name`, `lastname`, `mail`, `password`, `age`, `gender`, `orientation`, `popularity`, `resume`, `interests`, `last_seen`, `is_connected`, `token`, `verified`, `created_at`, `updated_at`) VALUES
(1, 'Hoareau', 'Alexandre', 'hoa.alexandre@gmail.com', '74dfc2b27acfa364da55f93a5caee29ccad3557247eda238831b3e9bd931b01d77fe994e4f12b9d4cfa92a124461d2065197d8cf7f33fc88566da2db2a4d6eae', 28, 'male', 'Woman', 0, 'salut alors moi je recherche des licornes!!! FUUUUUUUUUUUUUUUUUCK', 'a:4:{i:0;s:4:"#cul";i:1;s:6:"#seins";i:2;s:5:"#foot";i:3;s:5:"#hand";}', '28/07/2017 21:05:24', 1, 'toto', 1, '2017-07-28 00:00:00', '2017-07-28 20:54:56'),
(2, 'Medarhri', 'Roeam', 'mroeam@live.fr', '74dfc2b27acfa364da55f93a5caee29ccad3557247eda238831b3e9bd931b01d77fe994e4f12b9d4cfa92a124461d2065197d8cf7f33fc88566da2db2a4d6eae', 25, 'other', 'Bisexuel', 0, 'love is love', 'a:5:{i:0;s:10:"#concombre";i:1;s:7:"#cornet";i:2;s:6:"#music";i:3;s:7:"#epaule";i:4;s:7:"#banane";}', '28/07/2017 20:59:50', 0, 'tutu', 1, '2017-07-28 00:00:00', '2017-07-28 20:56:54'),
(3, 'Hoareau', 'Xav', 'lekz@hotmail.fr', '2eea15a08a8527cc516f9d26a0af12027d597f2dad3e90e3d2b161549010d30d850cf2e1b9b92d2fd9afda92d54b57561ae0eb65cc1f8e5b6300c0206dbf0906', 19, 'm', 'bisexuel', 0, NULL, NULL, '28/07/2017 21:05:19', 1, '2f69f4701c9d8a504a8076d6f288ed47', 1, '2017-07-28 21:00:23', '2017-07-28 21:01:04');

--
-- Index pour les tables exportées
--

--
-- Index pour la table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `pictures`
--
ALTER TABLE `pictures`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `userinterests`
--
ALTER TABLE `userinterests`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `userlocation`
--
ALTER TABLE `userlocation`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT pour la table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT pour la table `pictures`
--
ALTER TABLE `pictures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT pour la table `userinterests`
--
ALTER TABLE `userinterests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT pour la table `userlocation`
--
ALTER TABLE `userlocation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
