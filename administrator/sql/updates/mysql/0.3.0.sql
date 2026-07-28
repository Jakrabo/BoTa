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
  CONSTRAINT `fk_jt_attendance_session`
    FOREIGN KEY (`training_session_id`)
    REFERENCES `#__jt_training_sessions` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_jt_attendance_athlete`
    FOREIGN KEY (`athlete_id`)
    REFERENCES `#__jt_athletes` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;
