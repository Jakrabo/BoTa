<?php

namespace Jugendtraining\Component\Jugendtraining\Administrator\View\Training;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;

final class HtmlView extends BaseHtmlView
{
    public $form;
    public $item;
    public array $athletes = [];
    public array $attendance = [];

    public function display($tpl = null): void
    {
        $this->form = $this->get('Form');
        $this->item = $this->get('Item');
        $this->athletes = (array) ($this->get('Athletes') ?? []);
        $this->attendance = (array) ($this->get('Attendance') ?? []);

        ToolbarHelper::title(Text::_($this->item->id ? 'COM_JUGENDTRAINING_TRAINING_EDIT' : 'COM_JUGENDTRAINING_TRAINING_NEW'), 'calendar');

        ToolbarHelper::apply('training.apply');
        ToolbarHelper::save('training.save');
        ToolbarHelper::save2new('training.save2new');
        ToolbarHelper::cancel('training.cancel');

        parent::display($tpl);
    }
}
