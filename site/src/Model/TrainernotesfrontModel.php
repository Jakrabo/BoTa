<?php
namespace Jugendtraining\Component\Jugendtraining\Site\Model;
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;

final class TrainernotesfrontModel extends TrainerModel
{
 public function getFilteredNotes(): array
 {
  $this->requireTrainer();$app=Factory::getApplication();$db=$this->getDatabase();
  $athleteId=$app->input->getInt('athlete_id');$groupId=$app->input->getInt('group_id');
  $status=$app->input->getCmd('status','current');
  $q=$db->getQuery(true)->select(['n.*',"CONCAT(a.firstname,' ',a.lastname) athlete_name",'GROUP_CONCAT(DISTINCT g.title ORDER BY g.title SEPARATOR ", ") group_names'])
   ->from('#__jt_trainer_notes n')->innerJoin('#__jt_athletes a ON a.id=n.athlete_id')
   ->leftJoin('#__jt_training_group_athletes ga ON ga.athlete_id=a.id')
   ->leftJoin('#__jt_training_groups g ON g.id=ga.group_id')
   ->where('n.athlete_id IN ('.$this->idListPublic().')');
  if($athleteId>0)$q->where('n.athlete_id='.$athleteId);
  if($groupId>0)$q->where('ga.group_id='.$groupId);
  if(in_array($status,['current','done'],true))$q->where('n.status='.$db->quote($status));
  $q->group('n.id')->order('n.note_date DESC,n.id DESC');
  $db->setQuery($q);return$db->loadObjectList();
 }

 public function getFilterGroups(): array{return$this->getGroups();}
 public function getFilterAthletes(): array{return$this->getAthletes();}

public function getEditNote(): ?object
{
 $id=Factory::getApplication()->input->getInt('edit_id');
 if($id<=0)return null;
 $db=$this->getDatabase();$q=$db->getQuery(true)->select('*')->from('#__jt_trainer_notes')->where('id='.$id);
 $db->setQuery($q);$row=$db->loadObject();
 if(!$row||!$this->canManageAthletePublic((int)$row->athlete_id))throw new \RuntimeException('JERROR_ALERTNOAUTHOR',403);
 return$row;
}
public function saveNote(array $data): void
{
 $this->requireTrainer();$athleteId=(int)($data['athlete_id']??0);$id=(int)($data['id']??0);
 if(!$this->canManageAthletePublic($athleteId))throw new \RuntimeException('JERROR_ALERTNOAUTHOR',403);
 $note=trim((string)($data['note']??''));if($note==='')throw new \RuntimeException('Bitte eine Notiz eingeben.');
 if(mb_strlen($note)>10000)throw new \RuntimeException('Die Notiz ist zu lang.');
 $noteDate=(string)($data['note_date']??Factory::getDate()->format('Y-m-d'));$date=\DateTimeImmutable::createFromFormat('!Y-m-d',$noteDate);
 if(!$date||$date->format('Y-m-d')!==$noteDate)throw new \RuntimeException('Ungültiges Datum.');
 $category=trim((string)($data['category']??'general'))?:'general';if(mb_strlen($category)>30)throw new \RuntimeException('Kategorie ist zu lang.');
 $db=$this->getDatabase();$obj=(object)[
  'athlete_id'=>$athleteId,'note_date'=>$noteDate,
  'category'=>$category,'note'=>$note,
  'private_note'=>!empty($data['private_note'])?1:0,'status'=>($data['status']??'current')==='done'?'done':'current',
  'modified'=>Factory::getDate()->toSql(),'modified_by'=>(int)Factory::getApplication()->getIdentity()->id
 ];
 if($id>0){
  $q=$db->getQuery(true)->select('athlete_id')->from('#__jt_trainer_notes')->where('id='.$id);$db->setQuery($q);
  if(!$this->canManageAthletePublic((int)$db->loadResult()))throw new \RuntimeException('JERROR_ALERTNOAUTHOR',403);
  $obj->id=$id;$db->updateObject('#__jt_trainer_notes',$obj,'id');
 }else{
  $obj->created=Factory::getDate()->toSql();$obj->created_by=(int)Factory::getApplication()->getIdentity()->id;
  $db->insertObject('#__jt_trainer_notes',$obj);
 }
}
 public function setStatus(int $id,string $status):void
 {
  $this->requireTrainer();$status=$status==='done'?'done':'current';$db=$this->getDatabase();
  $q=$db->getQuery(true)->select('n.athlete_id')->from('#__jt_trainer_notes n')->where('n.id='.$id);$db->setQuery($q);
  $aid=(int)$db->loadResult();if(!$this->canManageAthletePublic($aid))throw new \RuntimeException('JERROR_ALERTNOAUTHOR',403);
  $q=$db->getQuery(true)->update('#__jt_trainer_notes')->set('status='.$db->quote($status))
   ->set('modified='.$db->quote(Factory::getDate()->toSql()))->set('modified_by='.(int)Factory::getApplication()->getIdentity()->id)
   ->where('id='.$id);$db->setQuery($q)->execute();
 }

 private function idListPublic():string
 {
  $ids=(new \Jugendtraining\Component\Jugendtraining\Site\Service\AccessService())->getTrainerAthleteIds();
  return$ids?implode(',',array_map('intval',$ids)):'0';
 }
 private function canManageAthletePublic(int $id):bool
 {
  return(new \Jugendtraining\Component\Jugendtraining\Site\Service\AccessService())->canManageAthlete($id);
 }
}
