<?php
namespace Jugendtraining\Component\Jugendtraining\Site\View\Trainertrainings;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
final class HtmlView extends BaseHtmlView {
 public array $trainings=[];
 public function display($tpl=null):void{
  $this->trainings = (array) ($this->get('Trainings') ?? []);
  parent::display($tpl);
 }
}
