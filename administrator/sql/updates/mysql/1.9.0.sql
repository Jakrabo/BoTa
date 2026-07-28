CREATE TABLE IF NOT EXISTS `#__jt_calendar_events` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(190) NOT NULL,
  `event_date` date NOT NULL,
  `event_time` time DEFAULT NULL,
  `location` varchar(190) DEFAULT NULL,
  `category` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `published` tinyint NOT NULL DEFAULT 1,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int unsigned NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `modified_by` int unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_jt_calendar_date` (`event_date`),
  KEY `idx_jt_calendar_category` (`category`),
  KEY `idx_jt_calendar_location` (`location`),
  KEY `idx_jt_calendar_published` (`published`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__jt_calendar_attachments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int unsigned NOT NULL,
  `file_name` varchar(190) NOT NULL,
  `mime_type` varchar(100) NOT NULL DEFAULT 'application/pdf',
  `file_size` int unsigned NOT NULL DEFAULT 0,
  `file_data` mediumtext NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_jt_calendar_attachment_event` (`event_id`),
  CONSTRAINT `fk_jt_calendar_attachment_event`
    FOREIGN KEY (`event_id`) REFERENCES `#__jt_calendar_events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;
