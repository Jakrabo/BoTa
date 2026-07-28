<?php
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

Factory::getApplication()->getDocument()->getWebAssetManager()
    ->useScript('keepalive')
    ->useScript('form.validate');

$help = [
    'score' => Text::_('COM_JUGENDTRAINING_GOAL_HELP_SCORE'),
    'average' => Text::_('COM_JUGENDTRAINING_GOAL_HELP_AVERAGE'),
    'attendance' => Text::_('COM_JUGENDTRAINING_GOAL_HELP_ATTENDANCE'),
    'program' => Text::_('COM_JUGENDTRAINING_GOAL_HELP_PROGRAM'),
    'custom' => Text::_('COM_JUGENDTRAINING_GOAL_HELP_CUSTOM'),
];

Factory::getDocument()->addScriptOptions('com_jugendtraining.goalHelp', $help);
?>
<form
  action="<?php echo Route::_('index.php?option=com_jugendtraining&view=goal&layout=edit&id=' . (int) $this->item->id); ?>"
  method="post"
  name="adminForm"
  id="adminForm"
  class="form-validate"
>
  <div class="alert alert-info">
    <h2 class="h4"><?php echo Text::_('COM_JUGENDTRAINING_GOAL_METRICS_HELP_TITLE'); ?></h2>
    <p class="mb-2"><?php echo Text::_('COM_JUGENDTRAINING_GOAL_METRICS_HELP_INTRO'); ?></p>
    <ul class="mb-0">
      <li><strong><?php echo Text::_('COM_JUGENDTRAINING_TARGET_ATTENDANCE'); ?>:</strong> <?php echo Text::_('COM_JUGENDTRAINING_GOAL_HELP_ATTENDANCE'); ?></li>
      <li><strong><?php echo Text::_('COM_JUGENDTRAINING_TARGET_SCORE'); ?>:</strong> <?php echo Text::_('COM_JUGENDTRAINING_GOAL_HELP_SCORE'); ?></li>
      <li><strong><?php echo Text::_('COM_JUGENDTRAINING_TARGET_AVERAGE'); ?>:</strong> <?php echo Text::_('COM_JUGENDTRAINING_GOAL_HELP_AVERAGE'); ?></li>
      <li><strong><?php echo Text::_('COM_JUGENDTRAINING_TARGET_PROGRAM'); ?>:</strong> <?php echo Text::_('COM_JUGENDTRAINING_GOAL_HELP_PROGRAM'); ?></li>
      <li><strong><?php echo Text::_('COM_JUGENDTRAINING_TARGET_CUSTOM'); ?>:</strong> <?php echo Text::_('COM_JUGENDTRAINING_GOAL_HELP_CUSTOM'); ?></li>
    </ul>
  </div>

  <div id="jt-goal-context-help" class="alert alert-secondary" aria-live="polite"></div>

  <div class="card">
    <div class="card-body">
      <?php echo $this->form->renderFieldset('details'); ?>
    </div>
  </div>

  <input type="hidden" name="task" value="">
  <?php echo HTMLHelper::_('form.token'); ?>
</form>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const help = Joomla.getOptions('com_jugendtraining.goalHelp', {});
    const targetType = document.getElementById('jform_target_type');
    const context = document.getElementById('jt-goal-context-help');
    const current = document.getElementById('jform_current_value');
    const mode = document.getElementById('jform_calculation_mode');

    window.jtGoalHelp = (value) => {
        if (context) {
            context.textContent = help[value] || '';
        }

        const automatic = mode && mode.value === 'automatic' && value !== 'custom';

        if (current) {
            current.readOnly = automatic;
            current.closest('.control-group, .mb-3')?.classList.toggle('opacity-75', automatic);
        }
    };

    targetType?.addEventListener('change', event => window.jtGoalHelp(event.target.value));
    mode?.addEventListener('change', () => window.jtGoalHelp(targetType?.value || 'custom'));

    window.jtGoalHelp(targetType?.value || 'custom');
});
</script>
