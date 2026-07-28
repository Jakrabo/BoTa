<?php
namespace Jugendtraining\Component\Jugendtraining\Site\Model;
\defined('_JEXEC') or die;
use Joomla\CMS\Factory;use Joomla\CMS\MVC\Model\BaseDatabaseModel;use Jugendtraining\Component\Jugendtraining\Site\Service\AccessService;
final class TrainerathletetaskModel extends BaseDatabaseModel{
 private function athleteId():int{$id=Factory::getApplication()->input->getInt('athlete_id');if(!(new AccessService())->canManageAthlete($id))throw new \RuntimeException('JERROR_ALERTNOAUTHOR',403);return$id;}
 public function getAthlete():?object{$id=$this->athleteId();$db=$this->getDatabase();$q=$db->getQuery(true)->select(['id','firstname','lastname'])->from('#__jt_athletes')->where('id='.$id);$db->setQuery($q);return$db->loadObject();}
 public function getPrograms():array{$db=$this->getDatabase();$q=$db->getQuery(true)->select(['id','title'])->from('#__jt_training_programs')->where('published=1')->order('title');$db->setQuery($q);return$db->loadObjectList();}
 public function getAssignment():?object{$id=Factory::getApplication()->input->getInt('assignment_id');if($id<=0)return null;$db=$this->getDatabase();$q=$db->getQuery(true)->select('*')->from('#__jt_athlete_programs')->where('id='.$id)->where('athlete_id='.$this->athleteId());$db->setQuery($q);return$db->loadObject();}
 public function save(array$data):int{
 $athleteId=(int)$data['athlete_id'];
 if(!(new AccessService())->canManageAthlete($athleteId))throw new \RuntimeException('JERROR_ALERTNOAUTHOR',403);
 $db=$this->getDatabase();$id=(int)($data['id']??0);$programId=(int)($data['program_id']??0);
 if($programId<=0)throw new \RuntimeException('Bitte ein Trainingsprogramm auswählen.');
 $o=(object)['athlete_id'=>$athleteId,'program_id'=>$programId,'due_date'=>trim((string)($data['due_date']??''))?:null,'active'=>!empty($data['active'])?1:0];
 if($id>0){
  $q=$db->getQuery(true)->select('athlete_id')->from('#__jt_athlete_programs')->where('id='.$id);$db->setQuery($q);
  $existingAthleteId=(int)$db->loadResult();
  if($existingAthleteId!==$athleteId || !(new AccessService())->canManageAthlete($existingAthleteId))throw new \RuntimeException('JERROR_ALERTNOAUTHOR',403);
  $q=$db->getQuery(true)->select('id')->from('#__jt_athlete_programs')->where('athlete_id='.$athleteId)->where('program_id='.$programId)->where('id<>'.$id);
  $db->setQuery($q);$duplicateId=(int)$db->loadResult();
  if($duplicateId>0){$o->id=$duplicateId;$db->updateObject('#__jt_athlete_programs',$o,'id');$q=$db->getQuery(true)->delete('#__jt_athlete_programs')->where('id='.$id);$db->setQuery($q)->execute();}
  else{$o->id=$id;$db->updateObject('#__jt_athlete_programs',$o,'id');}
 }else{
  $q=$db->getQuery(true)->select('id')->from('#__jt_athlete_programs')->where('athlete_id='.$athleteId)->where('program_id='.$programId);
  $db->setQuery($q);$existingId=(int)$db->loadResult();
  if($existingId>0){$o->id=$existingId;$db->updateObject('#__jt_athlete_programs',$o,'id');}
  else{$o->assigned_by=(int)Factory::getApplication()->getIdentity()->id;$o->assigned_at=Factory::getDate()->toSql();$db->insertObject('#__jt_athlete_programs',$o);}
 }
 return$athleteId;
}
}
