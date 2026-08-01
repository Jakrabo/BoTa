<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\View\Traininglocations;
\defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;use Joomla\CMS\Toolbar\ToolbarHelper;
final class HtmlView extends BaseHtmlView{public $items;public $pagination;public $state;public function display($tpl=null):void{$this->items=$this->get('Items');$this->pagination=$this->get('Pagination');$this->state=$this->get('State');ToolbarHelper::title(Text::_('COM_JUGENDTRAINING_TRAINING_LOCATIONS'),'location');ToolbarHelper::addNew('traininglocation.add');ToolbarHelper::editList('traininglocation.edit');ToolbarHelper::publish('traininglocations.publish','JTOOLBAR_PUBLISH',true);ToolbarHelper::unpublish('traininglocations.unpublish','JTOOLBAR_UNPUBLISH',true);ToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE','traininglocations.delete');parent::display($tpl);}}
