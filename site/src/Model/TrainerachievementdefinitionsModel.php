<?php
namespace Jugendtraining\Component\Jugendtraining\Site\Model;
\defined('_JEXEC') or die;
final class TrainerachievementdefinitionsModel extends TrainerModel{
 public function getDefinitions():array{$this->requireTrainer();$db=$this->getDatabase();$q=$db->getQuery(true)->select(['a.*','COUNT(aa.id) award_count'])->from($db->quoteName('#__jt_achievements','a'))->leftJoin($db->quoteName('#__jt_athlete_achievements','aa').' ON aa.achievement_id=a.id AND aa.revoked_at IS NULL')->group('a.id')->order('a.category,a.ordering,a.title');$db->setQuery($q);return$db->loadObjectList();}
}
