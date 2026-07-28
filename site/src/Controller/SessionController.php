<?php
namespace Jugendtraining\Component\Jugendtraining\Site\Controller;
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

final class SessionController extends BaseController
{
    public function logout(): void
    {
        Session::checkToken('get') or jexit('JINVALID_TOKEN');

        $app = Factory::getApplication();
        $app->logout((int) $app->getIdentity()->id);
        $app->redirect(Route::_('index.php', false));
    }
}
