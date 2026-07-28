<?php
namespace Jugendtraining\Component\Jugendtraining\Site\Model;
\defined('_JEXEC') or die;
final class AchievementsModel extends DashboardModel
{
 public function getItems():array{
  $athlete=$this->getAthlete();if(!$athlete)return[];
  (new \Jugendtraining\Component\Jugendtraining\Site\Service\AchievementService($this->getDatabase()))->evaluateAthlete((int)$athlete->id);
  $db=$this->getDatabase();$q=$db->getQuery(true)->select(['aa.*','b.title','b.description','b.category','b.badge_image','b.award_mode'])
   ->from($db->quoteName('#__jt_athlete_achievements','aa'))->innerJoin($db->quoteName('#__jt_achievements','b').' ON b.id=aa.achievement_id')
   ->where('aa.athlete_id='.(int)$athlete->id)->where('aa.revoked_at IS NULL')->where('b.published=1')->order('aa.awarded_at DESC,aa.id DESC');
  $db->setQuery($q);return$db->loadObjectList();
 }
}
