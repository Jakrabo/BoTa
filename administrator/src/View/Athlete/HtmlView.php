<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\View\Athlete;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
final class HtmlView extends BaseHtmlView
{ public $form; public $item; public function display($tpl=null) { $this->form=$this->get('Form');$this->item=$this->get('Item');ToolbarHelper::title(Text::_($this->item->id ? 'COM_JUGENDTRAINING_ATHLETE_EDIT' : 'COM_JUGENDTRAINING_ATHLETE_NEW'),'pencil');ToolbarHelper::apply('athlete.apply');ToolbarHelper::save('athlete.save');ToolbarHelper::save2new('athlete.save2new');ToolbarHelper::cancel('athlete.cancel');parent::display($tpl); } }
