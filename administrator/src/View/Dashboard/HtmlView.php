<?php

namespace Jugendtraining\Component\Jugendtraining\Administrator\View\Dashboard;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;

final class HtmlView extends BaseHtmlView
{
    public $stats;
    public $currentSportyear;
    public $recentAthletes;
    public $upcomingTrainings;

    public function display($tpl = null): void
    {
        $this->stats = $this->get('Stats');
        $this->currentSportyear = $this->get('CurrentSportyear');
        $this->recentAthletes = $this->get('RecentAthletes');
        $this->upcomingTrainings = $this->get('UpcomingTrainings');

        ToolbarHelper::title(Text::_('COM_JUGENDTRAINING_DASHBOARD'), 'home');
        ToolbarHelper::preferences('com_jugendtraining');

        parent::display($tpl);
    }
}
