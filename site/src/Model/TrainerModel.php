<?php
namespace Jugendtraining\Component\Jugendtraining\Site\Model;
\defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Jugendtraining\Component\Jugendtraining\Site\Service\AccessService;

class TrainerModel extends BaseDatabaseModel {
 private AccessService $access;
 public function __construct($config=[]){parent::__construct($config);$this->access=new AccessService();}
 private function ids():array{return $this->access->getTrainerAthleteIds();}
 protected function requireTrainer():void{if(!$this->access->isTrainer())throw new \RuntimeException('JERROR_ALERTNOAUTHOR',403);}
 private function idList():string{$ids=$this->ids();return $ids?implode(',',array_map('intval',$ids)):'0';}
 public function getGroups():array{
  $this->requireTrainer();$u=(int)Factory::getApplication()->getIdentity()->id;$db=$this->getDatabase();
  $q=$db->getQuery(true)->select(['g.*','COUNT(DISTINCT ga.athlete_id) athlete_count'])->from($db->quoteName('#__jt_training_groups','g'))
   ->innerJoin($db->quoteName('#__jt_training_group_trainers','gt').' ON gt.group_id=g.id')
   ->leftJoin($db->quoteName('#__jt_training_group_athletes','ga').' ON ga.group_id=g.id')
   ->where('gt.user_id='.$u)->where('g.published=1')->group('g.id')->order('g.title');
  $db->setQuery($q);return $db->loadObjectList();
 }
 public function getAthletes():array{
  $this->requireTrainer();$db=$this->getDatabase();$q=$db->getQuery(true)
   ->select(['a.*','c.name club_name','cl.name class_name','GROUP_CONCAT(DISTINCT g.title ORDER BY g.title SEPARATOR ", ") group_names'])
   ->from($db->quoteName('#__jt_athletes','a'))
   ->leftJoin($db->quoteName('#__jt_clubs','c').' ON c.id=a.club_id')
   ->leftJoin($db->quoteName('#__jt_classes','cl').' ON cl.id=a.class_id')
   ->leftJoin($db->quoteName('#__jt_training_group_athletes','ga').' ON ga.athlete_id=a.id')
   ->leftJoin($db->quoteName('#__jt_training_groups','g').' ON g.id=ga.group_id')
   ->where('a.id IN ('.$this->idList().')')->where('a.published=1');
  $groupId=Factory::getApplication()->input->getInt('group_id');
  if($groupId>0){$q->where('ga.group_id='.$groupId);}
  $q->group('a.id')->order('a.lastname,a.firstname');
  $db->setQuery($q);return $db->loadObjectList();
 }
 public function getResults():array{
  $this->requireTrainer();$db=$this->getDatabase();$q=$db->getQuery(true)
   ->select(['r.*',"CONCAT(a.firstname,' ',a.lastname) athlete_name"])->from($db->quoteName('#__jt_results','r'))
   ->innerJoin($db->quoteName('#__jt_athletes','a').' ON a.id=r.athlete_id')
   ->where('r.athlete_id IN ('.$this->idList().')')->order('r.result_date DESC,r.id DESC');
  $db->setQuery($q,0,100);return $db->loadObjectList();
 }
 public function getTrainings():array{
  $this->requireTrainer();$u=(int)Factory::getApplication()->getIdentity()->id;$db=$this->getDatabase();
  $q=$db->getQuery(true)->select(['s.*','g.title group_title'])->from($db->quoteName('#__jt_training_sessions','s'))
   ->leftJoin($db->quoteName('#__jt_training_groups','g').' ON g.id=s.training_group_id')
   ->leftJoin($db->quoteName('#__jt_training_group_trainers','gt').' ON gt.group_id=s.training_group_id')
   ->where('(gt.user_id='.$u.' OR s.trainer_user_id='.$u.')')->group('s.id')->order('s.training_date DESC');
  $db->setQuery($q,0,100);return $db->loadObjectList();
 }
 public function getPrograms():array{
  $this->requireTrainer();$db=$this->getDatabase();$q=$db->getQuery(true)
   ->select(['p.*','COUNT(DISTINCT ap.athlete_id) assigned_count'])->from($db->quoteName('#__jt_training_programs','p'))
   ->leftJoin($db->quoteName('#__jt_athlete_programs','ap').' ON ap.program_id=p.id AND ap.active=1 AND ap.athlete_id IN ('.$this->idList().')')
   ->group('p.id')->order('p.title');$db->setQuery($q);return $db->loadObjectList();
 }
 public function getGoals():array{
  $this->requireTrainer();$db=$this->getDatabase();$q=$db->getQuery(true)
   ->select(['g.*',"CONCAT(a.firstname,' ',a.lastname) athlete_name"])->from($db->quoteName('#__jt_goals','g'))
   ->innerJoin($db->quoteName('#__jt_athletes','a').' ON a.id=g.athlete_id')
   ->where('g.athlete_id IN ('.$this->idList().')')->order('g.completed,g.due_date');$db->setQuery($q);return $db->loadObjectList();
 }
 public function getNotes():array{
  $this->requireTrainer();$db=$this->getDatabase();$q=$db->getQuery(true)
   ->select(['n.*',"CONCAT(a.firstname,' ',a.lastname) athlete_name"])->from($db->quoteName('#__jt_trainer_notes','n'))
   ->innerJoin($db->quoteName('#__jt_athletes','a').' ON a.id=n.athlete_id')
   ->where('n.athlete_id IN ('.$this->idList().')')->order('n.note_date DESC,n.id DESC');$db->setQuery($q,0,100);return $db->loadObjectList();
 }
 public function getDiaries():array{
  $this->requireTrainer();$db=$this->getDatabase();$q=$db->getQuery(true)
   ->select(['d.*',"CONCAT(a.firstname,' ',a.lastname) athlete_name",'s.title setup_title','s.revision_no'])
   ->from($db->quoteName('#__jt_training_diary','d'))
   ->innerJoin($db->quoteName('#__jt_athletes','a').' ON a.id=d.athlete_id')
   ->leftJoin($db->quoteName('#__jt_bow_setups','s').' ON s.id=d.bow_setup_id')
   ->where('d.athlete_id IN ('.$this->idList().')')->order('d.training_date DESC,d.id DESC');
  $db->setQuery($q,0,200);return$db->loadObjectList();
 }


public function getTrainerDashboardConfig(): array
{
 $defaults=[
  ['key'=>'groups','visible'=>1],
  ['key'=>'today_trainings','visible'=>1],
  ['key'=>'penalty_summary','visible'=>1],
  ['key'=>'open_penalties','visible'=>1],
  ['key'=>'signals','visible'=>1],
  ['key'=>'class_changes','visible'=>1],
  ['key'=>'navigation','visible'=>1]
 ];
 $db=$this->getDatabase();
 $q=$db->getQuery(true)->select('setting_value')->from('#__jt_settings')
  ->where('setting_key='.$db->quote('trainer_dashboard_config'));
 $db->setQuery($q);
 $saved=json_decode((string)$db->loadResult(),true);
 return is_array($saved)?$saved:$defaults;
}

public function getOpenPenalties(): array
{
 $this->requireTrainer();$db=$this->getDatabase();
 $q=$db->getQuery(true)->select(['r.*','d.title','d.penalty_type',"CONCAT(a.firstname,' ',a.lastname) athlete_name"])
  ->from($db->quoteName('#__jt_penalty_register','r'))
  ->innerJoin($db->quoteName('#__jt_penalty_definitions','d').' ON d.id=r.penalty_definition_id')
  ->innerJoin($db->quoteName('#__jt_athletes','a').' ON a.id=r.athlete_id')
  ->where('r.athlete_id IN ('.$this->idList().')')->where("r.status='open'")
  ->order('r.assigned_at DESC,r.id DESC');
 $db->setQuery($q);return$db->loadObjectList();
}

public function getPenaltyBalance(): float
{
 $this->requireTrainer();$db=$this->getDatabase();
 $q=$db->getQuery(true)->select('setting_value')->from($db->quoteName('#__jt_settings'))
  ->where('setting_key='.$db->quote('penalty_balance_reset_at'));
 $db->setQuery($q);$reset=(string)$db->loadResult();
 $q=$db->getQuery(true)->select('COALESCE(SUM(r.amount_snapshot),0)')
  ->from($db->quoteName('#__jt_penalty_register','r'))
  ->where("r.status='completed'")->where('r.amount_snapshot IS NOT NULL');
 if($reset!=='')$q->where('r.completed_at>='.$db->quote($reset));
 $db->setQuery($q);return(float)$db->loadResult();
}
 public function getAthleteSignals():array{
  $this->requireTrainer();$db=$this->getDatabase();$ids=$this->idList();
  $q=$db->getQuery(true)->select([
   'a.id',"CONCAT(a.firstname,' ',a.lastname) athlete_name",'cl.name class_name',
   'COALESCE(SUM(CASE WHEN d.training_date >= DATE_SUB(CURDATE(),INTERVAL 28 DAY) THEN d.arrow_count ELSE 0 END),0) arrows_28',
   'COALESCE(SUM(CASE WHEN d.training_date >= DATE_SUB(CURDATE(),INTERVAL 28 DAY) THEN d.duration_minutes ELSE 0 END),0) minutes_28',
   'MAX(d.training_date) last_training'
  ])->from($db->quoteName('#__jt_athletes','a'))
   ->leftJoin($db->quoteName('#__jt_classes','cl').' ON cl.id=a.class_id')
   ->leftJoin($db->quoteName('#__jt_training_diary','d').' ON d.athlete_id=a.id')
   ->where('a.id IN ('.$ids.')')->where('a.published=1')->group('a.id')->order('a.lastname,a.firstname');
  $db->setQuery($q);$rows=$db->loadObjectList();
  foreach($rows as$r){
   $r->average_recent=$this->resultAverage((int)$r->id,90,0);
   $r->average_previous=$this->resultAverage((int)$r->id,90,90);
   $r->trend=round($r->average_recent-$r->average_previous,3);
   $days=$r->last_training?(int)((new \DateTimeImmutable($r->last_training))->diff(new \DateTimeImmutable('today'))->format('%a')):999;
   if($days>21||(int)$r->arrows_28<100){$r->signal='red';$r->signal_reason='COM_JUGENDTRAINING_SIGNAL_LOW_TRAINING';}
   elseif((int)$r->arrows_28<250||$r->trend<=0){$r->signal='yellow';$r->signal_reason=$r->trend<=0?'COM_JUGENDTRAINING_SIGNAL_STAGNATING':'COM_JUGENDTRAINING_SIGNAL_MODERATE_TRAINING';}
   else{$r->signal='green';$r->signal_reason='COM_JUGENDTRAINING_SIGNAL_POSITIVE';}
  }
  return$rows;
 }
 public function getClassTransitions():array{
  $this->requireTrainer();$db=$this->getDatabase();
  $q=$db->getQuery(true)->select('*')->from($db->quoteName('#__jt_sportyears'))->where('published=1')->order('is_current DESC,date_end DESC');
  $db->setQuery($q);$years=$db->loadObjectList();$current=null;$next=null;
  foreach($years as$y){if(!$current&&(int)$y->is_current===1)$current=$y;}
  if(!$current&&$years)$current=$years[0];
  if($current){$q=$db->getQuery(true)->select('*')->from($db->quoteName('#__jt_sportyears'))->where('published=1')->where('date_start>'.$db->quote($current->date_end))->order('date_start ASC');$db->setQuery($q,0,1);$next=$db->loadObject();}
  if(!$next)return[];
  $q=$db->getQuery(true)->select('*')->from($db->quoteName('#__jt_classes'))->where('published=1')->order('ordering ASC,min_age DESC');$db->setQuery($q);$classes=$db->loadObjectList();
  $athletes=$this->getAthletes();$result=[];$year=(int)substr($next->date_end,0,4);
  foreach($athletes as$a){
   if(!$a->birthdate)continue;$age=$year-(int)substr($a->birthdate,0,4);$match=null;
   foreach($classes as$c){$min=(int)($c->min_age??0);$max=(int)($c->max_age??0);$gender=(string)($c->gender??'');if($age>=$min&&($max===0||$age<=$max)&&($gender===''||$gender==='all'||$gender===$a->gender)){$match=$c;break;}}
   if($match&&(int)$match->id!==(int)$a->class_id)$result[]=(object)['athlete_id'=>$a->id,'athlete_name'=>$a->firstname.' '.$a->lastname,'current_class'=>$a->class_name,'next_class'=>$match->name,'next_age'=>$age,'sportyear_name'=>$next->name,'change_date'=>$next->date_start,'group_names'=>$a->group_names];
  }
  usort($result,fn($x,$y)=>strcmp($x->athlete_name,$y->athlete_name));return$result;
 }
 public function getStatistics():object{
  $this->requireTrainer();$db=$this->getDatabase();$ids=$this->idList();
  $stats=(object)['athletes'=>[],'setupPerformance'=>[],'shaftPerformance'=>[],'bracePerformance'=>[],'weatherSight'=>[],'correlation'=>null,'monthly'=>[]];
  $q=$db->getQuery(true)->select([
   'a.id',"CONCAT(a.firstname,' ',a.lastname) athlete_name",
   'COALESCE(SUM(CASE WHEN d.training_date>=DATE_SUB(CURDATE(),INTERVAL 90 DAY) THEN d.arrow_count ELSE 0 END),0) arrows_90',
   'COALESCE(SUM(CASE WHEN d.training_date>=DATE_SUB(CURDATE(),INTERVAL 90 DAY) THEN d.duration_minutes ELSE 0 END),0) minutes_90',
   'COUNT(DISTINCT CASE WHEN d.training_date>=DATE_SUB(CURDATE(),INTERVAL 90 DAY) THEN d.id END) diary_entries'
  ])->from($db->quoteName('#__jt_athletes','a'))->leftJoin($db->quoteName('#__jt_training_diary','d').' ON d.athlete_id=a.id')
   ->where('a.id IN ('.$ids.')')->group('a.id')->order('a.lastname,a.firstname');
  $db->setQuery($q);$stats->athletes=$db->loadObjectList();
  foreach($stats->athletes as$a){$a->average_recent=$this->resultAverage((int)$a->id,90,0);$a->average_previous=$this->resultAverage((int)$a->id,90,90);$a->trend=round($a->average_recent-$a->average_previous,3);}
  $q=$db->getQuery(true)->select(['s.id','s.title','s.revision_no','COUNT(r.id) result_count','ROUND(AVG(r.average),3) avg_result','MAX(r.average) best_result'])
   ->from($db->quoteName('#__jt_results','r'))->innerJoin($db->quoteName('#__jt_bow_setups','s').' ON s.id=r.bow_setup_id')
   ->where('r.athlete_id IN ('.$ids.')')->where('r.published=1')->group('s.id')->having('COUNT(r.id)>0')->order('avg_result DESC');
  $db->setQuery($q);$stats->setupPerformance=$db->loadObjectList();
  $q=$db->getQuery(true)->select(['s.arrow_shaft','s.arrow_spine','COUNT(r.id) result_count','ROUND(AVG(r.average),3) avg_result'])
   ->from($db->quoteName('#__jt_results','r'))->innerJoin($db->quoteName('#__jt_bow_setups','s').' ON s.id=r.bow_setup_id')
   ->where('r.athlete_id IN ('.$ids.')')->where('s.arrow_shaft IS NOT NULL')->where("s.arrow_shaft<>''")->group('s.arrow_shaft,s.arrow_spine')->order('avg_result DESC');
  $db->setQuery($q);$stats->shaftPerformance=$db->loadObjectList();
  $q=$db->getQuery(true)->select(['s.brace_height_mm','COUNT(r.id) result_count','ROUND(AVG(r.average),3) avg_result'])
   ->from($db->quoteName('#__jt_results','r'))->innerJoin($db->quoteName('#__jt_bow_setups','s').' ON s.id=r.bow_setup_id')
   ->where('r.athlete_id IN ('.$ids.')')->where('s.brace_height_mm IS NOT NULL')->group('s.brace_height_mm')->order('s.brace_height_mm');
  $db->setQuery($q);$stats->bracePerformance=$db->loadObjectList();
  $q=$db->getQuery(true)->select(['r.result_date','r.distance_m','r.average','r.weather_condition','r.temperature_c','r.wind_speed_kmh','r.wind_direction','s.title setup_title','s.revision_no','v.extension_setting','v.height_setting','v.side_setting',"CONCAT(a.firstname,' ',a.lastname) athlete_name"])
   ->from($db->quoteName('#__jt_results','r'))->innerJoin($db->quoteName('#__jt_athletes','a').' ON a.id=r.athlete_id')->leftJoin($db->quoteName('#__jt_bow_setups','s').' ON s.id=r.bow_setup_id')->leftJoin($db->quoteName('#__jt_sight_settings','v').' ON v.bow_setup_id=s.id AND v.distance_m=r.distance_m')
   ->where('r.athlete_id IN ('.$ids.')')->where("(r.weather_condition IS NOT NULL OR r.wind_speed_kmh IS NOT NULL OR r.temperature_c IS NOT NULL)")->order('r.result_date DESC');
  $db->setQuery($q,0,100);$stats->weatherSight=$db->loadObjectList();
  $q=$db->getQuery(true)->select(["DATE_FORMAT(m.month_date,'%Y-%m') month_key",'m.arrows','ROUND(AVG(r.average),3) avg_result'])
   ->from("(SELECT athlete_id,STR_TO_DATE(DATE_FORMAT(training_date,'%Y-%m-01'),'%Y-%m-%d') month_date,SUM(arrow_count) arrows FROM #__jt_training_diary WHERE athlete_id IN (".$ids.") GROUP BY athlete_id,DATE_FORMAT(training_date,'%Y-%m')) m")
   ->leftJoin($db->quoteName('#__jt_results','r')." ON r.athlete_id=m.athlete_id AND DATE_FORMAT(r.result_date,'%Y-%m')=DATE_FORMAT(m.month_date,'%Y-%m')")
   ->group('m.month_date,m.arrows')->order('m.month_date');
  $db->setQuery($q);$stats->monthly=$db->loadObjectList();$stats->correlation=$this->pearson($stats->monthly);return$stats;
 }
 private function resultAverage(int$athleteId,int$days,int$offset):float{
  $db=$this->getDatabase();$q=$db->getQuery(true)->select('COALESCE(AVG(average),0)')->from($db->quoteName('#__jt_results'))->where('athlete_id='.$athleteId)->where('published=1')->where('result_date < DATE_SUB(CURDATE(),INTERVAL '.$offset.' DAY)')->where('result_date >= DATE_SUB(CURDATE(),INTERVAL '.($days+$offset).' DAY)');$db->setQuery($q);return(float)$db->loadResult();
 }
 private function pearson(array$rows):?float{
  $pairs=array_values(array_filter($rows,fn($r)=>$r->avg_result!==null));$n=count($pairs);if($n<3)return null;$sx=$sy=$sxx=$syy=$sxy=0.0;foreach($pairs as$r){$x=(float)$r->arrows;$y=(float)$r->avg_result;$sx+=$x;$sy+=$y;$sxx+=$x*$x;$syy+=$y*$y;$sxy+=$x*$y;}$den=sqrt(($n*$sxx-$sx*$sx)*($n*$syy-$sy*$sy));return$den>0?round(($n*$sxy-$sx*$sy)/$den,3):null;
 }


