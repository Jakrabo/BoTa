-- Migration 0.1.4: fehlende Tabellen bei bestehenden Installationen anlegen.

CREATE TABLE IF NOT EXISTS `#__jt_clubs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(190) NOT NULL,
  `short_name` varchar(50) DEFAULT NULL,
  `published` tinyint NOT NULL DEFAULT 1,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int unsigned NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `modified_by` int unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_jt_clubs_name` (`name`),
  KEY `idx_jt_clubs_published` (`published`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__jt_classes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `min_age` tinyint unsigned DEFAULT NULL,
  `max_age` tinyint unsigned DEFAULT NULL,
  `gender` char(1) DEFAULT NULL,
  `published` tinyint NOT NULL DEFAULT 1,
  `ordering` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_jt_classes_published` (`published`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__jt_sportyears` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `date_start` date NOT NULL,
  `date_end` date NOT NULL,
  `is_current` tinyint NOT NULL DEFAULT 0,
  `published` tinyint NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_jt_sportyears_current` (`is_current`),
  KEY `idx_jt_sportyears_dates` (`date_start`, `date_end`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__jt_athletes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL DEFAULT 0,
  `club_id` int unsigned DEFAULT NULL,
  `class_id` int unsigned DEFAULT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `birthdate` date DEFAULT NULL,
  `membership_number` varchar(100) DEFAULT NULL,
  `email` varchar(190) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `published` tinyint NOT NULL DEFAULT 1,
  `ordering` int NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int unsigned NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `modified_by` int unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_jt_athletes_name` (`lastname`, `firstname`),
  KEY `idx_jt_athletes_user` (`user_id`),
  KEY `idx_jt_athletes_club` (`club_id`),
  KEY `idx_jt_athletes_class` (`class_id`),
  KEY `idx_jt_athletes_published` (`published`),
  CONSTRAINT `fk_jt_athletes_club` FOREIGN KEY (`club_id`) REFERENCES `#__jt_clubs` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_jt_athletes_class` FOREIGN KEY (`class_id`) REFERENCES `#__jt_classes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__jt_training_sessions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(190) NOT NULL,
  `training_date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `location` varchar(190) DEFAULT NULL,
  `trainer_user_id` int unsigned NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `published` tinyint NOT NULL DEFAULT 1,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int unsigned NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `modified_by` int unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_jt_training_date` (`training_date`),
  KEY `idx_jt_training_trainer` (`trainer_user_id`),
  KEY `idx_jt_training_published` (`published`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__jt_attendance` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `training_session_id` int unsigned NOT NULL,
  `athlete_id` int unsigned NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'present',
  `comment` varchar(500) DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int unsigned NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `modified_by` int unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_jt_attendance_unique` (`training_session_id`, `athlete_id`),
  KEY `idx_jt_attendance_athlete` (`athlete_id`),
  KEY `idx_jt_attendance_status` (`status`),
  CONSTRAINT `fk_jt_attendance_session` FOREIGN KEY (`training_session_id`) REFERENCES `#__jt_training_sessions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_jt_attendance_athlete` FOREIGN KEY (`athlete_id`) REFERENCES `#__jt_athletes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__jt_settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(190) NOT NULL,
  `setting_value` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_jt_settings_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__jt_audit_log` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL DEFAULT 0,
  `action` varchar(100) NOT NULL,
  `entity_type` varchar(100) NOT NULL,
  `entity_id` int unsigned DEFAULT NULL,
  `payload` longtext DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_jt_audit_entity` (`entity_type`, `entity_id`),
  KEY `idx_jt_audit_user` (`user_id`),
  KEY `idx_jt_audit_created` (`created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;
