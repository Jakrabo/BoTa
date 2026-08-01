<?php
namespace Jugendtraining\Component\Jugendtraining\Site\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Language\Text;

final class TrainertrainingunitController extends FormController
{
    protected $view_list = 'trainertrainingunits';

    protected function allowAdd($data = []): bool
    {
        return !Factory::getApplication()->getIdentity()->guest;
    }

    protected function allowEdit($data = [], $key = 'id'): bool
    {
        return !Factory::getApplication()->getIdentity()->guest;
    }

    public function save($key = null, $urlVar = null)
    {
        Session::checkToken() or jexit('JINVALID_TOKEN');
        $result = parent::save($key, $urlVar);
        $this->setRedirect($this->returnUrl());
        return $result;
    }

    public function cancel($key = null)
    {
        $result = parent::cancel($key);
        $this->setRedirect($this->returnUrl());
        return $result;
    }

    public function delete(): void
    {
        Session::checkToken('get') or jexit('JINVALID_TOKEN');
        $model = $this->getModel('Trainertrainingunit');
        $id = Factory::getApplication()->getInput()->getInt('id');
        Factory::getApplication()->enqueueMessage(
            $model->deleteOwn($id) ? Text::_('COM_JUGENDTRAINING_TRAINING_UNIT_DELETED') : (string) $model->getError(),
            $model->getError() ? 'error' : 'message'
        );
        $this->setRedirect($this->returnUrl());
    }

    private function returnUrl(): string
    {
        $encoded = Factory::getApplication()->getInput()->get('return', '', 'BASE64');
        $decoded = $encoded !== '' ? base64_decode($encoded, true) : false;
        if (is_string($decoded) && str_starts_with($decoded, 'index.php?option=com_jugendtraining') && !str_contains($decoded, "\n") && !str_contains($decoded, "\r")) {
            return Route::_($decoded, false);
        }
        return Route::_('index.php?option=com_jugendtraining&view=trainertrainingunits', false);
    }
}
