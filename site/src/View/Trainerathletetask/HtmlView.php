<?php
namespace Jugendtraining\Component\Jugendtraining\Site\View\Trainerathletetask;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
final class HtmlView extends BaseHtmlView{public $athlete;public $assignment;public array $programs=[];public function display($tpl=null):void{$this->athlete=$this->get('Athlete');$this->assignment=$this->get('Assignment');$this->programs=(array)($this->get('Programs')??[]);parent::display($tpl);}}
