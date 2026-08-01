<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\View\Classes;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
final class HtmlView extends BaseHtmlView
{ public $items; public $pagination; public $state; public function display($tpl=null) { $this->items=$this->get('Items');$this->pagination=$this->get('Pagination');$this->state=$this->get('State'); ToolbarHelper::title(Text::_('COM_JUGENDTRAINING_CLASSES'),'users');ToolbarHelper::addNew('class.add');ToolbarHelper::editList('class.edit');ToolbarHelper::publish('classes.publish','JTOOLBAR_PUBLISH',true);ToolbarHelper::unpublish('classes.unpublish','JTOOLBAR_UNPUBLISH',true);ToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE','classes.delete'); parent::display($tpl); } }