 public function getAchievementCockpit():object{
  $this->requireTrainer();$ids=$this->ids();(new \Jugendtraining\Component\Jugendtraining\Site\Service\AchievementService($this->getDatabase()))->evaluateAthletes($ids);$db=$this->getDatabase();$o=(object)['athletes'=>$this->getAthletes(),'achievements'=>[],'awards'=>[]];
  $q=$db->getQuery(true)->select('*')->from($db->quoteName('#__jt_achievements'))->where('published=1')->order('category,ordering,title');$db->setQuery($q);$o->achievements=$db->loadObjectList();
  $q=$db->getQuery(true)->select(['aa.*','b.title achievement_title','b.badge_image','b.award_mode',"CONCAT(a.firstname,' ',a.lastname) athlete_name"])
   ->from($db->quoteName('#__jt_athlete_achievements','aa'))->innerJoin($db->quoteName('#__jt_achievements','b').' ON b.id=aa.achievement_id')->innerJoin($db->quoteName('#__jt_athletes','a').' ON a.id=aa.athlete_id')
   ->where('aa.athlete_id IN ('.$this->idList().')')->where('aa.revoked_at IS NULL')->order('aa.awarded_at DESC,aa.id DESC');$db->setQuery($q,0,200);$o->awards=$db->loadObjectList();return$o;
 }

}
