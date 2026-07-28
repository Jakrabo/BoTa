<?php
namespace Jugendtraining\Component\Jugendtraining\Site\View\Trainertraining;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

final class HtmlView extends BaseHtmlView
{
    public $form;
    public $item;
    public array $athletes = [];
    public array $attendance = [];

    public function display($tpl = null): void
    {
        \Joomla\CMS\Factory::getApplication()->getLanguage()->load(
            'com_jugendtraining',
            JPATH_SITE
        );
        $this->form = $this->get('Form');
        $this->item = $this->get('Item');
        $this->athletes = (array) ($this->get('Athletes') ?? []);
        $this->attendance = (array) ($this->get('Attendance') ?? []);
        parent::display($tpl);
    }
}
