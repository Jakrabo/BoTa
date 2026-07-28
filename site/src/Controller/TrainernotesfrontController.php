<?php
namespace Jugendtraining\Component\Jugendtraining\Site\Controller;
\defined('_JEXEC') or die;
use Joomla\CMS\Factory;use Joomla\CMS\MVC\Controller\BaseController;use Joomla\CMS\Router\Route;use Joomla\CMS\Session\Session;

final class TrainernotesfrontController extends BaseController
{
 public function save():void
 {
  Session::checkToken() or jexit('JINVALID_TOKEN');$app=Factory::getApplication();
  try{$this->getModel('Trainernotesfront')->saveNote($app->input->get('jform',[],'array'));$app->enqueueMessage('Notiz gespeichert.','success');}
  catch(\Throwable$e){$app->enqueueMessage($e->getMessage(),'error');}
  $this->setRedirect(Route::_('index.php?option=com_jugendtraining&view=trainernotesfront',false));
 }
 public function status():void
 {
  Session::checkToken('get') or jexit('JINVALID_TOKEN');$app=Factory::getApplication();
  try{$this->getModel('Trainernotesfront')->setStatus($app->input->getInt('id'),$app->input->getCmd('status'));$app->enqueueMessage('Status aktualisiert.','success');}
  catch(\Throwable$e){$app->enqueueMessage($e->getMessage(),'error');}
  $this->setRedirect(Route::_('index.php?option=com_jugendtraining&view=trainernotesfront',false));
 }
}
