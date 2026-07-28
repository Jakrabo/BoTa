ALTER TABLE `#__jt_achievements`
  ADD COLUMN IF NOT EXISTS `rule_terms` varchar(500) DEFAULT NULL AFTER `rule_value`,
  ADD COLUMN IF NOT EXISTS `requires_verified_result` tinyint NOT NULL DEFAULT 0 AFTER `rule_terms`;

UPDATE `#__jt_achievements`
SET `requires_verified_result` = 1
WHERE `rule_type` = 'event_name_contains';

UPDATE `#__jt_achievements`
SET `rule_terms` = 'vereinsmeisterschaft,VM'
WHERE `code` = 'first_vm' AND (`rule_terms` IS NULL OR `rule_terms` = '');

UPDATE `#__jt_achievements`
SET `rule_terms` = 'bezirksmeisterschaft,BM'
WHERE `code` = 'first_bm' AND (`rule_terms` IS NULL OR `rule_terms` = '');
