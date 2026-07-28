<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\View\Traininggroup;
\defined('_JEXEC') or die;
use Joomla\CMS\Factory;use Joomla\CMS\Language\Text;use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;use Joomla\CMS\Toolbar\ToolbarHelper;
final class HtmlView extends BaseHtmlView {
 public $form;public $item;
 public function display($tpl=null):void{
  if(!Factory::getApplication()->getIdentity()->authorise('core.admin'))throw new \RuntimeException('JERROR_ALERTNOAUTHOR',403);
  $this->form=$this->get('Form');$this->item=$this->get('Item');
  ToolbarHelper::title(Text::_($this->item->id?'COM_JUGENDTRAINING_TRAINING_GROUP_EDIT':'COM_JUGENDTRAINING_TRAINING_GROUP_NEW'),'users');
  ToolbarHelper::apply('traininggroup.apply');ToolbarHelper::save('traininggroup.save');ToolbarHelper::cancel('traininggroup.cancel');
  parent::display($tpl);
 }
}
