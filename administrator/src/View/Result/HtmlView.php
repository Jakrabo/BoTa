<?php

namespace Jugendtraining\Component\Jugendtraining\Administrator\View\Result;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

final class HtmlView extends BaseHtmlView
{
    public $form;
    public $item;

    public function display($tpl = null): void
    {
        $this->form = $this->get('Form');
        $this->item = $this->get('Item');

        ToolbarHelper::title(
            $this->item->id ? 'COM_JUGENDTRAINING_RESULT_EDIT' : 'COM_JUGENDTRAINING_RESULT_NEW',
            'chart'
        );
        ToolbarHelper::apply('result.apply');
        ToolbarHelper::save('result.save');
        ToolbarHelper::save2new('result.save2new');
        ToolbarHelper::cancel('result.cancel');

        parent::display($tpl);
    }
}
