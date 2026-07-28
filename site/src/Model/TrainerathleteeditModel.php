<?php
namespace Jugendtraining\Component\Jugendtraining\Site\Model;
\defined('_JEXEC') or die;
use Joomla\CMS\Factory;use Joomla\CMS\MVC\Model\BaseDatabaseModel;use Jugendtraining\Component\Jugendtraining\Site\Service\AccessService;
final class TrainerathleteeditModel extends BaseDatabaseModel
{
 private function id():int{$id=Factory::getApplication()->input->getInt('id');if(!(new AccessService())->canManageAthlete($id))throw new \RuntimeException('JERROR_ALERTNOAUTHOR',403);return$id;}
 public function getAthlete():?object{$db=$this->getDatabase();$q=$db->getQuery(true)->select('*')->from('#__jt_athletes')->where('id='.$this->id());$db->setQuery($q);return$db->loadObject()?:null;}
 public function getClasses():array{$db=$this->getDatabase();$q=$db->getQuery(true)->select(['id','name'])->from('#__jt_classes')->where('published=1')->order('ordering,name');$db->setQuery($q);return$db->loadObjectList();}
 public function save(array $data):int{$id=(int)($data['id']??0);if(!(new AccessService())->canManageAthlete($id))throw new \RuntimeException('JERROR_ALERTNOAUTHOR',403);$db=$this->getDatabase();$o=(object)['id'=>$id,'firstname'=>trim((string)$data['firstname']),'lastname'=>trim((string)$data['lastname']),'email'=>trim((string)($data['email']??''))?:null,'phone'=>trim((string)($data['phone']??''))?:null,'class_id'=>(int)($data['class_id']??0)?:null,'bow_type'=>trim((string)($data['bow_type']??''))?:null,'membership_number'=>trim((string)($data['membership_number']??''))?:null,'notes'=>trim((string)($data['notes']??''))?:null,'modified'=>Factory::getDate()->toSql(),'modified_by'=>(int)Factory::getApplication()->getIdentity()->id];if($o->firstname===''||$o->lastname==='')throw new \RuntimeException('Vor- und Nachname sind erforderlich.');$db->updateObject('#__jt_athletes',$o,'id');return$id;}
}
