<?php
\defined('_JEXEC') or die;
use Joomla\CMS\HTML\HTMLHelper;use Joomla\CMS\Language\Text;use Joomla\CMS\Router\Route;
HTMLHelper::_('behavior.multiselect');
?>
<form action="<?php echo Route::_('index.php?option=com_jugendtraining&view=traininggroups');?>" method="post" name="adminForm" id="adminForm">
<table class="table table-striped"><thead><tr><th width="1%"><input type="checkbox" name="checkall-toggle" onclick="Joomla.checkAll(this)"></th><th><?php echo Text::_('COM_JUGENDTRAINING_COLUMN_TITLE');?></th><th><?php echo Text::_('COM_JUGENDTRAINING_GROUP_ATHLETES');?></th><th><?php echo Text::_('COM_JUGENDTRAINING_GROUP_TRAINERS');?></th><th>ID</th></tr></thead><tbody>
<?php foreach($this->items as $i=>$item):?><tr><td><?php echo HTMLHelper::_('grid.id',$i,$item->id);?></td><td><a href="<?php echo Route::_('index.php?option=com_jugendtraining&task=traininggroup.edit&id='.(int)$item->id);?>"><?php echo htmlspecialchars($item->title,ENT_QUOTES,'UTF-8');?></a></td><td><?php echo (int)$item->athlete_count;?></td><td><?php echo (int)$item->trainer_count;?></td><td><?php echo (int)$item->id;?></td></tr><?php endforeach;?>
</tbody></table><?php echo $this->pagination->getListFooter();?><input type="hidden" name="task" value=""><input type="hidden" name="boxchecked" value="0"><?php echo HTMLHelper::_('form.token');?>
</form>
