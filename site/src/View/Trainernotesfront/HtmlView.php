<?php
namespace Jugendtraining\Component\Jugendtraining\Site\View\Trainernotesfront;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
final class HtmlView extends BaseHtmlView {
 public array $notes=[];public array $groups=[];public array $athletes=[];public $editNote=null;
 public function display($tpl=null):void{
  $this->notes=(array)($this->get('FilteredNotes')??[]);
  $this->groups=(array)($this->get('FilterGroups')??[]);
  $this->athletes=(array)($this->get('FilterAthletes')??[]);$this->editNote=$this->get('EditNote');
  parent::display($tpl);
 }
}
