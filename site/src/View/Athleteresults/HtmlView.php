<?php
namespace Jugendtraining\Component\Jugendtraining\Site\View\Athleteresults;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
final class HtmlView extends BaseHtmlView {
 public array $myResults=[];
 public function display($tpl=null):void{$this->myResults = (array) ($this->get('MyResults') ?? []);parent::display($tpl);}
}
