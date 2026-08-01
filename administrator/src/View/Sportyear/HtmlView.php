<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\View\Sportyear;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
final class HtmlView extends BaseHtmlView
{ public $form; public $item; public function display($tpl=null) { $this->form=$this->get('Form');$this->item=$this->get('Item');ToolbarHelper::title(Text::_($this->item->id ? 'COM_JUGENDTRAINING_SPORTYEAR_EDIT' : 'COM_JUGENDTRAINING_SPORTYEAR_NEW'),'pencil');ToolbarHelper::apply('sportyear.apply');ToolbarHelper::save('sportyear.save');ToolbarHelper::save2new('sportyear.save2new');ToolbarHelper::cancel('sportyear.cancel');parent::display($tpl); } }
