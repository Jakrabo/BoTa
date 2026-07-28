<?php
namespace Jugendtraining\Component\Jugendtraining\Site\Controller;
\defined('_JEXEC') or die;
use Joomla\CMS\Factory;use Joomla\CMS\MVC\Controller\BaseController;use Joomla\CMS\Router\Route;use Joomla\CMS\Session\Session;
final class ProgressController extends BaseController {
 public function toggle():void{
  Session::checkToken() or jexit('JINVALID_TOKEN');
  $app=Factory::getApplication();$assignmentId=$app->getInput()->post->getInt('assignment_id');$exerciseId=$app->getInput()->post->getInt('exercise_id');
  $model=$this->getModel('Progress');
  if($model->toggle($assignmentId,$exerciseId))$app->enqueueMessage('COM_JUGENDTRAINING_PROGRESS_SAVED','message');else $app->enqueueMessage($model->getError()?:'COM_JUGENDTRAINING_PROGRESS_FAILED','error');
  $this->setRedirect(Route::_('index.php?option=com_jugendtraining&view=dashboard',false));
 }
}
