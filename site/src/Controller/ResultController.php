<?php

namespace Jugendtraining\Component\Jugendtraining\Site\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

final class ResultController extends FormController
{
    protected $view_list = 'dashboard';

    protected function allowAdd($data = []): bool
    {
        $user = Factory::getApplication()->getIdentity();

        if ($user->guest) {
            return false;
        }

        /** @var \Jugendtraining\Component\Jugendtraining\Site\Model\ResultModel $model */
        $model = $this->getModel('Result');

        return $model->canCreateResult();
    }

    protected function allowEdit($data = [], $key = 'id'): bool
    {
        $user = Factory::getApplication()->getIdentity();

        if ($user->guest) {
            return false;
        }

        $id = isset($data[$key])
            ? (int) $data[$key]
            : Factory::getApplication()->getInput()->getInt($key);

        if ($id <= 0) {
            return false;
        }

        /** @var \Jugendtraining\Component\Jugendtraining\Site\Model\ResultModel $model */
        $model = $this->getModel('Result');

        return $model->canEditResult($id);
    }

    public function save($key = null, $urlVar = null)
    {
        Session::checkToken() or jexit('JINVALID_TOKEN');

        $result = parent::save($key, $urlVar);

        $this->setRedirect(
            Route::_('index.php?option=com_jugendtraining&view=dashboard', false)
        );

        return $result;
    }

    public function cancel($key = null)
    {
        $result = parent::cancel($key);

        $this->setRedirect(
            Route::_('index.php?option=com_jugendtraining&view=dashboard', false)
        );

        return $result;
    }

    public function delete()
    {
        Session::checkToken('get') or jexit('JINVALID_TOKEN');

        $id = Factory::getApplication()->getInput()->getInt('id');

        /** @var \Jugendtraining\Component\Jugendtraining\Site\Model\ResultModel $model */
        $model = $this->getModel('Result');

        if ($id > 0 && $model->deleteOwnResult($id)) {
            Factory::getApplication()->enqueueMessage(
                'COM_JUGENDTRAINING_RESULT_DELETED',
                'message'
            );
        } else {
            Factory::getApplication()->enqueueMessage(
                $model->getError() ?: 'COM_JUGENDTRAINING_RESULT_DELETE_FAILED',
                'error'
            );
        }

        $this->setRedirect(
            Route::_('index.php?option=com_jugendtraining&view=dashboard', false)
        );
    }
}
