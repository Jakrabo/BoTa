<?php
namespace Jugendtraining\Component\Jugendtraining\Site\View\Trainerpenalties;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;use Joomla\CMS\Factory;

final class HtmlView extends BaseHtmlView
{
    public array $athletes=[];
    public array $definitions=[];
    public array $entries=[];

    public function display($tpl=null): void
    {
        Factory::getApplication()->setHeader('Cache-Control','private, no-store, no-cache, max-age=0',true);
        $this->athletes=(array)($this->get('Athletes')??[]);
        $this->definitions=(array)($this->get('Definitions')??[]);
        $this->entries=(array)($this->get('Entries')??[]);
        parent::display($tpl);
    }
}
