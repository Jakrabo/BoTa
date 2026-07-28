<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\View\Goal;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;use Joomla\CMS\Toolbar\ToolbarHelper;
final class HtmlView extends BaseHtmlView {
  public $form;public $item;
  public function display($tpl=null):void{$this->form=$this->get('Form');$this->item=$this->get('Item');ToolbarHelper::title($this->item->id?'COM_JUGENDTRAINING_GOAL_EDIT':'COM_JUGENDTRAINING_GOAL_NEW','target');ToolbarHelper::apply('goal.apply');ToolbarHelper::save('goal.save');ToolbarHelper::save2new('goal.save2new');ToolbarHelper::cancel('goal.cancel');parent::display($tpl);}
}
