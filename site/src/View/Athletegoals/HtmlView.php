<?php
namespace Jugendtraining\Component\Jugendtraining\Site\View\Athletegoals;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
final class HtmlView extends BaseHtmlView {
 public array $myGoals=[];
 public function display($tpl=null):void{$this->myGoals = (array) ($this->get('MyGoals') ?? []);parent::display($tpl);}
}
