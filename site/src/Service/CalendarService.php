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

 public function categories():array
 {
  $q=$this->db->getQuery(true)->select('setting_value')->from('#__jt_settings')->where('setting_key='.$this->db->quote('calendar_categories'));
  $this->db->setQuery($q);$v=json_decode((string)$this->db->loadResult(),true);
  return is_array($v)&&$v?array_values(array_unique(array_filter(array_map('trim',$v)))):['Liga','Wettkampf','Training','Lehrgang','Vereinstermin','Sonstiges'];
 }

 public function locations(bool $publishedOnly=true):array
 {
  $q=$this->db->getQuery(true)->select('DISTINCT location')->from('#__jt_calendar_events')->where("location IS NOT NULL")->where("location<>''");
  if($publishedOnly)$q->where('published=1');
  $q->order('location');$this->db->setQuery($q);return array_values(array_filter(array_map('strval',$this->db->loadColumn())));
 }

 public function events(array $filters=[],bool $publishedOnly=true):array
 {
  $q=$this->db->getQuery(true)->select(['e.*','COUNT(a.id) attachment_count'])
   ->from('#__jt_calendar_events e')->leftJoin('#__jt_calendar_attachments a ON a.event_id=e.id');
  if($publishedOnly)$q->where('e.published=1');

  $mode=in_array(($filters['mode']??'future'),['future','past','all'],true)?$filters['mode']:'future';
  $today=Factory::getDate('now')->format('Y-m-d');
  if($mode==='future')$q->where('e.event_date>='.$this->db->quote($today));
  elseif($mode==='past')$q->where('e.event_date<'.$this->db->quote($today));

  $from=trim((string)($filters['date_from']??''));$to=trim((string)($filters['date_to']??''));
  if($this->validDate($from))$q->where('e.event_date>='.$this->db->quote($from));
  if($this->validDate($to))$q->where('e.event_date<='.$this->db->quote($to));

  $category=trim((string)($filters['category']??''));$location=trim((string)($filters['location']??''));
  if($category!==''&&in_array($category,$this->categories(),true))$q->where('e.category='.$this->db->quote($category));
  if($location!=='')$q->where('e.location='.$this->db->quote($location));

  $q->group('e.id');
  $q->order($mode==='past'?'e.event_date DESC,e.event_time DESC,e.id DESC':'e.event_date ASC,e.event_time ASC,e.id ASC');
  $this->db->setQuery($q);return$this->db->loadObjectList();
 }

 public function event(int $id,bool $publishedOnly=false):?object
 {
  if($id<=0)return null;
  $q=$this->db->getQuery(true)->select('e.*')->from('#__jt_calendar_events e')->where('e.id='.$id);
  if($publishedOnly)$q->where('e.published=1');
  $this->db->setQuery($q);$event=$this->db->loadObject();
  if($event)$event->attachments=$this->attachments($id);
  return$event?:null;
 }

 public function save(array $data,mixed $uploads):int
 {
  $this->guardTrainer();
  $id=(int)($data['id']??0);$title=trim((string)($data['title']??''));$date=trim((string)($data['event_date']??''));
  $time=trim((string)($data['event_time']??''));$location=trim((string)($data['location']??''));$category=trim((string)($data['category']??''));
  $description=trim((string)($data['description']??''));
  if($title===''||mb_strlen($title)>190)throw new \RuntimeException('Bitte einen gültigen Titel eingeben.');
  if(!$this->validDate($date))throw new \RuntimeException('Bitte ein gültiges Datum eingeben.');
  if($time!==''&&!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d(?::[0-5]\d)?$/',$time))throw new \RuntimeException('Bitte eine gültige Uhrzeit eingeben.');
  if($location!==''&&mb_strlen($location)>190)throw new \RuntimeException('Ort ist zu lang.');
  if(!in_array($category,$this->categories(),true))throw new \RuntimeException('Ungültige Kategorie.');
  if(mb_strlen($description)>10000)throw new \RuntimeException('Beschreibung ist zu lang.');

  $now=Factory::getDate()->toSql();$uid=(int)Factory::getApplication()->getIdentity()->id;
  $obj=(object)['title'=>$title,'event_date'=>$date,'event_time'=>$time?:null,'location'=>$location?:null,'category'=>$category,'description'=>$description?:null,'published'=>!empty($data['published'])?1:0,'modified'=>$now,'modified_by'=>$uid];
  if($id>0){
   $this->assertExisting($id);$obj->id=$id;$this->db->updateObject('#__jt_calendar_events',$obj,'id');
  }else{
   $obj->created=$now;$obj->created_by=$uid;$this->db->insertObject('#__jt_calendar_events',$obj);$id=(int)$this->db->insertid();
  }
  $this->storeUploads($id,$uploads);
  return$id;
 }

 public function delete(int $id):void
 {
  $this->guardTrainer();$this->assertExisting($id);
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
  $q=$this->db->getQuery(true)->select(['a.*','e.published event_published'])->from('#__jt_calendar_attachments a')->innerJoin('#__jt_calendar_events e ON e.id=a.event_id')->where('a.id='.$id);
  $this->db->setQuery($q);return$this->db->loadObject()?:null;
 }

 public function canReadCalendar():bool
 {
  return $this->access->isTrainer()||$this->access->isAthlete()||$this->access->isSuperUser();
 }

 public function isTrainer():bool{return$this->access->isTrainer();}

 private function guardTrainer():void
 {
  if(!$this->access->isTrainer())throw new \RuntimeException('JERROR_ALERTNOAUTHOR',403);
 }

 private function assertExisting(int$id):void
 {
  $q=$this->db->getQuery(true)->select('COUNT(*)')->from('#__jt_calendar_events')->where('id='.$id);$this->db->setQuery($q);
  if((int)$this->db->loadResult()!==1)throw new \RuntimeException('JERROR_LAYOUT_REQUESTED_RESOURCE_WAS_NOT_FOUND',404);
 }

 private function validDate(string$v):bool
 {
  if($v==='')return false;$d=\DateTimeImmutable::createFromFormat('!Y-m-d',$v);return$d&&$d->format('Y-m-d')===$v;
 }

 private function storeUploads(int$eventId,mixed$uploads):void
 {
  $files=$this->normaliseUploads($uploads);if(!$files)return;
  if(count($files)>self::MAX_ATTACHMENTS)throw new \RuntimeException('Maximal fünf PDF-Anhänge pro Upload.');
  $existing=count($this->attachments($eventId));if($existing+count($files)>10)throw new \RuntimeException('Maximal zehn Anhänge pro Termin.');

  foreach($files as$file){
   $error=(int)($file['error']??UPLOAD_ERR_NO_FILE);if($error===UPLOAD_ERR_NO_FILE)continue;
   if($error!==UPLOAD_ERR_OK)throw new \RuntimeException('PDF-Upload fehlgeschlagen.');
   $tmp=(string)($file['tmp_name']??'');$size=(int)($file['size']??0);
   if($size<=0||$size>self::MAX_PDF_SIZE||!is_file($tmp))throw new \RuntimeException('PDF ist leer oder größer als 8 MB.');
   $head=(string)@file_get_contents($tmp,false,null,0,5);if($head!=='%PDF-')throw new \RuntimeException('Nur echte PDF-Dateien sind erlaubt.');
   $finfo=new \finfo(FILEINFO_MIME_TYPE);$mime=(string)$finfo->file($tmp);if($mime!=='application/pdf')throw new \RuntimeException('Nur PDF-Dateien sind erlaubt.');
   $name=basename((string)($file['name']??'Anhang.pdf'));$name=preg_replace('/[^\pL\pN._ -]+/u','_',$name)?:'Anhang.pdf';
   if(!str_ends_with(strtolower($name),'.pdf'))$name.='.pdf';$name=mb_substr($name,0,190);
   $raw=file_get_contents($tmp);if($raw===false)throw new \RuntimeException('PDF konnte nicht gelesen werden.');
   $obj=(object)['event_id'=>$eventId,'file_name'=>$name,'mime_type'=>'application/pdf','file_size'=>$size,'file_data'=>base64_encode($raw),'created'=>Factory::getDate()->toSql(),'created_by'=>(int)Factory::getApplication()->getIdentity()->id];
   $this->db->insertObject('#__jt_calendar_attachments',$obj);
  }
 }

 private function normaliseUploads(mixed$uploads):array
 {
  if(!is_array($uploads))return[];
  if(isset($uploads['name'])&&is_array($uploads['name'])){
   $out=[];foreach($uploads['name'] as$i=>$name)$out[]=['name'=>$name,'tmp_name'=>$uploads['tmp_name'][$i]??'','error'=>$uploads['error'][$i]??UPLOAD_ERR_NO_FILE,'size'=>$uploads['size'][$i]??0,'type'=>$uploads['type'][$i]??''];
   return$out;
  }
  if(isset($uploads['name']))return[$uploads];
  return array_values(array_filter($uploads,'is_array'));
 }
}
