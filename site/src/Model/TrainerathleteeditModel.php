<?php
namespace Jugendtraining\Component\Jugendtraining\Site\Model;
\defined('_JEXEC') or die;
use Joomla\CMS\Factory;use Joomla\CMS\MVC\Model\BaseDatabaseModel;use Jugendtraining\Component\Jugendtraining\Site\Service\AccessService;
final class TrainerathleteeditModel extends BaseDatabaseModel
{
 private function id():int{$id=Factory::getApplication()->input->getInt('id');if(!(new AccessService())->canManageAthlete($id))throw new \RuntimeException('JERROR_ALERTNOAUTHOR',403);return$id;}
 public function getAthlete():?object{$db=$this->getDatabase();$q=$db->getQuery(true)->select('*')->from('#__jt_athletes')->where('id='.$this->id());$db->setQuery($q);return$db->loadObject()?:null;}
 public function getClasses():array{$db=$this->getDatabase();$q=$db->getQuery(true)->select(['id','name'])->from('#__jt_classes')->where('published=1')->order('ordering,name');$db->setQuery($q);return$db->loadObjectList();}
 public function save(array $data):int{
  $id=(int)($data['id']??0);$access=new AccessService();
  if(!$access->canManageAthlete($id))throw new \RuntimeException('JERROR_ALERTNOAUTHOR',403);
  $firstname=trim((string)($data['firstname']??''));$lastname=trim((string)($data['lastname']??''));
  $email=trim((string)($data['email']??''));$phone=trim((string)($data['phone']??''));
  if($firstname===''||$lastname==='')throw new \RuntimeException('Vor- und Nachname sind erforderlich.');
  if(mb_strlen($firstname)>100||mb_strlen($lastname)>100)throw new \RuntimeException('Name ist zu lang.');
  if($email!==''&&(!filter_var($email,FILTER_VALIDATE_EMAIL)||mb_strlen($email)>190))throw new \RuntimeException('Ungültige E-Mail-Adresse.');
  if(mb_strlen($phone)>50)throw new \RuntimeException('Telefonnummer ist zu lang.');
  $bowType=trim((string)($data['bow_type']??''));$member=trim((string)($data['membership_number']??''));$notes=trim((string)($data['notes']??''));
  if(mb_strlen($bowType)>50||mb_strlen($member)>100||mb_strlen($notes)>5000)throw new \RuntimeException('Eingabewert ist zu lang.');
  $classId=(int)($data['class_id']??0);$db=$this->getDatabase();
  if($classId>0){$q=$db->getQuery(true)->select('COUNT(*)')->from('#__jt_classes')->where('id='.$classId)->where('published=1');$db->setQuery($q);if((int)$db->loadResult()!==1)throw new \RuntimeException('Ungültige Klasse.');}
  $o=(object)['id'=>$id,'firstname'=>$firstname,'lastname'=>$lastname,'email'=>$email?:null,'phone'=>$phone?:null,'class_id'=>$classId?:null,'bow_type'=>$bowType?:null,'membership_number'=>$member?:null,'notes'=>$notes?:null,'modified'=>Factory::getDate()->toSql(),'modified_by'=>(int)Factory::getApplication()->getIdentity()->id];
  $db->updateObject('#__jt_athletes',$o,'id');return$id;
 }
}
