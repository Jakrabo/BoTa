CREATE TABLE IF NOT EXISTS `#__jt_exercises` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(190) NOT NULL,
  `category` varchar(30) NOT NULL DEFAULT 'technique',
  `description` text DEFAULT NULL,
  `difficulty` tinyint unsigned NOT NULL DEFAULT 1,
  `material` varchar(255) DEFAULT NULL,
  `video_url` varchar(500) DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `published` tinyint NOT NULL DEFAULT 1,
  `ordering` int NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `created_by` int unsigned NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `modified_by` int unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_jt_exercises_category` (`category`),
  KEY `idx_jt_exercises_published` (`published`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__jt_training_programs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(190) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(30) NOT NULL DEFAULT 'technique',
  `published` tinyint NOT NULL DEFAULT 1,
  `created` datetime DEFAULT NULL,
  `created_by` int unsigned NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `modified_by` int unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_jt_programs_category` (`category`),
  KEY `idx_jt_programs_published` (`published`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__jt_program_exercises` (
  `program_id` int unsigned NOT NULL,
  `exercise_id` int unsigned NOT NULL,
  `ordering` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`program_id`,`exercise_id`),
  KEY `idx_jt_pe_exercise` (`exercise_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__jt_athlete_programs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `athlete_id` int unsigned NOT NULL,
  `program_id` int unsigned NOT NULL,
  `assigned_by` int unsigned NOT NULL DEFAULT 0,
  `assigned_at` datetime DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `active` tinyint NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_jt_ap_unique` (`athlete_id`,`program_id`),
  KEY `idx_jt_ap_program` (`program_id`),
  KEY `idx_jt_ap_active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__jt_program_progress` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `athlete_program_id` int unsigned NOT NULL,
  `exercise_id` int unsigned NOT NULL,
  `completed` tinyint NOT NULL DEFAULT 0,
  `completed_at` datetime DEFAULT NULL,
  `notes` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_jt_progress_unique` (`athlete_program_id`,`exercise_id`),
  KEY `idx_jt_progress_exercise` (`exercise_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;
