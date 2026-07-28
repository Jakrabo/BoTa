<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\Controller;
\defined('_JEXEC') or die;
use Joomla\CMS\Factory;use Joomla\CMS\MVC\Controller\BaseController;use Joomla\CMS\Router\Route;use Joomla\CMS\Session\Session;
final class CalendarController extends BaseController{
 private function guard():void{$u=Factory::getApplication()->getIdentity();if(!$u->authorise('core.manage','com_jugendtraining')&&!$u->authorise('core.admin'))throw new \RuntimeException('JERROR_ALERTNOAUTHOR',403);}
 public function save():void{Session::checkToken() or jexit('JINVALID_TOKEN');$this->guard();$app=Factory::getApplication();try{$this->getModel('Calendar')->save($app->input->get('jform',[],'array'),$app->input->files->get('attachments',[],'array'));$app->enqueueMessage('Termin gespeichert.','success');}catch(\Throwable$e){$app->enqueueMessage($e->getMessage(),'error');}$this->setRedirect(Route::_('index.php?option=com_jugendtraining&view=calendar',false));}
 public function delete():void{Session::checkToken() or jexit('JINVALID_TOKEN');$this->guard();$app=Factory::getApplication();try{$this->getModel('Calendar')->delete($app->input->post->getInt('id'));$app->enqueueMessage('Termin gelöscht.','success');}catch(\Throwable$e){$app->enqueueMessage($e->getMessage(),'error');}$this->setRedirect(Route::_('index.php?option=com_jugendtraining&view=calendar',false));}
}
