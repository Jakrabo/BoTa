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
  `trainer_user_id` int unsigned NOT NULL DEFAULT 0,
  `gender` varchar(20) DEFAULT NULL,
  `bow_type` varchar(50) DEFAULT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `birthdate` date DEFAULT NULL,
  `membership_number` varchar(100) DEFAULT NULL,
  `email` varchar(190) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `weather_condition` varchar(100) DEFAULT NULL,
  `temperature_c` decimal(5,2) DEFAULT NULL,
  `wind_speed_kmh` decimal(6,2) DEFAULT NULL,
  `wind_direction` varchar(50) DEFAULT NULL,
  `verification_status` varchar(20) NOT NULL DEFAULT 'pending',
  `verified_by` int unsigned NOT NULL DEFAULT 0,
  `verified_at` datetime DEFAULT NULL,
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
  KEY `idx_jt_athletes_trainer` (`trainer_user_id`),
  KEY `idx_jt_athletes_published` (`published`),
  CONSTRAINT `fk_jt_athletes_club` FOREIGN KEY (`club_id`) REFERENCES `#__jt_clubs` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_jt_athletes_class` FOREIGN KEY (`class_id`) REFERENCES `#__jt_classes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__jt_training_sessions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(190) NOT NULL,
  `training_group_id` int unsigned NOT NULL DEFAULT 0,
  `training_date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `location` varchar(190) DEFAULT NULL,
  `trainer_user_id` int unsigned NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `weather_condition` varchar(100) DEFAULT NULL,
  `temperature_c` decimal(5,2) DEFAULT NULL,
  `wind_speed_kmh` decimal(6,2) DEFAULT NULL,
  `wind_direction` varchar(50) DEFAULT NULL,
  `verification_status` varchar(20) NOT NULL DEFAULT 'pending',
  `verified_by` int unsigned NOT NULL DEFAULT 0,
  `verified_at` datetime DEFAULT NULL,
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


CREATE TABLE IF NOT EXISTS `#__jt_results` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `athlete_id` int unsigned NOT NULL,
  `result_date` date NOT NULL,
  `event_type` varchar(30) NOT NULL DEFAULT 'training',
  `event_name` varchar(190) DEFAULT NULL,
  `distance_m` int unsigned NOT NULL DEFAULT 18,
  `arrows` int unsigned NOT NULL DEFAULT 0,
  `score` int unsigned NOT NULL DEFAULT 0,
  `average` decimal(8,3) NOT NULL DEFAULT 0.000,
  `tens` int unsigned NOT NULL DEFAULT 0,
  `xs` int unsigned NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `weather_condition` varchar(100) DEFAULT NULL,
  `temperature_c` decimal(5,2) DEFAULT NULL,
  `wind_speed_kmh` decimal(6,2) DEFAULT NULL,
  `wind_direction` varchar(50) DEFAULT NULL,
  `verification_status` varchar(20) NOT NULL DEFAULT 'pending',
  `verified_by` int unsigned NOT NULL DEFAULT 0,
  `verified_at` datetime DEFAULT NULL,
  `published` tinyint NOT NULL DEFAULT 1,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int unsigned NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `modified_by` int unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_jt_results_athlete` (`athlete_id`),
  KEY `idx_jt_results_date` (`result_date`),
  KEY `idx_jt_results_event_type` (`event_type`),
  KEY `idx_jt_results_distance` (`distance_m`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;



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



CREATE TABLE IF NOT EXISTS `#__jt_goals` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `athlete_id` int unsigned NOT NULL,
  `title` varchar(190) NOT NULL,
  `description` text DEFAULT NULL,
  `target_type` varchar(30) NOT NULL DEFAULT 'score',
  `calculation_mode` varchar(20) NOT NULL DEFAULT 'automatic',
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
  `status` varchar(20) NOT NULL DEFAULT 'current',
  `created` datetime DEFAULT NULL,
  `created_by` int unsigned NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `modified_by` int unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_jt_notes_athlete` (`athlete_id`),
  KEY `idx_jt_notes_date` (`note_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;



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




