<?php \defined('_JEXEC') or die;use Joomla\CMS\Language\Text;use Joomla\CMS\Factory;use Joomla\CMS\Router\Route;use Joomla\CMS\HTML\HTMLHelper;Factory::getApplication()->getDocument()->getWebAssetManager()->useStyle('com_jugendtraining.site');?>
<div class="d-flex justify-content-between align-items-center gap-3 mb-3"><h1 class="mb-0"><?php echo Text::_('COM_JUGENDTRAINING_TRAINER_DASHBOARD');?></h1><a class="btn btn-sm btn-outline-secondary" href="<?php echo Route::_('index.php?option=com_jugendtraining&view=userpreferences');?>">⚙ <?php echo Text::_('COM_JUGENDTRAINING_APPEARANCE');?></a></div><div id="jt-trainer-dashboard" class="d-flex flex-column gap-4">
<div class="row g-3 mb-4 jt-dashboard-block" data-dashboard-key="groups"><?php foreach($this->groups as$g):?><div class="col-md-4"><a class="card h-100 text-decoration-none" href="<?php echo Route::_('index.php?option=com_jugendtraining&view=trainerathletes&group_id='.(int)$g->id);?>"><div class="card-body"><h2 class="h4"><?php echo htmlspecialchars($g->title,ENT_QUOTES,'UTF-8');?></h2><p><?php echo(int)$g->athlete_count;?> <?php echo Text::_('COM_JUGENDTRAINING_ATHLETES');?></p></div></a></div><?php endforeach;?></div>

<?php $dashboardReturn=base64_encode('index.php?option=com_jugendtraining&view=trainerdashboard'); ?>
<section class="card mb-4 jt-dashboard-block" data-dashboard-key="today_trainings">
 <div class="card-header"><h2 class="h4 mb-0"><?php echo Text::_('COM_JUGENDTRAINING_TODAY_TRAININGS'); ?></h2></div>
 <?php if ($this->todayTrainings) : ?>
 <div class="card-body"><div class="row g-3">
 <?php foreach ($this->todayTrainings as $training) :
  $editBase='index.php?option=com_jugendtraining&view=trainertraining&layout=edit&id='.(int)$training->id.'&return='.rawurlencode($dashboardReturn);
  $metrics=[
   'all'=>['COM_JUGENDTRAINING_EXPECTED_PARTICIPANTS',(int)$training->expected_total,'secondary'],
   'open'=>['COM_JUGENDTRAINING_FILTER_OPEN',(int)$training->open_total,'secondary'],
   'present'=>['COM_JUGENDTRAINING_ATTENDANCE_PRESENT',(int)$training->present_total,'success'],
   'excused'=>['COM_JUGENDTRAINING_ATTENDANCE_EXCUSED',(int)$training->excused_total,'warning'],
   'late'=>['COM_JUGENDTRAINING_ATTENDANCE_LATE',(int)$training->late_total,'info'],
   'absent'=>['COM_JUGENDTRAINING_ATTENDANCE_ABSENT',(int)$training->absent_total,'danger'],
  ];
 ?>
  <div class="col-12 col-xl-6"><article class="jt-today-training-card border rounded p-3 h-100">
   <div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
    <div><h3 class="h5 mb-1"><?php echo htmlspecialchars((string)$training->group_title,ENT_QUOTES,'UTF-8'); ?></h3>
     <div class="text-muted small"><?php echo htmlspecialchars((string)($training->location?:'–'),ENT_QUOTES,'UTF-8'); ?> · <?php echo htmlspecialchars(substr((string)$training->start_time,0,5),ENT_QUOTES,'UTF-8'); ?><?php if($training->end_time):?>–<?php echo htmlspecialchars(substr((string)$training->end_time,0,5),ENT_QUOTES,'UTF-8'); ?><?php endif; ?></div>
    </div>
    <?php if ((int)$training->training_unit_id > 0) : ?><a class="btn btn-sm btn-outline-primary" href="<?php echo Route::_('index.php?option=com_jugendtraining&view=trainertrainingunit&id='.(int)$training->training_unit_id.'&return='.rawurlencode($dashboardReturn)); ?>"><?php echo Text::_('COM_JUGENDTRAINING_TRAINING_UNIT'); ?></a><?php endif; ?>
   </div>
   <div class="jt-today-training-metrics">
    <?php foreach($metrics as $filter=>$metric): ?><a class="badge text-bg-<?php echo $metric[2]; ?> text-decoration-none" href="<?php echo Route::_($editBase.'&attendance_filter='.$filter); ?>" title="<?php echo Text::_($metric[0]); ?>"><strong><?php echo $metric[1]; ?></strong><span><?php echo Text::_($metric[0]); ?></span></a><?php endforeach; ?>
   </div>
  </article></div>
 <?php endforeach; ?>
 </div></div>
 <?php else : ?><div class="card-body text-muted"><?php echo Text::_('COM_JUGENDTRAINING_NO_TRAININGS_TODAY'); ?></div><?php endif; ?>
