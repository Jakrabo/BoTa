<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\Model;
\defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;
final class SportyearModel extends AdminModel
{
 public function getTable($name='Sportyear', $prefix='Administrator', $options=[]) { return parent::getTable($name,$prefix,$options); }
 public function getForm($data=[], $loadData=true) { return $this->loadForm('com_jugendtraining.sportyear','sportyear', ['control'=>'jform','load_data'=>$loadData]); }
 protected function loadFormData() { $data=Factory::getApplication()->getUserState('com_jugendtraining.edit.sportyear.data',[]); return $data ?: $this->getItem(); }
 
 protected function prepareTable($table): void
 {
  $date=Factory::getDate()->toSql(); $user=Factory::getApplication()->getIdentity();
  if (empty($table->id)) { if (property_exists($table,'created')) $table->created=$date; if (property_exists($table,'created_by')) $table->created_by=(int)$user->id; }
  else { if (property_exists($table,'modified')) $table->modified=$date; if (property_exists($table,'modified_by')) $table->modified_by=(int)$user->id; }
 }

}
