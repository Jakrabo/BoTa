<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\View\Exercise;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
final class HtmlView extends BaseHtmlView {
  public $form; public $item;
  public function display($tpl=null):void {
    $this->form=$this->get('Form');$this->item=$this->get('Item');
    ToolbarHelper::title($this->item->id?'COM_JUGENDTRAINING_EXERCISE_EDIT':'COM_JUGENDTRAINING_EXERCISE_NEW','puzzle');
    ToolbarHelper::apply('exercise.apply');ToolbarHelper::save('exercise.save');ToolbarHelper::save2new('exercise.save2new');ToolbarHelper::cancel('exercise.cancel');
    parent::display($tpl);
  }
}
