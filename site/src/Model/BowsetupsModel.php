<?php
namespace Jugendtraining\Component\Jugendtraining\Site\Model;
\defined('_JEXEC') or die;
use Joomla\CMS\Factory;use Joomla\CMS\MVC\Model\BaseDatabaseModel;
final class BowsetupsModel extends BaseDatabaseModel {
 public function getItems():array{$aid=$this->athleteId();if(!$aid)return[];$db=$this->getDatabase();$q=$db->getQuery(true)->select('*')->from($db->quoteName('#__jt_bow_setups'))->where('athlete_id='.$aid)->order('revision_no DESC');$db->setQuery($q);return $db->loadObjectList();}
 private function athleteId():int{$uid=(int)Factory::getApplication()->getIdentity()->id;$db=$this->getDatabase();$q=$db->getQuery(true)->select('id')->from($db->quoteName('#__jt_athletes'))->where('user_id='.$uid)->where('published=1');$db->setQuery($q,0,1);return(int)$db->loadResult();}
}
