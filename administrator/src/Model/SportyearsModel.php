<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\Model;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\QueryInterface;
final class SportyearsModel extends ListModel
{
 public function __construct($config=[]) { $config['filter_fields']=['name', 'a.name', 'date_start', 'a.date_start', 'is_current', 'a.is_current', 'published', 'a.published']; parent::__construct($config); }
 protected function populateState($ordering='a.date_start',$direction='ASC') { $this->setState('filter.search',$this->getUserStateFromRequest($this->context.'.filter.search','filter_search')); parent::populateState($ordering,$direction); }
 protected function getListQuery(): QueryInterface { $db=$this->getDatabase(); $q=$db->getQuery(true)->select('a.*')->from($db->quoteName('#__jt_sportyears','a')); $s=$this->getState('filter.search'); if($s){$like=$db->quote('%'.$db->escape($s,true).'%');$q->where('(a.name LIKE '.$like.')');} $q->order($db->escape($this->getState('list.ordering','a.date_start')).' '.$db->escape($this->getState('list.direction','ASC'))); return $q; }
}
