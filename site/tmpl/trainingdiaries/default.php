<?php

\defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

$exportUrl = Route::_(
    'index.php?option=com_jugendtraining'
    . '&task=csvexport.diary'
    . '&scope=athlete'
    . '&' . Session::getFormToken() . '=1'
);
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
  <h1 class="mb-0"><?php echo Text::_('COM_JUGENDTRAINING_TRAINING_DIARY'); ?></h1>

  <div class="d-flex flex-wrap gap-2">
    <a class="btn btn-outline-secondary" href="<?php echo $exportUrl; ?>">
      <?php echo Text::_('COM_JUGENDTRAINING_CSV_EXPORT'); ?>
    </a>

    <a
      class="btn btn-primary"
      href="<?php echo Route::_('index.php?option=com_jugendtraining&task=trainingdiary.add'); ?>"
    >
      <?php echo Text::_('COM_JUGENDTRAINING_DIARY_NEW'); ?>
    </a>
  </div>
</div>

<?php foreach ($this->items as $d) : ?>
  <div class="card mb-3">
    <div class="card-body">
      <div class="d-flex justify-content-between">
        <div>
          <h2 class="h5">
            <?php echo HTMLHelper::_('date', $d->training_date, Text::_('DATE_FORMAT_LC4')); ?>
            ·
            <?php echo htmlspecialchars((string) $d->training_method, ENT_QUOTES, 'UTF-8'); ?>
          </h2>
          <p>
            <?php echo (int) $d->arrow_count; ?>
            <?php echo Text::_('COM_JUGENDTRAINING_ARROWS'); ?>
            ·
            <?php echo (int) $d->duration_minutes; ?>
            <?php echo Text::_('COM_JUGENDTRAINING_MINUTES'); ?>
          </p>
        </div>

        <div>
          <a
            class="btn btn-sm btn-outline-primary"
            href="<?php echo Route::_(
                'index.php?option=com_jugendtraining'
                . '&task=trainingdiary.edit'
                . '&id=' . (int) $d->id
            ); ?>"
          >
            <?php echo Text::_('COM_JUGENDTRAINING_BUTTON_EDIT'); ?>
          </a>

          <a
            class="btn btn-sm btn-outline-danger"
            href="<?php echo Route::_(
                'index.php?option=com_jugendtraining'
                . '&task=trainingdiary.delete'
                . '&id=' . (int) $d->id
                . '&' . Session::getFormToken() . '=1'
            ); ?>"
          >
            <?php echo Text::_('COM_JUGENDTRAINING_BUTTON_DELETE'); ?>
          </a>
        </div>
      </div>

      <p><?php echo nl2br(htmlspecialchars((string) $d->notes, ENT_QUOTES, 'UTF-8')); ?></p>
    </div>
  </div>
<?php endforeach; ?>
