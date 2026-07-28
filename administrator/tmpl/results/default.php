<?php

\defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.multiselect');
?>
<form action="<?php echo Route::_('index.php?option=com_jugendtraining&view=results'); ?>" method="post" name="adminForm" id="adminForm">
    <div class="row g-2 mb-3">
        <div class="col-lg-5">
            <input class="form-control" type="search" name="filter_search"
                value="<?php echo htmlspecialchars((string) $this->state->get('filter.search'), ENT_QUOTES, 'UTF-8'); ?>"
                placeholder="<?php echo Text::_('COM_JUGENDTRAINING_SEARCH_RESULTS'); ?>">
        </div>
        <div class="col-lg-3">
            <select class="form-select" name="filter_athlete_id">
                <option value=""><?php echo Text::_('COM_JUGENDTRAINING_ALL_ATHLETES'); ?></option>
                <?php foreach ($this->athleteOptions as $option) : ?>
                    <option value="<?php echo (int) $option->value; ?>"
                        <?php echo (int) $this->state->get('filter.athlete_id') === (int) $option->value ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars((string) $option->text, ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-lg-2">
            <select class="form-select" name="filter_event_type">
                <option value=""><?php echo Text::_('COM_JUGENDTRAINING_ALL_EVENT_TYPES'); ?></option>
                <?php foreach (['training','competition','qualification','other'] as $type) : ?>
                    <option value="<?php echo $type; ?>"
                        <?php echo $this->state->get('filter.event_type') === $type ? 'selected' : ''; ?>>
                        <?php echo Text::_('COM_JUGENDTRAINING_EVENT_' . strtoupper($type)); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-lg-2">
            <button class="btn btn-primary w-100" type="submit"><?php echo Text::_('JSEARCH_FILTER_SUBMIT'); ?></button>
        </div>
    </div>

    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th width="1%"><input type="checkbox" name="checkall-toggle" onclick="Joomla.checkAll(this)"></th>
                <th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_RESULT_DATE'); ?></th>
                <th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_ATHLETE'); ?></th>
                <th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_EVENT_NAME'); ?></th>
                <th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_DISTANCE'); ?></th>
                <th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_ARROWS'); ?></th>
                <th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_SCORE'); ?></th>
                <th><?php echo Text::_('COM_JUGENDTRAINING_AVERAGE_PER_ARROW'); ?></th>
                <th>ID</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($this->items as $i => $item) : ?>
            <tr>
                <td><?php echo HTMLHelper::_('grid.id', $i, $item->id); ?></td>
                <td><?php echo HTMLHelper::_('date', $item->result_date, Text::_('DATE_FORMAT_LC4')); ?></td>
                <td>
                    <a href="<?php echo Route::_('index.php?option=com_jugendtraining&task=result.edit&id=' . (int) $item->id); ?>">
                        <?php echo htmlspecialchars((string) $item->athlete_name, ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                    <div class="small text-muted">
                        <?php echo htmlspecialchars((string) ($item->class_name ?: '–'), ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                </td>
                <td>
                    <?php echo htmlspecialchars((string) ($item->event_name ?: Text::_('COM_JUGENDTRAINING_EVENT_' . strtoupper($item->event_type))), ENT_QUOTES, 'UTF-8'); ?>
                </td>
                <td><?php echo (int) $item->distance_m; ?> m</td>
                <td><?php echo (int) $item->arrows; ?></td>
                <td><strong><?php echo (int) $item->score; ?></strong></td>
                <td><?php echo number_format((float) $item->average, 2, ',', '.'); ?></td>
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
