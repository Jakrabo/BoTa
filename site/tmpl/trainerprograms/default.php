<?php
\defined('_JEXEC') or die;use Joomla\CMS\Language\Text;
?>
<h1><?php echo Text::_('COM_JUGENDTRAINING_TRAINER_PROGRAMS');?></h1>
<div class="row g-3"><?php foreach($this->programs as $p):?><div class="col-md-6"><div class="card h-100"><div class="card-body"><h2 class="h4"><?php echo htmlspecialchars($p->title,ENT_QUOTES,'UTF-8');?></h2><p><?php echo htmlspecialchars((string)$p->description,ENT_QUOTES,'UTF-8');?></p><strong><?php echo (int)$p->assigned_count;?> <?php echo Text::_('COM_JUGENDTRAINING_ASSIGNED_ATHLETES');?></strong></div></div></div><?php endforeach;?></div>
