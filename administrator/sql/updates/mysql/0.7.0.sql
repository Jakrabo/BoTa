CREATE TABLE IF NOT EXISTS `#__jt_bow_setups` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `athlete_id` int unsigned NOT NULL,
  `revision_no` int unsigned NOT NULL DEFAULT 1,
  `parent_revision_id` int unsigned DEFAULT NULL,
  `title` varchar(190) NOT NULL,
  `valid_from` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_active` tinyint NOT NULL DEFAULT 1,
  `detail_mode` tinyint NOT NULL DEFAULT 0,
  `riser` varchar(190) DEFAULT NULL,
  `limbs` varchar(190) DEFAULT NULL,
  `button_name` varchar(190) DEFAULT NULL,
  `button_side` varchar(100) DEFAULT NULL,
  `button_tension` varchar(100) DEFAULT NULL,
  `string_name` varchar(190) DEFAULT NULL,
  `string_material` varchar(100) DEFAULT NULL,
  `string_strands` int unsigned DEFAULT NULL,
  `arrows_name` varchar(190) DEFAULT NULL,
  `arrow_shaft` varchar(190) DEFAULT NULL,
  `arrow_spine` varchar(50) DEFAULT NULL,
  `arrow_fletching` varchar(190) DEFAULT NULL,
  `arrow_point_weight_gr` decimal(8,2) DEFAULT NULL,
  `stabilizer_mono` varchar(190) DEFAULT NULL,
  `stabilizer_side` varchar(190) DEFAULT NULL,
  `sight` varchar(190) DEFAULT NULL,
  `arrow_rest` varchar(190) DEFAULT NULL,
  `brace_height_mm` decimal(8,2) DEFAULT NULL,
  `tiller_top_mm` decimal(8,2) DEFAULT NULL,
  `tiller_bottom_mm` decimal(8,2) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_jt_bow_setup_revision` (`athlete_id`,`revision_no`),
  KEY `idx_jt_bow_setup_active` (`athlete_id`,`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__jt_sight_settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `bow_setup_id` int unsigned NOT NULL,
  `distance_m` decimal(8,2) NOT NULL,
  `extension_setting` varchar(100) DEFAULT NULL,
  `height_setting` varchar(100) DEFAULT NULL,
  `side_setting` varchar(100) DEFAULT NULL,
  `notes` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_jt_sight_setup_distance` (`bow_setup_id`,`distance_m`),
  KEY `idx_jt_sight_setup` (`bow_setup_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__jt_training_diary` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `athlete_id` int unsigned NOT NULL,
  `training_date` date NOT NULL,
  `duration_minutes` int unsigned DEFAULT NULL,
  `arrow_count` int unsigned DEFAULT NULL,
  `training_method` varchar(190) DEFAULT NULL,
  `distance_m` decimal(8,2) DEFAULT NULL,
  `focus_topic` varchar(190) DEFAULT NULL,
  `intensity` tinyint unsigned DEFAULT NULL,
  `feeling` tinyint unsigned DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `bow_setup_id` int unsigned DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int unsigned NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `modified_by` int unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_jt_diary_athlete_date` (`athlete_id`,`training_date`),
  KEY `idx_jt_diary_setup` (`bow_setup_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `#__jt_results`
  ADD COLUMN IF NOT EXISTS `bow_setup_id` int unsigned DEFAULT NULL AFTER `athlete_id`;

ALTER TABLE `#__jt_results`
  ADD INDEX IF NOT EXISTS `idx_jt_results_bow_setup` (`bow_setup_id`);
