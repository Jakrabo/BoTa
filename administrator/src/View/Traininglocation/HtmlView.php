<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\View\Traininglocation;
\defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;use Joomla\CMS\Toolbar\ToolbarHelper;
final class HtmlView extends BaseHtmlView{public $form;public $item;public function display($tpl=null):void{$this->form=$this->get('Form');$this->item=$this->get('Item');ToolbarHelper::title(Text::_($this->item->id?'COM_JUGENDTRAINING_TRAINING_LOCATION_EDIT':'COM_JUGENDTRAINING_TRAINING_LOCATION_NEW'),'location');ToolbarHelper::apply('traininglocation.apply');ToolbarHelper::save('traininglocation.save');ToolbarHelper::save2new('traininglocation.save2new');ToolbarHelper::cancel('traininglocation.cancel');parent::display($tpl);}}
