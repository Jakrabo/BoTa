<?php
namespace Jugendtraining\Component\Jugendtraining\Site\View\Trainerachievementedit;
\defined('_JEXEC') or die;use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
final class HtmlView extends BaseHtmlView{public object $item;public array $availableBadges=[];public function display($tpl=null):void{$this->item=$this->get('Item');$this->availableBadges=(array)($this->get('AvailableBadges')??[]);parent::display($tpl);}}
