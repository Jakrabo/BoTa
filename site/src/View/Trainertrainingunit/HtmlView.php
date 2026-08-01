<?php
namespace Jugendtraining\Component\Jugendtraining\Site\View\Trainertrainingunit;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

final class HtmlView extends BaseHtmlView
{
    public $form;
    public $item;
    public array $unitItems = [];
    public array $exercises = [];

    public function display($tpl = null): void
    {
        $this->item = $this->get('Item');
        if ($this->getLayout() !== 'edit' && (int) ($this->item->id ?? 0) <= 0) {
            throw new \RuntimeException('JERROR_LAYOUT_REQUESTED_RESOURCE_WAS_NOT_FOUND', 404);
        }
        $this->unitItems = (array) ($this->get('UnitItems') ?? []);
        $this->exercises = (array) ($this->get('Exercises') ?? []);
        if ($this->getLayout() === 'edit') $this->form = $this->get('Form');
        parent::display($tpl);
    }
}
