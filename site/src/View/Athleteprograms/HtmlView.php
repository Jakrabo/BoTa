<?php
namespace Jugendtraining\Component\Jugendtraining\Site\View\Athleteprograms;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
final class HtmlView extends BaseHtmlView {
 public array $myPrograms=[];
 public function display($tpl=null):void{$this->myPrograms = (array) ($this->get('MyPrograms') ?? []);parent::display($tpl);}
}
