<?php
\defined('_JEXEC') or die;use Joomla\CMS\Language\Text;
?>
<h1><?php echo Text::_('COM_JUGENDTRAINING_TRAINER_GOALS');?></h1>
<div class="table-responsive"><table class="table table-striped"><thead><tr><th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_ATHLETE');?></th><th><?php echo Text::_('COM_JUGENDTRAINING_COLUMN_TITLE');?></th><th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_TARGET_TYPE');?></th><th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_PROGRESS');?></th></tr></thead><tbody>
<?php foreach($this->goals as $g):$pct=(float)$g->target_value>0?min(100,round((float)$g->current_value*100/(float)$g->target_value)):0;?><tr><td><?php echo htmlspecialchars($g->athlete_name,ENT_QUOTES,'UTF-8');?></td><td><?php echo htmlspecialchars($g->title,ENT_QUOTES,'UTF-8');?></td><td><?php echo Text::_('COM_JUGENDTRAINING_TARGET_'.strtoupper($g->target_type));?></td><td><div class="progress"><div class="progress-bar" style="width:<?php echo $pct;?>%"><?php echo $pct;?> %</div></div></td></tr><?php endforeach;?>
</tbody></table></div>
