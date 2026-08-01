<?php

\defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

$exportUrl = Route::_(
    'index.php?option=com_jugendtraining'
    . '&task=csvexport.diary'
    . '&scope=trainer'
    . '&' . Session::getFormToken() . '=1'
);
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
  <h1 class="mb-0"><?php echo Text::_('COM_JUGENDTRAINING_TRAINER_DIARIES'); ?></h1>

  <a class="btn btn-outline-secondary" href="<?php echo $exportUrl; ?>">
    <?php echo Text::_('COM_JUGENDTRAINING_CSV_EXPORT_ALL_ASSIGNED'); ?>
  </a>
</div>

<div class="table-responsive">
  <table class="table table-striped jt-mobile-card-table">
    <thead>
      <tr>
        <th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_TRAINING_DATE'); ?></th>
        <th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_ATHLETE'); ?></th>
        <th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_TRAINING_METHOD'); ?></th>
        <th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_ARROW_COUNT'); ?></th>
        <th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_DURATION_MINUTES'); ?></th>
        <th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_BOW_SETUP'); ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($this->diaries as $d) : ?>
        <tr>
          <td data-label="<?php echo Text::_('COM_JUGENDTRAINING_FIELD_TRAINING_DATE'); ?>"><?php echo HTMLHelper::_('date', $d->training_date, Text::_('DATE_FORMAT_LC4')); ?></td>
          <td data-label="<?php echo Text::_('COM_JUGENDTRAINING_FIELD_ATHLETE'); ?>"><?php echo htmlspecialchars($d->athlete_name, ENT_QUOTES, 'UTF-8'); ?></td>
          <td data-label="<?php echo Text::_('COM_JUGENDTRAINING_FIELD_TRAINING_METHOD'); ?>"><?php echo htmlspecialchars((string) $d->training_method, ENT_QUOTES, 'UTF-8'); ?></td>
          <td data-label="<?php echo Text::_('COM_JUGENDTRAINING_FIELD_ARROW_COUNT'); ?>"><?php echo (int) $d->arrow_count; ?></td>
          <td data-label="<?php echo Text::_('COM_JUGENDTRAINING_FIELD_DURATION_MINUTES'); ?>"><?php echo (int) $d->duration_minutes; ?></td>
          <td data-label="<?php echo Text::_('COM_JUGENDTRAINING_FIELD_BOW_SETUP'); ?>"><?php echo htmlspecialchars((string) $d->setup_title, ENT_QUOTES, 'UTF-8'); ?></td>
        </tr>
        <tr>
          <td colspan="6" data-label="<?php echo Text::_('COM_JUGENDTRAINING_FIELD_NOTES'); ?>">
            <?php echo nl2br(htmlspecialchars((string) $d->notes, ENT_QUOTES, 'UTF-8')); ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
