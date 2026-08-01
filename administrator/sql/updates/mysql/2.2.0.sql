CREATE TABLE IF NOT EXISTS `#__jt_training_locations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(190) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,7) NOT NULL,
  `longitude` decimal(10,7) NOT NULL,
  `published` tinyint NOT NULL DEFAULT 1,
  `ordering` int NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int unsigned NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `modified_by` int unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_jt_training_locations_name` (`name`),
  KEY `idx_jt_training_locations_published` (`published`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `#__jt_training_sessions`
  ADD COLUMN `location_id` int unsigned NOT NULL DEFAULT 0 AFTER `end_time`,
  ADD INDEX `idx_jt_training_location` (`location_id`);

ALTER TABLE `#__jt_athlete_programs`
  DROP INDEX `idx_jt_ap_unique`,
  ADD INDEX `idx_jt_ap_athlete_program` (`athlete_id`,`program_id`);

UPDATE `#__jt_results`
SET `verification_status`='pending'
WHERE `verification_status` IS NULL OR TRIM(`verification_status`)='' OR `verification_status` NOT IN ('pending','verified','rejected');
