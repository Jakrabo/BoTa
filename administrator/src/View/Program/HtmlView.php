<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\View\Program;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
final class HtmlView extends BaseHtmlView {
  public $form; public $item;
  public function display($tpl=null):void {
    $this->form=$this->get('Form');$this->item=$this->get('Item');
    ToolbarHelper::title($this->item->id?'COM_JUGENDTRAINING_PROGRAM_EDIT':'COM_JUGENDTRAINING_PROGRAM_NEW','list');
    ToolbarHelper::apply('program.apply');ToolbarHelper::save('program.save');ToolbarHelper::save2new('program.save2new');ToolbarHelper::cancel('program.cancel');
    parent::display($tpl);
  }
}
