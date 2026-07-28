<?php
namespace Jugendtraining\Component\Jugendtraining\Site\Model;
\defined('_JEXEC') or die;
use Joomla\CMS\Factory;use Joomla\CMS\MVC\Model\BaseDatabaseModel;
final class TrainingdiariesModel extends BaseDatabaseModel {
 public function getItems():array{$uid=(int)Factory::getApplication()->getIdentity()->id;$db=$this->getDatabase();$q=$db->getQuery(true)->select(['d.*','s.title setup_title','s.revision_no'])->from($db->quoteName('#__jt_training_diary','d'))->innerJoin($db->quoteName('#__jt_athletes','a').' ON a.id=d.athlete_id')->leftJoin($db->quoteName('#__jt_bow_setups','s').' ON s.id=d.bow_setup_id')->where('a.user_id='.$uid)->order('d.training_date DESC,d.id DESC');$db->setQuery($q);return$db->loadObjectList();}
}
