<?php

\defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

$exportUrl = Route::_(
    'index.php?option=com_jugendtraining'
    . '&task=csvexport.results'
    . '&scope=trainer'
    . '&' . Session::getFormToken() . '=1'
);
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
  <h1 class="mb-0"><?php echo Text::_('COM_JUGENDTRAINING_TRAINER_RESULTS'); ?></h1>

  <a class="btn btn-outline-secondary" href="<?php echo $exportUrl; ?>">
    <?php echo Text::_('COM_JUGENDTRAINING_CSV_EXPORT_ALL_ASSIGNED'); ?>
  </a>
</div>

<div class="table-responsive">
  <table class="table table-striped">
    <thead>
      <tr>
        <th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_RESULT_DATE'); ?></th>
        <th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_ATHLETE'); ?></th>
        <th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_EVENT_NAME'); ?></th>
        <th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_DISTANCE'); ?></th>
        <th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_SCORE'); ?></th>
        <th>Ø</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($this->results as $r) : ?>
        <tr>
          <td><?php echo HTMLHelper::_('date', $r->result_date, Text::_('DATE_FORMAT_LC4')); ?></td>
          <td><?php echo htmlspecialchars($r->athlete_name, ENT_QUOTES, 'UTF-8'); ?></td>
          <td>
            <?php echo htmlspecialchars(
                (string) (
                    $r->event_name
                    ?: Text::_('COM_JUGENDTRAINING_EVENT_' . strtoupper($r->event_type))
                ),
                ENT_QUOTES,
                'UTF-8'
            ); ?>
          </td>
          <td><?php echo (int) $r->distance_m; ?> m</td>
          <td><strong><?php echo (int) $r->score; ?></strong></td>
          <td><?php echo number_format((float) $r->average, 2, ',', '.'); ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
