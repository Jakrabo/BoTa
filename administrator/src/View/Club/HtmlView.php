<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\View\Club;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
final class HtmlView extends BaseHtmlView
{ public $form; public $item; public function display($tpl=null) { $this->form=$this->get('Form');$this->item=$this->get('Item');ToolbarHelper::title($this->item->id ? 'COM_JUGENDTRAINING_CLUB_EDIT' : 'COM_JUGENDTRAINING_CLUB_NEW','pencil');ToolbarHelper::apply('club.apply');ToolbarHelper::save('club.save');ToolbarHelper::save2new('club.save2new');ToolbarHelper::cancel('club.cancel');parent::display($tpl); } }
