ALTER TABLE `#__jt_athletes`
    ADD COLUMN IF NOT EXISTS `joomla_user_id` int unsigned NOT NULL DEFAULT 0 AFTER `class_id`;

ALTER TABLE `#__jt_athletes`
    ADD INDEX IF NOT EXISTS `idx_jt_athletes_joomla_user_id` (`joomla_user_id`);
