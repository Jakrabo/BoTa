<?php

namespace Jugendtraining\Component\Jugendtraining\Administrator\View\Trainings;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;

final class HtmlView extends BaseHtmlView
{
    public $items;
    public $pagination;
    public $state;
    public array $trainingGroups = [];
    public array $trainingLocations = [];

    public function display($tpl = null): void
    {
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');
        $this->trainingGroups = (array) ($this->get('TrainingGroups') ?? []);
        $this->trainingLocations = (array) ($this->get('TrainingLocations') ?? []);

        ToolbarHelper::title(Text::_('COM_JUGENDTRAINING_TRAININGS'), 'calendar');
        ToolbarHelper::addNew('training.add');
        ToolbarHelper::editList('training.edit');
        ToolbarHelper::publish('trainings.publish', 'JTOOLBAR_PUBLISH', true);
        ToolbarHelper::unpublish('trainings.unpublish', 'JTOOLBAR_UNPUBLISH', true);
        ToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'trainings.delete');

        parent::display($tpl);
    }
}
