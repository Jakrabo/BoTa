<?php
namespace Jugendtraining\Component\Jugendtraining\Site\Model;
\defined('_JEXEC') or die;
use Joomla\CMS\Factory;use Joomla\CMS\MVC\Model\BaseDatabaseModel;use Jugendtraining\Component\Jugendtraining\Site\Service\CalendarService;
final class CalendarModel extends BaseDatabaseModel{
 private CalendarService $calendar;
 public function __construct($config=[],$factory=null){parent::__construct($config,$factory);$this->calendar=new CalendarService();if(!$this->calendar->canReadCalendar())throw new \RuntimeException('JERROR_ALERTNOAUTHOR',403);}
 public function getEvents():array{return$this->calendar->events($this->filters(),true,false);}
 public function getCategories():array{return$this->calendar->categoryConfigs(true);}
 public function getCategoryMap():array{return$this->calendar->categoryMap(false);}
 public function getLocations():array{return$this->calendar->locations(true);}
 public function getGroups():array{return$this->calendar->trainerGroups();}
 public function getIsTrainer():bool{return$this->calendar->isTrainer();}
 public function getEditEvent():?object{$id=Factory::getApplication()->input->getInt('edit_id');return$id&&$this->calendar->isTrainer()?$this->calendar->event($id,false,false):null;}
 public function save(array$d,mixed$u):int{return$this->calendar->save($d,$u);}
 public function delete(int$id):void{$this->calendar->delete($id);}
 private function filters():array{$i=Factory::getApplication()->input;return['mode'=>$i->getCmd('mode','future'),'date_from'=>$i->getString('date_from'),'date_to'=>$i->getString('date_to'),'category'=>$i->getString('category'),'location'=>$i->getString('location')];}
}
