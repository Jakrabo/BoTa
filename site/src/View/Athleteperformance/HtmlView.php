<?php
namespace Jugendtraining\Component\Jugendtraining\Site\View\Athleteperformance;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
final class HtmlView extends BaseHtmlView {
 public array $myResultDevelopment=[];
 public object $myDiaryStatistics;
 public object $myDiaryArrowSeries;
 public array $availableSportYears=[];
 public function display($tpl=null):void{$this->myResultDevelopment = (array) ($this->get('MyResultDevelopment') ?? []);$this->myDiaryStatistics=$this->get('MyDiaryStatistics')??(object)[];$this->myDiaryArrowSeries=$this->get('MyDiaryArrowSeries')??(object)[];$this->availableSportYears=(array)($this->get('AvailableSportYears')??[]);parent::display($tpl);}
}
