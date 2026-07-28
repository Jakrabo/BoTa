<?php
namespace Jugendtraining\Component\Jugendtraining\Site\View\Loginredirect;
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Router\Route;
use Jugendtraining\Component\Jugendtraining\Site\Service\AccessService;

final class HtmlView extends BaseHtmlView
{
    public function display($tpl = null): void
    {
        $app = Factory::getApplication();
        $user = $app->getIdentity();

        if ($user->guest) {
            $app->redirect(Route::_('index.php', false));
            return;
        }

        $access = new AccessService();

        // Trainer has priority in case a trainer also has an athlete record.
        if ($access->isTrainer()) {
            $app->redirect(Route::_('index.php?option=com_jugendtraining&view=trainerdashboard', false));
            return;
        }

        if ($access->isAthlete()) {
            $app->redirect(Route::_('index.php?option=com_jugendtraining&view=dashboard', false));
            return;
        }

        $app->redirect(Route::_('index.php', false));
    }
}
