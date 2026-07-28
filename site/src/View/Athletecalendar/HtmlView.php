<?php
namespace Jugendtraining\Component\Jugendtraining\Site\View\Athletecalendar;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;use Joomla\CMS\Factory;
final class HtmlView extends BaseHtmlView{public array $events=[];public array $categories=[];public array $locations=[];public function display($tpl=null):void{Factory::getApplication()->setHeader('Cache-Control','private, no-store, no-cache, max-age=0',true);$this->events=(array)($this->get('Events')??[]);$this->categories=(array)($this->get('Categories')??[]);$this->locations=(array)($this->get('Locations')??[]);parent::display($tpl);}}
