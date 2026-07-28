<?php
namespace Jugendtraining\Component\Jugendtraining\Site\Model;
\defined('_JEXEC') or die;
use Joomla\CMS\Factory;use Joomla\CMS\MVC\Model\BaseDatabaseModel;use Jugendtraining\Component\Jugendtraining\Site\Service\CalendarService;
final class AthletecalendarModel extends BaseDatabaseModel{
 private CalendarService $calendar;
 public function __construct($config=[],$factory=null){parent::__construct($config,$factory);$this->calendar=new CalendarService();if(!$this->calendar->canReadCalendar())throw new \RuntimeException('JERROR_ALERTNOAUTHOR',403);}
 public function getEvents():array{return$this->calendar->events($this->filters(),true);}
 public function getCategories():array{return$this->calendar->categories();}
 public function getLocations():array{return$this->calendar->locations(true);}
 public function getAttachments():array{return[];}
 private function filters():array{$i=Factory::getApplication()->input;return['mode'=>$i->getCmd('mode','future'),'date_from'=>$i->getString('date_from'),'date_to'=>$i->getString('date_to'),'category'=>$i->getString('category'),'location'=>$i->getString('location')];}
}
