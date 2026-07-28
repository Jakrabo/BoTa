<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\Model;
\defined('_JEXEC') or die;
use Joomla\CMS\Factory;use Joomla\CMS\MVC\Model\AdminModel;use Jugendtraining\Component\Jugendtraining\Site\Service\BadgeUploadService;
final class AchievementModel extends AdminModel{
 public function getTable($name='Achievement',$prefix='Administrator',$options=[]){return parent::getTable($name,$prefix,$options);}
 public function getForm($data=[],$loadData=true){return$this->loadForm('com_jugendtraining.achievement','achievement',['control'=>'jform','load_data'=>$loadData]);}
 protected function loadFormData(){$d=Factory::getApplication()->getUserState('com_jugendtraining.edit.achievement.data',[]);return$d?:$this->getItem();}
 public function save($data):bool{
  $upload=Factory::getApplication()->input->files->get('badge_upload',null,'array');
  $path=(new BadgeUploadService())->store($upload);if($path){$data['badge_image']=$path;}elseif(!empty($data['badge_image_existing'])){$data['badge_image']=$data['badge_image_existing'];}
  if(($data['award_mode']??'manual')==='manual'){$data['rule_type']=null;$data['rule_value']=null;$data['rule_terms']=null;$data['requires_verified_result']=0;}
  if(($data['rule_type']??'')==='event_name_contains')$data['requires_verified_result']=1;
  return parent::save($data);
 }
 public function getAvailableBadges(): array
 {
  return (new \Jugendtraining\Component\Jugendtraining\Site\Service\BadgeUploadService())->listAvailable();
 }

 protected function prepareTable($table):void{$date=Factory::getDate()->toSql();$uid=(int)Factory::getApplication()->getIdentity()->id;if(empty($table->id)){$table->created=$date;$table->created_by=$uid;}else{$table->modified=$date;$table->modified_by=$uid;}}
}
