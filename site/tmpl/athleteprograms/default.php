<?php \defined('_JEXEC') or die;use Joomla\CMS\Language\Text;?>
<h1><?php echo Text::_('COM_JUGENDTRAINING_MY_PROGRAMS');?></h1>
<?php foreach($this->myPrograms as $p):$pct=$p->exercise_count?round($p->completed_count*100/$p->exercise_count):0;?><div class="card mb-3"><div class="card-body"><h2 class="h4"><?php echo htmlspecialchars($p->title,ENT_QUOTES,'UTF-8');?></h2><div class="progress"><div class="progress-bar" style="width:<?php echo $pct;?>%"><?php echo $pct;?> %</div></div></div></div><?php endforeach;?>
