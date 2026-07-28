CREATE TABLE IF NOT EXISTS `#__jt_achievements` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(190) NOT NULL,
  `code` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(50) NOT NULL DEFAULT 'general',
  `award_mode` varchar(20) NOT NULL DEFAULT 'manual',
  `rule_type` varchar(50) DEFAULT NULL,
  `rule_value` decimal(12,2) DEFAULT NULL,
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
(`title`,`code`,`description`,`category`,`award_mode`,`rule_type`,`rule_value`,`rule_config`,`badge_image`,`ordering`,`published`) VALUES
('100 Pfeile an einem Tag','arrows_day_100','An einem Kalendertag mindestens 100 Pfeile dokumentiert.','fleiß','automatic','arrows_single_day',100,NULL,'images/jugendtraining/badges/arrows-day-100.png',10,1),
('500 Pfeile in einer Woche','arrows_week_500','Innerhalb einer Kalenderwoche mindestens 500 Pfeile dokumentiert.','fleiß','automatic','arrows_calendar_week',500,NULL,'images/jugendtraining/badges/arrows-week-500.png',20,1),
('4 Wochen Tagebuch-Streak','diary_streak_4','In vier aufeinanderfolgenden Kalenderwochen trainiert und Tagebuch geführt.','kontinuität','automatic','diary_week_streak',4,NULL,'images/jugendtraining/badges/streak-4.png',30,1),
('8 Wochen Tagebuch-Streak','diary_streak_8','In acht aufeinanderfolgenden Kalenderwochen trainiert und Tagebuch geführt.','kontinuität','automatic','diary_week_streak',8,NULL,'images/jugendtraining/badges/streak-8.png',40,1),
('12 Wochen Tagebuch-Streak','diary_streak_12','In zwölf aufeinanderfolgenden Kalenderwochen trainiert und Tagebuch geführt.','kontinuität','automatic','diary_week_streak',12,NULL,'images/jugendtraining/badges/streak-12.png',50,1),
('Erste Vereinsmeisterschaft','first_vm','Erste dokumentierte Teilnahme an einer Vereinsmeisterschaft.','meisterschaft','automatic','event_name_contains',1,'{"terms":["vereinsmeisterschaft"," vm "]}','images/jugendtraining/badges/first-vm.png',60,1),
('Erste Bezirksmeisterschaft','first_bm','Erste dokumentierte Teilnahme an einer Bezirksmeisterschaft.','meisterschaft','automatic','event_name_contains',1,'{"terms":["bezirksmeisterschaft"," bm "]}','images/jugendtraining/badges/first-bm.png',70,1),
('Nockpunkte wickeln','nockpoints','Kann Nockpunkte selbstständig wickeln.','technik','manual',NULL,NULL,NULL,'images/jugendtraining/badges/nockpoints.png',80,1),
('Pfeile befiedern','fletching','Kann Pfeile selbstständig befiedern.','technik','manual',NULL,NULL,NULL,'images/jugendtraining/badges/fletching.png',90,1),
('Schlechtwetter-Training','bad-weather','Hat bei anspruchsvollen Wetterbedingungen engagiert trainiert.','einsatz','manual',NULL,NULL,NULL,'images/jugendtraining/badges/bad-weather.png',100,1);
