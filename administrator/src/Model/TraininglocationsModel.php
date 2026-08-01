<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\Model;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\Model\ListModel;use Joomla\Database\QueryInterface;
final class TraininglocationsModel extends ListModel{
 public function __construct($config=[]){$config['filter_fields']=['name','a.name','published','a.published','ordering','a.ordering'];parent::__construct($config);}
 protected function populateState($ordering='a.ordering',$direction='ASC'){$this->setState('filter.search',$this->getUserStateFromRequest($this->context.'.filter.search','filter_search'));parent::populateState($ordering,$direction);}
 protected function getListQuery():QueryInterface{$db=$this->getDatabase();$q=$db->getQuery(true)->select('a.*')->from($db->quoteName('#__jt_training_locations','a'));$search=trim((string)$this->getState('filter.search'));if($search!==''){$like=$db->quote('%'.$db->escape($search,true).'%');$q->where('(a.name LIKE '.$like.' OR a.address LIKE '.$like.')');}$q->order($db->escape($this->getState('list.ordering','a.ordering')).' '.$db->escape($this->getState('list.direction','ASC')).', a.name ASC');return$q;}
}
