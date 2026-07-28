<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\Model;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\QueryInterface;
final class AthletesModel extends ListModel
{
 public function __construct($config=[]){ $config['filter_fields']=['id','a.id','firstname','a.firstname','lastname','a.lastname','published','a.published','club_name','class_name']; parent::__construct($config); }
 protected function populateState($ordering='a.lastname',$direction='ASC') { $this->setState('filter.search',$this->getUserStateFromRequest($this->context.'.filter.search','filter_search')); $this->setState('filter.published',$this->getUserStateFromRequest($this->context.'.filter.published','filter_published','')); parent::populateState($ordering,$direction); }
 protected function getListQuery(): QueryInterface
 {
  $db=$this->getDatabase(); $q=$db->getQuery(true)->select('a.*, c.name AS club_name, cl.name AS class_name, u.name AS user_name, t.name AS trainer_name')->from($db->quoteName('#__jt_athletes','a'))->leftJoin($db->quoteName('#__jt_clubs','c').' ON c.id=a.club_id')->leftJoin($db->quoteName('#__jt_classes','cl').' ON cl.id=a.class_id')->leftJoin($db->quoteName('#__users','u').' ON u.id=a.user_id')->leftJoin($db->quoteName('#__users','t').' ON t.id=a.trainer_user_id');
  $s=$this->getState('filter.search'); if($s){$like=$db->quote('%'.$db->escape($s,true).'%');$q->where('(a.firstname LIKE '.$like.' OR a.lastname LIKE '.$like.' OR a.membership_number LIKE '.$like.')');}
  $p=$this->getState('filter.published'); if($p!=='')$q->where('a.published='.(int)$p);
  $q->order($db->escape($this->getState('list.ordering','a.lastname')).' '.$db->escape($this->getState('list.direction','ASC'))); return $q;
 }
}
