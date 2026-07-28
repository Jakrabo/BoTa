ALTER TABLE `#__jt_results`
  ADD COLUMN IF NOT EXISTS `weather_condition` varchar(100) DEFAULT NULL AFTER `notes`,
  ADD COLUMN IF NOT EXISTS `temperature_c` decimal(5,2) DEFAULT NULL AFTER `weather_condition`,
  ADD COLUMN IF NOT EXISTS `wind_speed_kmh` decimal(6,2) DEFAULT NULL AFTER `temperature_c`,
  ADD COLUMN IF NOT EXISTS `wind_direction` varchar(50) DEFAULT NULL AFTER `wind_speed_kmh`;
