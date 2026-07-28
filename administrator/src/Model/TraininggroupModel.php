<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\Model;
\defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;
final class TraininggroupModel extends AdminModel {
 public function getTable($name='Traininggroup',$prefix='Administrator',$options=[]){return parent::getTable($name,$prefix,$options);}
 public function getForm($data=[],$loadData=true){return $this->loadForm('com_jugendtraining.traininggroup','traininggroup',['control'=>'jform','load_data'=>$loadData]);}
 protected function loadFormData(){
  $data=Factory::getApplication()->getUserState('com_jugendtraining.edit.traininggroup.data',[]);
  if($data)return $data;
  $item=$this->getItem();$data=(array)$item;
  if(!empty($item->id)){
   $data['athlete_ids']=$this->linkedIds('#__jt_training_group_athletes','athlete_id',(int)$item->id);
   $data['trainer_user_ids']=$this->linkedIds('#__jt_training_group_trainers','user_id',(int)$item->id);
  }
  return $data;
 }
 private function linkedIds(string $table,string $field,int $groupId):array{
  $db=$this->getDatabase();$q=$db->getQuery(true)->select($db->quoteName($field))->from($db->quoteName($table))->where('group_id='.(int)$groupId);$db->setQuery($q);return array_map('intval',$db->loadColumn());
 }
 public function save($data):bool{
  $athletes=array_values(array_unique(array_filter(array_map('intval',(array)($data['athlete_ids']??[])))));
  $trainers=array_values(array_unique(array_filter(array_map('intval',(array)($data['trainer_user_ids']??[])))));
  unset($data['athlete_ids'],$data['trainer_ids'],$data['trainer_user_ids']);
  if(!parent::save($data))return false;
  $id=(int)$this->getState($this->getName().'.id');$db=$this->getDatabase();$db->transactionStart();
  try{
   foreach(['#__jt_training_group_athletes','#__jt_training_group_trainers'] as $table){$q=$db->getQuery(true)->delete($db->quoteName($table))->where('group_id='.$id);$db->setQuery($q)->execute();}
   foreach($athletes as $aid){$q=$db->getQuery(true)->insert($db->quoteName('#__jt_training_group_athletes'))->columns(['group_id','athlete_id'])->values($id.','.$aid);$db->setQuery($q)->execute();}
   foreach($trainers as $uid){$q=$db->getQuery(true)->insert($db->quoteName('#__jt_training_group_trainers'))->columns(['group_id','user_id'])->values($id.','.$uid);$db->setQuery($q)->execute();}
   $db->transactionCommit();
  }catch(\Throwable $e){$db->transactionRollback();$this->setError($e->getMessage());return false;}
  return true;
 }
 protected function prepareTable($table):void{$d=Factory::getDate()->toSql();$u=(int)Factory::getApplication()->getIdentity()->id;if(empty($table->id)){$table->created=$d;$table->created_by=$u;}else{$table->modified=$d;$table->modified_by=$u;}}
}
