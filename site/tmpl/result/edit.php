<?php

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->useScript('keepalive')->useScript('form.validate');
?>
<div class="com-jugendtraining-result-edit">
    <h1>
        <?php echo $this->item->id
            ? Text::_('COM_JUGENDTRAINING_EDIT_MY_RESULT')
            : Text::_('COM_JUGENDTRAINING_ADD_MY_RESULT'); ?>
    </h1>

    <form
        action="<?php echo Route::_('index.php?option=com_jugendtraining&view=result&layout=edit&id=' . (int) $this->item->id); ?>"
        method="post"
        name="adminForm"
        id="adminForm"
        class="form-validate"
    >
        <div class="card">
            <div class="card-body">
                <?php echo $this->form->renderFieldset('details'); ?>
            </div>
        </div>

        <div class="mt-3 d-flex gap-2">
            <button class="btn btn-primary" type="button" onclick="Joomla.submitbutton('result.save')">
                <?php echo Text::_('COM_JUGENDTRAINING_BUTTON_SAVE'); ?>
            </button>

            <button class="btn btn-secondary" type="button" onclick="Joomla.submitbutton('result.cancel')">
                <?php echo Text::_('COM_JUGENDTRAINING_BUTTON_CANCEL'); ?>
            </button>
        </div>

        <input type="hidden" name="task" value="">
        <?php echo HTMLHelper::_('form.token'); ?>
    </form>
</div>
