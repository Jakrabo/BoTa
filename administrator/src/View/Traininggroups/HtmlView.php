<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\View\Traininggroups;
\defined('_JEXEC') or die;
use Joomla\CMS\Factory;use Joomla\CMS\Language\Text;use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;use Joomla\CMS\Toolbar\ToolbarHelper;
final class HtmlView extends BaseHtmlView {
 public $items;public $pagination;
 public function display($tpl=null):void{
  if(!Factory::getApplication()->getIdentity()->authorise('core.admin'))throw new \RuntimeException('JERROR_ALERTNOAUTHOR',403);
  $this->items=$this->get('Items');$this->pagination=$this->get('Pagination');
  ToolbarHelper::title(Text::_('COM_JUGENDTRAINING_TRAINING_GROUPS'),'users');
  ToolbarHelper::addNew('traininggroup.add');ToolbarHelper::editList('traininggroup.edit');ToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE','traininggroups.delete');
  parent::display($tpl);
 }
}
