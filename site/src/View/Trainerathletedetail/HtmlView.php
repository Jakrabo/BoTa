<?php
namespace Jugendtraining\Component\Jugendtraining\Site\View\Trainerathletedetail;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
final class HtmlView extends BaseHtmlView{
 public $athlete;public array $attendance=[];public array $notes=[];public array $tasks=[];public array $results=[];public object $arrows;public array $sportYears=[];
 public function display($tpl=null):void{$this->athlete=$this->get('AthleteDetail');$this->attendance=(array)($this->get('RecentAttendance')??[]);$this->notes=(array)($this->get('AthleteNotes')??[]);$this->tasks=(array)($this->get('TrainingTasks')??[]);$this->results=(array)($this->get('ResultDevelopment')??[]);$this->arrows=$this->get('ArrowSeries')??(object)[];$this->sportYears=(array)($this->get('AvailableSportYears')??[]);parent::display($tpl);}
}
