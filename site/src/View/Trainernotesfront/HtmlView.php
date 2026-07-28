<?php
namespace Jugendtraining\Component\Jugendtraining\Site\View\Trainernotesfront;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;use Joomla\CMS\Factory;
final class HtmlView extends BaseHtmlView {
 public array $notes=[];public array $groups=[];public array $athletes=[];public $editNote=null;
 public function display($tpl=null):void{Factory::getApplication()->setHeader('Cache-Control','private, no-store, no-cache, max-age=0',true);
  $this->notes=(array)($this->get('FilteredNotes')??[]);
  $this->groups=(array)($this->get('FilterGroups')??[]);
  $this->athletes=(array)($this->get('FilterAthletes')??[]);$this->editNote=$this->get('EditNote');
  parent::display($tpl);
 }
}
