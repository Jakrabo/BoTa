<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\View\Athletes;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
final class HtmlView extends BaseHtmlView
{ public $items; public $pagination; public $state; public array $clubs=[]; public array $bowTypes=[]; public function display($tpl=null) { $this->items=$this->get('Items');$this->pagination=$this->get('Pagination');$this->state=$this->get('State');$this->clubs=(array)($this->get('Clubs')??[]);$this->bowTypes=(array)($this->get('BowTypes')??[]); ToolbarHelper::title(Text::_('COM_JUGENDTRAINING_ATHLETES'),'users');ToolbarHelper::addNew('athlete.add');ToolbarHelper::editList('athlete.edit');ToolbarHelper::publish('athletes.publish','JTOOLBAR_PUBLISH',true);ToolbarHelper::unpublish('athletes.unpublish','JTOOLBAR_UNPUBLISH',true);ToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE','athletes.delete'); parent::display($tpl); } }
