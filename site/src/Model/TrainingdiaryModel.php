<?php
namespace Jugendtraining\Component\Jugendtraining\Site\Model;
\defined('_JEXEC') or die;
use Joomla\CMS\Factory;use Joomla\CMS\MVC\Model\AdminModel;
final class TrainingdiaryModel extends AdminModel {
 public function getTable($name='Trainingdiary',$prefix='Administrator',$options=[]){return parent::getTable($name,$prefix,$options);}
 public function getForm($data=[],$loadData=true){
  $f=$this->loadForm('com_jugendtraining.trainingdiary','trainingdiary',['control'=>'jform','load_data'=>$loadData]);if(!$f)return$f;
  $setup=$f->getField('bow_setup_id');foreach($this->setups() as$s)$setup->addOption($s->title.' · Rev. '.$s->revision_no,['value'=>$s->id]);
  foreach($this->configured('diary_methods',['Techniktraining','Wettkampftraining','Kraft und Stabilität','Materialtraining','Freies Training']) as$v)$f->getField('training_method')->addOption($v,['value'=>$v]);
  foreach($this->configured('diary_focus_topics',['Ankerpunkt','Lösen','Stand','Zielbild','Rhythmus','Mentales Training']) as$v)$f->getField('focus_topic')->addOption($v,['value'=>$v]);
  return$f;
 }
 protected function loadFormData(){
  $item=$this->getItem();if(!empty($item->id)&&!$this->owns((int)$item->id))throw new \RuntimeException('JERROR_ALERTNOAUTHOR',403);
  if(empty($item->id)&&empty($item->bow_setup_id))$item->bow_setup_id=$this->activeSetup();
  return$item;
 }
 public function save($data):bool{$aid=$this->athleteId();if(!$aid)return false;$id=(int)($data['id']??0);if($id&&!$this->owns($id))return false;$data['athlete_id']=$aid;if(empty($data['bow_setup_id']))$data['bow_setup_id']=$this->activeSetup();return parent::save($data);}
 protected function prepareTable($t):void{$now=Factory::getDate()->toSql();$uid=(int)Factory::getApplication()->getIdentity()->id;if(empty($t->id)){$t->created=$now;$t->created_by=$uid;}else{$t->modified=$now;$t->modified_by=$uid;}}
 public function deleteOwn(int$id):bool{return$this->owns($id)?$this->delete($id):false;}
 private function athleteId():int{$uid=(int)Factory::getApplication()->getIdentity()->id;$db=$this->getDatabase();$q=$db->getQuery(true)->select('id')->from('#__jt_athletes')->where('user_id='.$uid)->where('published=1');$db->setQuery($q,0,1);return(int)$db->loadResult();}
 private function owns(int$id):bool{$aid=$this->athleteId();$db=$this->getDatabase();$q=$db->getQuery(true)->select('COUNT(*)')->from('#__jt_training_diary')->where('id='.$id)->where('athlete_id='.$aid);$db->setQuery($q);return(int)$db->loadResult()===1;}
 private function setups():array{$aid=$this->athleteId();$db=$this->getDatabase();$q=$db->getQuery(true)->select(['id','title','revision_no','is_active'])->from('#__jt_bow_setups')->where('athlete_id='.$aid)->order('is_active DESC, revision_no DESC');$db->setQuery($q);return$db->loadObjectList();}
 private function activeSetup():?int{$aid=$this->athleteId();$db=$this->getDatabase();$q=$db->getQuery(true)->select('id')->from('#__jt_bow_setups')->where('athlete_id='.$aid)->where('is_active=1')->order('revision_no DESC');$db->setQuery($q,0,1);$id=(int)$db->loadResult();return$id?:null;}
 private function configured(string$key,array$default):array{$db=$this->getDatabase();$q=$db->getQuery(true)->select('setting_value')->from('#__jt_settings')->where('setting_key='.$db->quote($key));$db->setQuery($q);$v=json_decode((string)$db->loadResult(),true);return is_array($v)&&$v?$v:$default;}
}
