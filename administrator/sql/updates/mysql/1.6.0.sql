ALTER TABLE `#__jt_trainer_notes`
  ADD COLUMN IF NOT EXISTS `status` varchar(20) NOT NULL DEFAULT 'current' AFTER `private_note`;

CREATE INDEX IF NOT EXISTS `idx_jt_notes_status`
  ON `#__jt_trainer_notes` (`status`);
