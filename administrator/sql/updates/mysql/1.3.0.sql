ALTER TABLE `#__jt_athlete_programs`
  ADD COLUMN IF NOT EXISTS `completed_at` datetime DEFAULT NULL AFTER `due_date`;

INSERT INTO `#__jt_settings` (`setting_key`,`setting_value`)
SELECT 'penalty_balance_reset_at',''
WHERE NOT EXISTS (
  SELECT 1 FROM `#__jt_settings` WHERE `setting_key`='penalty_balance_reset_at'
);