</section>

<div class="row g-3 mb-4 jt-dashboard-block" data-dashboard-key="penalty_summary">
  <div class="col-md-4">
    <a class="card h-100 text-decoration-none border-warning" href="<?php echo Route::_('index.php?option=com_jugendtraining&view=trainerpenalties'); ?>">
      <div class="card-body">
        <h2 class="h4"><?php echo Text::_('COM_JUGENDTRAINING_OPEN_PENALTIES'); ?></h2>
        <strong class="display-6"><?php echo count($this->openPenalties); ?></strong>
      </div>
    </a>
  </div>
  <div class="col-md-4">
    <div class="card h-100 border-success">
      <div class="card-body">
        <h2 class="h4"><?php echo Text::_('COM_JUGENDTRAINING_PENALTY_BALANCE'); ?></h2>
        <strong class="display-6"><?php echo number_format($this->penaltyBalance, 2, ',', '.'); ?> €</strong>
        <p class="small text-muted mb-0"><?php echo Text::_('COM_JUGENDTRAINING_PENALTY_BALANCE_COMPLETED'); ?></p>
      </div>
    </div>
  </div>
</div>
<?php if ($this->openPenalties) : ?>
<section class="card mb-4 jt-dashboard-block" data-dashboard-key="open_penalties">
 <div class="card-header d-flex justify-content-between align-items-center">
  <h2 class="h4 mb-0"><?php echo Text::_('COM_JUGENDTRAINING_OPEN_PENALTIES'); ?></h2>
  <a href="<?php echo Route::_('index.php?option=com_jugendtraining&view=trainerpenalties'); ?>"><?php echo Text::_('COM_JUGENDTRAINING_OPEN_REGISTER'); ?></a>
 </div>
 <div class="table-responsive"><table class="table mb-0"><thead><tr>
  <th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_ATHLETE'); ?></th><th><?php echo Text::_('COM_JUGENDTRAINING_PENALTY'); ?></th><th><?php echo Text::_('COM_JUGENDTRAINING_PENALTY_VALUE'); ?></th><th><?php echo Text::_('JDATE'); ?></th>
 </tr></thead><tbody>
 <?php foreach(array_slice($this->openPenalties,0,10) as $penalty): ?><tr>
  <td><?php echo htmlspecialchars($penalty->athlete_name,ENT_QUOTES,'UTF-8'); ?></td>
  <td><?php echo htmlspecialchars($penalty->title,ENT_QUOTES,'UTF-8'); ?></td>
  <td><?php echo $penalty->penalty_type==='monetary'?number_format((float)$penalty->amount_snapshot,2,',','.').' €':htmlspecialchars((string)$penalty->action_snapshot,ENT_QUOTES,'UTF-8'); ?></td>
  <td><?php echo HTMLHelper::_('date',$penalty->assigned_at,Text::_('DATE_FORMAT_LC4')); ?></td>
 </tr><?php endforeach; ?>
 </tbody></table></div>
</section>
<?php endif; ?>

<section class="card mb-4 jt-dashboard-block" data-dashboard-key="signals"><div class="card-header d-flex justify-content-between"><h2 class="h4 mb-0"><?php echo Text::_('COM_JUGENDTRAINING_TRAINER_SIGNAL_OVERVIEW');?></h2><a href="<?php echo Route::_('index.php?option=com_jugendtraining&view=trainerstatistics');?>"><?php echo Text::_('COM_JUGENDTRAINING_OPEN_STATISTICS');?></a></div><div class="table-responsive"><table class="table mb-0"><thead><tr><th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_ATHLETE');?></th><th><?php echo Text::_('COM_JUGENDTRAINING_SIGNAL');?></th><th><?php echo Text::_('COM_JUGENDTRAINING_TRAINING_28_DAYS');?></th><th><?php echo Text::_('COM_JUGENDTRAINING_RESULT_TREND');?></th><th><?php echo Text::_('COM_JUGENDTRAINING_LAST_TRAINING');?></th></tr></thead><tbody><?php foreach($this->signals as$s):?><tr><td><a href="<?php echo Route::_('index.php?option=com_jugendtraining&view=trainerathletedetail&id='.(int)$s->id);?>"><?php echo htmlspecialchars($s->athlete_name,ENT_QUOTES,'UTF-8');?></a></td><td><span
 class="jt-traffic jt-traffic-<?php echo htmlspecialchars($s->signal, ENT_QUOTES, 'UTF-8'); ?>"
 style="display:inline-block;width:1rem;height:1rem;border-radius:50%;vertical-align:-.1rem;border:1px solid rgba(0,0,0,.2);background:<?php echo $s->signal === 'green' ? '#198754' : ($s->signal === 'yellow' ? '#ffc107' : '#dc3545'); ?>;"
 title="<?php echo htmlspecialchars(Text::_($s->signal_reason), ENT_QUOTES, 'UTF-8'); ?>"
 aria-label="<?php echo htmlspecialchars(Text::_($s->signal_reason), ENT_QUOTES, 'UTF-8'); ?>"
