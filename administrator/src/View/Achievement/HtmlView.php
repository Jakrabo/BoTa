<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\View\Achievement;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
final class HtmlView extends BaseHtmlView{public $form;public $item;public array $availableBadges=[];public function display($tpl=null):void{$this->form=$this->get('Form');$this->item=$this->get('Item');$this->availableBadges=(array)($this->get('AvailableBadges')??[]);ToolbarHelper::title(Text::_($this->item->id ? 'COM_JUGENDTRAINING_ACHIEVEMENT_EDIT' : 'COM_JUGENDTRAINING_ACHIEVEMENT_NEW'),'star');ToolbarHelper::apply('achievement.apply');ToolbarHelper::save('achievement.save');ToolbarHelper::save2new('achievement.save2new');ToolbarHelper::cancel('achievement.cancel');parent::display($tpl);}}
