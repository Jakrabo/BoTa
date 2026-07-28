CREATE TABLE IF NOT EXISTS `#__jt_training_groups` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(190) NOT NULL,
  `description` text DEFAULT NULL,
  `published` tinyint NOT NULL DEFAULT 1,
  `created` datetime DEFAULT NULL,
  `created_by` int unsigned NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `modified_by` int unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_jt_training_groups_published` (`published`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__jt_training_group_athletes` (
  `group_id` int unsigned NOT NULL,
  `athlete_id` int unsigned NOT NULL,
  PRIMARY KEY (`group_id`,`athlete_id`),
  KEY `idx_jt_tga_athlete` (`athlete_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__jt_training_group_trainers` (
  `group_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  PRIMARY KEY (`group_id`,`user_id`),
  KEY `idx_jt_tgt_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `#__jt_training_sessions`
  ADD COLUMN IF NOT EXISTS `training_group_id` int unsigned NOT NULL DEFAULT 0 AFTER `title`;

ALTER TABLE `#__jt_training_sessions`
  ADD INDEX IF NOT EXISTS `idx_jt_training_group_id` (`training_group_id`);
