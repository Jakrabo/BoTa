<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\Controller;
\defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\FormController;
final class TraininggroupController extends FormController {
 protected $view_list='traininggroups';
 protected function allowAdd($data=[]):bool{return Factory::getApplication()->getIdentity()->authorise('core.admin');}
 protected function allowEdit($data=[],$key='id'):bool{return Factory::getApplication()->getIdentity()->authorise('core.admin');}
}
