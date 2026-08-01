<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\Service;
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;

final class ClassTransitionService
{
    private DatabaseInterface $db;

    public function __construct()
    {
        $this->db=Factory::getContainer()->get(DatabaseInterface::class);
    }

    public function applyForSportyear(int $sportyearId,bool $force=false):array
    {
        if($sportyearId<=0)return['changed'=>0,'skipped'=>1];

        $settingKey='class_transition_sportyear_'.$sportyearId;
        if(!$force&&$this->settingExists($settingKey))return['changed'=>0,'skipped'=>1];

        $q=$this->db->getQuery(true)->select(['id','name','date_end'])
            ->from('#__jt_sportyears')->where('id='.$sportyearId)->where('published=1');
        $this->db->setQuery($q,0,1);
        $sportyear=$this->db->loadObject();
        if(!$sportyear)return['changed'=>0,'skipped'=>1];

        $q=$this->db->getQuery(true)->select('*')->from('#__jt_classes')
            ->where('published=1')->order('ordering ASC,min_age DESC,id ASC');
        $this->db->setQuery($q);
        $classes=$this->db->loadObjectList();

        $q=$this->db->getQuery(true)
            ->select(['id','firstname','lastname','birthdate','gender','class_id'])
            ->from('#__jt_athletes')->where('published=1')->where('birthdate IS NOT NULL');
        $this->db->setQuery($q);
        $athletes=$this->db->loadObjectList();

        $sportYearNumber=(int)substr((string)$sportyear->date_end,0,4);
        $changes=[];
        $uid=(int)Factory::getApplication()->getIdentity()->id;
        $now=Factory::getDate()->toSql();

        $this->db->transactionStart();
        try{
            foreach($athletes as$a){
                $birthYear=(int)substr((string)$a->birthdate,0,4);
                if($birthYear<=0)continue;

                $age=$sportYearNumber-$birthYear;
                $target=$this->findClass($classes,$age,$this->gender((string)$a->gender));
                if(!$target||(int)$target->id===(int)$a->class_id)continue;

                $old=(int)$a->class_id;
                $q=$this->db->getQuery(true)->update('#__jt_athletes')
                    ->set('class_id='.(int)$target->id)
                    ->set('modified='.$this->db->quote($now))
                    ->set('modified_by='.$uid)
                    ->where('id='.(int)$a->id);
                $this->db->setQuery($q)->execute();

                $changes[]=[
                    'athlete_id'=>(int)$a->id,
                    'athlete'=>$a->firstname.' '.$a->lastname,
                    'old_class_id'=>$old,
                    'new_class_id'=>(int)$target->id,
                    'new_class'=>(string)$target->name,
                    'age'=>$age,
                ];
            }

            $this->upsert($settingKey,$now);

            $audit=(object)[
                'user_id'=>$uid,
                'action'=>'sportyear_class_transition',
                'entity_type'=>'sportyear',
                'entity_id'=>$sportyearId,
                'payload'=>json_encode([
                    'sportyear'=>(string)$sportyear->name,
                    'forced'=>$force?1:0,
                    'changed'=>count($changes),
                    'changes'=>$changes
                ],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
                'created'=>$now,
            ];
            $this->db->insertObject('#__jt_audit_log',$audit);
            $this->db->transactionCommit();
        }catch(\Throwable$e){
            $this->db->transactionRollback();
            throw$e;
        }

        return['changed'=>count($changes),'skipped'=>0];
    }

    private function findClass(array$classes,int$age,string$athleteGender):?object
    {
        foreach($classes as$c){
            $min=$c->min_age===null?0:(int)$c->min_age;
            $max=$c->max_age===null?0:(int)$c->max_age;
            $cg=$this->gender((string)($c->gender??''));
            if($age<$min)continue;
            if($max>0&&$age>$max)continue;
            if($cg!==''&&$athleteGender!==''&&$cg!==$athleteGender)continue;
            return$c;
        }
        return null;
    }

    private function gender(string$value):string
    {
        return match(mb_strtolower(trim($value))){
            'm','male','mann','männlich','maennlich'=>'m',
            'w','f','female','frau','weiblich'=>'w',
            default=>'',
        };
    }

    private function settingExists(string$key):bool
    {
        $q=$this->db->getQuery(true)->select('COUNT(*)')->from('#__jt_settings')
            ->where('setting_key='.$this->db->quote($key));
        $this->db->setQuery($q);
        return(int)$this->db->loadResult()>0;
    }

    private function upsert(string$key,string$value):void
    {
        $q=$this->db->getQuery(true)->select('id')->from('#__jt_settings')
            ->where('setting_key='.$this->db->quote($key));
        $this->db->setQuery($q);
        $id=(int)$this->db->loadResult();
        $o=(object)['setting_key'=>$key,'setting_value'=>$value];
        if($id){$o->id=$id;$this->db->updateObject('#__jt_settings',$o,'id');}
        else{$this->db->insertObject('#__jt_settings',$o);}
    }
}
