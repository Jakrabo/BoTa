<?php
namespace Jugendtraining\Component\Jugendtraining\Site\View\Trainerachievementdefinitions;
\defined('_JEXEC') or die;use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
final class HtmlView extends BaseHtmlView{public array $definitions=[];public function display($tpl=null):void{$this->definitions=(array)($this->get('Definitions')??[]);parent::display($tpl);}}
