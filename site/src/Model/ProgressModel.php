<?php
namespace Jugendtraining\Component\Jugendtraining\Site\Model;
\defined('_JEXEC') or die;
use Joomla\CMS\Factory;use Joomla\CMS\MVC\Model\BaseDatabaseModel;
final class ProgressModel extends BaseDatabaseModel {
 public function toggle(int $assignmentId,int $exerciseId):bool{
  $user=(int)Factory::getApplication()->getIdentity()->id;if($user<=0||$assignmentId<=0||$exerciseId<=0){$this->setError('JERROR_ALERTNOAUTHOR');return false;}
  $db=$this->getDatabase();
  $q=$db->getQuery(true)->select('ap.id')->from($db->quoteName('#__jt_athlete_programs','ap'))->innerJoin($db->quoteName('#__jt_athletes','a').' ON a.id=ap.athlete_id')->innerJoin($db->quoteName('#__jt_program_exercises','pe').' ON pe.program_id=ap.program_id AND pe.exercise_id='.(int)$exerciseId)->where('ap.id='.(int)$assignmentId)->where('ap.active=1')->where('a.user_id='.(int)$user)->where('a.published=1');$db->setQuery($q);if(!(int)$db->loadResult()){$this->setError('JERROR_ALERTNOAUTHOR');return false;}
  $q=$db->getQuery(true)->select(['id','completed'])->from($db->quoteName('#__jt_program_progress'))->where('athlete_program_id='.(int)$assignmentId)->where('exercise_id='.(int)$exerciseId);$db->setQuery($q);$row=$db->loadObject();$now=$db->quote(Factory::getDate()->toSql());
  if($row){$new=(int)!((int)$row->completed);$q=$db->getQuery(true)->update($db->quoteName('#__jt_program_progress'))->set('completed='.$new)->set('completed_at='.($new?$now:'NULL'))->where('id='.(int)$row->id);}
  else{$q=$db->getQuery(true)->insert($db->quoteName('#__jt_program_progress'))->columns(['athlete_program_id','exercise_id','completed','completed_at'])->values($assignmentId.','.$exerciseId.',1,'.$now);}
  try{
   $db->setQuery($q)->execute();
   $this->updateAssignmentCompletion($assignmentId);
   return true;
  }catch(\Throwable $e){$this->setError($e->getMessage());return false;}
 }

 private function updateAssignmentCompletion(int $assignmentId): void
 {
  $db=$this->getDatabase();
  $q=$db->getQuery(true)->select([
   'COUNT(pe.exercise_id) total_count',
   'SUM(CASE WHEN COALESCE(pp.completed,0)=1 THEN 1 ELSE 0 END) completed_count'
  ])->from($db->quoteName('#__jt_athlete_programs','ap'))
   ->innerJoin($db->quoteName('#__jt_program_exercises','pe').' ON pe.program_id=ap.program_id')
   ->leftJoin($db->quoteName('#__jt_program_progress','pp').' ON pp.athlete_program_id=ap.id AND pp.exercise_id=pe.exercise_id')
   ->where('ap.id='.$assignmentId);
  $db->setQuery($q);
  $status=$db->loadObject();
  $isComplete=$status && (int)$status->total_count>0 && (int)$status->completed_count>=(int)$status->total_count;
  $q=$db->getQuery(true)->update($db->quoteName('#__jt_athlete_programs'))
   ->set('completed_at='.($isComplete?$db->quote(Factory::getDate()->toSql()):'NULL'))
   ->where('id='.$assignmentId);
  $db->setQuery($q)->execute();
 }
}
