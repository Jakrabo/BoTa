ALTER TABLE `#__jt_results`
    ADD COLUMN IF NOT EXISTS `verification_status` varchar(20) NOT NULL DEFAULT 'pending' AFTER `notes`,
    ADD COLUMN IF NOT EXISTS `verified_by` int unsigned NOT NULL DEFAULT 0 AFTER `verification_status`,
    ADD COLUMN IF NOT EXISTS `verified_at` datetime DEFAULT NULL AFTER `verified_by`;
