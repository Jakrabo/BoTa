<?php
\defined('_JEXEC') or die;
use Joomla\CMS\Factory;use Joomla\CMS\HTML\HTMLHelper;use Joomla\CMS\Router\Route;
Factory::getApplication()->getDocument()->getWebAssetManager()->useScript('keepalive')->useScript('form.validate');
?>
<form action="<?php echo Route::_('index.php?option=com_jugendtraining&view=trainernote&layout=edit&id='.(int)$this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
  <div class="card"><div class="card-body"><?php echo $this->form->renderFieldset('details'); ?></div></div>
  <input type="hidden" name="task" value=""><?php echo HTMLHelper::_('form.token'); ?>
</form>
