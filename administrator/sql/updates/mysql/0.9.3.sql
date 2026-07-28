UPDATE `#__jt_achievements`
SET `badge_image` = REPLACE(
  `badge_image`,
  'media/com_jugendtraining/images/badges/',
  'images/jugendtraining/badges/'
)
WHERE `badge_image` LIKE 'media/com_jugendtraining/images/badges/%';
