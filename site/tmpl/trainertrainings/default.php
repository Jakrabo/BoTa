<?php
\defined('_JEXEC') or die;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1><?php echo Text::_('COM_JUGENDTRAINING_TRAINER_TRAININGS');?></h1>
  <a class="btn btn-primary" href="<?php echo Route::_('index.php?option=com_jugendtraining&task=trainertraining.add'); ?>">
    <?php echo Text::_('COM_JUGENDTRAINING_TRAINING_NEW'); ?>
  </a>
</div>
<div class="table-responsive"><table class="table table-striped"><thead><tr>
<th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_TRAINING_DATE');?></th>
<th><?php echo Text::_('COM_JUGENDTRAINING_COLUMN_TITLE');?></th>
<th><?php echo Text::_('COM_JUGENDTRAINING_TRAINING_GROUP');?></th>
<th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_LOCATION');?></th><th></th>
</tr></thead><tbody>
<?php foreach($this->trainings as $t):?><tr>
<td><?php echo HTMLHelper::_('date',$t->training_date,Text::_('DATE_FORMAT_LC4'));?></td>
<td><?php echo htmlspecialchars($t->title,ENT_QUOTES,'UTF-8');?></td>
<td><?php echo htmlspecialchars((string)$t->group_title,ENT_QUOTES,'UTF-8');?></td>
<td><?php echo htmlspecialchars((string)$t->location,ENT_QUOTES,'UTF-8');?></td>
<td class="text-nowrap">
<a class="btn btn-sm btn-outline-primary" href="<?php echo Route::_('index.php?option=com_jugendtraining&task=trainertraining.edit&id='.(int)$t->id);?>"><?php echo Text::_('COM_JUGENDTRAINING_BUTTON_EDIT');?></a>
<a class="btn btn-sm btn-outline-danger" onclick="return confirm('<?php echo Text::_('COM_JUGENDTRAINING_CONFIRM_DELETE_TRAINING');?>');" href="<?php echo Route::_('index.php?option=com_jugendtraining&task=trainertraining.delete&id='.(int)$t->id.'&'.Session::getFormToken().'=1');?>"><?php echo Text::_('COM_JUGENDTRAINING_BUTTON_DELETE');?></a>
</td></tr><?php endforeach;?>
</tbody></table></div>
