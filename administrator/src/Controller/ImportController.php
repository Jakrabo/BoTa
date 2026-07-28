<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\Controller;
\defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Router\Route;
use Jugendtraining\Component\Jugendtraining\Administrator\Service\CsvImportService;
use Jugendtraining\Component\Jugendtraining\Site\Service\AccessService;

final class ImportController extends BaseController
{
 public function upload():void
 {
  Session::checkToken() or jexit('JINVALID_TOKEN');$this->assertTrainer();
  $app=Factory::getApplication();$type=$app->input->getCmd('import_type');
  $file=$app->input->files->get('csv_file',null,'array');
  try{
   if(!$file||($file['error']??UPLOAD_ERR_NO_FILE)!==UPLOAD_ERR_OK)throw new \RuntimeException('Bitte eine CSV-Datei auswählen.');
   if(($file['size']??0)>10*1024*1024)throw new \RuntimeException('Die Datei ist größer als 10 MB.');
   $r=(new CsvImportService())->import($type,$file['tmp_name']);
   $app->setUserState('com_jugendtraining.import.result',$r);
   $app->enqueueMessage($r['success'].' Datensätze importiert, '.$r['failed'].' Fehler.',$r['failed']?'warning':'success');
  }catch(\Throwable$e){$app->enqueueMessage($e->getMessage(),'error');}
  $this->setRedirect(Route::_('index.php?option=com_jugendtraining&view=import',false));
 }
 public function template():void
 {
  Session::checkToken('get') or jexit('JINVALID_TOKEN');$this->assertTrainer();
  $type=Factory::getApplication()->input->getCmd('type');
  $rows=(new CsvImportService())->template($type);
  while(ob_get_level())ob_end_clean();
  header('Content-Type: text/csv; charset=UTF-8');header('Content-Disposition: attachment; filename="jugendtraining-'.$type.'-template.csv"');
  $h=fopen('php://output','wb');fwrite($h,"\xEF\xBB\xBF");foreach($rows as$r)fputcsv($h,$r,';','"','\\');fclose($h);Factory::getApplication()->close();
 }
 public function saveOptions():void
 {
  Session::checkToken() or jexit('JINVALID_TOKEN');$this->assertTrainer();
  $model=$this->getModel('Import');$model->saveOptions(Factory::getApplication()->input->get('jform',[],'array'));
  Factory::getApplication()->enqueueMessage('Dropdownwerte gespeichert.','success');
  $this->setRedirect(Route::_('index.php?option=com_jugendtraining&view=import',false));
 }

public function saveSelfCancelSettings():void
{
 Session::checkToken() or jexit('JINVALID_TOKEN');$this->assertTrainer();$app=Factory::getApplication();
 try{
  $this->getModel('Import')->saveSelfCancelSettings($app->input->get('jform',[],'array'));
  $app->enqueueMessage('Abmeldeeinstellungen gespeichert.','success');
 }catch(\Throwable$e){$app->enqueueMessage($e->getMessage(),'error');}
 $this->setRedirect(Route::_('index.php?option=com_jugendtraining&view=import#self-attendance',false));
}

public function saveCalendarCategories():void
{
 Session::checkToken() or jexit('JINVALID_TOKEN');$this->assertTrainer();$app=Factory::getApplication();
 try{$this->getModel('Import')->saveCalendarCategories($app->input->get('jform',[],'array'));$app->enqueueMessage('Kalender-Kategorien gespeichert.','success');}
 catch(\Throwable$e){$app->enqueueMessage($e->getMessage(),'error');}
 $this->setRedirect(Route::_('index.php?option=com_jugendtraining&view=import#calendar-config',false));
}
public function savePenalty():void
{
 Session::checkToken() or jexit('JINVALID_TOKEN');$this->assertTrainer();
 $app=Factory::getApplication();
 try{
  $this->getModel('Import')->savePenalty($app->input->get('jform',[],'array'));
  $app->enqueueMessage('Strafe gespeichert.','success');
 }catch(\Throwable$e){$app->enqueueMessage($e->getMessage(),'error');}
 $this->setRedirect(Route::_('index.php?option=com_jugendtraining&view=import#penalties',false));
}

public function deletePenalty():void
{
 Session::checkToken('get') or jexit('JINVALID_TOKEN');$this->assertTrainer();
 $app=Factory::getApplication();
 try{
  $this->getModel('Import')->deletePenalty($app->input->getInt('id'));
  $app->enqueueMessage('Strafe gelöscht oder deaktiviert.','success');
 }catch(\Throwable$e){$app->enqueueMessage($e->getMessage(),'error');}
 $this->setRedirect(Route::_('index.php?option=com_jugendtraining&view=import#penalties',false));
}
public function resetPenaltyBalance():void
{
 Session::checkToken() or jexit('JINVALID_TOKEN');$this->assertTrainer();
 $db=Factory::getContainer()->get('DatabaseDriver');
 $key='penalty_balance_reset_at';$value=Factory::getDate()->toSql();
 $q=$db->getQuery(true)->select('id')->from('#__jt_settings')->where('setting_key='.$db->quote($key));
 $db->setQuery($q);$id=(int)$db->loadResult();
 $obj=(object)['setting_key'=>$key,'setting_value'=>$value];
 if($id){$obj->id=$id;$db->updateObject('#__jt_settings',$obj,'id');}else{$db->insertObject('#__jt_settings',$obj);}
 Factory::getApplication()->enqueueMessage('Monetäre Strafbilanz wurde auf 0,00 € zurückgesetzt.','success');
 $this->setRedirect(Route::_('index.php?option=com_jugendtraining&view=import#penalties',false));
}
public function saveDashboardConfig():void
{
 Session::checkToken() or jexit('JINVALID_TOKEN');$this->assertTrainer();
 $app=Factory::getApplication();$type=$app->input->getCmd('dashboard_type','athlete');
 $rows=$app->input->get('dashboard',[],'array');
 $this->getModel('Import')->saveDashboardConfig($type,$rows);
 $app->enqueueMessage('Dashboard-Konfiguration gespeichert.','success');
 $this->setRedirect(Route::_('index.php?option=com_jugendtraining&view=import#dashboard-'.$type,false));
}
public function saveLanguage():void
{
 Session::checkToken() or jexit('JINVALID_TOKEN');$this->assertTrainer();$app=Factory::getApplication();
 try{$this->getModel('Import')->saveLanguageOverrides($app->input->getCmd('language'),$app->input->get('translations',[],'array'));$app->enqueueMessage('Übersetzungen gespeichert.','success');}
 catch(\Throwable$e){$app->enqueueMessage($e->getMessage(),'error');}
 $this->setRedirect(Route::_('index.php?option=com_jugendtraining&view=import#language',false));
}
 private function assertTrainer():void{
 $user=Factory::getApplication()->getIdentity();
 if((!$user->authorise('core.manage','com_jugendtraining')&&!$user->authorise('core.admin'))||!(new AccessService())->isTrainer()){
  throw new \RuntimeException('JERROR_ALERTNOAUTHOR',403);
 }
}
}
