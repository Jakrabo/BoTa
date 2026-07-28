<?php
namespace Jugendtraining\Component\Jugendtraining\Site\Service;
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;

final class CalendarService
{
 private const MAX_PDF_SIZE=8*1024*1024;
 private const MAX_ATTACHMENTS=5;
 private DatabaseInterface $db;
 private AccessService $access;

 public function __construct()
 {
  $this->db=Factory::getContainer()->get(DatabaseInterface::class);
  $this->access=new AccessService();
 }

 public function categoryConfigs(bool $activeOnly=true):array
 {
  $q=$this->db->getQuery(true)->select('setting_value')->from('#__jt_settings')->where('setting_key='.$this->db->quote('calendar_categories'));
  $this->db->setQuery($q);$raw=json_decode((string)$this->db->loadResult(),true);
  $defaults=[
   ['name'=>'Liga','color'=>'#6f42c1','active'=>1],['name'=>'Wettkampf','color'=>'#dc3545','active'=>1],
   ['name'=>'Training','color'=>'#198754','active'=>1],['name'=>'Lehrgang','color'=>'#0d6efd','active'=>1],
   ['name'=>'Vereinstermin','color'=>'#fd7e14','active'=>1],['name'=>'Sonstiges','color'=>'#6c757d','active'=>1]
  ];
  if(!is_array($raw)||!$raw)$raw=$defaults;
  $out=[];$palette=['#6f42c1','#dc3545','#198754','#0d6efd','#fd7e14','#6c757d'];
  foreach(array_values($raw) as$i=>$row){
   if(is_string($row))$row=['name'=>trim($row),'color'=>$palette[$i%count($palette)],'active'=>1];
   if(!is_array($row))continue;
   $name=trim((string)($row['name']??''));if($name==='')continue;
   $color=$this->validColor((string)($row['color']??''))?(string)$row['color']:'#6c757d';
   $cfg=['name'=>$name,'color'=>$color,'active'=>!empty($row['active'])?1:0];
   if(!$activeOnly||$cfg['active'])$out[]=$cfg;
  }
  return$out?:$defaults;
 }

 public function categories(bool $activeOnly=true):array
 {
  return array_column($this->categoryConfigs($activeOnly),'name');
 }

 public function categoryMap(bool $activeOnly=false):array
 {
  $map=[];foreach($this->categoryConfigs($activeOnly) as$c)$map[$c['name']]=$c;return$map;
 }

 public function locations(bool $publishedOnly=true):array
 {
  $q=$this->db->getQuery(true)->select('DISTINCT location')->from('#__jt_calendar_events')->where("location IS NOT NULL")->where("location<>''");
  if($publishedOnly)$q->where('published=1');
  $q->order('location');$this->db->setQuery($q);return array_values(array_filter(array_map('strval',$this->db->loadColumn())));
 }

 public function trainerGroups():array
 {
  if(!$this->isTrainer())return[];
  $uid=(int)Factory::getApplication()->getIdentity()->id;
  $q=$this->db->getQuery(true)->select(['g.id','g.title'])->from('#__jt_training_groups g')
   ->innerJoin('#__jt_training_group_trainers gt ON gt.group_id=g.id')
   ->where('gt.user_id='.$uid)->where('g.published=1')->group('g.id')->order('g.title');
  $this->db->setQuery($q);return$this->db->loadObjectList();
 }

 public function allGroups():array
 {
  $q=$this->db->getQuery(true)->select(['id','title'])->from('#__jt_training_groups')->where('published=1')->order('title');
  $this->db->setQuery($q);return$this->db->loadObjectList();
 }

 public function events(array $filters=[],bool $publishedOnly=true,bool $backend=false):array
 {
  $q=$this->db->getQuery(true)->select(['e.*','g.title training_group_title','COUNT(a.id) attachment_count'])
   ->from('#__jt_calendar_events e')->leftJoin('#__jt_training_groups g ON g.id=e.training_group_id')
   ->leftJoin('#__jt_calendar_attachments a ON a.event_id=e.id');
  if($publishedOnly)$q->where('e.published=1');

  if(!$backend){
   if($this->isTrainer()){
    $groupIds=array_map(fn($g)=>(int)$g->id,$this->trainerGroups());
    $visible=["e.audience='all'","(e.audience='trainers' AND e.training_group_id IS NULL)"];
    if($groupIds)$visible[]="(e.audience='trainers' AND e.training_group_id IN (".implode(',',$groupIds)."))";
    $q->where('('.implode(' OR ',$visible).')');
   }else{
    $q->where("e.audience='all'");
   }
  }

  $mode=in_array(($filters['mode']??'future'),['future','past','all'],true)?$filters['mode']:'future';
  $today=Factory::getDate('now')->format('Y-m-d');
  if($mode==='future')$q->where('COALESCE(e.event_date_end,e.event_date)>='.$this->db->quote($today));
  elseif($mode==='past')$q->where('COALESCE(e.event_date_end,e.event_date)<'.$this->db->quote($today));

  $from=trim((string)($filters['date_from']??''));$to=trim((string)($filters['date_to']??''));
  // Overlap logic: event intersects selected range.
  if($this->validDate($from))$q->where('COALESCE(e.event_date_end,e.event_date)>='.$this->db->quote($from));
  if($this->validDate($to))$q->where('e.event_date<='.$this->db->quote($to));

  $category=trim((string)($filters['category']??''));$location=trim((string)($filters['location']??''));
  if($category!==''&&in_array($category,$this->categories(false),true))$q->where('e.category='.$this->db->quote($category));
  if($location!=='')$q->where('e.location='.$this->db->quote($location));

  $q->group('e.id');
  $q->order($mode==='past'?'e.event_date DESC,e.event_time DESC,e.id DESC':'e.event_date ASC,e.event_time ASC,e.id ASC');
  $this->db->setQuery($q);return$this->db->loadObjectList();
 }

