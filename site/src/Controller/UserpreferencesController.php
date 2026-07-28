<?php
namespace Jugendtraining\Component\Jugendtraining\Site\Controller;
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

final class UserpreferencesController extends BaseController
{
    public function save(): void
    {
        Session::checkToken() or jexit('JINVALID_TOKEN');
        $app=Factory::getApplication();

        try{
            $this->getModel('Userpreferences')->saveTheme($app->input->post->getCmd('theme','auto'));
            $app->enqueueMessage('Darstellung gespeichert.','success');
        }catch(\Throwable $e){
            $app->enqueueMessage($e->getMessage(),'error');
        }

        $this->setRedirect(Route::_('index.php?option=com_jugendtraining&view=userpreferences',false));
    }
}
