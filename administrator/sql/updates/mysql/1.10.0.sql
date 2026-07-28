ALTER TABLE `#__jt_calendar_events`
  ADD COLUMN IF NOT EXISTS `event_date_end` date DEFAULT NULL AFTER `event_date`,
  ADD COLUMN IF NOT EXISTS `event_time_end` time DEFAULT NULL AFTER `event_time`,
  ADD COLUMN IF NOT EXISTS `audience` varchar(20) NOT NULL DEFAULT 'all' AFTER `description`,
  ADD COLUMN IF NOT EXISTS `training_group_id` int unsigned DEFAULT NULL AFTER `audience`;

UPDATE `#__jt_calendar_events`
SET `event_date_end`=`event_date`
WHERE `event_date_end` IS NULL;

CREATE INDEX IF NOT EXISTS `idx_jt_calendar_date_end`
  ON `#__jt_calendar_events` (`event_date_end`);

CREATE INDEX IF NOT EXISTS `idx_jt_calendar_audience`
  ON `#__jt_calendar_events` (`audience`);

CREATE INDEX IF NOT EXISTS `idx_jt_calendar_group`
  ON `#__jt_calendar_events` (`training_group_id`);
