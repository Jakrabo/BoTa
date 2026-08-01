<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\Model;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\QueryInterface;
final class AthletesModel extends ListModel
{
 public function __construct($config=[]){ $config['filter_fields']=['id','a.id','firstname','a.firstname','lastname','a.lastname','published','a.published','club_name','c.name','class_name','cl.name','bow_type','a.bow_type']; parent::__construct($config); }
 protected function populateState($ordering='a.lastname',$direction='ASC') {
  $this->setState('filter.search',$this->getUserStateFromRequest($this->context.'.filter.search','filter_search'));
  $this->setState('filter.published',$this->getUserStateFromRequest($this->context.'.filter.published','filter_published',''));
  $this->setState('filter.club_id',$this->getUserStateFromRequest($this->context.'.filter.club_id','filter_club_id',0,'int'));
  $this->setState('filter.bow_type',$this->getUserStateFromRequest($this->context.'.filter.bow_type','filter_bow_type',''));
  parent::populateState($ordering,$direction);
 }
 protected function getListQuery(): QueryInterface
 {
  $db=$this->getDatabase(); $q=$db->getQuery(true)->select('a.*, c.name AS club_name, cl.name AS class_name, u.name AS user_name, t.name AS trainer_name, GROUP_CONCAT(DISTINCT g.title ORDER BY g.title SEPARATOR ", ") AS group_names')->from($db->quoteName('#__jt_athletes','a'))->leftJoin($db->quoteName('#__jt_clubs','c').' ON c.id=a.club_id')->leftJoin($db->quoteName('#__jt_classes','cl').' ON cl.id=a.class_id')->leftJoin($db->quoteName('#__users','u').' ON u.id=a.user_id')->leftJoin($db->quoteName('#__users','t').' ON t.id=a.trainer_user_id')->leftJoin($db->quoteName('#__jt_training_group_athletes','ga').' ON ga.athlete_id=a.id')->leftJoin($db->quoteName('#__jt_training_groups','g').' ON g.id=ga.group_id');
  $s=$this->getState('filter.search'); if($s){$like=$db->quote('%'.$db->escape($s,true).'%');$q->where('(a.firstname LIKE '.$like.' OR a.lastname LIKE '.$like.' OR CONCAT(a.firstname,\' \',a.lastname) LIKE '.$like.' OR CONCAT(a.lastname,\', \',a.firstname) LIKE '.$like.' OR a.membership_number LIKE '.$like.' OR c.name LIKE '.$like.' OR cl.name LIKE '.$like.' OR g.title LIKE '.$like.')');}
  $p=$this->getState('filter.published'); if($p!=='')$q->where('a.published='.(int)$p);
  $clubId=(int)$this->getState('filter.club_id'); if($clubId>0)$q->where('a.club_id='.$clubId);
  $bowType=(string)$this->getState('filter.bow_type'); if($bowType!=='')$q->where('a.bow_type='.$db->quote($bowType));
  $q->group('a.id');
  $q->order($db->escape($this->getState('list.ordering','a.lastname')).' '.$db->escape($this->getState('list.direction','ASC'))); return $q;
 }

 public function getClubs():array{$db=$this->getDatabase();$q=$db->getQuery(true)->select(['id','name'])->from($db->quoteName('#__jt_clubs'))->where('published=1')->order('name');$db->setQuery($q);return$db->loadObjectList();}
 public function getBowTypes():array{$db=$this->getDatabase();$q=$db->getQuery(true)->select('DISTINCT bow_type')->from($db->quoteName('#__jt_athletes'))->where('bow_type IS NOT NULL')->where("bow_type<>''")->order('bow_type');$db->setQuery($q);return array_map('strval',$db->loadColumn());}
}
