<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\View\Programs;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;use Joomla\CMS\Language\Text;use Joomla\CMS\Toolbar\ToolbarHelper;
final class HtmlView extends BaseHtmlView {
 public $items;public $pagination;public $state;
 public function display($tpl=null):void{$this->items=$this->get('Items');$this->pagination=$this->get('Pagination');$this->state=$this->get('State');ToolbarHelper::title(Text::_('COM_JUGENDTRAINING_PROGRAMS'),'list');ToolbarHelper::addNew('program.add');ToolbarHelper::editList('program.edit');ToolbarHelper::publish('programs.publish','JTOOLBAR_PUBLISH',true);ToolbarHelper::unpublish('programs.unpublish','JTOOLBAR_UNPUBLISH',true);ToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE','programs.delete');parent::display($tpl);}
}
