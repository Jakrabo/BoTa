<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\Model;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\Model\ListModel;use Joomla\Database\QueryInterface;
final class AchievementsModel extends ListModel{
 public function __construct($config=[]){$config['filter_fields']=['id','a.id','title','a.title','category','a.category','award_mode','a.award_mode','published','a.published','ordering','a.ordering'];parent::__construct($config);}
 protected function populateState($ordering='a.ordering',$direction='ASC'):void{$this->setState('filter.search',$this->getUserStateFromRequest($this->context.'.filter.search','filter_search'));$this->setState('filter.mode',$this->getUserStateFromRequest($this->context.'.filter.mode','filter_mode',''));parent::populateState($ordering,$direction);}
 protected function getListQuery():QueryInterface{$db=$this->getDatabase();$q=$db->getQuery(true)->select(['a.*','COUNT(aa.id) award_count'])->from($db->quoteName('#__jt_achievements','a'))->leftJoin($db->quoteName('#__jt_athlete_achievements','aa').' ON aa.achievement_id=a.id AND aa.revoked_at IS NULL');$s=trim((string)$this->getState('filter.search'));if($s!==''){$like=$db->quote('%'.$db->escape($s,true).'%');$q->where('(a.title LIKE '.$like.' OR a.code LIKE '.$like.' OR a.description LIKE '.$like.')');}$m=(string)$this->getState('filter.mode');if($m!=='')$q->where('a.award_mode='.$db->quote($m));return$q->group('a.id')->order($db->escape((string)$this->getState('list.ordering','a.ordering')).' '.($this->getState('list.direction')==='DESC'?'DESC':'ASC').',a.title');}
}
