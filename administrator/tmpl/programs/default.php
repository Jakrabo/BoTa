<?php
\defined('_JEXEC') or die;
use Joomla\CMS\HTML\HTMLHelper;use Joomla\CMS\Language\Text;use Joomla\CMS\Router\Route;
HTMLHelper::_('behavior.multiselect');
?>
<form action="<?php echo Route::_('index.php?option=com_jugendtraining&view=programs'); ?>" method="post" name="adminForm" id="adminForm">
 <div class="row g-2 mb-3"><div class="col-md-10"><input class="form-control" type="search" name="filter_search" value="<?php echo htmlspecialchars((string)$this->state->get('filter.search'),ENT_QUOTES,'UTF-8'); ?>" placeholder="<?php echo Text::_('COM_JUGENDTRAINING_SEARCH_PROGRAMS'); ?>"></div><div class="col-md-2"><button class="btn btn-primary w-100" type="submit"><?php echo Text::_('JSEARCH_FILTER_SUBMIT');?></button></div></div>
 <table class="table table-striped align-middle"><thead><tr><th width="1%"><input type="checkbox" name="checkall-toggle" onclick="Joomla.checkAll(this)"></th><th><?php echo Text::_('COM_JUGENDTRAINING_COLUMN_TITLE');?></th><th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_CATEGORY');?></th><th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_EXERCISES');?></th><th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_ASSIGNED_ATHLETES');?></th><th>ID</th></tr></thead><tbody>
 <?php foreach($this->items as $i=>$item):?><tr><td><?php echo HTMLHelper::_('grid.id',$i,$item->id);?></td><td><a href="<?php echo Route::_('index.php?option=com_jugendtraining&task=program.edit&id='.(int)$item->id);?>"><?php echo htmlspecialchars($item->title,ENT_QUOTES,'UTF-8');?></a></td><td><?php echo Text::_('COM_JUGENDTRAINING_CATEGORY_'.strtoupper($item->category));?></td><td><?php echo (int)$item->exercise_count;?></td><td><?php echo (int)$item->athlete_count;?></td><td><?php echo (int)$item->id;?></td></tr><?php endforeach;?>
 </tbody></table><?php echo $this->pagination->getListFooter();?><input type="hidden" name="task" value=""><input type="hidden" name="boxchecked" value="0"><?php echo HTMLHelper::_('form.token');?>
</form>
