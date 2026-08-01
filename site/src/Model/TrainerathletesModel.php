<?php
namespace Jugendtraining\Component\Jugendtraining\Site\Model;
\defined('_JEXEC') or die;
use Joomla\CMS\Factory;

final class TrainerathletesModel extends TrainerModel
{
    public function getAthletes():array
    {
        $rows=parent::getAthletes();
        $app=Factory::getApplication();
        $sort=$app->input->getCmd('sort','athlete');
        $direction=strtolower($app->input->getCmd('direction','asc'))==='desc'?'desc':'asc';
        if(!in_array($sort,['athlete','groups','class','bow_type','phone'],true))$sort='athlete';

        usort($rows,function($a,$b)use($sort,$direction){
            $value=static function($r,$key){
                return match($key){
                    'groups'=>(string)$r->group_names,
                    'class'=>(string)$r->class_name,
                    'bow_type'=>(string)$r->bow_type,
                    'phone'=>(string)$r->phone,
                    default=>(string)$r->firstname."\0".(string)$r->lastname,
                };
            };
            $cmp=strnatcasecmp($value($a,$sort),$value($b,$sort));
            if($cmp===0)$cmp=strnatcasecmp((string)$a->firstname,(string)$b->firstname);
            if($cmp===0)$cmp=strnatcasecmp((string)$a->lastname,(string)$b->lastname);
            return$direction==='desc'?-1*$cmp:$cmp;
        });
        return$rows;
    }

    public function getAthleteSort():object
    {
        $app=Factory::getApplication();
        $sort=$app->input->getCmd('sort','athlete');
        if(!in_array($sort,['athlete','groups','class','bow_type','phone'],true))$sort='athlete';
        return(object)['sort'=>$sort,'direction'=>strtolower($app->input->getCmd('direction','asc'))==='desc'?'desc':'asc'];
    }
}
