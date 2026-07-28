<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\Model;
\defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

final class ImportModel extends BaseDatabaseModel
{
 public function getOptions(): array
 {
  $db=$this->getDatabase();$q=$db->getQuery(true)->select(['setting_key','setting_value'])->from('#__jt_settings')
    ->where('setting_key IN ('.$db->quote('diary_methods').','.$db->quote('diary_focus_topics').')');
  $db->setQuery($q);$rows=$db->loadAssocList('setting_key','setting_value');
  return [
   'methods'=>$this->decode($rows['diary_methods']??'', ['Techniktraining','Wettkampftraining','Kraft und Stabilität','Materialtraining','Freies Training']),
   'focus'=>$this->decode($rows['diary_focus_topics']??'', ['Ankerpunkt','Lösen','Stand','Zielbild','Rhythmus','Mentales Training'])
  ];
 }
 public function saveOptions(array $data): void
 {
  foreach(['diary_methods'=>'methods','diary_focus_topics'=>'focus'] as$key=>$input){
   $values=array_values(array_unique(array_filter(array_map('trim',preg_split('/\R/',(string)($data[$input]??''))))));
   $this->upsert($key,json_encode($values,JSON_UNESCAPED_UNICODE));
  }
 }
public function getCalendarCategories(): array
{
 $db=$this->getDatabase();$q=$db->getQuery(true)->select('setting_value')->from('#__jt_settings')->where('setting_key='.$db->quote('calendar_categories'));$db->setQuery($q);$raw=json_decode((string)$db->loadResult(),true);
 $defaults=[['name'=>'Liga','color'=>'#6f42c1','active'=>1],['name'=>'Wettkampf','color'=>'#dc3545','active'=>1],['name'=>'Training','color'=>'#198754','active'=>1],['name'=>'Lehrgang','color'=>'#0d6efd','active'=>1],['name'=>'Vereinstermin','color'=>'#fd7e14','active'=>1],['name'=>'Sonstiges','color'=>'#6c757d','active'=>1]];
 if(!is_array($raw)||!$raw)return$defaults;$out=[];
 foreach(array_values($raw)as$i=>$r){if(is_string($r))$r=['name'=>trim($r),'color'=>$defaults[$i%count($defaults)]['color'],'active'=>1];if(!is_array($r))continue;$name=trim((string)($r['name']??''));if($name==='')continue;$color=preg_match('/^#[0-9A-Fa-f]{6}$/',(string)($r['color']??''))?(string)$r['color']:'#6c757d';$out[]=['name'=>$name,'color'=>$color,'active'=>!empty($r['active'])?1:0];}
 return$out?:$defaults;
}
public function saveCalendarCategories(array$data):void
{
 $rows=$data['categories']??[];if(!is_array($rows))throw new \RuntimeException('Ungültige Kategorien.');$clean=[];$names=[];
 foreach($rows as$r){if(!is_array($r))continue;$name=trim((string)($r['name']??''));if($name==='')continue;if(mb_strlen($name)>100)throw new \RuntimeException('Kategorie ist zu lang.');$key=mb_strtolower($name);if(isset($names[$key]))continue;$names[$key]=1;$color=(string)($r['color']??'');if(!preg_match('/^#[0-9A-Fa-f]{6}$/',$color))$color='#6c757d';$clean[]=['name'=>$name,'color'=>$color,'active'=>!empty($r['active'])?1:0];}
 if(!$clean)throw new \RuntimeException('Mindestens eine Kategorie ist erforderlich.');if(count($clean)>50)throw new \RuntimeException('Maximal 50 Kategorien.');
 $this->upsert('calendar_categories',json_encode($clean,JSON_UNESCAPED_UNICODE));
}
public function getPenalties(): array
{
 $db=$this->getDatabase();
 $q=$db->getQuery(true)->select('*')->from('#__jt_penalty_definitions')->order('ordering,title,id');
 $db->setQuery($q);
 return $db->loadObjectList();
}

public function savePenalty(array $data): int
{
 $db=$this->getDatabase();
 $id=(int)($data['id']??0);
 $title=trim((string)($data['title']??''));
 $type=($data['penalty_type']??'non_monetary')==='monetary'?'monetary':'non_monetary';

 if($title==='') throw new \RuntimeException('Bitte eine Bezeichnung eingeben.');

 $amount=null;
 $action=null;

 if($type==='monetary'){
  $raw=str_replace(',','.',trim((string)($data['amount']??'')));
  if($raw===''||!is_numeric($raw)||(float)$raw<0) throw new \RuntimeException('Bitte einen gültigen Betrag eingeben.');
  $amount=round((float)$raw,2);
 }else{
  $action=trim((string)($data['non_monetary_action']??''));
  if($action==='') throw new \RuntimeException('Bitte die nichtmonetäre Maßnahme beschreiben.');
 }

 $now=\Joomla\CMS\Factory::getDate()->toSql();
 $uid=(int)\Joomla\CMS\Factory::getApplication()->getIdentity()->id;
 $obj=(object)[
  'title'=>$title,
  'description'=>trim((string)($data['description']??''))?:null,
  'penalty_type'=>$type,
  'amount'=>$amount,
  'non_monetary_action'=>$action,
  'published'=>(int)($data['published']??1),
  'ordering'=>(int)($data['ordering']??0),
  'modified'=>$now,
  'modified_by'=>$uid
 ];

 if($id>0){
  $obj->id=$id;
  $db->updateObject('#__jt_penalty_definitions',$obj,'id');
 }else{
  $obj->created=$now;
  $obj->created_by=$uid;
  $db->insertObject('#__jt_penalty_definitions',$obj);
  $id=(int)$db->insertid();
 }

 return $id;
}

public function deletePenalty(int $id): void
{
 if($id<=0) return;
 $db=$this->getDatabase();
 $q=$db->getQuery(true)->select('COUNT(*)')->from('#__jt_penalty_register')
  ->where('penalty_definition_id='.$id);
 $db->setQuery($q);
 if((int)$db->loadResult()>0){
  $q=$db->getQuery(true)->update('#__jt_penalty_definitions')->set('published=0')->where('id='.$id);
  $db->setQuery($q)->execute();
  return;
 }
 $q=$db->getQuery(true)->delete('#__jt_penalty_definitions')->where('id='.$id);
 $db->setQuery($q)->execute();
}
public function getDashboardConfigs(): array
{
 $athleteDefaults=[
  ['key'=>'profile','visible'=>1],['key'=>'results','visible'=>1],['key'=>'penalties','visible'=>1],
  ['key'=>'achievements','visible'=>1],['key'=>'programs','visible'=>1],['key'=>'overview','visible'=>1],
  ['key'=>'performance','visible'=>1]
 ];
 $trainerDefaults=[
  ['key'=>'groups','visible'=>1],['key'=>'penalty_summary','visible'=>1],['key'=>'open_penalties','visible'=>1],
  ['key'=>'signals','visible'=>1],['key'=>'class_changes','visible'=>1],['key'=>'navigation','visible'=>1]
 ];
 $db=$this->getDatabase();$q=$db->getQuery(true)->select(['setting_key','setting_value'])->from('#__jt_settings')
  ->where('setting_key IN ('.$db->quote('athlete_dashboard_config').','.$db->quote('trainer_dashboard_config').')');
 $db->setQuery($q);$rows=$db->loadAssocList('setting_key','setting_value');
 return[
  'athlete'=>$this->normaliseDashboardConfig($rows['athlete_dashboard_config']??'', $athleteDefaults),
  'trainer'=>$this->normaliseDashboardConfig($rows['trainer_dashboard_config']??'', $trainerDefaults)
 ];
}

public function saveDashboardConfig(string $type,array $rows):void
{
 $allowed=$type==='trainer'
  ?['groups','penalty_summary','open_penalties','signals','class_changes','navigation']
  :['profile','results','penalties','achievements','programs','overview','performance'];
 $clean=[];
 foreach($rows as$key=>$row){
  if(!in_array($key,$allowed,true))continue;
  $clean[]=['key'=>$key,'visible'=>!empty($row['visible'])?1:0,'ordering'=>(int)($row['ordering']??999)];
 }
 usort($clean,fn($a,$b)=>$a['ordering']<=>$b['ordering']);
 $clean=array_map(fn($row)=>['key'=>$row['key'],'visible'=>$row['visible']],$clean);
 $this->upsert(($type==='trainer'?'trainer':'athlete').'_dashboard_config',json_encode($clean,JSON_UNESCAPED_UNICODE));
}

private function normaliseDashboardConfig(string $json,array $defaults):array
{
 $saved=json_decode($json,true);if(!is_array($saved))return$defaults;
 $map=[];foreach($saved as$row)if(isset($row['key']))$map[$row['key']]=['key'=>$row['key'],'visible'=>!empty($row['visible'])?1:0];
 $out=[];foreach($saved as$row)if(isset($row['key'],$map[$row['key']])){$out[]=$map[$row['key']];unset($map[$row['key']]);}
 foreach($defaults as$row)if(!array_filter($out,fn($x)=>$x['key']===$row['key']))$out[]=$row;
 return$out;
}
public function getLanguageOverview(): array
{
 $languages=['de-DE','en-GB'];$result=[];
 foreach($languages as$lang){
  $paths=[JPATH_ROOT.'/components/com_jugendtraining/language/'.$lang.'/com_jugendtraining.ini',JPATH_ADMINISTRATOR.'/components/com_jugendtraining/language/'.$lang.'/com_jugendtraining.ini'];
  $values=[];
  foreach($paths as$path)if(is_file($path)){$parsed=parse_ini_file($path,false,INI_SCANNER_RAW)?:[];$values=array_replace($values,$parsed);}
  $override=JPATH_ROOT.'/language/overrides/'.$lang.'.override.ini';
  if(is_file($override)){$parsed=parse_ini_file($override,false,INI_SCANNER_RAW)?:[];$values=array_replace($values,array_filter($parsed,fn($k)=>str_starts_with($k,'COM_JUGENDTRAINING_'),ARRAY_FILTER_USE_KEY));}
  $result[$lang]=$values;
 }
 $keys=[];foreach($result as$values)$keys=array_unique(array_merge($keys,array_keys($values)));sort($keys);
 return['languages'=>$languages,'keys'=>$keys,'values'=>$result];
}

public function saveLanguageOverrides(string $language,array $values):void
{
 $overview=$this->getLanguageOverview();$allowedLanguages=$overview['languages']??[];
 if(!in_array($language,$allowedLanguages,true))throw new \RuntimeException('Ungültige Sprache.');
 if(count($values)>2000)throw new \RuntimeException('Zu viele Sprachwerte.');
 $allowedKeys=array_flip($overview['keys']??[]);
 $dir=JPATH_ROOT.'/language/overrides';if(!is_dir($dir)&&!mkdir($dir,0755,true)&&!is_dir($dir))throw new \RuntimeException('Override-Verzeichnis konnte nicht angelegt werden.');
 $path=$dir.'/'.$language.'.override.ini';$existing=is_file($path)?(parse_ini_file($path,false,INI_SCANNER_RAW)?:[]):[];
 foreach($values as$key=>$value){
  if(!isset($allowedKeys[$key])||!str_starts_with($key,'COM_JUGENDTRAINING_'))continue;
  $value=trim((string)$value);if(mb_strlen($value)>2000)throw new \RuntimeException('Übersetzung ist zu lang: '.$key);
  $existing[$key]=$value;
 }
 ksort($existing);$lines=[];foreach($existing as$key=>$value)$lines[]=$key.'="'.str_replace(['\\','"'],['\\\\','\\"'],$value).'"';
 if(file_put_contents($path,implode("\n",$lines)."\n",LOCK_EX)===false)throw new \RuntimeException('Sprach-Override konnte nicht gespeichert werden.');
 @chmod($path,0644);
}
 private function decode(string $json,array $default):array{$v=json_decode($json,true);return is_array($v)&&$v?$v:$default;}
 private function upsert(string$key,string$value):void{$db=$this->getDatabase();$q=$db->getQuery(true)->select('id')->from('#__jt_settings')->where('setting_key='.$db->quote($key));$db->setQuery($q);$id=(int)$db->loadResult();$o=(object)['setting_key'=>$key,'setting_value'=>$value];if($id){$o->id=$id;$db->updateObject('#__jt_settings',$o,'id');}else$db->insertObject('#__jt_settings',$o);}
}
