-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Hôte : database
-- Généré le :  jeu. 19 mars 2020 à 09:27
-- Version du serveur :  10.4.11-MariaDB-1:10.4.11+maria~bionic
-- Version de PHP :  7.2.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `collabmedia`
--

-- --------------------------------------------------------

--
-- Structure de la table `comment`
--

CREATE TABLE `comment` (
                           `id` int(10) UNSIGNED NOT NULL,
                           `proposal_id` int(10) UNSIGNED NOT NULL,
                           `author_id` int(10) UNSIGNED NOT NULL,
                           `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
                           `date` datetime NOT NULL,
                           `edited_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `enabled_social_media`
--

CREATE TABLE `enabled_social_media` (
                                        `social_media_name` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
                                        `is_enabled` bit(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `file`
--

CREATE TABLE `file` (
                        `id` int(10) UNSIGNED NOT NULL,
                        `proposal_id` int(10) UNSIGNED NOT NULL,
                        `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `proposal`
--

CREATE TABLE `proposal` (
                            `id` int(10) UNSIGNED NOT NULL,
                            `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                            `date` datetime NOT NULL,
                            `submitter_id` int(10) UNSIGNED NOT NULL,
                            `social_media` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'social medias where the proposal will be published',
                            `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `proposal_approvement_setting`
--

CREATE TABLE `proposal_approvement_setting` (
                                                `id` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
                                                `required_review` int(11) NOT NULL,
                                                `approvement_percent` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `proposal_content_history`
--

CREATE TABLE `proposal_content_history` (
                                            `id` int(10) UNSIGNED NOT NULL,
                                            `proposal_id` int(10) UNSIGNED NOT NULL,
                                            `date` datetime NOT NULL,
                                            `content` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `proposal_file_history`
--

CREATE TABLE `proposal_file_history` (
                                         `id` int(11) UNSIGNED NOT NULL,
                                         `proposal_id` int(11) UNSIGNED NOT NULL,
                                         `date` datetime NOT NULL,
                                         `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `review`
--

CREATE TABLE `review` (
                          `id` int(10) UNSIGNED NOT NULL,
                          `reviewer_id` int(10) UNSIGNED NOT NULL,
                          `proposal_id` int(10) UNSIGNED NOT NULL,
                          `date` datetime NOT NULL,
                          `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `scheduled_publication`
--

CREATE TABLE `scheduled_publication` (
                                         `proposal_id` int(10) UNSIGNED NOT NULL,
                                         `scheduler_id` int(10) UNSIGNED NOT NULL,
                                         `record_date` datetime NOT NULL,
                                         `publication_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `social_media_permission`
--

CREATE TABLE `social_media_permission` (
                                           `publisher_id` int(10) UNSIGNED NOT NULL,
                                           `facebook_enabled` bit(1) NOT NULL,
                                           `twitter_enabled` bit(1) NOT NULL,
                                           `linkedin_enabled` bit(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
                        `id` int(10) UNSIGNED NOT NULL,
                        `firstname` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
                        `lastname` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
                        `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                        `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                        `role` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
                        `is_validated` bit(1) NOT NULL COMMENT 'true if user validated his account by choosing his password',
                        `is_active` bit(1) NOT NULL COMMENT 'true if account is active by admin',
                        `token` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `comment`
--
ALTER TABLE `comment`
    ADD PRIMARY KEY (`id`),
    ADD KEY `comment_proposal_id` (`proposal_id`),
    ADD KEY `comment_author_id` (`author_id`);

--
-- Index pour la table `enabled_social_media`
--
ALTER TABLE `enabled_social_media`
    ADD PRIMARY KEY (`social_media_name`);

--
-- Index pour la table `file`
--
ALTER TABLE `file`
    ADD PRIMARY KEY (`id`),
    ADD UNIQUE KEY `proposal_id` (`proposal_id`),
    ADD KEY `file_proposal_id` (`proposal_id`);

--
-- Index pour la table `proposal`
--
ALTER TABLE `proposal`
    ADD PRIMARY KEY (`id`),
    ADD KEY `proposal_submitter_id` (`submitter_id`);

--
-- Index pour la table `proposal_approvement_setting`
--
ALTER TABLE `proposal_approvement_setting`
    ADD UNIQUE KEY `id` (`id`);

--
-- Index pour la table `proposal_content_history`
--
ALTER TABLE `proposal_content_history`
    ADD PRIMARY KEY (`id`),
    ADD KEY `proposal_content_history_proposal_id` (`proposal_id`);

--
-- Index pour la table `proposal_file_history`
--
ALTER TABLE `proposal_file_history`
    ADD PRIMARY KEY (`id`),
    ADD KEY `proposal_file_history_proposal_id` (`proposal_id`);

--
-- Index pour la table `review`
--
ALTER TABLE `review`
    ADD PRIMARY KEY (`id`),
    ADD KEY `review_reviewer_id` (`reviewer_id`),
    ADD KEY `review_proposal_id` (`proposal_id`);

--
-- Index pour la table `scheduled_publication`
--
ALTER TABLE `scheduled_publication`
    ADD PRIMARY KEY (`proposal_id`),
    ADD KEY `scheduled_publication_scheduler_id` (`scheduler_id`);

--
-- Index pour la table `social_media_permission`
--
ALTER TABLE `social_media_permission`
    ADD PRIMARY KEY (`publisher_id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
    ADD PRIMARY KEY (`id`),
    ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `comment`
--
ALTER TABLE `comment`
    MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `file`
--
ALTER TABLE `file`
    MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `proposal`
--
ALTER TABLE `proposal`
    MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `proposal_content_history`
--
ALTER TABLE `proposal_content_history`
    MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `proposal_file_history`
--
ALTER TABLE `proposal_file_history`
    MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `review`
--
ALTER TABLE `review`
    MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
    MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `comment`
--
ALTER TABLE `comment`
    ADD CONSTRAINT `comment_author_id` FOREIGN KEY (`author_id`) REFERENCES `user` (`id`),
    ADD CONSTRAINT `comment_proposal_id` FOREIGN KEY (`proposal_id`) REFERENCES `proposal` (`id`);

--
-- Contraintes pour la table `file`
--
ALTER TABLE `file`
    ADD CONSTRAINT `file_proposal_id` FOREIGN KEY (`proposal_id`) REFERENCES `proposal` (`id`);

--
-- Contraintes pour la table `proposal`
--
ALTER TABLE `proposal`
    ADD CONSTRAINT `proposal_submitter_id` FOREIGN KEY (`submitter_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `proposal_content_history`
--
ALTER TABLE `proposal_content_history`
    ADD CONSTRAINT `proposal_content_history_proposal_id` FOREIGN KEY (`proposal_id`) REFERENCES `proposal` (`id`);

--
-- Contraintes pour la table `proposal_file_history`
--
ALTER TABLE `proposal_file_history`
    ADD CONSTRAINT `proposal_file_history_proposal_id` FOREIGN KEY (`proposal_id`) REFERENCES `proposal` (`id`);

--
-- Contraintes pour la table `review`
--
ALTER TABLE `review`
    ADD CONSTRAINT `review_proposal_id` FOREIGN KEY (`proposal_id`) REFERENCES `proposal` (`id`),
    ADD CONSTRAINT `review_reviewer_id` FOREIGN KEY (`reviewer_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `scheduled_publication`
--
ALTER TABLE `scheduled_publication`
    ADD CONSTRAINT `scheduled_publication_proposal_id` FOREIGN KEY (`proposal_id`) REFERENCES `proposal` (`id`),
    ADD CONSTRAINT `scheduled_publication_scheduler_id` FOREIGN KEY (`scheduler_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `social_media_permission`
--
ALTER TABLE `social_media_permission`
    ADD CONSTRAINT `social_media_permission` FOREIGN KEY (`publisher_id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
