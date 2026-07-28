<?php
\defined('_JEXEC') or die;
use Joomla\CMS\HTML\HTMLHelper; use Joomla\CMS\Language\Text; use Joomla\CMS\Router\Route;
HTMLHelper::_('behavior.multiselect');
?>
<form action="<?php echo Route::_('index.php?option=com_jugendtraining&view=sportyears'); ?>" method="post" name="adminForm" id="adminForm">
<div class="row mb-3"><div class="col-md-6"><input class="form-control" type="search" name="filter_search" value="<?php echo htmlspecialchars((string)$this->state->get('filter.search')); ?>" placeholder="<?php echo Text::_('JSEARCH_FILTER'); ?>"><button class="btn btn-primary mt-2" type="submit"><?php echo Text::_('JSEARCH_FILTER_SUBMIT'); ?></button></div></div>
<table class="table table-striped"><thead><tr><th width="1%"><input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this)"></th><th><?php echo Text::_('COM_JUGENDTRAINING_COLUMN_TITLE'); ?></th><th><?php echo Text::_('COM_JUGENDTRAINING_PERIOD'); ?></th><th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_CURRENT'); ?></th><th><?php echo Text::_('COM_JUGENDTRAINING_COLUMN_STATUS'); ?></th><th>ID</th></tr></thead><tbody>
<?php foreach($this->items as $i=>$item): ?><tr><td><?php echo HTMLHelper::_('grid.id',$i,$item->id); ?></td><td><a href="<?php echo Route::_('index.php?option=com_jugendtraining&task=sportyear.edit&id='.(int)$item->id); ?>"><?php echo htmlspecialchars((string)($item->name),ENT_QUOTES,'UTF-8'); ?></a></td><td><?php echo htmlspecialchars((string)($item->date_start . ' – ' . $item->date_end), ENT_QUOTES, 'UTF-8'); ?></td><td><?php echo htmlspecialchars((string)($item->is_current ? Text::_('COM_JUGENDTRAINING_YES') : Text::_('COM_JUGENDTRAINING_NO')), ENT_QUOTES, 'UTF-8'); ?></td><td><?php echo htmlspecialchars((string)($item->published ? Text::_('COM_JUGENDTRAINING_STATUS_PUBLISHED') : Text::_('COM_JUGENDTRAINING_STATUS_UNPUBLISHED')), ENT_QUOTES, 'UTF-8'); ?></td><td><?php echo (int)$item->id; ?></td></tr><?php endforeach; ?>
</tbody></table><?php echo $this->pagination->getListFooter(); ?>
<input type="hidden" name="task" value=""><input type="hidden" name="boxchecked" value="0"><?php echo HTMLHelper::_('form.token'); ?></form>
