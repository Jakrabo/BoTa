<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\Controller;
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Jugendtraining\Component\Jugendtraining\Administrator\Service\ClassTransitionService;

final class SportyearsController extends AdminController
{
    public function getModel($name='Sportyear',$prefix='Administrator',$config=['ignore_request'=>true])
    {
        return parent::getModel($name,$prefix,$config);
    }

    public function applyClassTransition():void
    {
        Session::checkToken() or jexit('JINVALID_TOKEN');
        $app=Factory::getApplication();
        $db=Factory::getContainer()->get('DatabaseDriver');

        $q=$db->getQuery(true)->select('id')->from('#__jt_sportyears')
            ->where('is_current=1')->where('published=1')->order('date_start DESC');
        $db->setQuery($q,0,1);
        $id=(int)$db->loadResult();

        try{
            if($id<=0)throw new \RuntimeException('COM_JUGENDTRAINING_NO_CURRENT_SPORTYEAR');
            $result=(new ClassTransitionService())->applyForSportyear($id,true);
            $app->enqueueMessage(Text::sprintf('COM_JUGENDTRAINING_CLASS_TRANSITION_DONE',(int)$result['changed']),'success');
        }catch(\Throwable$e){
            $message=$e->getMessage();
            if(str_starts_with($message,'COM_JUGENDTRAINING_'))$message=Text::_($message);
            $app->enqueueMessage($message,'error');
        }

        $this->setRedirect(Route::_('index.php?option=com_jugendtraining&view=sportyears',false));
    }
}
