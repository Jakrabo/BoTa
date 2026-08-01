<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\Model;
\defined('_JEXEC') or die;
use Joomla\CMS\Factory;use Joomla\CMS\MVC\Model\AdminModel;
final class TraininglocationModel extends AdminModel{
 public function getTable($name='Traininglocation',$prefix='Administrator',$options=[]){return parent::getTable($name,$prefix,$options);}
 public function getForm($data=[],$loadData=true){return $this->loadForm('com_jugendtraining.traininglocation','traininglocation',['control'=>'jform','load_data'=>$loadData]);}
 protected function loadFormData(){$data=Factory::getApplication()->getUserState('com_jugendtraining.edit.traininglocation.data',[]);return$data?:$this->getItem();}
 protected function prepareTable($table):void{$now=Factory::getDate()->toSql();$uid=(int)Factory::getApplication()->getIdentity()->id;if(empty($table->id)){$table->created=$now;$table->created_by=$uid;}else{$table->modified=$now;$table->modified_by=$uid;}}
 public function delete(&$pks):bool{
  $ids=array_values(array_filter(array_map('intval',(array)$pks)));if(!$ids)return true;$db=$this->getDatabase();
  foreach($ids as$id){$q=$db->getQuery(true)->select('COUNT(*)')->from('#__jt_training_sessions')->where('location_id='.$id);$db->setQuery($q);if((int)$db->loadResult()>0){$q=$db->getQuery(true)->update('#__jt_training_locations')->set('published=0')->where('id='.$id);$db->setQuery($q)->execute();$pks=array_values(array_diff((array)$pks,[$id]));}}
  return empty($pks)?true:parent::delete($pks);
 }

}
