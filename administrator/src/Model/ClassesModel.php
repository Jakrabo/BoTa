<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\Model;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\QueryInterface;
final class ClassesModel extends ListModel
{
 public function __construct($config=[]) { $config['filter_fields']=['name', 'a.name', 'code', 'a.code', 'ordering', 'a.ordering', 'published', 'a.published']; parent::__construct($config); }
 protected function populateState($ordering='a.ordering',$direction='ASC') { $this->setState('filter.search',$this->getUserStateFromRequest($this->context.'.filter.search','filter_search')); parent::populateState($ordering,$direction); }
 protected function getListQuery(): QueryInterface { $db=$this->getDatabase(); $q=$db->getQuery(true)->select('a.*')->from($db->quoteName('#__jt_classes','a')); $s=$this->getState('filter.search'); if($s){$like=$db->quote('%'.$db->escape($s,true).'%');$q->where('(a.name LIKE '.$like.')');} $q->order($db->escape($this->getState('list.ordering','a.ordering')).' '.$db->escape($this->getState('list.direction','ASC'))); return $q; }
}