 public function event(int $id,bool $publishedOnly=false,bool $backend=false):?object
 {
  if($id<=0)return null;
  $q=$this->db->getQuery(true)->select(['e.*','g.title training_group_title'])->from('#__jt_calendar_events e')
   ->leftJoin('#__jt_training_groups g ON g.id=e.training_group_id')->where('e.id='.$id);
  if($publishedOnly)$q->where('e.published=1');
  $this->db->setQuery($q);$event=$this->db->loadObject();
  if(!$event)return null;
  if(!$backend&&!$this->canReadEvent($event))throw new \RuntimeException('JERROR_ALERTNOAUTHOR',403);
  $event->attachments=$this->attachments($id);
  return$event;
 }

 public function save(array $data,mixed $uploads):int
 {
  $this->guardTrainer();
  $id=(int)($data['id']??0);$title=trim((string)($data['title']??''));
  $date=trim((string)($data['event_date']??''));$dateEnd=trim((string)($data['event_date_end']??''))?:$date;
  $time=trim((string)($data['event_time']??''));$timeEnd=trim((string)($data['event_time_end']??''));
  $location=trim((string)($data['location']??''));$category=trim((string)($data['category']??''));
  $description=trim((string)($data['description']??''));$audience=($data['audience']??'all')==='trainers'?'trainers':'all';
  $groupId=$audience==='trainers'?(int)($data['training_group_id']??0):0;

  if($title===''||mb_strlen($title)>190)throw new \RuntimeException('Bitte einen gültigen Titel eingeben.');
  if(!$this->validDate($date)||!$this->validDate($dateEnd)||$dateEnd<$date)throw new \RuntimeException('Bitte einen gültigen Datumsbereich eingeben.');
  foreach([$time,$timeEnd] as$v)if($v!==''&&!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d(?::[0-5]\d)?$/',$v))throw new \RuntimeException('Bitte eine gültige Uhrzeit eingeben.');
  if($dateEnd===$date&&$time!==''&&$timeEnd!==''&&substr($timeEnd,0,5)<substr($time,0,5))throw new \RuntimeException('Die Endzeit darf nicht vor der Startzeit liegen.');
  if($location!==''&&mb_strlen($location)>190)throw new \RuntimeException('Ort ist zu lang.');
  if(!in_array($category,$this->categories(true),true)&&!($id>0&&in_array($category,$this->categories(false),true)))throw new \RuntimeException('Ungültige Kategorie.');
  if(mb_strlen($description)>10000)throw new \RuntimeException('Beschreibung ist zu lang.');
  if($groupId>0){
   $allowed=array_map(fn($g)=>(int)$g->id,$this->trainerGroups());
   if(!in_array($groupId,$allowed,true))throw new \RuntimeException('JERROR_ALERTNOAUTHOR',403);
  }

  $now=Factory::getDate()->toSql();$uid=(int)Factory::getApplication()->getIdentity()->id;
  $obj=(object)['title'=>$title,'event_date'=>$date,'event_date_end'=>$dateEnd,'event_time'=>$time?:null,'event_time_end'=>$timeEnd?:null,'location'=>$location?:null,'category'=>$category,'description'=>$description?:null,'audience'=>$audience,'training_group_id'=>$groupId?:null,'published'=>!empty($data['published'])?1:0,'modified'=>$now,'modified_by'=>$uid];
  if($id>0){$existing=$this->event($id,false,false);if(!$existing)throw new \RuntimeException('JERROR_LAYOUT_REQUESTED_RESOURCE_WAS_NOT_FOUND',404);$obj->id=$id;$this->db->updateObject('#__jt_calendar_events',$obj,'id');}
  else{$obj->created=$now;$obj->created_by=$uid;$this->db->insertObject('#__jt_calendar_events',$obj);$id=(int)$this->db->insertid();}
  $this->storeUploads($id,$uploads);return$id;
 }

 public function delete(int $id):void
 {
  $this->guardTrainer();$event=$this->event($id,false,false);if(!$event)throw new \RuntimeException('JERROR_LAYOUT_REQUESTED_RESOURCE_WAS_NOT_FOUND',404);
  $q=$this->db->getQuery(true)->delete('#__jt_calendar_events')->where('id='.$id);$this->db->setQuery($q)->execute();
 }

 public function attachments(int $eventId):array
 {
  $q=$this->db->getQuery(true)->select(['id','event_id','file_name','mime_type','file_size','created'])->from('#__jt_calendar_attachments')->where('event_id='.$eventId)->order('id');
  $this->db->setQuery($q);return$this->db->loadObjectList();
 }

 public function attachment(int $id):?object
 {
  if($id<=0)return null;
  $q=$this->db->getQuery(true)->select(['a.*','e.published event_published','e.audience','e.training_group_id'])->from('#__jt_calendar_attachments a')->innerJoin('#__jt_calendar_events e ON e.id=a.event_id')->where('a.id='.$id);
  $this->db->setQuery($q);return$this->db->loadObject()?:null;
 }

 public function canReadCalendar():bool{return$this->access->isTrainer()||$this->access->isAthlete()||$this->access->isSuperUser();}
 public function isTrainer():bool{return$this->access->isTrainer();}

 public function canReadAttachment(object$row):bool
 {
  if(!(int)$row->event_published&&!$this->isTrainer())return false;
  return$this->canReadEvent($row);
 }

 private function canReadEvent(object$event):bool
 {
  if(($event->audience??'all')==='all')return$this->canReadCalendar();
  if(!$this->isTrainer())return false;
  $gid=(int)($event->training_group_id??0);if($gid<=0)return true;
  return in_array($gid,array_map(fn($g)=>(int)$g->id,$this->trainerGroups()),true);
 }

 private function guardTrainer():void{if(!$this->access->isTrainer())throw new \RuntimeException('JERROR_ALERTNOAUTHOR',403);}
 private function validDate(?string$v):bool{$v=trim((string)$v);if($v==='')return false;$d=\DateTimeImmutable::createFromFormat('!Y-m-d',$v);return$d&&$d->format('Y-m-d')===$v;}
 private function validColor(string$v):bool{return(bool)preg_match('/^#[0-9A-Fa-f]{6}$/',$v);}

 private function storeUploads(int$eventId,mixed$uploads):void
 {
  $files=$this->normaliseUploads($uploads);if(!$files)return;
  if(count($files)>self::MAX_ATTACHMENTS)throw new \RuntimeException('Maximal fünf PDF-Anhänge pro Upload.');
  $existing=count($this->attachments($eventId));if($existing+count($files)>10)throw new \RuntimeException('Maximal zehn Anhänge pro Termin.');
  foreach($files as$file){
   $error=(int)($file['error']??UPLOAD_ERR_NO_FILE);if($error===UPLOAD_ERR_NO_FILE)continue;if($error!==UPLOAD_ERR_OK)throw new \RuntimeException('PDF-Upload fehlgeschlagen.');
   $tmp=(string)($file['tmp_name']??'');$size=(int)($file['size']??0);if($size<=0||$size>self::MAX_PDF_SIZE||!is_file($tmp))throw new \RuntimeException('PDF ist leer oder größer als 8 MB.');
   if((string)@file_get_contents($tmp,false,null,0,5)!=='%PDF-')throw new \RuntimeException('Nur echte PDF-Dateien sind erlaubt.');
   $finfo=new \finfo(FILEINFO_MIME_TYPE);if((string)$finfo->file($tmp)!=='application/pdf')throw new \RuntimeException('Nur PDF-Dateien sind erlaubt.');
   $name=basename((string)($file['name']??'Anhang.pdf'));$name=preg_replace('/[^\pL\pN._ -]+/u','_',$name)?:'Anhang.pdf';if(!str_ends_with(strtolower($name),'.pdf'))$name.='.pdf';$name=mb_substr($name,0,190);
   $raw=file_get_contents($tmp);if($raw===false)throw new \RuntimeException('PDF konnte nicht gelesen werden.');
   $obj=(object)['event_id'=>$eventId,'file_name'=>$name,'mime_type'=>'application/pdf','file_size'=>$size,'file_data'=>base64_encode($raw),'created'=>Factory::getDate()->toSql(),'created_by'=>(int)Factory::getApplication()->getIdentity()->id];$this->db->insertObject('#__jt_calendar_attachments',$obj);
  }
 }
 private function normaliseUploads(mixed$u):array{if(!is_array($u))return[];if(isset($u['name'])&&is_array($u['name'])){$r=[];foreach($u['name']as$i=>$n)$r[]=['name'=>$n,'tmp_name'=>$u['tmp_name'][$i]??'','error'=>$u['error'][$i]??UPLOAD_ERR_NO_FILE,'size'=>$u['size'][$i]??0];return$r;}if(isset($u['name']))return[$u];return array_values(array_filter($u,'is_array'));}
}
