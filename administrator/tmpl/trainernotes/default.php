<?php
\defined('_JEXEC') or die;
use Joomla\CMS\HTML\HTMLHelper;use Joomla\CMS\Language\Text;use Joomla\CMS\Router\Route;
HTMLHelper::_('behavior.multiselect');
?>
<form action="<?php echo Route::_('index.php?option=com_jugendtraining&view=trainernotes');?>" method="post" name="adminForm" id="adminForm">
<div class="mb-3"><input class="form-control" type="search" name="filter_search" value="<?php echo htmlspecialchars((string)$this->state->get('filter.search'),ENT_QUOTES,'UTF-8');?>" placeholder="<?php echo Text::_('COM_JUGENDTRAINING_SEARCH_NOTES');?>"></div>
<table class="table table-striped"><thead><tr><th width="1%"><input type="checkbox" name="checkall-toggle" onclick="Joomla.checkAll(this)"></th><th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_NOTE_DATE');?></th><th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_ATHLETE');?></th><th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_CATEGORY');?></th><th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_NOTE');?></th><th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_PRIVATE_NOTE');?></th></tr></thead><tbody>
<?php foreach($this->items as $i=>$item):?><tr><td><?php echo HTMLHelper::_('grid.id',$i,$item->id);?></td><td><?php echo HTMLHelper::_('date',$item->note_date,Text::_('DATE_FORMAT_LC4'));?></td><td><?php echo htmlspecialchars($item->athlete_name,ENT_QUOTES,'UTF-8');?></td><td><?php echo Text::_('COM_JUGENDTRAINING_NOTE_'.strtoupper($item->category));?></td><td><a href="<?php echo Route::_('index.php?option=com_jugendtraining&task=trainernote.edit&id='.(int)$item->id);?>"><?php echo htmlspecialchars(mb_strimwidth(strip_tags($item->note),0,90,'…'),ENT_QUOTES,'UTF-8');?></a></td><td><?php echo (int)$item->private_note?Text::_('COM_JUGENDTRAINING_YES'):Text::_('COM_JUGENDTRAINING_NO');?></td></tr><?php endforeach;?>
</tbody></table><?php echo $this->pagination->getListFooter();?><input type="hidden" name="task" value=""><input type="hidden" name="boxchecked" value="0"><?php echo HTMLHelper::_('form.token');?>
</form>
