<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\View\Calendar;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;use Joomla\CMS\Toolbar\ToolbarHelper;use Joomla\CMS\Language\Text;
final class HtmlView extends BaseHtmlView{public array$events=[];public array$categories=[];public array$categoryMap=[];public array$locations=[];public array$groups=[];public$editEvent=null;public function display($tpl=null):void{$this->events=(array)($this->get('Events')??[]);$this->categories=(array)($this->get('Categories')??[]);$this->categoryMap=(array)($this->get('CategoryMap')??[]);$this->groups=(array)($this->get('Groups')??[]);$this->locations=(array)($this->get('Locations')??[]);$this->editEvent=$this->get('EditEvent');ToolbarHelper::title(Text::_('COM_JUGENDTRAINING_CALENDAR'),'calendar');parent::display($tpl);}}
