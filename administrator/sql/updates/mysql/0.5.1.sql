CREATE TABLE IF NOT EXISTS `#__jt_goals` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `athlete_id` int unsigned NOT NULL,
  `title` varchar(190) NOT NULL,
  `description` text DEFAULT NULL,
  `target_type` varchar(30) NOT NULL DEFAULT 'score',
  `target_value` decimal(10,2) NOT NULL DEFAULT 0,
  `current_value` decimal(10,2) NOT NULL DEFAULT 0,
  `distance_m` smallint unsigned NOT NULL DEFAULT 0,
  `arrows` smallint unsigned NOT NULL DEFAULT 0,
  `due_date` date DEFAULT NULL,
  `completed` tinyint NOT NULL DEFAULT 0,
  `published` tinyint NOT NULL DEFAULT 1,
  `created` datetime DEFAULT NULL,
  `created_by` int unsigned NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `modified_by` int unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_jt_goals_athlete` (`athlete_id`),
  KEY `idx_jt_goals_completed` (`completed`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__jt_trainer_notes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `athlete_id` int unsigned NOT NULL,
  `note_date` date NOT NULL,
  `category` varchar(30) NOT NULL DEFAULT 'general',
  `note` text NOT NULL,
  `private_note` tinyint NOT NULL DEFAULT 1,
  `created` datetime DEFAULT NULL,
  `created_by` int unsigned NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `modified_by` int unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_jt_notes_athlete` (`athlete_id`),
  KEY `idx_jt_notes_date` (`note_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;
