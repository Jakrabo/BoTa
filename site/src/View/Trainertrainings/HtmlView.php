<?php
namespace Jugendtraining\Component\Jugendtraining\Site\View\Trainertrainings;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

final class HtmlView extends BaseHtmlView
{
    public array$trainings=[];
    public array$groups=[];
    public object$trainingFilter;

    public function display($tpl=null):void
    {
        $this->trainings=(array)($this->get('Trainings')??[]);
        $this->groups=(array)($this->get('Groups')??[]);
        $this->trainingFilter=$this->get('TrainingFilter')??(object)['period'=>'14','group_id'=>0];
        parent::display($tpl);
    }
}
