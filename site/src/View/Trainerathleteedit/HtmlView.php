<?php
namespace Jugendtraining\Component\Jugendtraining\Site\View\Trainerathleteedit;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
final class HtmlView extends BaseHtmlView{public $athlete;public array $classes=[];public function display($tpl=null):void{$this->athlete=$this->get('Athlete');$this->classes=(array)($this->get('Classes')??[]);parent::display($tpl);}}
