<?php
namespace Jugendtraining\Component\Jugendtraining\Site\Controller;
\defined('_JEXEC') or die;
use Joomla\CMS\Factory;use Joomla\CMS\MVC\Controller\BaseController;use Joomla\CMS\Router\Route;
final class AchievementdefinitionController extends BaseController{
 public function save():void{$this->checkToken();$app=Factory::getApplication();try{$model=$this->getModel('Trainerachievementedit','Site');$id=$model->save($app->input->post->getArray(),$app->input->files->get('badge_upload',null,'array'));$app->enqueueMessage('COM_JUGENDTRAINING_ACHIEVEMENT_SAVED');$this->setRedirect(Route::_('index.php?option=com_jugendtraining&view=trainerachievementedit&id='.$id,false));}catch(\Throwable$e){$app->enqueueMessage($e->getMessage(),'error');$this->setRedirect(Route::_('index.php?option=com_jugendtraining&view=trainerachievementedit&id='.$app->input->post->getInt('id'),false));}}
 public function delete():void{$this->checkToken();$app=Factory::getApplication();try{$this->getModel('Trainerachievementedit','Site')->delete($app->input->post->getInt('id'));$app->enqueueMessage('COM_JUGENDTRAINING_ACHIEVEMENT_DELETED');}catch(\Throwable$e){$app->enqueueMessage($e->getMessage(),'error');}$this->setRedirect(Route::_('index.php?option=com_jugendtraining&view=trainerachievementdefinitions',false));}
}
