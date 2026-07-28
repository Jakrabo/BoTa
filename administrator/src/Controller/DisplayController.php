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
  $this->syncCurrentSportyear();
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
 private function syncCurrentSportyear():void
 {
  $db=Factory::getContainer()->get('DatabaseDriver');$today=Factory::getDate()->format('Y-m-d');
  $q=$db->getQuery(true)->select('id')->from('#__jt_sportyears')->where('published=1')->where('date_start<='.$db->quote($today))->where('date_end>='.$db->quote($today))->order('date_start DESC,id DESC');$db->setQuery($q,0,1);$id=(int)$db->loadResult();
  $q=$db->getQuery(true)->update('#__jt_sportyears')->set('is_current=0')->where('is_current<>0');$db->setQuery($q)->execute();
  if($id>0){$q=$db->getQuery(true)->update('#__jt_sportyears')->set('is_current=1')->where('id='.$id);$db->setQuery($q)->execute();}
 }
}
