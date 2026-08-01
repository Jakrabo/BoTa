<?php
namespace Jugendtraining\Component\Jugendtraining\Site\View\Dashboard;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
final class HtmlView extends BaseHtmlView {
 public $athlete;
 public array $myResults=[];
 public array $myPrograms=[];
 public array $myGoals=[];
 public array $myPersonalBests=[];
 public array $myResultDevelopment=[];
 public array $visibleTrainerNotes=[];
 public array $myAchievements=[];
 public int $myAchievementCount=0;
 public array $myOpenPenalties=[];
 public array $upcomingCalendarEvents=[];
 public array $calendarCategoryMap=[];
 public array $myUpcomingTrainingSessions=[];
 public object $selfCancelSettings;
 public object $selfCheckinSettings;
 public object $myDiaryStatistics;
 public array $athleteDashboardConfig=[];
 public function display($tpl=null):void{
  $this->athlete=$this->get('Athlete');
  $this->myResults = (array) ($this->get('MyResults') ?? []);
  $this->myPrograms = (array) ($this->get('MyPrograms') ?? []);
  $this->myGoals = (array) ($this->get('MyGoals') ?? []);
  $this->myPersonalBests = (array) ($this->get('MyPersonalBests') ?? []);
  $this->myResultDevelopment = (array) ($this->get('MyResultDevelopment') ?? []);
  $this->visibleTrainerNotes = (array) ($this->get('VisibleTrainerNotes') ?? []);
  $this->myAchievements = (array) ($this->get('MyAchievements') ?? []);
  $this->myAchievementCount = (int) ($this->get('MyAchievementCount') ?? 0);
  $this->myOpenPenalties = (array) ($this->get('MyOpenPenalties') ?? []);
  $this->upcomingCalendarEvents=(array)($this->get('UpcomingCalendarEvents')??[]);
  $this->calendarCategoryMap=(array)($this->get('CalendarCategoryMap')??[]);
  $this->myUpcomingTrainingSessions=(array)($this->get('MyUpcomingTrainingSessions')??[]);
  $this->selfCancelSettings=$this->get('SelfCancelSettings')??(object)[];
  $this->selfCheckinSettings=$this->get('SelfCheckinSettings')??(object)[];
  $this->myDiaryStatistics = $this->get('MyDiaryStatistics') ?? (object)[];
  $this->athleteDashboardConfig=(array)($this->get('AthleteDashboardConfig')??[]);
  parent::display($tpl);
 }
}
