<?php
\defined('_JEXEC') or die;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
$period=(string)($this->trainingFilter->period??'14');
$groupId=(int)($this->trainingFilter->group_id??0);
?>
<div class="d-flex justify-content-between align-items-center mb-3">
 <h1><?php echo Text::_('COM_JUGENDTRAINING_TRAINER_TRAININGS');?></h1>
 <a class="btn btn-primary" href="<?php echo Route::_('index.php?option=com_jugendtraining&task=trainertraining.add');?>"><?php echo Text::_('COM_JUGENDTRAINING_TRAINING_NEW');?></a>
</div>
<form class="card card-body mb-4" method="get" action="<?php echo Route::_('index.php');?>">
 <input type="hidden" name="option" value="com_jugendtraining"><input type="hidden" name="view" value="trainertrainings">
 <div class="row g-3 align-items-end">
  <div class="col-md-5"><label class="form-label" for="jt-training-period"><?php echo Text::_('COM_JUGENDTRAINING_FILTER_PERIOD');?></label>
   <select class="form-select" id="jt-training-period" name="period">
    <option value="14" <?php echo$period==='14'?'selected':'';?>><?php echo Text::_('COM_JUGENDTRAINING_PERIOD_NEXT_14_DAYS');?></option>
    <option value="30" <?php echo$period==='30'?'selected':'';?>><?php echo Text::_('COM_JUGENDTRAINING_PERIOD_NEXT_30_DAYS');?></option>
    <option value="future" <?php echo$period==='future'?'selected':'';?>><?php echo Text::_('COM_JUGENDTRAINING_PERIOD_ALL_FUTURE');?></option>
    <option value="past" <?php echo$period==='past'?'selected':'';?>><?php echo Text::_('COM_JUGENDTRAINING_PERIOD_PAST');?></option>
    <option value="all" <?php echo$period==='all'?'selected':'';?>><?php echo Text::_('COM_JUGENDTRAINING_PERIOD_ALL');?></option>
   </select>
  </div>
  <div class="col-md-5"><label class="form-label" for="jt-training-group"><?php echo Text::_('COM_JUGENDTRAINING_TRAINING_GROUP');?></label>
   <select class="form-select" id="jt-training-group" name="group_id">
    <option value="0"><?php echo Text::_('COM_JUGENDTRAINING_ALL_TRAINING_GROUPS');?></option>
    <?php foreach($this->groups as$g):?><option value="<?php echo(int)$g->id;?>" <?php echo$groupId===(int)$g->id?'selected':'';?>><?php echo htmlspecialchars((string)$g->title,ENT_QUOTES,'UTF-8');?></option><?php endforeach;?>
   </select>
  </div>
  <div class="col-md-2"><button class="btn btn-primary w-100" type="submit"><?php echo Text::_('JFILTER');?></button></div>
 </div>
</form>
<?php if($this->trainings):?>
<div class="table-responsive"><table class="table table-striped"><thead><tr>
<th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_TRAINING_DATE');?></th><th><?php echo Text::_('COM_JUGENDTRAINING_COLUMN_TITLE');?></th><th><?php echo Text::_('COM_JUGENDTRAINING_TRAINING_GROUP');?></th><th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_LOCATION');?></th><th></th>
</tr></thead><tbody>
<?php foreach($this->trainings as$t):?><tr>
<td><?php echo HTMLHelper::_('date',$t->training_date,Text::_('DATE_FORMAT_LC4'));?><?php if($t->start_time):?><div class="small text-muted"><?php echo htmlspecialchars(substr((string)$t->start_time,0,5),ENT_QUOTES,'UTF-8');?> Uhr</div><?php endif;?></td>
<td><?php echo htmlspecialchars((string)$t->title,ENT_QUOTES,'UTF-8');?></td><td><?php echo htmlspecialchars((string)$t->group_title,ENT_QUOTES,'UTF-8');?></td><td><?php echo htmlspecialchars((string)$t->location,ENT_QUOTES,'UTF-8');?></td>
<td class="text-nowrap"><a class="btn btn-sm btn-outline-primary" href="<?php echo Route::_('index.php?option=com_jugendtraining&task=trainertraining.edit&id='.(int)$t->id);?>"><?php echo Text::_('COM_JUGENDTRAINING_BUTTON_EDIT');?></a>
<a class="btn btn-sm btn-outline-danger" onclick="return confirm('<?php echo Text::_('COM_JUGENDTRAINING_CONFIRM_DELETE_TRAINING');?>');" href="<?php echo Route::_('index.php?option=com_jugendtraining&task=trainertraining.delete&id='.(int)$t->id.'&'.Session::getFormToken().'=1');?>"><?php echo Text::_('COM_JUGENDTRAINING_BUTTON_DELETE');?></a></td>
</tr><?php endforeach;?></tbody></table></div>
<?php else:?><div class="alert alert-info"><?php echo Text::_('COM_JUGENDTRAINING_NO_TRAININGS_FILTER');?></div><?php endif;?>
