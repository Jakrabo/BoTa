<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\Controller;
\defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\AdminController;
final class TraininggroupsController extends AdminController {
 public function getModel($name='Traininggroup',$prefix='Administrator',$config=['ignore_request'=>true]){return parent::getModel($name,$prefix,$config);}
 public function delete(){if(!Factory::getApplication()->getIdentity()->authorise('core.admin')){throw new \RuntimeException('JERROR_ALERTNOAUTHOR',403);}parent::delete();}
}
