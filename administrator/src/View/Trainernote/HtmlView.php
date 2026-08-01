<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\View\Trainernote;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
final class HtmlView extends BaseHtmlView {
  public $form;public $item;
  public function display($tpl=null):void{$this->form=$this->get('Form');$this->item=$this->get('Item');ToolbarHelper::title(Text::_($this->item->id ? 'COM_JUGENDTRAINING_TRAINER_NOTE_EDIT' : 'COM_JUGENDTRAINING_TRAINER_NOTE_NEW'),'comments');ToolbarHelper::apply('trainernote.apply');ToolbarHelper::save('trainernote.save');ToolbarHelper::save2new('trainernote.save2new');ToolbarHelper::cancel('trainernote.cancel');parent::display($tpl);}
}
