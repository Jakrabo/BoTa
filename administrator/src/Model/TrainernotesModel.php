<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\Model;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\Model\ListModel;use Joomla\Database\QueryInterface;
final class TrainernotesModel extends ListModel {
 public function __construct($config=[]){$config['filter_fields']=['id','n.id','note_date','n.note_date','athlete','a.lastname','category','n.category'];parent::__construct($config);}
 protected function populateState($ordering='n.note_date',$direction='DESC'):void{$this->setState('filter.search',$this->getUserStateFromRequest($this->context.'.filter.search','filter_search'));parent::populateState($ordering,$direction);}
 protected function getListQuery():QueryInterface{$db=$this->getDatabase();$q=$db->getQuery(true)->select(['n.*',"CONCAT(a.firstname,' ',a.lastname) athlete_name"])->from($db->quoteName('#__jt_trainer_notes','n'))->leftJoin($db->quoteName('#__jt_athletes','a').' ON a.id=n.athlete_id');$s=trim((string)$this->getState('filter.search'));if($s!==''){$like=$db->quote('%'.$db->escape($s,true).'%');$q->where('(n.note LIKE '.$like.' OR a.firstname LIKE '.$like.' OR a.lastname LIKE '.$like.')');}$ord=$db->escape((string)$this->getState('list.ordering','n.note_date'));$dir=strtoupper((string)$this->getState('list.direction','DESC'))==='ASC'?'ASC':'DESC';return $q->order($ord.' '.$dir);}
}
