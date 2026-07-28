<?php
namespace Jugendtraining\Component\Jugendtraining\Site\View\Trainerprograms;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
final class HtmlView extends BaseHtmlView {
 public array $programs=[];
 public function display($tpl=null):void{
  $this->programs = (array) ($this->get('Programs') ?? []);
  parent::display($tpl);
 }
}
