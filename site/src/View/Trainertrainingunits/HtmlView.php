<?php
namespace Jugendtraining\Component\Jugendtraining\Site\View\Trainertrainingunits;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

final class HtmlView extends BaseHtmlView
{
    public array $trainingUnits = [];

    public function display($tpl = null): void
    {
        $this->trainingUnits = (array) ($this->get('TrainingUnits') ?? []);
        parent::display($tpl);
    }
}
