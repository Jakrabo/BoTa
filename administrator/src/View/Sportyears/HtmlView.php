<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\View\Sportyears;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
final class HtmlView extends BaseHtmlView
{ public $items; public $pagination; public $state; public function display($tpl=null) { $this->items=$this->get('Items');$this->pagination=$this->get('Pagination');$this->state=$this->get('State'); ToolbarHelper::title('COM_JUGENDTRAINING_SPORTYEARS','users');ToolbarHelper::addNew('sportyear.add');ToolbarHelper::editList('sportyear.edit');ToolbarHelper::publish('sportyears.publish','JTOOLBAR_PUBLISH',true);ToolbarHelper::unpublish('sportyears.unpublish','JTOOLBAR_UNPUBLISH',true);ToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE','sportyears.delete'); parent::display($tpl); } }
