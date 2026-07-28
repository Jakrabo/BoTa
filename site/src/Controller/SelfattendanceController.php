<?php
namespace Jugendtraining\Component\Jugendtraining\Site\Controller;
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Language\Text;
use Jugendtraining\Component\Jugendtraining\Site\Service\SelfAttendanceService;

final class SelfattendanceController extends BaseController
{
    public function cancel(): void
    {
        Session::checkToken() or jexit('JINVALID_TOKEN');
        $app=Factory::getApplication();

        try{
            (new SelfAttendanceService())->cancel($app->input->post->getInt('session_id'));
            $app->enqueueMessage(Text::_('COM_JUGENDTRAINING_SELF_CANCEL_SUCCESS'),'success');
        }catch(\Throwable $e){
            $message=$e->getMessage();
            if(str_starts_with($message,'COM_JUGENDTRAINING_'))$message=Text::_($message);
            $app->enqueueMessage($message,'error');
        }

        $this->setRedirect(Route::_('index.php?option=com_jugendtraining&view=dashboard',false));
    }
}
