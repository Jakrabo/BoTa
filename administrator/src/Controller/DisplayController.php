<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\Controller;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;
final class DisplayController extends BaseController
{
 protected $default_view = 'dashboard';
 public function display($cachable=false, $urlparams=[])
 {
  $user=Factory::getApplication()->getIdentity();
  if(!$user->authorise('core.manage','com_jugendtraining')&&!$user->authorise('core.admin')){
   throw new \RuntimeException('JERROR_ALERTNOAUTHOR',403);
  }
  $view=$this->input->getCmd('view','dashboard');
  $layout=$this->input->getCmd('layout','default');
  $id=$this->input->getInt('id');
  if ($layout==='edit' && !$this->checkEditId('com_jugendtraining.edit.'.$view,$id)) {
   $this->setMessage('JLIB_APPLICATION_ERROR_UNHELD_ID','error');
   $this->setRedirect('index.php?option=com_jugendtraining&view='.$view.'s');
   return false;
  }
  return parent::display($cachable,$urlparams);
 }
}
