<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\Model;
\defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\Database\ParameterType;
final class ProgramModel extends AdminModel {
  public function getTable($name='Program',$prefix='Administrator',$options=[]){return parent::getTable($name,$prefix,$options);}
  public function getForm($data=[],$loadData=true){return $this->loadForm('com_jugendtraining.program','program',['control'=>'jform','load_data'=>$loadData]);}
  protected function loadFormData(){
    $data=Factory::getApplication()->getUserState('com_jugendtraining.edit.program.data',[]);
    if(!$data){$item=$this->getItem();$data=(array)$item;if(!empty($item->id)){
      $data['exercise_ids']=$this->getLinkedIds('#__jt_program_exercises','exercise_id','program_id',(int)$item->id);
      $data['athlete_ids']=$this->getLinkedIds('#__jt_athlete_programs','athlete_id','program_id',(int)$item->id,'active = 1');
      $data['due_date']=$this->getDueDate((int)$item->id);
    }}
    return $data;
  }
  private function getLinkedIds(string $table,string $field,string $whereField,int $id,string $extra=''):array{
    $db=$this->getDatabase();$q=$db->getQuery(true)->select($db->quoteName($field))->from($db->quoteName($table))->where($db->quoteName($whereField).' = :id')->bind(':id',$id,ParameterType::INTEGER);
    if($extra)$q->where($extra);$db->setQuery($q);return array_map('intval',$db->loadColumn());
  }
  private function getDueDate(int $id):?string{
    $db=$this->getDatabase();$q=$db->getQuery(true)->select('MAX(due_date)')->from($db->quoteName('#__jt_athlete_programs'))->where('program_id='.(int)$id)->where('active=1');$db->setQuery($q);return $db->loadResult() ?: null;
  }
  public function save($data):bool{
    $exerciseIds=array_values(array_unique(array_filter(array_map('intval',(array)($data['exercise_ids']??[])))));
    $athleteIds=array_values(array_unique(array_filter(array_map('intval',(array)($data['athlete_ids']??[])))));
    $dueDate=!empty($data['due_date'])?(string)$data['due_date']:null;
    unset($data['exercise_ids'],$data['athlete_ids'],$data['due_date']);
    if(!parent::save($data))return false;
    $programId=(int)$this->getState($this->getName().'.id');
    $db=$this->getDatabase();
    $db->transactionStart();
    try{
      $q=$db->getQuery(true)->delete($db->quoteName('#__jt_program_exercises'))->where('program_id='.(int)$programId);$db->setQuery($q)->execute();
      foreach($exerciseIds as $i=>$eid){$q=$db->getQuery(true)->insert($db->quoteName('#__jt_program_exercises'))->columns(['program_id','exercise_id','ordering'])->values($programId.','.$eid.','.(int)$i);$db->setQuery($q)->execute();}
      $q=$db->getQuery(true)->update($db->quoteName('#__jt_athlete_programs'))->set('active=0')->where('program_id='.(int)$programId);$db->setQuery($q)->execute();
      $uid=(int)Factory::getApplication()->getIdentity()->id;$now=$db->quote(Factory::getDate()->toSql());$due=$dueDate?$db->quote($dueDate):'NULL';
      foreach($athleteIds as $aid){
        $q=$db->getQuery(true)->select('id')->from($db->quoteName('#__jt_athlete_programs'))->where('program_id='.(int)$programId)->where('athlete_id='.(int)$aid);$db->setQuery($q);$existing=(int)$db->loadResult();
        if($existing){$q=$db->getQuery(true)->update($db->quoteName('#__jt_athlete_programs'))->set(['active=1','due_date='.$due])->where('id='.$existing);}
        else{$q=$db->getQuery(true)->insert($db->quoteName('#__jt_athlete_programs'))->columns(['athlete_id','program_id','assigned_by','assigned_at','due_date','active'])->values($aid.','.$programId.','.$uid.','.$now.','.$due.',1');}
        $db->setQuery($q)->execute();
      }
      $db->transactionCommit();
    }catch(\Throwable $e){$db->transactionRollback();$this->setError($e->getMessage());return false;}
    return true;
  }
  protected function prepareTable($table):void{$date=Factory::getDate()->toSql();$uid=(int)Factory::getApplication()->getIdentity()->id;if(empty($table->id)){$table->created=$date;$table->created_by=$uid;}else{$table->modified=$date;$table->modified_by=$uid;}}
}
