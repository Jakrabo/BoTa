ALTER TABLE `#__jt_athletes`
    ADD COLUMN IF NOT EXISTS `user_id` int unsigned NOT NULL DEFAULT 0 AFTER `class_id`;

ALTER TABLE `#__jt_athletes`
    ADD INDEX IF NOT EXISTS `idx_jt_athletes_user_id` (`user_id`);
