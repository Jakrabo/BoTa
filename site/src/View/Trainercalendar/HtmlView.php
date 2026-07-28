<?php
namespace Jugendtraining\Component\Jugendtraining\Site\View\Trainercalendar;
\defined('_JEXEC') or die;
use Joomla\CMS\Factory;use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;use Joomla\CMS\Router\Route;
final class HtmlView extends BaseHtmlView{public function display($tpl=null):void{Factory::getApplication()->redirect(Route::_('index.php?option=com_jugendtraining&view=calendar',false));}}
