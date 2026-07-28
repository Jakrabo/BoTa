<?php
namespace Jugendtraining\Component\Jugendtraining\Site\View\Trainerdiaries;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
final class HtmlView extends BaseHtmlView {public array $diaries=[];public function display($tpl=null):void{$this->diaries=(array)($this->get('Diaries')??[]);parent::display($tpl);}}
