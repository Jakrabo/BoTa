<?php
namespace Jugendtraining\Component\Jugendtraining\Site\Controller;
\defined('_JEXEC') or die;
use Joomla\CMS\Factory;use Joomla\CMS\MVC\Controller\BaseController;use Joomla\CMS\Router\Route;use Joomla\CMS\Session\Session;
final class TrainerathletetaskController extends BaseController{public function save():void{Session::checkToken() or jexit('JINVALID_TOKEN');$app=Factory::getApplication();try{$aid=$this->getModel('Trainerathletetask')->save($app->input->get('jform',[],'array'));$app->enqueueMessage('Trainingsaufgabe gespeichert.','success');$this->setRedirect(Route::_('index.php?option=com_jugendtraining&view=trainerathletedetail&id='.$aid,false));}catch(\Throwable$e){$app->enqueueMessage($e->getMessage(),'error');$this->setRedirect(Route::_('index.php?option=com_jugendtraining&view=trainerathletetask&athlete_id='.$app->input->getInt('jform.athlete_id'),false));}}}
