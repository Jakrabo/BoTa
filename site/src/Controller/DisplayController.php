<?php

namespace Jugendtraining\Component\Jugendtraining\Site\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;

final class DisplayController extends BaseController
{
    protected $default_view = 'dashboard';

    public function display($cachable = true, $urlparams = []): BaseController
    {
        $this->input->set('view', $this->input->getCmd('view', $this->default_view));

        return parent::display($cachable, $urlparams);
    }
}
