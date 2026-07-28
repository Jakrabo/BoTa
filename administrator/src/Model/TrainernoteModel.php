<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\Model;
\defined('_JEXEC') or die;
use Joomla\CMS\Factory;use Joomla\CMS\MVC\Model\AdminModel;
final class TrainernoteModel extends AdminModel {
 public function getTable($name='Trainernote',$prefix='Administrator',$options=[]){return parent::getTable($name,$prefix,$options);}
 public function getForm($data=[],$loadData=true){return $this->loadForm('com_jugendtraining.trainernote','trainernote',['control'=>'jform','load_data'=>$loadData]);}
 protected function loadFormData(){ $d=Factory::getApplication()->getUserState('com_jugendtraining.edit.trainernote.data',[]); if(!$d){$d=$this->getItem();if(empty($d->id))$d->note_date=Factory::getDate()->format('Y-m-d');} return $d; }
 protected function prepareTable($table):void{$date=Factory::getDate()->toSql();$uid=(int)Factory::getApplication()->getIdentity()->id;if(empty($table->id)){$table->created=$date;$table->created_by=$uid;}else{$table->modified=$date;$table->modified_by=$uid;}}
}
