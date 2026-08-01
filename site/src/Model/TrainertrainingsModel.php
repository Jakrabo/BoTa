<?php
namespace Jugendtraining\Component\Jugendtraining\Site\Model;
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;

final class TrainertrainingsModel extends TrainerModel
{
    public function getTrainings():array
    {
        $this->requireTrainer();
        $app=Factory::getApplication();
        $uid=(int)$app->getIdentity()->id;
        $period=$app->input->getCmd('period','14');
        $groupId=$app->input->getInt('group_id');

        $timezone=(string)$app->get('offset','UTC');
        try{$today=new \DateTimeImmutable('today',new \DateTimeZone($timezone?:'UTC'));}
        catch(\Throwable){$today=new \DateTimeImmutable('today');}

        $db=$this->getDatabase();
        $q=$db->getQuery(true)->select(['s.*','g.title group_title'])
            ->from($db->quoteName('#__jt_training_sessions','s'))
            ->leftJoin($db->quoteName('#__jt_training_groups','g').' ON g.id=s.training_group_id')
            ->leftJoin($db->quoteName('#__jt_training_group_trainers','gt').' ON gt.group_id=s.training_group_id')
            ->where('(gt.user_id='.$uid.' OR s.trainer_user_id='.$uid.')');

        if($groupId>0){
            $allowed=array_map(static fn($g)=>(int)$g->id,$this->getGroups());
            $q->where(in_array($groupId,$allowed,true)?'s.training_group_id='.$groupId:'1=0');
        }

        $todaySql=$db->quote($today->format('Y-m-d'));

        switch($period){
            case '30':
                $end=$today->modify('+29 days');
                $q->where('s.training_date>='.$todaySql)->where('s.training_date<='.$db->quote($end->format('Y-m-d')))
                  ->order('s.training_date ASC,s.start_time ASC,s.id ASC');
                break;
            case 'future':
                $q->where('s.training_date>='.$todaySql)->order('s.training_date ASC,s.start_time ASC,s.id ASC');
                break;
            case 'past':
                $q->where('s.training_date<'.$todaySql)->order('s.training_date DESC,s.start_time DESC,s.id DESC');
                break;
            case 'all':
                $q->order('s.training_date DESC,s.start_time DESC,s.id DESC');
                break;
            default:
                $period='14';
                $end=$today->modify('+13 days');
                $q->where('s.training_date>='.$todaySql)->where('s.training_date<='.$db->quote($end->format('Y-m-d')))
                  ->order('s.training_date ASC,s.start_time ASC,s.id ASC');
        }

        $q->group('s.id');
        $db->setQuery($q,0,500);
        return$db->loadObjectList();
    }

    public function getTrainingFilter():object
    {
        $app=Factory::getApplication();
        return(object)[
            'period'=>$app->input->getCmd('period','14'),
            'group_id'=>$app->input->getInt('group_id'),
        ];
    }
}
