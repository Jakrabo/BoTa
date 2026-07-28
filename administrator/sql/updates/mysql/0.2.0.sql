ALTER TABLE `#__jt_athletes` ADD COLUMN `trainer_user_id` int unsigned NOT NULL DEFAULT 0 AFTER `class_id`;
ALTER TABLE `#__jt_athletes` ADD COLUMN `gender` varchar(20) DEFAULT NULL AFTER `trainer_user_id`;
ALTER TABLE `#__jt_athletes` ADD COLUMN `bow_type` varchar(50) DEFAULT NULL AFTER `gender`;
ALTER TABLE `#__jt_athletes` ADD KEY `idx_jt_athletes_trainer` (`trainer_user_id`);
