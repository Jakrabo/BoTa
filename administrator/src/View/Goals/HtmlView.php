<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\View\Goals;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;use Joomla\CMS\Language\Text;use Joomla\CMS\Toolbar\ToolbarHelper;
final class HtmlView extends BaseHtmlView {public $items;public $pagination;public $state;public function display($tpl=null):void{$this->items=$this->get('Items');$this->pagination=$this->get('Pagination');$this->state=$this->get('State');ToolbarHelper::title(Text::_('COM_JUGENDTRAINING_GOALS'),'target');ToolbarHelper::addNew('goal.add');ToolbarHelper::editList('goal.edit');ToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE','goals.delete');parent::display($tpl);}}
