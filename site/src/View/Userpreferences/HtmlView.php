<?php
namespace Jugendtraining\Component\Jugendtraining\Site\View\Userpreferences;
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

final class HtmlView extends BaseHtmlView
{
    public string $theme='auto';

    public function display($tpl=null): void
    {
        Factory::getApplication()->setHeader('Cache-Control','private, no-store, no-cache, max-age=0',true);
        $this->theme=(string)($this->get('Theme')??'auto');
        parent::display($tpl);
    }
}
