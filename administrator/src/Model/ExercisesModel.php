<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\Model;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\QueryInterface;
final class ExercisesModel extends ListModel {
  public function __construct($config=[]){$config['filter_fields']=['id','e.id','title','e.title','category','e.category','published','e.published'];parent::__construct($config);}
  protected function populateState($ordering='e.category',$direction='ASC'):void{
    $this->setState('filter.search',$this->getUserStateFromRequest($this->context.'.filter.search','filter_search'));
    $this->setState('filter.category',$this->getUserStateFromRequest($this->context.'.filter.category','filter_category',''));
    parent::populateState($ordering,$direction);
  }
  protected function getListQuery():QueryInterface{
    $db=$this->getDatabase();$q=$db->getQuery(true)->select('e.*')->from($db->quoteName('#__jt_exercises','e'));
    $s=trim((string)$this->getState('filter.search'));if($s!==''){$like=$db->quote('%'.$db->escape($s,true).'%');$q->where('(e.title LIKE '.$like.' OR e.description LIKE '.$like.')');}
    $c=(string)$this->getState('filter.category');if($c!=='')$q->where('e.category='.$db->quote($c));
    $ord=$db->escape((string)$this->getState('list.ordering','e.category'));$dir=strtoupper((string)$this->getState('list.direction','ASC'))==='DESC'?'DESC':'ASC';
    return $q->order($ord.' '.$dir.', e.ordering ASC, e.title ASC');
  }
}
