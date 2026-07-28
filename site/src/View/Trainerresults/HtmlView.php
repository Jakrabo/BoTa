<?php
namespace Jugendtraining\Component\Jugendtraining\Site\View\Trainerresults;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
final class HtmlView extends BaseHtmlView {
 public array $results=[];
 public function display($tpl=null):void{
  $this->results = (array) ($this->get('Results') ?? []);
  parent::display($tpl);
 }
}
