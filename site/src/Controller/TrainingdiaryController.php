<?php
namespace Jugendtraining\Component\Jugendtraining\Site\Controller;
\defined('_JEXEC') or die;
use Joomla\CMS\Factory;use Joomla\CMS\MVC\Controller\FormController;use Joomla\CMS\Router\Route;use Joomla\CMS\Session\Session;
final class TrainingdiaryController extends FormController {
 protected $view_list='trainingdiaries';
 protected function allowAdd($data=[]):bool{return !Factory::getApplication()->getIdentity()->guest;}
 protected function allowEdit($data=[],$key='id'):bool{
 $id=isset($data[$key])?(int)$data[$key]:Factory::getApplication()->getInput()->getInt($key);
 return $id>0 && $this->getModel('Trainingdiary')->canEditDiary($id);
}
 public function save($key=null,$urlVar=null){Session::checkToken() or jexit('JINVALID_TOKEN');$r=parent::save($key,$urlVar);$this->setRedirect(Route::_('index.php?option=com_jugendtraining&view=trainingdiaries',false));return$r;}
 public function cancel($key=null){$r=parent::cancel($key);$this->setRedirect(Route::_('index.php?option=com_jugendtraining&view=trainingdiaries',false));return$r;}
 public function delete():void{Session::checkToken('get') or jexit('JINVALID_TOKEN');$id=Factory::getApplication()->getInput()->getInt('id');$m=$this->getModel('Trainingdiary');$ok=method_exists($m,'deleteOwn')?$m->deleteOwn($id):false;Factory::getApplication()->enqueueMessage($ok?'COM_JUGENDTRAINING_DELETED':'JERROR_ALERTNOAUTHOR',$ok?'message':'error');$this->setRedirect(Route::_('index.php?option=com_jugendtraining&view=trainingdiaries',false));}
}
