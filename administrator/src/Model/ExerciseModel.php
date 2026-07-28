<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\Model;
\defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;
final class ExerciseModel extends AdminModel {
  public function getTable($name='Exercise',$prefix='Administrator',$options=[]){return parent::getTable($name,$prefix,$options);}
  public function getForm($data=[],$loadData=true){return $this->loadForm('com_jugendtraining.exercise','exercise',['control'=>'jform','load_data'=>$loadData]);}
  protected function loadFormData(){ $d=Factory::getApplication()->getUserState('com_jugendtraining.edit.exercise.data',[]); return $d ?: $this->getItem(); }
  protected function prepareTable($table): void {
    $date=Factory::getDate()->toSql(); $uid=(int)Factory::getApplication()->getIdentity()->id;
    if(empty($table->id)){ $table->created=$date; $table->created_by=$uid; } else { $table->modified=$date; $table->modified_by=$uid; }
  }
}
