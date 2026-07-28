<?php
\defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.multiselect');
?>
<div class="alert alert-info">
  <strong><?php echo Text::_('COM_JUGENDTRAINING_GOAL_METRICS_HELP_TITLE'); ?>:</strong>
  <?php echo Text::_('COM_JUGENDTRAINING_GOAL_LIST_HELP'); ?>
</div>

<form action="<?php echo Route::_('index.php?option=com_jugendtraining&view=goals'); ?>" method="post" name="adminForm" id="adminForm">
  <div class="mb-3">
    <input class="form-control" type="search" name="filter_search"
      value="<?php echo htmlspecialchars((string) $this->state->get('filter.search'), ENT_QUOTES, 'UTF-8'); ?>"
      placeholder="<?php echo Text::_('COM_JUGENDTRAINING_SEARCH_GOALS'); ?>">
  </div>

  <table class="table table-striped align-middle">
    <thead>
      <tr>
        <th width="1%"><input type="checkbox" name="checkall-toggle" onclick="Joomla.checkAll(this)"></th>
        <th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_ATHLETE'); ?></th>
        <th><?php echo Text::_('COM_JUGENDTRAINING_COLUMN_TITLE'); ?></th>
        <th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_TARGET_TYPE'); ?></th>
        <th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_PROGRESS'); ?></th>
        <th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_DUE_DATE'); ?></th>
        <th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_COMPLETED'); ?></th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($this->items as $i => $item) :
      $percent = (float) $item->target_value > 0
        ? min(100, round((float) $item->current_value * 100 / (float) $item->target_value))
        : 0;
    ?>
      <tr>
        <td><?php echo HTMLHelper::_('grid.id', $i, $item->id); ?></td>
        <td><?php echo htmlspecialchars($item->athlete_name, ENT_QUOTES, 'UTF-8'); ?></td>
        <td>
          <a href="<?php echo Route::_('index.php?option=com_jugendtraining&task=goal.edit&id=' . (int) $item->id); ?>">
            <?php echo htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8'); ?>
          </a>
        </td>
        <td>
          <?php echo Text::_('COM_JUGENDTRAINING_TARGET_' . strtoupper($item->target_type)); ?>
          <div class="small text-muted">
            <?php echo Text::_('COM_JUGENDTRAINING_CALCULATION_' . strtoupper($item->calculation_mode)); ?>
          </div>
        </td>
        <td>
          <strong><?php echo number_format((float) $item->current_value, 2, ',', '.'); ?></strong>
          /
          <?php echo number_format((float) $item->target_value, 2, ',', '.'); ?>
          <div class="progress mt-1" style="min-width:140px">
            <div class="progress-bar" style="width:<?php echo $percent; ?>%"><?php echo $percent; ?> %</div>
          </div>
        </td>
        <td><?php echo $item->due_date ? HTMLHelper::_('date', $item->due_date, Text::_('DATE_FORMAT_LC4')) : '–'; ?></td>
        <td><?php echo (int) $item->completed ? Text::_('COM_JUGENDTRAINING_YES') : Text::_('COM_JUGENDTRAINING_NO'); ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>

  <?php echo $this->pagination->getListFooter(); ?>
  <input type="hidden" name="task" value="">
  <input type="hidden" name="boxchecked" value="0">
  <?php echo HTMLHelper::_('form.token'); ?>
</form>
