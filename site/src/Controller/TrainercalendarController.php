<?php
namespace Jugendtraining\Component\Jugendtraining\Site\Controller;
\defined('_JEXEC') or die;
use Joomla\CMS\Factory;use Joomla\CMS\MVC\Controller\BaseController;use Joomla\CMS\Router\Route;use Joomla\CMS\Session\Session;
final class TrainercalendarController extends BaseController{
 public function save():void{Session::checkToken() or jexit('JINVALID_TOKEN');$app=Factory::getApplication();try{$this->getModel('Trainercalendar')->save($app->input->get('jform',[],'array'),$app->input->files->get('attachments',[],'array'));$app->enqueueMessage('Termin gespeichert.','success');}catch(\Throwable$e){$app->enqueueMessage($e->getMessage(),'error');}$this->setRedirect(Route::_('index.php?option=com_jugendtraining&view=trainercalendar',false));}
 public function delete():void{Session::checkToken() or jexit('JINVALID_TOKEN');$app=Factory::getApplication();try{$this->getModel('Trainercalendar')->delete($app->input->post->getInt('id'));$app->enqueueMessage('Termin gelöscht.','success');}catch(\Throwable$e){$app->enqueueMessage($e->getMessage(),'error');}$this->setRedirect(Route::_('index.php?option=com_jugendtraining&view=trainercalendar',false));}
}
