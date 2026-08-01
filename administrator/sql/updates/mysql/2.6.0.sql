ALTER TABLE `#__jt_training_sessions`
  ADD COLUMN IF NOT EXISTS `training_unit_id` int unsigned DEFAULT NULL AFTER `location`,
  ADD COLUMN IF NOT EXISTS `cancelled` tinyint NOT NULL DEFAULT 0 AFTER `notes`,
  ADD COLUMN IF NOT EXISTS `cancelled_at` datetime DEFAULT NULL AFTER `cancelled`,
  ADD COLUMN IF NOT EXISTS `cancelled_by` int unsigned NOT NULL DEFAULT 0 AFTER `cancelled_at`,
  ADD COLUMN IF NOT EXISTS `cancellation_reason` text DEFAULT NULL AFTER `cancelled_by`;

ALTER TABLE `#__jt_training_sessions`
  ADD INDEX IF NOT EXISTS `idx_jt_training_cancelled` (`cancelled`),
  ADD INDEX IF NOT EXISTS `idx_jt_training_unit` (`training_unit_id`);
