<?php
namespace Jugendtraining\Component\Jugendtraining\Site\View\Trainingdiary;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
final class HtmlView extends BaseHtmlView {public $item;public $form;public function display($tpl=null):void{$this->item=$this->get('Item');$this->form=$this->get('Form');parent::display($tpl);}}
