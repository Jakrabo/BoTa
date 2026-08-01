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
    <div class="row g-2 mb-3 align-items-end">
        <div class="col-lg-4">
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
        <div class="col-md-4 col-lg-2"><label class="form-label" for="filter_group_id"><?php echo Text::_('COM_JUGENDTRAINING_TRAINING_GROUP'); ?></label><select class="form-select" id="filter_group_id" name="filter_group_id"><option value="0"><?php echo Text::_('COM_JUGENDTRAINING_ALL_TRAINING_GROUPS'); ?></option><?php foreach($this->trainingGroups as$group):?><option value="<?php echo(int)$group->id;?>" <?php echo(int)$this->state->get('filter.group_id')===(int)$group->id?'selected':'';?>><?php echo htmlspecialchars((string)$group->title,ENT_QUOTES,'UTF-8');?></option><?php endforeach;?></select></div>
        <div class="col-md-4 col-lg-2"><label class="form-label" for="filter_location_id"><?php echo Text::_('COM_JUGENDTRAINING_FIELD_TRAINING_LOCATION'); ?></label><select class="form-select" id="filter_location_id" name="filter_location_id"><option value="0"><?php echo Text::_('COM_JUGENDTRAINING_ALL_TRAINING_LOCATIONS'); ?></option><?php foreach($this->trainingLocations as$location):?><option value="<?php echo(int)$location->id;?>" <?php echo(int)$this->state->get('filter.location_id')===(int)$location->id?'selected':'';?>><?php echo htmlspecialchars((string)$location->name,ENT_QUOTES,'UTF-8');?></option><?php endforeach;?></select></div>
        <div class="col-md-4 col-lg-2"><label class="form-label" for="filter_status"><?php echo Text::_('COM_JUGENDTRAINING_COLUMN_STATUS'); ?></label><select class="form-select" id="filter_status" name="filter_status"><option value=""><?php echo Text::_('JOPTION_SELECT_PUBLISHED'); ?></option><?php foreach(['planned'=>'COM_JUGENDTRAINING_STATUS_PLANNED','cancelled'=>'COM_JUGENDTRAINING_STATUS_CANCELLED','unpublished'=>'COM_JUGENDTRAINING_STATUS_UNPUBLISHED'] as$value=>$label):?><option value="<?php echo$value;?>" <?php echo(string)$this->state->get('filter.status')===$value?'selected':'';?>><?php echo Text::_($label);?></option><?php endforeach;?></select></div>
        <div class="col-md-6 col-lg-1"><label class="form-label" for="filter_date_from"><?php echo Text::_('COM_JUGENDTRAINING_DATE_FROM'); ?></label><input class="form-control" type="date" id="filter_date_from" name="filter_date_from" value="<?php echo htmlspecialchars((string)$this->state->get('filter.date_from'),ENT_QUOTES,'UTF-8');?>"></div>
        <div class="col-md-6 col-lg-1"><label class="form-label" for="filter_date_to"><?php echo Text::_('COM_JUGENDTRAINING_DATE_TO'); ?></label><input class="form-control" type="date" id="filter_date_to" name="filter_date_to" value="<?php echo htmlspecialchars((string)$this->state->get('filter.date_to'),ENT_QUOTES,'UTF-8');?>"></div>
    </div>

    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th width="1%">
                    <input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this)">
                </th>
                <th><?php echo HTMLHelper::_('grid.sort','COM_JUGENDTRAINING_FIELD_TRAINING_DATE','s.training_date',$this->state->get('list.direction'),$this->state->get('list.ordering')); ?></th>
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
                <td><?php echo htmlspecialchars((string) ($item->location_name ?: '–'), ENT_QUOTES, 'UTF-8'); ?></td>
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
                    <?php echo Text::_((int)($item->cancelled??0)===1?'COM_JUGENDTRAINING_STATUS_CANCELLED':($item->published?'COM_JUGENDTRAINING_STATUS_PLANNED':'COM_JUGENDTRAINING_STATUS_UNPUBLISHED')); ?>
                </td>
                <td><?php echo (int) $item->id; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <?php echo $this->pagination->getListFooter(); ?>

    <input type="hidden" name="task" value="">
    <input type="hidden" name="boxchecked" value="0">
    <input type="hidden" name="filter_order" value="<?php echo htmlspecialchars((string)$this->state->get('list.ordering'),ENT_QUOTES,'UTF-8'); ?>">
    <input type="hidden" name="filter_order_Dir" value="<?php echo htmlspecialchars((string)$this->state->get('list.direction'),ENT_QUOTES,'UTF-8'); ?>">
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
