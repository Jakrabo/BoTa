ALTER TABLE `#__jt_training_sessions`
  ADD COLUMN IF NOT EXISTS `training_unit_id` int unsigned DEFAULT NULL AFTER `location`,
  ADD COLUMN IF NOT EXISTS `cancelled` tinyint NOT NULL DEFAULT 0 AFTER `notes`,
  ADD COLUMN IF NOT EXISTS `cancelled_at` datetime DEFAULT NULL AFTER `cancelled`,
  ADD COLUMN IF NOT EXISTS `cancelled_by` int unsigned NOT NULL DEFAULT 0 AFTER `cancelled_at`,
  ADD COLUMN IF NOT EXISTS `cancellation_reason` text DEFAULT NULL AFTER `cancelled_by`;

ALTER TABLE `#__jt_training_sessions`
  ADD INDEX IF NOT EXISTS `idx_jt_training_cancelled` (`cancelled`),
  ADD INDEX IF NOT EXISTS `idx_jt_training_unit` (`training_unit_id`);

CREATE TABLE IF NOT EXISTS `#__jt_training_units` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(190) NOT NULL,
  `description` text DEFAULT NULL,
  `published` tinyint NOT NULL DEFAULT 1,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int unsigned NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `modified_by` int unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_jt_training_units_published` (`published`),
  KEY `idx_jt_training_units_created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__jt_training_unit_items` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `training_unit_id` int unsigned NOT NULL,
  `exercise_id` int unsigned DEFAULT NULL,
  `exercise_title` varchar(190) NOT NULL,
  `duration_minutes` int unsigned NOT NULL DEFAULT 0,
  `goal` varchar(500) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `method` varchar(255) DEFAULT NULL,
  `material` text DEFAULT NULL,
  `remarks` varchar(1000) DEFAULT NULL,
  `ordering` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_jt_training_unit_items_unit` (`training_unit_id`),
  KEY `idx_jt_training_unit_items_exercise` (`exercise_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;
