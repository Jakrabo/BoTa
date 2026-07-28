<?php
namespace Jugendtraining\Component\Jugendtraining\Site\Model;
\defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Jugendtraining\Component\Jugendtraining\Site\Service\AccessService;

final class TrainerathletedetailModel extends TrainerModel
{
 private function athleteId():int{
  $id=Factory::getApplication()->input->getInt('id');
  if(!(new AccessService())->canManageAthlete($id))throw new \RuntimeException('JERROR_ALERTNOAUTHOR',403);
  return$id;
 }
 public function getAthleteDetail():?object{
  $id=$this->athleteId();$db=$this->getDatabase();$q=$db->getQuery(true)->select(['a.*','c.name club_name','cl.name class_name','GROUP_CONCAT(DISTINCT g.title ORDER BY g.title SEPARATOR ", ") group_names'])
   ->from('#__jt_athletes a')->leftJoin('#__jt_clubs c ON c.id=a.club_id')->leftJoin('#__jt_classes cl ON cl.id=a.class_id')
   ->leftJoin('#__jt_training_group_athletes ga ON ga.athlete_id=a.id')->leftJoin('#__jt_training_groups g ON g.id=ga.group_id')
   ->where('a.id='.$id)->group('a.id');$db->setQuery($q);return$db->loadObject()?:null;
 }
 public function getRecentAttendance():array{
  $id=$this->athleteId();$db=$this->getDatabase();$q=$db->getQuery(true)->select(['s.training_date','s.title','s.location','att.status','att.comment'])
   ->from('#__jt_attendance att')->innerJoin('#__jt_training_sessions s ON s.id=att.training_session_id')
   ->where('att.athlete_id='.$id)->order('s.training_date DESC');$db->setQuery($q,0,10);return$db->loadObjectList();
 }
 public function getAthleteNotes():array{
  $id=$this->athleteId();$db=$this->getDatabase();$q=$db->getQuery(true)->select('*')->from('#__jt_trainer_notes')->where('athlete_id='.$id)->order('note_date DESC,id DESC');$db->setQuery($q,0,20);return$db->loadObjectList();
 }
 public function getTrainingTasks():array{
  $id=$this->athleteId();$db=$this->getDatabase();$q=$db->getQuery(true)->select(['ap.id assignment_id','p.title program_title','ap.due_date','e.title exercise_title','COALESCE(pp.completed,0) completed'])
   ->from('#__jt_athlete_programs ap')->innerJoin('#__jt_training_programs p ON p.id=ap.program_id')
   ->innerJoin('#__jt_program_exercises pe ON pe.program_id=p.id')->innerJoin('#__jt_exercises e ON e.id=pe.exercise_id')
   ->leftJoin('#__jt_program_progress pp ON pp.athlete_program_id=ap.id AND pp.exercise_id=e.id')
   ->where('ap.athlete_id='.$id)->where('ap.active=1')->order('p.title,pe.ordering,e.ordering');$db->setQuery($q);return$db->loadObjectList();
 }

 public function getOpenPenalties():array{
  $id=$this->athleteId();$db=$this->getDatabase();
  $q=$db->getQuery(true)->select(['r.*','d.title','d.penalty_type'])
   ->from('#__jt_penalty_register r')
   ->innerJoin('#__jt_penalty_definitions d ON d.id=r.penalty_definition_id')
   ->where('r.athlete_id='.$id)->where("r.status='open'")
   ->order('r.assigned_at DESC,r.id DESC');
  $db->setQuery($q);return$db->loadObjectList();
 }
 public function getResultDevelopment():array{
  $id=$this->athleteId();[$start,$end]=$this->period();$db=$this->getDatabase();$q=$db->getQuery(true)->select(['result_date','score','average','event_name'])
   ->from('#__jt_results')->where('athlete_id='.$id)->where('published=1')->where('result_date>='.$db->quote($start))->where('result_date<='.$db->quote($end))->order('result_date,id');$db->setQuery($q);return$db->loadObjectList();
 }
 public function getArrowSeries():object{
  $id=$this->athleteId();[$start,$end]=$this->period();$db=$this->getDatabase();$periodKey=Factory::getApplication()->input->getCmd('period','last12');$r=(object)['monthly'=>[],'weekly'=>[],'date_start'=>$start,'date_end'=>$end,'period_key'=>$periodKey];
  foreach(['monthly'=>["DATE_FORMAT(training_date,'%Y-%m')","DATE_FORMAT(training_date,'%m.%Y')"],'weekly'=>["DATE_FORMAT(training_date,'%x-%v')","CONCAT('KW ',DATE_FORMAT(training_date,'%v'),' / ',DATE_FORMAT(training_date,'%x'))"]] as$key=>$fmt){
   $q=$db->getQuery(true)->select([$fmt[0].' period_key',$fmt[1].' period_label','SUM(arrow_count) arrows'])->from('#__jt_training_diary')->where('athlete_id='.$id)->where('training_date>='.$db->quote($start))->where('training_date<='.$db->quote($end))->group($fmt[0])->order($fmt[0]);$db->setQuery($q);$r->$key=$db->loadObjectList();
  }return$r;
 }
 public function getAvailableSportYears():array{
  $db=$this->getDatabase();$q=$db->getQuery(true)->select('*')->from('#__jt_sportyears')->where('published=1')->order('date_start DESC');$db->setQuery($q);return$db->loadObjectList();
 }
private function period():array{
  $period=Factory::getApplication()->input->getCmd('period','last12');
  $today=new \DateTimeImmutable('today');
  if($period==='lastweek'){$end=$today;$start=$today->modify('-6 days');}
  elseif($period==='lastmonth'){$end=$today;$start=$today->modify('-29 days');}
  else{$end=$today;$start=$today->modify('first day of this month')->modify('-11 months');}
  if(str_starts_with($period,'sportyear_')){
   $sid=(int)substr($period,10);$db=$this->getDatabase();
   $q=$db->getQuery(true)->select(['date_start','date_end'])->from('#__jt_sportyears')->where('id='.$sid)->where('published=1');
   $db->setQuery($q);$sy=$db->loadObject();
   if($sy){$start=new \DateTimeImmutable($sy->date_start);$end=new \DateTimeImmutable($sy->date_end);}
  }
  return[$start->format('Y-m-d'),$end->format('Y-m-d')];
 }
}
