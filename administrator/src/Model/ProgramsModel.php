<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\Model;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\QueryInterface;
final class ProgramsModel extends ListModel {
  public function __construct($config=[]){$config['filter_fields']=['id','p.id','title','p.title','category','p.category','published','p.published'];parent::__construct($config);}
  protected function populateState($ordering='p.title',$direction='ASC'):void{$this->setState('filter.search',$this->getUserStateFromRequest($this->context.'.filter.search','filter_search'));parent::populateState($ordering,$direction);}
  protected function getListQuery():QueryInterface{
    $db=$this->getDatabase();$q=$db->getQuery(true)->select(['p.*','COUNT(DISTINCT pe.exercise_id) exercise_count','COUNT(DISTINCT CASE WHEN ap.active=1 THEN ap.athlete_id END) athlete_count'])->from($db->quoteName('#__jt_training_programs','p'))->leftJoin($db->quoteName('#__jt_program_exercises','pe').' ON pe.program_id=p.id')->leftJoin($db->quoteName('#__jt_athlete_programs','ap').' ON ap.program_id=p.id')->group('p.id');
    $s=trim((string)$this->getState('filter.search'));if($s!==''){$like=$db->quote('%'.$db->escape($s,true).'%');$q->where('(p.title LIKE '.$like.' OR p.description LIKE '.$like.')');}
    $ord=$db->escape((string)$this->getState('list.ordering','p.title'));$dir=strtoupper((string)$this->getState('list.direction','ASC'))==='DESC'?'DESC':'ASC';return $q->order($ord.' '.$dir);
  }
}
