ALTER TABLE `#__jt_goals`
  ADD COLUMN IF NOT EXISTS `calculation_mode` varchar(20) NOT NULL DEFAULT 'automatic' AFTER `target_type`,
  ADD COLUMN IF NOT EXISTS `program_id` int unsigned NOT NULL DEFAULT 0 AFTER `arrows`;

ALTER TABLE `#__jt_goals`
  ADD INDEX IF NOT EXISTS `idx_jt_goals_program_id` (`program_id`);
