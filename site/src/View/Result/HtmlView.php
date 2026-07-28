<?php

namespace Jugendtraining\Component\Jugendtraining\Site\View\Result;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

final class HtmlView extends BaseHtmlView
{
    public $form;
    public $item;

    public function display($tpl = null): void
    {
        $user = Factory::getApplication()->getIdentity();

        if ($user->guest) {
            throw new \RuntimeException('JERROR_ALERTNOAUTHOR', 403);
        }

        $this->form = $this->get('Form');
        $this->item = $this->get('Item');

        parent::display($tpl);
    }
}
