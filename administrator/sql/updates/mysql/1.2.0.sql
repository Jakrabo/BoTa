CREATE TABLE IF NOT EXISTS `#__jt_penalty_definitions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(190) NOT NULL,
  `description` text DEFAULT NULL,
  `penalty_type` varchar(20) NOT NULL DEFAULT 'non_monetary',
  `amount` decimal(10,2) DEFAULT NULL,
  `non_monetary_action` varchar(500) DEFAULT NULL,
  `published` tinyint NOT NULL DEFAULT 1,
  `ordering` int NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int unsigned NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `modified_by` int unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_jt_penalty_definitions_type` (`penalty_type`),
  KEY `idx_jt_penalty_definitions_published` (`published`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__jt_penalty_register` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `athlete_id` int unsigned NOT NULL,
  `penalty_definition_id` int unsigned NOT NULL,
  `assigned_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `assigned_by` int unsigned NOT NULL DEFAULT 0,
  `reason_note` varchar(500) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'open',
  `completed_at` datetime DEFAULT NULL,
  `completed_by` int unsigned NOT NULL DEFAULT 0,
  `completion_note` varchar(500) DEFAULT NULL,
  `amount_snapshot` decimal(10,2) DEFAULT NULL,
  `action_snapshot` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_jt_penalty_register_athlete` (`athlete_id`),
  KEY `idx_jt_penalty_register_definition` (`penalty_definition_id`),
  KEY `idx_jt_penalty_register_status` (`status`),
  KEY `idx_jt_penalty_register_assigned` (`assigned_at`),
  CONSTRAINT `fk_jt_penalty_register_athlete`
    FOREIGN KEY (`athlete_id`) REFERENCES `#__jt_athletes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_jt_penalty_register_definition`
    FOREIGN KEY (`penalty_definition_id`) REFERENCES `#__jt_penalty_definitions` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;