CREATE TABLE IF NOT EXISTS `#__jt_achievements` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(190) NOT NULL,
  `code` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(50) NOT NULL DEFAULT 'general',
  `award_mode` varchar(20) NOT NULL DEFAULT 'manual',
  `rule_type` varchar(50) DEFAULT NULL,
  `rule_value` decimal(12,2) DEFAULT NULL,
  `rule_terms` varchar(500) DEFAULT NULL,
  `requires_verified_result` tinyint NOT NULL DEFAULT 0,
  `rule_config` text DEFAULT NULL,
  `badge_image` varchar(500) DEFAULT NULL,
  `ordering` int NOT NULL DEFAULT 0,
  `published` tinyint NOT NULL DEFAULT 1,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int unsigned NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `modified_by` int unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_jt_achievement_code` (`code`),
  KEY `idx_jt_achievement_mode` (`award_mode`),
  KEY `idx_jt_achievement_category` (`category`),
  KEY `idx_jt_achievement_published` (`published`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__jt_athlete_achievements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `athlete_id` int unsigned NOT NULL,
  `achievement_id` int unsigned NOT NULL,
  `awarded_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `awarded_by` int unsigned NOT NULL DEFAULT 0,
  `award_source` varchar(20) NOT NULL DEFAULT 'manual',
  `note` varchar(500) DEFAULT NULL,
  `evidence_value` decimal(12,2) DEFAULT NULL,
  `revoked_at` datetime DEFAULT NULL,
  `revoked_by` int unsigned NOT NULL DEFAULT 0,
  `revoke_note` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_jt_athlete_achievement_unique` (`athlete_id`,`achievement_id`),
  KEY `idx_jt_aa_athlete` (`athlete_id`),
  KEY `idx_jt_aa_achievement` (`achievement_id`),
  KEY `idx_jt_aa_awarded` (`awarded_at`),
  CONSTRAINT `fk_jt_aa_athlete` FOREIGN KEY (`athlete_id`) REFERENCES `#__jt_athletes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_jt_aa_achievement` FOREIGN KEY (`achievement_id`) REFERENCES `#__jt_achievements` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `#__jt_achievements`
(`title`,`code`,`description`,`category`,`award_mode`,`rule_type`,`rule_value`,`rule_terms`,`requires_verified_result`,`rule_config`,`badge_image`,`ordering`,`published`) VALUES
('100 Pfeile an einem Tag','arrows_day_100','An einem Kalendertag mindestens 100 Pfeile dokumentiert.','fleiß','automatic','arrows_single_day',100,NULL,0,NULL,'images/jugendtraining/badges/arrows-day-100.png',10,1),
('500 Pfeile in einer Woche','arrows_week_500','Innerhalb einer Kalenderwoche mindestens 500 Pfeile dokumentiert.','fleiß','automatic','arrows_calendar_week',500,NULL,0,NULL,'images/jugendtraining/badges/arrows-week-500.png',20,1),
('4 Wochen Tagebuch-Streak','diary_streak_4','In vier aufeinanderfolgenden Kalenderwochen trainiert und Tagebuch geführt.','kontinuität','automatic','diary_week_streak',4,NULL,0,NULL,'images/jugendtraining/badges/streak-4.png',30,1),
('8 Wochen Tagebuch-Streak','diary_streak_8','In acht aufeinanderfolgenden Kalenderwochen trainiert und Tagebuch geführt.','kontinuität','automatic','diary_week_streak',8,NULL,0,NULL,'images/jugendtraining/badges/streak-8.png',40,1),
('12 Wochen Tagebuch-Streak','diary_streak_12','In zwölf aufeinanderfolgenden Kalenderwochen trainiert und Tagebuch geführt.','kontinuität','automatic','diary_week_streak',12,NULL,0,NULL,'images/jugendtraining/badges/streak-12.png',50,1),
('Erste Vereinsmeisterschaft','first_vm','Erste bestätigte Teilnahme an einer Vereinsmeisterschaft.','meisterschaft','automatic','event_name_contains',1,'vereinsmeisterschaft,VM',1,'{"terms":["vereinsmeisterschaft"," vm "]}','images/jugendtraining/badges/first-vm.png',60,1),
('Erste Bezirksmeisterschaft','first_bm','Erste bestätigte Teilnahme an einer Bezirksmeisterschaft.','meisterschaft','automatic','event_name_contains',1,'bezirksmeisterschaft,BM',1,'{"terms":["bezirksmeisterschaft"," bm "]}','images/jugendtraining/badges/first-bm.png',70,1),
('Nockpunkte wickeln','nockpoints','Kann Nockpunkte selbstständig wickeln.','technik','manual',NULL,NULL,NULL,0,NULL,'images/jugendtraining/badges/nockpoints.png',80,1),
('Pfeile befiedern','fletching','Kann Pfeile selbstständig befiedern.','technik','manual',NULL,NULL,NULL,0,NULL,'images/jugendtraining/badges/fletching.png',90,1),
('Schlechtwetter-Training','bad-weather','Hat bei anspruchsvollen Wetterbedingungen engagiert trainiert.','einsatz','manual',NULL,NULL,NULL,0,NULL,'images/jugendtraining/badges/bad-weather.png',100,1);

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
