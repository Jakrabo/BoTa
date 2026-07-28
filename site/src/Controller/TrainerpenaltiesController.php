<?php
namespace Jugendtraining\Component\Jugendtraining\Site\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

final class TrainerpenaltiesController extends BaseController
{
    public function assign(): void
    {
        Session::checkToken() or jexit('JINVALID_TOKEN');
        $app=Factory::getApplication();
        try{
            $this->getModel('Trainerpenalties')->assign($app->input->get('jform',[],'array'));
            $app->enqueueMessage('Strafe wurde eingetragen.','success');
        }catch(\Throwable$e){$app->enqueueMessage($e->getMessage(),'error');}
        $this->setRedirect(Route::_('index.php?option=com_jugendtraining&view=trainerpenalties',false));
    }

    public function complete(): void
    {
        Session::checkToken() or jexit('JINVALID_TOKEN');
        $app=Factory::getApplication();
        try{
            $this->getModel('Trainerpenalties')->complete(
                $app->input->getInt('id'),
                $app->input->getString('completion_note')
            );
            $app->enqueueMessage('Strafe wurde als erledigt markiert.','success');
        }catch(\Throwable$e){$app->enqueueMessage($e->getMessage(),'error');}
        $this->setRedirect(Route::_('index.php?option=com_jugendtraining&view=trainerpenalties',false));
    }

    public function reopen(): void
    {
        Session::checkToken('get') or jexit('JINVALID_TOKEN');
        $app=Factory::getApplication();
        try{
            $this->getModel('Trainerpenalties')->reopen($app->input->getInt('id'));
            $app->enqueueMessage('Eintrag wurde wieder geöffnet.','success');
        }catch(\Throwable$e){$app->enqueueMessage($e->getMessage(),'error');}
        $this->setRedirect(Route::_('index.php?option=com_jugendtraining&view=trainerpenalties',false));
    }
}
