<?php

\defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.multiselect');
?>
<form
    action="<?php echo Route::_('index.php?option=com_jugendtraining&view=trainings'); ?>"
    method="post"
    name="adminForm"
    id="adminForm"
>
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="input-group">
                <input
                    class="form-control"
                    type="search"
                    name="filter_search"
                    value="<?php echo htmlspecialchars((string) $this->state->get('filter.search'), ENT_QUOTES, 'UTF-8'); ?>"
                    placeholder="<?php echo Text::_('COM_JUGENDTRAINING_SEARCH_TRAININGS'); ?>"
                >
                <button class="btn btn-primary" type="submit">
                    <?php echo Text::_('JSEARCH_FILTER_SUBMIT'); ?>
                </button>
            </div>
        </div>
    </div>

    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th width="1%">
                    <input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this)">
                </th>
                <th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_TRAINING_DATE'); ?></th>
                <th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_TRAINING_TITLE'); ?></th>
                <th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_LOCATION'); ?></th>
                <th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_TRAINER'); ?></th>
                <th><?php echo Text::_('COM_JUGENDTRAINING_ATTENDANCE'); ?></th>
                <th><?php echo Text::_('COM_JUGENDTRAINING_COLUMN_STATUS'); ?></th>
                <th>ID</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($this->items as $i => $item) : ?>
            <tr>
                <td><?php echo HTMLHelper::_('grid.id', $i, $item->id); ?></td>
                <td>
                    <?php echo HTMLHelper::_('date', $item->training_date, Text::_('DATE_FORMAT_LC4')); ?>
                    <?php if ($item->start_time) : ?>
                        <div class="small text-muted">
                            <?php echo htmlspecialchars(substr((string) $item->start_time, 0, 5), ENT_QUOTES, 'UTF-8'); ?>
                            <?php if ($item->end_time) : ?>
                                – <?php echo htmlspecialchars(substr((string) $item->end_time, 0, 5), ENT_QUOTES, 'UTF-8'); ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="<?php echo Route::_('index.php?option=com_jugendtraining&task=training.edit&id=' . (int) $item->id); ?>">
                        <?php echo htmlspecialchars((string) $item->title, ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                </td>
                <td><?php echo htmlspecialchars((string) ($item->location ?: '–'), ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars((string) ($item->trainer_name ?: '–'), ENT_QUOTES, 'UTF-8'); ?></td>
                <td>
                    <span class="badge bg-success" title="<?php echo Text::_('COM_JUGENDTRAINING_ATTENDANCE_PRESENT'); ?>">
                        <?php echo (int) $item->present_total; ?>
                    </span>
                    <span class="badge bg-info text-dark" title="<?php echo Text::_('COM_JUGENDTRAINING_ATTENDANCE_LATE'); ?>">
                        <?php echo (int) $item->late_total; ?>
                    </span>
                    <span class="badge bg-warning text-dark" title="<?php echo Text::_('COM_JUGENDTRAINING_ATTENDANCE_EXCUSED'); ?>">
                        <?php echo (int) $item->excused_total; ?>
                    </span>
                    <span class="badge bg-danger" title="<?php echo Text::_('COM_JUGENDTRAINING_ATTENDANCE_ABSENT'); ?>">
                        <?php echo (int) $item->absent_total; ?>
                    </span>
                </td>
                <td>
                    <?php echo $item->published ? Text::_('COM_JUGENDTRAINING_STATUS_PUBLISHED') : Text::_('COM_JUGENDTRAINING_STATUS_UNPUBLISHED'); ?>
                </td>
                <td><?php echo (int) $item->id; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <?php echo $this->pagination->getListFooter(); ?>

    <input type="hidden" name="task" value="">
    <input type="hidden" name="boxchecked" value="0">
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
