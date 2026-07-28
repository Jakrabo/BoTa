<?php
namespace Jugendtraining\Component\Jugendtraining\Site\View\Achievements;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
final class HtmlView extends BaseHtmlView{public array $items=[];public function display($tpl=null):void{$this->items=(array)($this->get('Items')??[]);parent::display($tpl);}}
