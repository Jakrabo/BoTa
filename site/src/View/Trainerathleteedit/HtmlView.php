<?php
namespace Jugendtraining\Component\Jugendtraining\Site\View\Trainerathleteedit;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;use Joomla\CMS\Factory;
final class HtmlView extends BaseHtmlView{public $athlete;public array $classes=[];public function display($tpl=null):void{Factory::getApplication()->setHeader('Cache-Control','private, no-store, no-cache, max-age=0',true);$this->athlete=$this->get('Athlete');$this->classes=(array)($this->get('Classes')??[]);parent::display($tpl);}}
