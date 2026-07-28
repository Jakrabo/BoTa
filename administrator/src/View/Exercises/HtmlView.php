<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\View\Exercises;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;use Joomla\CMS\Language\Text;use Joomla\CMS\Toolbar\ToolbarHelper;
final class HtmlView extends BaseHtmlView {
 public $items;public $pagination;public $state;
 public function display($tpl=null):void{$this->items=$this->get('Items');$this->pagination=$this->get('Pagination');$this->state=$this->get('State');ToolbarHelper::title(Text::_('COM_JUGENDTRAINING_EXERCISES'),'puzzle');ToolbarHelper::addNew('exercise.add');ToolbarHelper::editList('exercise.edit');ToolbarHelper::publish('exercises.publish','JTOOLBAR_PUBLISH',true);ToolbarHelper::unpublish('exercises.unpublish','JTOOLBAR_UNPUBLISH',true);ToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE','exercises.delete');parent::display($tpl);}
}
