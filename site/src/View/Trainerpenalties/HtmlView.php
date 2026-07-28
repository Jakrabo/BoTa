<?php
namespace Jugendtraining\Component\Jugendtraining\Site\View\Trainerpenalties;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

final class HtmlView extends BaseHtmlView
{
    public array $athletes=[];
    public array $definitions=[];
    public array $entries=[];

    public function display($tpl=null): void
    {
        $this->athletes=(array)($this->get('Athletes')??[]);
        $this->definitions=(array)($this->get('Definitions')??[]);
        $this->entries=(array)($this->get('Entries')??[]);
        parent::display($tpl);
    }
}
