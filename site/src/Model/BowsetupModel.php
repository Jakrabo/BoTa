<?php
namespace Jugendtraining\Component\Jugendtraining\Site\Model;
\defined('_JEXEC') or die;
use Joomla\CMS\Factory;use Joomla\CMS\MVC\Model\AdminModel;
final class BowsetupModel extends AdminModel {
 public function getTable($name='Bowsetup',$prefix='Administrator',$options=[]){return parent::getTable($name,$prefix,$options);}
 public function getForm($data=[],$loadData=true){return $this->loadForm('com_jugendtraining.bowsetup','bowsetup',['control'=>'jform','load_data'=>$loadData]);}
 protected function loadFormData(){
  $item=$this->getItem();
  if(!empty($item->id)&&!$this->canEditSetup((int)$item->id))throw new \RuntimeException('JERROR_ALERTNOAUTHOR',403);
  return$item;
 }
 public function save($data):bool{
  $aid=$this->athleteId();if(!$aid){$this->setError('COM_JUGENDTRAINING_NO_LINKED_ATHLETE');return false;}
  $sourceId=(int)($data['id']??0);if($sourceId && !$this->owns($sourceId,$aid)){return false;}
  $db=$this->getDatabase();$db->transactionStart();
  try{
   $q=$db->getQuery(true)->select('COALESCE(MAX(revision_no),0)+1')->from($db->quoteName('#__jt_bow_setups'))->where('athlete_id='.$aid);$db->setQuery($q);$rev=(int)$db->loadResult();
   $deactivate=$db->getQuery(true)->update($db->quoteName('#__jt_bow_setups'))->set('is_active=0')->where('athlete_id='.$aid);$db->setQuery($deactivate)->execute();
   $data['id']=0;$data['athlete_id']=$aid;$data['revision_no']=$rev;$data['parent_revision_id']=$sourceId?:null;$data['is_active']=1;$data['valid_from']=Factory::getDate()->toSql();$data['created']=Factory::getDate()->toSql();$data['created_by']=(int)Factory::getApplication()->getIdentity()->id;
   if(!parent::save($data)){throw new \RuntimeException($this->getError());}
   $newId=(int)$this->getState($this->getName().'.id');
   $sights=Factory::getApplication()->getInput()->get('sights',[],'array');
   if(!$sights && $sourceId){$q=$db->getQuery(true)->select('*')->from($db->quoteName('#__jt_sight_settings'))->where('bow_setup_id='.$sourceId);$db->setQuery($q);$sights=array_map(fn($o)=>(array)$o,$db->loadObjectList());}
   foreach($sights as $row){$dist=(float)($row['distance_m']??0);if($dist<=0)continue;$q=$db->getQuery(true)->insert($db->quoteName('#__jt_sight_settings'))->columns(['bow_setup_id','distance_m','extension_setting','height_setting','side_setting','notes'])->values(implode(',',[$newId,$dist,$db->quote((string)($row['extension_setting']??'')),$db->quote((string)($row['height_setting']??'')),$db->quote((string)($row['side_setting']??'')),$db->quote((string)($row['notes']??''))]));$db->setQuery($q)->execute();}
   $db->transactionCommit();return true;
  }catch(\Throwable $e){$db->transactionRollback();$this->setError($e->getMessage());return false;}
 }
 public function getSightSettings():array{
  $id=(int)Factory::getApplication()->getInput()->getInt('id');if(!$id)return[];
  if(!$this->canEditSetup($id))throw new \RuntimeException('JERROR_ALERTNOAUTHOR',403);
  $db=$this->getDatabase();$q=$db->getQuery(true)->select('*')->from($db->quoteName('#__jt_sight_settings'))->where('bow_setup_id='.$id)->order('distance_m');$db->setQuery($q);return$db->loadAssocList();
 }
 public function canEditSetup(int$id):bool{$aid=$this->athleteId();return$aid>0&&$this->owns($id,$aid);}
 private function athleteId():int{$uid=(int)Factory::getApplication()->getIdentity()->id;$db=$this->getDatabase();$q=$db->getQuery(true)->select('id')->from($db->quoteName('#__jt_athletes'))->where('user_id='.$uid)->where('published=1');$db->setQuery($q,0,1);return(int)$db->loadResult();}
 private function owns(int$id,int$aid):bool{$db=$this->getDatabase();$q=$db->getQuery(true)->select('COUNT(*)')->from($db->quoteName('#__jt_bow_setups'))->where('id='.$id)->where('athlete_id='.$aid);$db->setQuery($q);return(int)$db->loadResult()===1;}
}
