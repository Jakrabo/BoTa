<?php
\defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$returnToken = Joomla\CMS\Factory::getApplication()->getInput()->get('return', '', 'BASE64');
$back = $returnToken !== '' ? base64_decode($returnToken, true) : '';
if (!is_string($back) || !str_starts_with($back, 'index.php?option=com_jugendtraining')) {
    $back = 'index.php?option=com_jugendtraining&view=trainertrainingunits';
}
$totalMinutes = array_sum(array_map(static fn($item) => (int) $item->duration_minutes, $this->unitItems));
?>
<div class="jt-training-unit-page">
 <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
  <div><h1 class="mb-1"><?php echo htmlspecialchars((string)$this->item->title,ENT_QUOTES,'UTF-8'); ?></h1>
   <?php if($this->item->description):?><p class="text-muted mb-0"><?php echo nl2br(htmlspecialchars((string)$this->item->description,ENT_QUOTES,'UTF-8')); ?></p><?php endif; ?>
  </div>
  <div class="d-flex flex-wrap gap-2"><a class="btn btn-outline-secondary" href="<?php echo Route::_($back); ?>"><?php echo Text::_('COM_JUGENDTRAINING_BACK'); ?></a><a class="btn btn-primary" href="<?php echo Route::_('index.php?option=com_jugendtraining&view=trainertrainingunit&layout=edit&id='.(int)$this->item->id.'&return='.rawurlencode($returnToken)); ?>"><?php echo Text::_('JACTION_EDIT'); ?></a></div>
 </div>
 <div class="jt-raster-summary mb-3"><span><?php echo count($this->unitItems); ?> <?php echo Text::_('COM_JUGENDTRAINING_RASTER_ROWS'); ?></span><span><?php echo $totalMinutes; ?> <?php echo Text::_('COM_JUGENDTRAINING_MINUTES'); ?></span></div>
 <?php if($this->unitItems):?><div class="jt-raster-grid">
  <?php foreach($this->unitItems as$index=>$row):?><article class="jt-raster-row">
   <header><span class="jt-raster-step"><?php echo Text::sprintf('COM_JUGENDTRAINING_RASTER_STEP',(int)$index+1); ?></span><strong><?php echo htmlspecialchars((string)$row->exercise_title,ENT_QUOTES,'UTF-8'); ?></strong><span class="jt-raster-duration"><?php echo(int)$row->duration_minutes;?> <?php echo Text::_('COM_JUGENDTRAINING_MINUTES_SHORT'); ?></span></header>
   <div class="jt-raster-fields">
    <?php foreach([['goal','COM_JUGENDTRAINING_RASTER_GOAL'],['content','COM_JUGENDTRAINING_RASTER_CONTENT'],['method','COM_JUGENDTRAINING_RASTER_METHOD'],['material','COM_JUGENDTRAINING_RASTER_MATERIALS'],['remarks','COM_JUGENDTRAINING_RASTER_REMARKS']] as[$field,$label]):?>
     <div class="jt-raster-field"><span><?php echo Text::_($label); ?></span><p><?php echo trim((string)$row->$field)!==''?nl2br(htmlspecialchars((string)$row->$field,ENT_QUOTES,'UTF-8')):'–'; ?></p></div>
    <?php endforeach; ?>
   </div>
  </article><?php endforeach; ?>
 </div><?php else:?><div class="alert alert-info"><?php echo Text::_('COM_JUGENDTRAINING_NO_RASTER_ROWS'); ?></div><?php endif; ?>
</div>
