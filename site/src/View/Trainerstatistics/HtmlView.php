<?php
namespace Jugendtraining\Component\Jugendtraining\Site\View\Trainerstatistics;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
final class HtmlView extends BaseHtmlView {public object $statistics;public function display($tpl=null):void{$this->statistics=$this->get('Statistics')??(object)[];parent::display($tpl);}}
