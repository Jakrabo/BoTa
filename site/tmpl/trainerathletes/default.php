<?php
\defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$groupId=Factory::getApplication()->input->getInt('group_id');
$currentSort=(string)($this->athleteSort->sort??'athlete');
$currentDirection=(string)($this->athleteSort->direction??'asc');

$sortLabel=static function(string$key,string$label)use($groupId,$currentSort,$currentDirection):string{
    $dir=$currentSort===$key&&$currentDirection==='asc'?'desc':'asc';
    $url=Route::_('index.php?option=com_jugendtraining&view=trainerathletes'.($groupId?'&group_id='.$groupId:'').'&sort='.$key.'&direction='.$dir);
    $arrow=$currentSort===$key?($currentDirection==='asc'?' ▲':' ▼'):'';
    return'<a class="text-decoration-none text-reset" href="'.$url.'">'.htmlspecialchars($label.$arrow,ENT_QUOTES,'UTF-8').'</a>';
};
?>
<div class="d-flex justify-content-between align-items-center gap-3 mb-4"><h1 class="mb-0"><?php echo Text::_('COM_JUGENDTRAINING_TRAINER_ATHLETES');?></h1><a class="btn btn-outline-secondary" href="<?php echo Route::_('index.php?option=com_jugendtraining&view=trainerdashboard');?>"><?php echo Text::_('COM_JUGENDTRAINING_BACK_TO_DASHBOARD');?></a></div>
<?php if($groupId):?><p class="text-muted"><?php echo Text::_('COM_JUGENDTRAINING_FILTERED_TRAINING_GROUP');?></p><?php endif;?>
<div class="table-responsive"><table class="table table-striped"><thead><tr>
<th><?php echo$sortLabel('athlete',Text::_('COM_JUGENDTRAINING_FIELD_ATHLETE'));?></th>
<th><?php echo$sortLabel('groups',Text::_('COM_JUGENDTRAINING_TRAINING_GROUPS'));?></th>
<th><?php echo$sortLabel('class',Text::_('COM_JUGENDTRAINING_FIELD_CLASS'));?></th>
<th><?php echo$sortLabel('bow_type',Text::_('COM_JUGENDTRAINING_FIELD_BOW_TYPE'));?></th>
<th><?php echo$sortLabel('phone',Text::_('COM_JUGENDTRAINING_FIELD_PHONE'));?></th>
</tr></thead><tbody>
<?php foreach($this->athletes as$a):?><tr>
<td><a href="<?php echo Route::_('index.php?option=com_jugendtraining&view=trainerathletedetail&id='.(int)$a->id);?>"><strong><?php echo htmlspecialchars($a->firstname.' '.$a->lastname,ENT_QUOTES,'UTF-8');?></strong></a></td>
<td><?php echo htmlspecialchars((string)$a->group_names,ENT_QUOTES,'UTF-8');?></td><td><?php echo htmlspecialchars((string)$a->class_name,ENT_QUOTES,'UTF-8');?></td><td><?php echo htmlspecialchars((string)$a->bow_type,ENT_QUOTES,'UTF-8');?></td><td><?php echo htmlspecialchars((string)$a->phone,ENT_QUOTES,'UTF-8');?></td>
</tr><?php endforeach;?></tbody></table></div>
