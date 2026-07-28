<?php

namespace Jugendtraining\Component\Jugendtraining\Administrator\View\Results;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;

final class HtmlView extends BaseHtmlView
{
    public $items;
    public $pagination;
    public $state;
    public $athleteOptions;

    public function display($tpl = null): void
    {
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');
        $this->athleteOptions = $this->get('AthleteOptions');

        ToolbarHelper::title(Text::_('COM_JUGENDTRAINING_RESULTS'), 'chart');
        ToolbarHelper::addNew('result.add');
        ToolbarHelper::editList('result.edit');
        ToolbarHelper::publish('results.publish', 'JTOOLBAR_PUBLISH', true);
        ToolbarHelper::unpublish('results.unpublish', 'JTOOLBAR_UNPUBLISH', true);
        ToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'results.delete');

        parent::display($tpl);
    }
}
