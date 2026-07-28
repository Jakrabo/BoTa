<?php
namespace Jugendtraining\Component\Jugendtraining\Site\View\Trainerachievements;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
final class HtmlView extends BaseHtmlView{public object $cockpit;public function display($tpl=null):void{$this->cockpit=$this->get('AchievementCockpit')??(object)['athletes'=>[],'achievements'=>[],'awards'=>[]];parent::display($tpl);}}
