<?php
namespace Jugendtraining\Component\Jugendtraining\Site\View\Trainerathletes;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

final class HtmlView extends BaseHtmlView
{
    public array$athletes=[];
    public object$athleteSort;

    public function display($tpl=null):void
    {
        $this->athletes=(array)($this->get('Athletes')??[]);
        $this->athleteSort=$this->get('AthleteSort')??(object)['sort'=>'athlete','direction'=>'asc'];
        parent::display($tpl);
    }
}
