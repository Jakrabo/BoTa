<?php
namespace Jugendtraining\Component\Jugendtraining\Site\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Session\Session;

final class TrainertrainingController extends FormController
{
    protected $view_list = 'trainertrainings';

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
        $this->setRedirect($this->getReturnUrl());
        return $result;
    }

    public function cancel($key = null)
    {
        $result = parent::cancel($key);
        $this->setRedirect($this->getReturnUrl());
        return $result;
    }


    public function saveAttendance(): void
    {
        Session::checkToken('post') or jexit('JINVALID_TOKEN');

        $app = Factory::getApplication();
        $input = $app->getInput();
        $sessionId = $input->post->getInt('session_id');
        $athleteId = $input->post->getInt('athlete_id');
        $status = $input->post->getCmd('status');
        $comment = $input->post->getString('comment', '');

        $model = $this->getModel('Trainertraining');

        if ($model->saveSingleAttendance($sessionId, $athleteId, $status, $comment)) {
            echo new JsonResponse([
                'saved' => true,
                'session_id' => $sessionId,
                'athlete_id' => $athleteId,
                'status' => $status,
            ]);
        } else {
            echo new JsonResponse(
                null,
                (string) $model->getError(),
                true
            );
        }

        $app->close();
    }

    public function delete(): void
    {
        Session::checkToken('get') or jexit('JINVALID_TOKEN');
        $id = Factory::getApplication()->getInput()->getInt('id');
        $model = $this->getModel('Trainertraining');

        if ($model->deleteOwn($id)) {
            Factory::getApplication()->enqueueMessage('COM_JUGENDTRAINING_TRAINING_DELETED');
        } else {
            Factory::getApplication()->enqueueMessage($model->getError(), 'error');
        }

        $this->setRedirect($this->getReturnUrl());
    }

    private function getReturnUrl(): string
    {
        $encoded = Factory::getApplication()->getInput()->get('return', '', 'BASE64');
        $decoded = $encoded !== '' ? base64_decode($encoded, true) : false;

        if (
            is_string($decoded)
            && str_starts_with($decoded, 'index.php?option=com_jugendtraining')
            && !str_contains($decoded, "\r")
            && !str_contains($decoded, "\n")
        ) {
            return Route::_($decoded, false);
        }

        return Route::_('index.php?option=com_jugendtraining&view=trainertrainings', false);
    }
}
