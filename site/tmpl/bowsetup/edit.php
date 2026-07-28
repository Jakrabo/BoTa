<?php
\defined('_JEXEC') or die;
use Joomla\CMS\Factory;use Joomla\CMS\HTML\HTMLHelper;use Joomla\CMS\Language\Text;use Joomla\CMS\Router\Route;
Factory::getApplication()->getDocument()->getWebAssetManager()->useScript('keepalive')->useScript('form.validate')->useScript('showon');
$rows=$this->sightSettings?:[];
?>
<h1><?php echo $this->item->id?Text::_('COM_JUGENDTRAINING_CREATE_REVISION'):Text::_('COM_JUGENDTRAINING_SETUP_NEW');?></h1>
<div class="alert alert-info"><?php echo Text::_('COM_JUGENDTRAINING_REVISION_NOTICE');?></div>
<form action="<?php echo Route::_('index.php?option=com_jugendtraining&view=bowsetup&layout=edit&id='.(int)$this->item->id);?>" method="post" name="adminForm" id="adminForm" class="form-validate">
<?php foreach([
['general','COM_JUGENDTRAINING_SETUP_GENERAL'],['bow','COM_JUGENDTRAINING_SETUP_BOW'],['button','COM_JUGENDTRAINING_SETUP_BUTTON'],
['string','COM_JUGENDTRAINING_SETUP_STRING'],['arrows','COM_JUGENDTRAINING_SETUP_ARROWS'],['stabilizers','COM_JUGENDTRAINING_SETUP_STABILIZERS'],['measurements','COM_JUGENDTRAINING_SETUP_MEASUREMENTS']
] as [$fieldset,$label]):?>
<section class="card mb-4 jt-setup-section"><div class="card-header"><h2 class="h5 mb-0"><?php echo Text::_($label);?></h2></div><div class="card-body"><?php echo $this->form->renderFieldset($fieldset);?></div></section>
<?php endforeach;?>
<section class="card mb-4 jt-setup-section">
<div class="card-header d-flex justify-content-between align-items-center gap-3"><div><h2 class="h5 mb-1"><?php echo Text::_('COM_JUGENDTRAINING_SIGHT_SETTINGS');?></h2><div class="small text-muted"><?php echo Text::_('COM_JUGENDTRAINING_SIGHT_SETTINGS_DESC');?></div></div><button type="button" class="btn btn-sm btn-outline-primary" id="jt-add-sight-row"><?php echo Text::_('COM_JUGENDTRAINING_ADD_SIGHT_SETTING');?></button></div>
<div class="card-body"><div class="table-responsive"><table class="table align-middle" id="jt-sights"><thead><tr><th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_DISTANCE');?></th><th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_EXTENSION');?></th><th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_SIGHT_HEIGHT');?></th><th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_SIGHT_SIDE');?></th><th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_NOTES');?></th><th></th></tr></thead><tbody id="jt-sight-rows">
<?php foreach($rows as$i=>$r):?><tr>
<td><input class="form-control" type="number" min="0" step="0.1" name="sights[<?php echo(int)$i;?>][distance_m]" value="<?php echo htmlspecialchars((string)($r['distance_m']??''),ENT_QUOTES,'UTF-8');?>"></td>
<td><input class="form-control" name="sights[<?php echo(int)$i;?>][extension_setting]" value="<?php echo htmlspecialchars((string)($r['extension_setting']??''),ENT_QUOTES,'UTF-8');?>"></td>
<td><input class="form-control" name="sights[<?php echo(int)$i;?>][height_setting]" value="<?php echo htmlspecialchars((string)($r['height_setting']??''),ENT_QUOTES,'UTF-8');?>"></td>
<td><input class="form-control" name="sights[<?php echo(int)$i;?>][side_setting]" value="<?php echo htmlspecialchars((string)($r['side_setting']??''),ENT_QUOTES,'UTF-8');?>"></td>
<td><input class="form-control" name="sights[<?php echo(int)$i;?>][notes]" value="<?php echo htmlspecialchars((string)($r['notes']??''),ENT_QUOTES,'UTF-8');?>"></td>
<td><button type="button" class="btn btn-sm btn-outline-danger jt-remove-sight-row"><?php echo Text::_('COM_JUGENDTRAINING_BUTTON_REMOVE');?></button></td>
</tr><?php endforeach;?>
</tbody></table></div><div class="alert alert-light border mb-0" id="jt-no-sights"<?php echo$rows?' hidden':'';?>><?php echo Text::_('COM_JUGENDTRAINING_NO_SIGHT_SETTINGS');?></div></div>
</section>
<button class="btn btn-primary" type="button" onclick="Joomla.submitbutton('bowsetup.save')"><?php echo Text::_('COM_JUGENDTRAINING_BUTTON_SAVE');?></button>
<button class="btn btn-secondary" type="button" onclick="Joomla.submitbutton('bowsetup.cancel')"><?php echo Text::_('COM_JUGENDTRAINING_BUTTON_CANCEL');?></button>
<input type="hidden" name="task" value=""><?php echo HTMLHelper::_('form.token');?>
</form>
<template id="jt-sight-row-template"><tr>
<td><input class="form-control" type="number" min="0" step="0.1" data-field="distance_m"></td>
<td><input class="form-control" data-field="extension_setting"></td>
<td><input class="form-control" data-field="height_setting"></td>
<td><input class="form-control" data-field="side_setting"></td>
<td><input class="form-control" data-field="notes"></td>
<td><button type="button" class="btn btn-sm btn-outline-danger jt-remove-sight-row"><?php echo Text::_('COM_JUGENDTRAINING_BUTTON_REMOVE');?></button></td>
</tr></template>
<script>
Joomla.Text.load({
  'COM_JUGENDTRAINING_CONFIRM_DELETE_SIGHT': <?php echo json_encode(Text::_('COM_JUGENDTRAINING_CONFIRM_DELETE_SIGHT')); ?>
});
document.addEventListener('DOMContentLoaded',()=>{const rows=document.getElementById('jt-sight-rows'),tpl=document.getElementById('jt-sight-row-template'),add=document.getElementById('jt-add-sight-row'),empty=document.getElementById('jt-no-sights');if(!rows||!tpl||!add)return;let next=rows.querySelectorAll('tr').length;const refresh=()=>{if(empty)empty.hidden=rows.querySelectorAll('tr').length>0};const bind=row=>{const b=row.querySelector('.jt-remove-sight-row');if(b)b.addEventListener('click',()=>{if(window.confirm(Joomla.Text._('COM_JUGENDTRAINING_CONFIRM_DELETE_SIGHT'))){row.remove();refresh()}})};rows.querySelectorAll('tr').forEach(bind);add.addEventListener('click',()=>{const frag=tpl.content.cloneNode(true),row=frag.querySelector('tr');row.querySelectorAll('[data-field]').forEach(input=>{input.name=`sights[${next}][${input.dataset.field}]`;input.removeAttribute('data-field')});bind(row);rows.appendChild(frag);next++;refresh();rows.lastElementChild?.querySelector('input')?.focus()});refresh()});
</script>
