<?php
   namespace Jugendtraining\Component\Jugendtraining\Site\View\Trainerdashboard;
   \defined('_JEXEC') or die;
   use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
   final class HtmlView extends BaseHtmlView {
    public array $groups=[];
public array $athletes=[];
public array $results=[];
public array $trainings=[];
public array $todayTrainings=[];
public int $calendarEventCount=0;
public array $programs=[];
public array $goals=[];
public array $notes=[];
public array $signals=[];
public array $classTransitions=[];
public array $openPenalties=[];
public float $penaltyBalance=0.0;
public array $trainerDashboardConfig=[];
    public function display($tpl=null):void{
     $this->groups = (array) ($this->get('Groups') ?? []);
 $this->athletes = (array) ($this->get('Athletes') ?? []);
 $this->results = (array) ($this->get('Results') ?? []);
 $this->trainings = (array) ($this->get('Trainings') ?? []);
 $this->todayTrainings = (array) ($this->get('TodayTrainings') ?? []);
 $this->calendarEventCount = (int) ($this->get('CalendarEventCount') ?? 0);
 $this->programs = (array) ($this->get('Programs') ?? []);
 $this->goals = (array) ($this->get('Goals') ?? []);
 $this->notes = (array) ($this->get('Notes') ?? []);
 $this->signals = (array) ($this->get('AthleteSignals') ?? []);
 $this->classTransitions = (array) ($this->get('ClassTransitions') ?? []);
 $this->openPenalties = (array) ($this->get('OpenPenalties') ?? []);
 $this->penaltyBalance = (float) ($this->get('PenaltyBalance') ?? 0);
 $this->trainerDashboardConfig=(array)($this->get('TrainerDashboardConfig')??[]);
     parent::display($tpl);
    }
   }
