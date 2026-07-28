<?php

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->useScript('keepalive')->useScript('form.validate')->useScript('showon');

$statusOptions = [
    '' => Text::_('COM_JUGENDTRAINING_ATTENDANCE_NOT_RECORDED'),
    'present' => Text::_('COM_JUGENDTRAINING_ATTENDANCE_PRESENT'),
    'late' => Text::_('COM_JUGENDTRAINING_ATTENDANCE_LATE'),
    'excused' => Text::_('COM_JUGENDTRAINING_ATTENDANCE_EXCUSED'),
    'absent' => Text::_('COM_JUGENDTRAINING_ATTENDANCE_ABSENT'),
];
?>
<form
    action="<?php echo Route::_('index.php?option=com_jugendtraining&view=training&layout=edit&id=' . (int) $this->item->id); ?>"
    method="post"
    name="adminForm"
    id="adminForm"
    class="form-validate"
>
    <div class="row g-4">
        <div class="col-xl-5">
            <div class="card">
                <div class="card-header">
                    <?php echo Text::_('COM_JUGENDTRAINING_TRAINING_DETAILS'); ?>
                </div>
                <div class="card-body">
                    <?php echo $this->form->renderFieldset('details'); ?>
                </div>
            </div>
        </div>

        <div class="col-xl-7">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><?php echo Text::_('COM_JUGENDTRAINING_ATTENDANCE'); ?></span>
                    <span class="badge bg-secondary">
                        <?php echo count($this->athletes); ?> <?php echo Text::_('COM_JUGENDTRAINING_ATHLETES'); ?>
                    </span>
                </div>
                <div class="card-body">
                    <?php if (!$this->item->id) : ?>
                        <div class="alert alert-info mb-0">
                            <?php echo Text::_('COM_JUGENDTRAINING_SAVE_TRAINING_FOR_ROSTER'); ?>
                        </div>
                    <?php elseif (!$this->athletes) : ?>
                        <div class="alert alert-info mb-0">
                            <?php echo Text::_('COM_JUGENDTRAINING_NO_ACTIVE_ATHLETES'); ?>
                        </div>
                    <?php else : ?>
                        <div class="mb-3 d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-sm btn-outline-success jt-set-all" data-status="present">
                                <?php echo Text::_('COM_JUGENDTRAINING_MARK_ALL_PRESENT'); ?>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary jt-set-all" data-status="">
                                <?php echo Text::_('COM_JUGENDTRAINING_CLEAR_ATTENDANCE'); ?>
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th><?php echo Text::_('COM_JUGENDTRAINING_NAME'); ?></th>
                                        <th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_CLASS'); ?></th>
                                        <th style="min-width: 175px;"><?php echo Text::_('COM_JUGENDTRAINING_ATTENDANCE_STATUS'); ?></th>
                                        <th style="min-width: 220px;"><?php echo Text::_('COM_JUGENDTRAINING_ATTENDANCE_COMMENT'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($this->athletes as $athlete) :
                                    $athleteId = (int) $athlete['id'];
                                    $entry = $this->attendance[$athleteId] ?? ['status' => '', 'comment' => ''];
                                ?>
                                    <tr>
                                        <td>
                                            <strong>
                                                <?php echo htmlspecialchars(
                                                    $athlete['firstname'] . ' ' . $athlete['lastname'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ); ?>
                                            </strong>
                                            <div class="small text-muted">
                                                <?php echo htmlspecialchars(
                                                    implode(' · ', array_filter([
                                                        $athlete['club_name'] ?: null,
                                                        $athlete['bow_type'] ?: null,
                                                    ])) ?: '–',
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars((string) ($athlete['class_name'] ?: '–'), ENT_QUOTES, 'UTF-8'); ?>
                                        </td>
                                        <td>
                                            <select
                                                class="form-select form-select-sm jt-attendance-status"
                                                name="attendance[<?php echo $athleteId; ?>][status]"
                                            >
                                                <?php foreach ($statusOptions as $value => $label) : ?>
                                                    <option
                                                        value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>"
                                                        <?php echo $entry['status'] === $value ? 'selected' : ''; ?>
                                                    >
                                                        <?php echo $label; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <input
                                                type="text"
                                                class="form-control form-control-sm"
                                                maxlength="500"
                                                name="attendance[<?php echo $athleteId; ?>][comment]"
                                                value="<?php echo htmlspecialchars((string) $entry['comment'], ENT_QUOTES, 'UTF-8'); ?>"
                                                placeholder="<?php echo Text::_('COM_JUGENDTRAINING_ATTENDANCE_COMMENT_PLACEHOLDER'); ?>"
                                            >
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="task" value="">
    <?php echo HTMLHelper::_('form.token'); ?>
</form>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.jt-set-all').forEach((button) => {
        button.addEventListener('click', () => {
            const status = button.dataset.status ?? '';

            document.querySelectorAll('.jt-attendance-status').forEach((select) => {
                select.value = status;
                select.dispatchEvent(new Event('change', { bubbles: true }));
            });
        });
    });
});
</script>
