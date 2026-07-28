<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\View\Class;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
final class HtmlView extends BaseHtmlView
{ public $form; public $item; public function display($tpl=null) { $this->form=$this->get('Form');$this->item=$this->get('Item');ToolbarHelper::title($this->item->id ? 'COM_JUGENDTRAINING_CLASS_EDIT' : 'COM_JUGENDTRAINING_CLASS_NEW','pencil');ToolbarHelper::apply('class.apply');ToolbarHelper::save('class.save');ToolbarHelper::save2new('class.save2new');ToolbarHelper::cancel('class.cancel');parent::display($tpl); } }
