<?php \defined('_JEXEC') or die;use Joomla\CMS\Language\Text;?>
<h1><?php echo Text::_('COM_JUGENDTRAINING_MY_GOALS');?></h1>
<?php foreach($this->myGoals as $g):$pct=(float)$g->target_value>0?min(100,round((float)$g->current_value*100/(float)$g->target_value)):0;?><div class="card mb-3"><div class="card-body"><h2 class="h4"><?php echo htmlspecialchars($g->title,ENT_QUOTES,'UTF-8');?></h2><div class="progress"><div class="progress-bar" style="width:<?php echo $pct;?>%"><?php echo $pct;?> %</div></div></div></div><?php endforeach;?>
