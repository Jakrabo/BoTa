<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\View\Achievements;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;use Joomla\CMS\Toolbar\ToolbarHelper;use Joomla\CMS\Language\Text;
final class HtmlView extends BaseHtmlView{public $items;public $pagination;public $state;public function display($tpl=null):void{$this->items=$this->get('Items');$this->pagination=$this->get('Pagination');$this->state=$this->get('State');ToolbarHelper::title(Text::_('COM_JUGENDTRAINING_ACHIEVEMENTS'),'star');ToolbarHelper::addNew('achievement.add');ToolbarHelper::editList('achievement.edit');ToolbarHelper::publish('achievements.publish','JTOOLBAR_PUBLISH',true);ToolbarHelper::unpublish('achievements.unpublish','JTOOLBAR_UNPUBLISH',true);ToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE','achievements.delete');parent::display($tpl);}}
