<?php
namespace Jugendtraining\Component\Jugendtraining\Site\View\Trainergoals;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
final class HtmlView extends BaseHtmlView {
 public array $goals=[];
 public function display($tpl=null):void{
  $this->goals = (array) ($this->get('Goals') ?? []);
  parent::display($tpl);
 }
}