></span> <?php echo Text::_($s->signal_reason);?></td><td><?php echo(int)$s->arrows_28;?> <?php echo Text::_('COM_JUGENDTRAINING_ARROWS');?> / <?php echo(int)$s->minutes_28;?> <?php echo Text::_('COM_JUGENDTRAINING_MINUTES');?></td><td><?php echo$s->trend>0?'+':'';echo number_format((float)$s->trend,3,',','.');?></td><td><?php echo$s->last_training?HTMLHelper::_('date',$s->last_training,Text::_('DATE_FORMAT_LC4')):Text::_('COM_JUGENDTRAINING_NEVER');?></td></tr><?php endforeach;?></tbody></table></div></section>
<section class="card mb-4 jt-dashboard-block" data-dashboard-key="class_changes"><div class="card-header"><h2 class="h4 mb-0"><?php echo Text::_('COM_JUGENDTRAINING_UPCOMING_CLASS_CHANGES');?></h2></div><?php if($this->classTransitions):?><div class="table-responsive"><table class="table mb-0"><thead><tr><th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_ATHLETE');?></th><th><?php echo Text::_('COM_JUGENDTRAINING_GROUPS');?></th><th><?php echo Text::_('COM_JUGENDTRAINING_CURRENT_CLASS');?></th><th><?php echo Text::_('COM_JUGENDTRAINING_NEXT_CLASS');?></th><th><?php echo Text::_('COM_JUGENDTRAINING_SPORTYEAR_CHANGE');?></th></tr></thead><tbody><?php foreach($this->classTransitions as$c):?><tr><td><a href="<?php echo Route::_('index.php?option=com_jugendtraining&view=trainerathletedetail&id='.(int)$c->athlete_id);?>"><?php echo htmlspecialchars($c->athlete_name,ENT_QUOTES,'UTF-8');?></a></td><td><?php echo htmlspecialchars((string)$c->group_names,ENT_QUOTES,'UTF-8');?></td><td><?php echo htmlspecialchars((string)$c->current_class,ENT_QUOTES,'UTF-8');?></td><td><strong><?php echo htmlspecialchars($c->next_class,ENT_QUOTES,'UTF-8');?></strong> (<?php echo(int)$c->next_age;?>)</td><td><?php echo htmlspecialchars($c->sportyear_name,ENT_QUOTES,'UTF-8');?> · <?php echo HTMLHelper::_('date',$c->change_date,Text::_('DATE_FORMAT_LC4'));?></td></tr><?php endforeach;?></tbody></table></div><?php else:?><div class="card-body text-muted"><?php echo Text::_('COM_JUGENDTRAINING_NO_UPCOMING_CLASS_CHANGES');?></div><?php endif;?></section>
<div class="row g-3 jt-dashboard-block" data-dashboard-key="navigation"><?php $cards=[['calendar','COM_JUGENDTRAINING_CALENDAR',$this->calendarEventCount],['trainerathletes','COM_JUGENDTRAINING_ATHLETES',count($this->athletes)],['trainerresults','COM_JUGENDTRAINING_RESULTS',count($this->results)],['trainertrainings','COM_JUGENDTRAINING_TRAININGS',count($this->trainings)],['trainerprograms','COM_JUGENDTRAINING_PROGRAMS',count($this->programs)],['trainergoals','COM_JUGENDTRAINING_GOALS',count($this->goals)],['trainernotesfront','COM_JUGENDTRAINING_TRAINER_NOTES',count($this->notes)],['trainerstatistics','COM_JUGENDTRAINING_STATISTICS',count($this->signals)],['trainerachievements','COM_JUGENDTRAINING_ACHIEVEMENTS',count($this->athletes)],['trainerpenalties','COM_JUGENDTRAINING_PENALTY_REGISTER',count($this->openPenalties)]];foreach($cards as[$view,$label,$count]):?><div class="col-md-4"><a class="card h-100 text-decoration-none" href="<?php echo Route::_('index.php?option=com_jugendtraining&view='.$view);?>"><div class="card-body"><h2 class="h4"><?php echo Text::_($label);?></h2><strong class="display-6"><?php echo$count;?></strong></div></a></div><?php endforeach;?></div>
</div>
<script>
document.addEventListener('DOMContentLoaded',function(){
 const config=<?php echo json_encode($this->trainerDashboardConfig,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); ?>;
 const root=document.getElementById('jt-trainer-dashboard');if(!root)return;
 config.forEach((row,index)=>{const el=root.querySelector('[data-dashboard-key="'+row.key+'"]');if(!el)return;el.style.order=String(index);el.hidden=!Number(row.visible);});
});
</script>
