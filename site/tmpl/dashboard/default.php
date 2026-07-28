<?php
\defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
?>
<div id="jt-athlete-dashboard" class="d-flex flex-column gap-4"><div class="text-end"><a class="btn btn-sm btn-outline-secondary" href="<?php echo Route::_('index.php?option=com_jugendtraining&view=userpreferences');?>">⚙ <?php echo Text::_('COM_JUGENDTRAINING_APPEARANCE');?></a></div><div class="jt-profile jt-dashboard-block" data-dashboard-key="profile"><h1><?php echo Text::_('COM_JUGENDTRAINING_MY_DATA'); ?></h1><?php if(!$this->athlete): ?><div class="alert alert-info"><?php echo Text::_('COM_JUGENDTRAINING_NO_ATHLETE_LINKED'); ?></div><?php else: ?><div class="card"><div class="card-body"><h2><?php echo htmlspecialchars($this->athlete->firstname.' '.$this->athlete->lastname); ?></h2><dl class="row"><dt class="col-sm-4"><?php echo Text::_('COM_JUGENDTRAINING_FIELD_CLUB'); ?></dt><dd class="col-sm-8"><?php echo htmlspecialchars($this->athlete->club_name ?: '–'); ?></dd><dt class="col-sm-4"><?php echo Text::_('COM_JUGENDTRAINING_FIELD_CLASS'); ?></dt><dd class="col-sm-8"><?php echo htmlspecialchars($this->athlete->class_name ?: '–'); ?></dd><dt class="col-sm-4"><?php echo Text::_('COM_JUGENDTRAINING_FIELD_BOW_TYPE'); ?></dt><dd class="col-sm-8"><?php echo htmlspecialchars($this->athlete->bow_type ?: '–'); ?></dd><dt class="col-sm-4"><?php echo Text::_('COM_JUGENDTRAINING_FIELD_TRAINER'); ?></dt><dd class="col-sm-8"><?php echo htmlspecialchars($this->athlete->trainer_name ?: '–'); ?></dd></dl></div></div><?php endif; ?></div>

<section class="jt-dashboard-block" data-dashboard-key="calendar">
 <div class="card">
  <div class="card-header d-flex justify-content-between align-items-center gap-3">
   <h2 class="h4 mb-0"><?php echo Text::_('COM_JUGENDTRAINING_UPCOMING_EVENTS');?></h2>
   <a class="btn btn-sm btn-outline-primary" href="<?php echo Route::_('index.php?option=com_jugendtraining&view=calendar');?>"><?php echo Text::_('COM_JUGENDTRAINING_VIEW_CALENDAR');?></a>
  </div>
  <div class="card-body">
   <?php if($this->upcomingCalendarEvents):?>
    <div class="row g-3">
     <?php foreach($this->upcomingCalendarEvents as$event):
      $cfg=$this->calendarCategoryMap[$event->category]??['color'=>'#6c757d'];
      $bg=(string)($cfg['color']??'#6c757d');
      $hex=ltrim($bg,'#');$fg='#fff';
      if(strlen($hex)===6){$r=hexdec(substr($hex,0,2));$g=hexdec(substr($hex,2,2));$b=hexdec(substr($hex,4,2));$fg=(($r*299+$g*587+$b*114)/1000)>160?'#111':'#fff';}
      $endDate=$event->event_date_end?:$event->event_date;
     ?>
      <div class="col-12 col-lg-4">
       <article class="border rounded h-100 p-3">
        <span class="badge mb-2" style="background:<?php echo htmlspecialchars($bg,ENT_QUOTES,'UTF-8');?>;color:<?php echo$fg;?>"><?php echo htmlspecialchars($event->category,ENT_QUOTES,'UTF-8');?></span>
        <h3 class="h6 mb-2"><?php echo htmlspecialchars($event->title,ENT_QUOTES,'UTF-8');?></h3>
        <div class="small text-muted">
         <?php echo \Joomla\CMS\HTML\HTMLHelper::_('date',$event->event_date,Text::_('DATE_FORMAT_LC4'));?>
         <?php if($endDate!==$event->event_date):?> – <?php echo \Joomla\CMS\HTML\HTMLHelper::_('date',$endDate,Text::_('DATE_FORMAT_LC4'));?><?php endif;?>
         <?php if($event->event_time):?><br><?php echo htmlspecialchars(substr((string)$event->event_time,0,5),ENT_QUOTES,'UTF-8');?> Uhr<?php endif;?>
         <?php if($event->location):?><br><?php echo htmlspecialchars($event->location,ENT_QUOTES,'UTF-8');?><?php endif;?>
        </div>
       </article>
      </div>
     <?php endforeach;?>
    </div>
   <?php else:?>
    <p class="text-muted mb-0"><?php echo Text::_('COM_JUGENDTRAINING_NO_UPCOMING_EVENTS');?></p>
   <?php endif;?>
  </div>
 </div>
</section>

<section class="jt-dashboard-block" data-dashboard-key="attendance">
 <div class="card">
  <div class="card-header">
   <h2 class="h4 mb-0"><?php echo Text::_('COM_JUGENDTRAINING_MY_UPCOMING_TRAININGS');?></h2>
  </div>
  <div class="card-body">
   <?php if(empty($this->selfCancelSettings->enabled)):?>
    <p class="text-muted mb-0"><?php echo Text::_('COM_JUGENDTRAINING_SELF_CANCEL_DISABLED_INFO');?></p>
   <?php elseif($this->myUpcomingTrainingSessions):?>
    <div class="vstack gap-3">
     <?php foreach($this->myUpcomingTrainingSessions as$session):?>
      <div class="border rounded p-3">
       <div class="d-flex flex-wrap justify-content-between gap-3 align-items-start">
        <div>
         <strong class="d-block"><?php echo htmlspecialchars($session->title,ENT_QUOTES,'UTF-8');?></strong>
         <div class="small text-muted">
          <?php echo \Joomla\CMS\HTML\HTMLHelper::_('date',$session->training_date,Text::_('DATE_FORMAT_LC4'));?>
          <?php if($session->start_time):?> · <?php echo htmlspecialchars(substr((string)$session->start_time,0,5),ENT_QUOTES,'UTF-8');?> Uhr<?php endif;?>
          <?php if($session->location):?> · <?php echo htmlspecialchars($session->location,ENT_QUOTES,'UTF-8');?><?php endif;?>
          <?php if($session->group_title):?><br><?php echo htmlspecialchars($session->group_title,ENT_QUOTES,'UTF-8');?><?php endif;?>
         </div>
        </div>
        <div class="text-end">
         <?php if((string)$session->attendance_status==='excused'):?>
          <span class="badge text-bg-secondary"><?php echo Text::_('COM_JUGENDTRAINING_SELF_CANCELLED');?></span>
         <?php elseif($session->can_self_cancel):?>
          <form method="post" action="<?php echo Route::_('index.php?option=com_jugendtraining&task=selfattendance.cancel');?>" onsubmit="return confirm('<?php echo Text::_('COM_JUGENDTRAINING_SELF_CANCEL_CONFIRM');?>');">
           <input type="hidden" name="session_id" value="<?php echo(int)$session->id;?>">
           <button class="btn btn-sm btn-outline-danger" type="submit"><?php echo Text::_('COM_JUGENDTRAINING_SELF_CANCEL_BUTTON');?></button>
           <?php echo \Joomla\CMS\HTML\HTMLHelper::_('form.token');?>
          </form>
          <?php if($session->deadline_at):?><div class="small text-muted mt-1"><?php echo Text::sprintf('COM_JUGENDTRAINING_SELF_CANCEL_UNTIL',\Joomla\CMS\HTML\HTMLHelper::_('date',$session->deadline_at,'d.m.Y H:i'));?></div><?php endif;?>
         <?php elseif(!empty($session->can_late_cancel)):?>
          <form method="post" action="<?php echo Route::_('index.php?option=com_jugendtraining&task=selfattendance.cancel');?>" onsubmit="return confirm('<?php echo Text::_('COM_JUGENDTRAINING_SELF_CANCEL_CONFIRM');?>');">
           <input type="hidden" name="session_id" value="<?php echo(int)$session->id;?>">
           <button class="btn btn-sm btn-outline-danger" type="submit"><?php echo Text::_('COM_JUGENDTRAINING_SELF_CANCEL_BUTTON');?></button>
           <?php echo \Joomla\CMS\HTML\HTMLHelper::_('form.token');?>
          </form>
         <?php elseif($session->cancel_reason==='deadline_passed'):?>
          <span class="badge text-bg-warning"><?php echo Text::_('COM_JUGENDTRAINING_SELF_CANCEL_DEADLINE_PASSED');?></span>
         <?php elseif($session->cancel_reason==='missing_start_time'):?>
          <span class="badge text-bg-secondary"><?php echo Text::_('COM_JUGENDTRAINING_SELF_CANCEL_NO_START_TIME_SHORT');?></span>
         <?php endif;?>
        </div>
       </div>
      </div>
     <?php endforeach;?>
    </div>
   <?php else:?>
    <p class="text-muted mb-0"><?php echo Text::_('COM_JUGENDTRAINING_NO_UPCOMING_TRAININGS');?></p>
   <?php endif;?>
  </div>
 </div>
</section>


<section class="mt-4 jt-dashboard-block" data-dashboard-key="results">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2><?php echo \Joomla\CMS\Language\Text::_('COM_JUGENDTRAINING_MY_RESULTS'); ?></h2>
        <a
            class="btn btn-primary"
            href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_jugendtraining&task=result.add'); ?>"
        >
            <?php echo \Joomla\CMS\Language\Text::_('COM_JUGENDTRAINING_ADD_MY_RESULT'); ?>
        </a>
    </div>

    <?php if ($this->myResults) : ?>
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th><?php echo \Joomla\CMS\Language\Text::_('COM_JUGENDTRAINING_FIELD_RESULT_DATE'); ?></th>
                        <th><?php echo \Joomla\CMS\Language\Text::_('COM_JUGENDTRAINING_FIELD_EVENT_NAME'); ?></th>
                        <th><?php echo \Joomla\CMS\Language\Text::_('COM_JUGENDTRAINING_FIELD_DISTANCE'); ?></th>
                        <th><?php echo \Joomla\CMS\Language\Text::_('COM_JUGENDTRAINING_FIELD_ARROWS'); ?></th>
                        <th><?php echo \Joomla\CMS\Language\Text::_('COM_JUGENDTRAINING_FIELD_SCORE'); ?></th>
                        <th><?php echo \Joomla\CMS\Language\Text::_('COM_JUGENDTRAINING_VERIFICATION_STATUS'); ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($this->myResults as $result) : ?>
                    <tr>
                        <td><?php echo \Joomla\CMS\HTML\HTMLHelper::_('date', $result->result_date, \Joomla\CMS\Language\Text::_('DATE_FORMAT_LC4')); ?></td>
                        <td>
                            <?php echo htmlspecialchars(
                                (string) ($result->event_name ?: \Joomla\CMS\Language\Text::_('COM_JUGENDTRAINING_EVENT_' . strtoupper($result->event_type))),
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>
                        </td>
                        <td><?php echo (int) $result->distance_m; ?> m</td>
                        <td><?php echo (int) $result->arrows; ?></td>
                        <td><strong><?php echo (int) $result->score; ?></strong></td>
                        <td>
                            <?php echo \Joomla\CMS\Language\Text::_(
                                'COM_JUGENDTRAINING_VERIFICATION_' . strtoupper($result->verification_status)
                            ); ?>
                        </td>
                        <td class="text-nowrap">
                            <a
                                class="btn btn-sm btn-outline-primary"
                                href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_jugendtraining&task=result.edit&id=' . (int) $result->id); ?>"
                            >
                                <?php echo \Joomla\CMS\Language\Text::_('COM_JUGENDTRAINING_BUTTON_EDIT'); ?>
                            </a>

                            <a
                                class="btn btn-sm btn-outline-danger"
                                href="<?php echo \Joomla\CMS\Router\Route::_(
                                    'index.php?option=com_jugendtraining&task=result.delete&id=' . (int) $result->id
                                    . '&' . \Joomla\CMS\Session\Session::getFormToken() . '=1'
                                ); ?>"
                                onclick="return confirm('<?php echo \Joomla\CMS\Language\Text::_('COM_JUGENDTRAINING_CONFIRM_DELETE_RESULT'); ?>');"
                            >
                                <?php echo \Joomla\CMS\Language\Text::_('COM_JUGENDTRAINING_BUTTON_DELETE'); ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else : ?>
        <div class="alert alert-info">
            <?php echo \Joomla\CMS\Language\Text::_('COM_JUGENDTRAINING_NO_OWN_RESULTS'); ?>
        </div>
    <?php endif; ?>
</section>


<section class="mt-4 jt-dashboard-block" data-dashboard-key="penalties">
  <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
    <h2 class="mb-0"><?php echo Text::_('COM_JUGENDTRAINING_MY_OPEN_PENALTIES'); ?></h2>
    <?php if ($this->myOpenPenalties) : ?>
      <span class="badge text-bg-warning"><?php echo count($this->myOpenPenalties); ?></span>
    <?php endif; ?>
  </div>
  <?php if ($this->myOpenPenalties) : ?>
    <div class="row g-3">
      <?php foreach ($this->myOpenPenalties as $penalty) : ?>
        <div class="col-md-6 col-xl-4">
          <div class="card h-100 border-warning">
            <div class="card-body">
              <h3 class="h5"><?php echo htmlspecialchars($penalty->title, ENT_QUOTES, 'UTF-8'); ?></h3>
              <p class="mb-2">
                <strong>
                <?php if ($penalty->penalty_type === 'monetary') : ?>
                  <?php echo number_format((float)$penalty->amount_snapshot, 2, ',', '.'); ?> €
                <?php else : ?>
                  <?php echo htmlspecialchars((string)$penalty->action_snapshot, ENT_QUOTES, 'UTF-8'); ?>
                <?php endif; ?>
                </strong>
              </p>
              <?php if ($penalty->reason_note) : ?>
                <p class="small mb-2"><?php echo htmlspecialchars($penalty->reason_note, ENT_QUOTES, 'UTF-8'); ?></p>
              <?php endif; ?>
              <div class="small text-muted"><?php echo \Joomla\CMS\HTML\HTMLHelper::_('date', $penalty->assigned_at, Text::_('DATE_FORMAT_LC4')); ?></div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else : ?>
    <div class="alert alert-success"><?php echo Text::_('COM_JUGENDTRAINING_NO_OPEN_PENALTIES'); ?></div>
  <?php endif; ?>
</section>

<section class="mt-4 jt-achievements-preview jt-dashboard-block" data-dashboard-key="achievements">
  <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
    <div>
      <h2 class="mb-1"><?php echo Text::_('COM_JUGENDTRAINING_MY_ACHIEVEMENTS'); ?></h2>
      <p class="text-muted mb-0"><?php echo Text::sprintf('COM_JUGENDTRAINING_ACHIEVEMENT_COUNT', (int) $this->myAchievementCount); ?></p>
    </div>
    <?php if ($this->myAchievementCount > 0) : ?>
      <a class="btn btn-outline-primary" href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_jugendtraining&view=achievements'); ?>">
        <?php echo Text::_('COM_JUGENDTRAINING_VIEW_ALL_ACHIEVEMENTS'); ?>
      </a>
    <?php endif; ?>
  </div>
  <?php if ($this->myAchievements) : ?>
    <div class="jt-badge-grid jt-badge-grid-preview">
      <?php foreach ($this->myAchievements as $badge) : ?>
        <article class="jt-badge-card">
          <img class="jt-badge-image" style="width:48px!important;height:48px!important;max-width:48px!important;max-height:48px!important;object-fit:contain;flex:0 0 48px;" src="<?php echo htmlspecialchars(\Joomla\CMS\Uri\Uri::root() . ltrim($badge->badge_image, '/'), ENT_QUOTES, 'UTF-8'); ?>" alt="">
          <div class="jt-badge-card__body">
            <h3 class="h5 mb-1"><?php echo htmlspecialchars($badge->title, ENT_QUOTES, 'UTF-8'); ?></h3>
            <div class="small text-muted"><?php echo \Joomla\CMS\HTML\HTMLHelper::_('date', $badge->awarded_at, Text::_('DATE_FORMAT_LC4')); ?></div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  <?php else : ?>
    <div class="alert alert-info"><?php echo Text::_('COM_JUGENDTRAINING_NO_ACHIEVEMENTS_YET'); ?></div>
  <?php endif; ?>
</section>



<section class="mt-5 jt-dashboard-block" data-dashboard-key="programs">
  <h2><?php echo \Joomla\CMS\Language\Text::_('COM_JUGENDTRAINING_MY_PROGRAMS'); ?></h2>
  <?php if (!$this->myPrograms) : ?>
    <div class="alert alert-info"><?php echo \Joomla\CMS\Language\Text::_('COM_JUGENDTRAINING_NO_PROGRAMS_ASSIGNED'); ?></div>
  <?php endif; ?>

  <?php foreach ($this->myPrograms as $program) :
    $percent = $program->exercise_count > 0 ? round($program->completed_count * 100 / $program->exercise_count) : 0;
  ?>
    <div class="card mb-4">
      <div class="card-header">
        <div class="d-flex justify-content-between align-items-start gap-3">
          <div>
            <h3 class="h4 mb-1"><?php echo htmlspecialchars($program->title, ENT_QUOTES, 'UTF-8'); ?></h3>
            <span class="badge bg-secondary"><?php echo \Joomla\CMS\Language\Text::_('COM_JUGENDTRAINING_CATEGORY_' . strtoupper($program->category)); ?></span>
          </div>
          <strong><?php echo $program->completed_count; ?> / <?php echo $program->exercise_count; ?></strong>
        </div>
        <div class="progress mt-3" role="progressbar" aria-valuenow="<?php echo $percent; ?>" aria-valuemin="0" aria-valuemax="100">
          <div class="progress-bar" style="width: <?php echo $percent; ?>%"><?php echo $percent; ?> %</div>
        </div>
      </div>
      <?php if ($program->description) : ?><div class="card-body border-bottom"><?php echo nl2br(htmlspecialchars($program->description, ENT_QUOTES, 'UTF-8')); ?></div><?php endif; ?>
      <div class="list-group list-group-flush">
        <?php foreach ($program->exercises as $exercise) : ?>
          <div class="list-group-item">
            <div class="d-flex gap-3 align-items-start">
              <form action="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_jugendtraining&task=progress.toggle'); ?>" method="post">
                <input type="hidden" name="assignment_id" value="<?php echo (int)$program->assignment_id; ?>">
                <input type="hidden" name="exercise_id" value="<?php echo (int)$exercise->exercise_id; ?>">
                <button class="btn <?php echo (int)$exercise->completed ? 'btn-success' : 'btn-outline-secondary'; ?>" type="submit" title="<?php echo \Joomla\CMS\Language\Text::_('COM_JUGENDTRAINING_TOGGLE_PROGRESS'); ?>">
                  <?php echo (int)$exercise->completed ? '✓' : '○'; ?>
                </button>
                <?php echo \Joomla\CMS\HTML\HTMLHelper::_('form.token'); ?>
              </form>
              <div class="flex-grow-1">
                <h4 class="h5 mb-1 <?php echo (int)$exercise->completed ? 'text-decoration-line-through text-muted' : ''; ?>"><?php echo htmlspecialchars($exercise->exercise_title, ENT_QUOTES, 'UTF-8'); ?></h4>
                <?php if ($exercise->exercise_description) : ?><p class="mb-2"><?php echo nl2br(htmlspecialchars($exercise->exercise_description, ENT_QUOTES, 'UTF-8')); ?></p><?php endif; ?>
                <div class="small text-muted">
                  <?php if ($exercise->material) : ?><span class="me-3"><strong><?php echo \Joomla\CMS\Language\Text::_('COM_JUGENDTRAINING_FIELD_MATERIAL'); ?>:</strong> <?php echo htmlspecialchars($exercise->material, ENT_QUOTES, 'UTF-8'); ?></span><?php endif; ?>
                  <?php if ($exercise->video_url) : ?><a href="<?php echo htmlspecialchars($exercise->video_url, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener"><?php echo \Joomla\CMS\Language\Text::_('COM_JUGENDTRAINING_OPEN_VIDEO'); ?></a><?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endforeach; ?>
</section>


<section class="mt-5 jt-dashboard-block" data-dashboard-key="overview">
  <div class="row g-4">
    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header"><h3 class="h4 mb-0"><?php echo \Joomla\CMS\Language\Text::_('COM_JUGENDTRAINING_PERSONAL_BESTS'); ?></h3></div>
        <div class="card-body">
          <?php if ($this->myPersonalBests) : ?>
            <div class="table-responsive"><table class="table mb-0">
              <thead><tr><th><?php echo \Joomla\CMS\Language\Text::_('COM_JUGENDTRAINING_FIELD_DISTANCE'); ?></th><th><?php echo \Joomla\CMS\Language\Text::_('COM_JUGENDTRAINING_FIELD_ARROWS'); ?></th><th><?php echo \Joomla\CMS\Language\Text::_('COM_JUGENDTRAINING_BEST_SCORE'); ?></th><th><?php echo \Joomla\CMS\Language\Text::_('COM_JUGENDTRAINING_BEST_AVERAGE'); ?></th></tr></thead>
              <tbody><?php foreach($this->myPersonalBests as $best):?><tr><td><?php echo (int)$best->distance_m;?> m</td><td><?php echo (int)$best->arrows;?></td><td><strong><?php echo (int)$best->best_score;?></strong></td><td><?php echo number_format((float)$best->best_average,2,',','.');?></td></tr><?php endforeach;?></tbody>
            </table></div>
          <?php else : ?><p class="mb-0"><?php echo \Joomla\CMS\Language\Text::_('COM_JUGENDTRAINING_NO_PERSONAL_BESTS'); ?></p><?php endif; ?>
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header"><h3 class="h4 mb-0"><?php echo \Joomla\CMS\Language\Text::_('COM_JUGENDTRAINING_MY_GOALS'); ?></h3></div>
        <div class="card-body">
          <?php if($this->myGoals): foreach($this->myGoals as $goal):
            $percent=(float)$goal->target_value>0?min(100,round((float)$goal->current_value*100/(float)$goal->target_value)):0;
          ?>
            <div class="mb-4">
              <div class="d-flex justify-content-between"><strong><?php echo htmlspecialchars($goal->title,ENT_QUOTES,'UTF-8');?></strong><span><?php echo $percent;?> %</span></div>
              <?php if($goal->description):?><div class="small text-muted mb-2"><?php echo nl2br(htmlspecialchars($goal->description,ENT_QUOTES,'UTF-8'));?></div><?php endif;?>
              <div class="progress"><div class="progress-bar" style="width:<?php echo $percent;?>%"><?php echo number_format((float)$goal->current_value,0,',','.');?> / <?php echo number_format((float)$goal->target_value,0,',','.');?></div></div>
              <?php if($goal->due_date):?><small class="text-muted"><?php echo \Joomla\CMS\Language\Text::_('COM_JUGENDTRAINING_FIELD_DUE_DATE');?>: <?php echo \Joomla\CMS\HTML\HTMLHelper::_('date',$goal->due_date,\Joomla\CMS\Language\Text::_('DATE_FORMAT_LC4'));?></small><?php endif;?>
            </div>
          <?php endforeach; else: ?><p class="mb-0"><?php echo \Joomla\CMS\Language\Text::_('COM_JUGENDTRAINING_NO_GOALS'); ?></p><?php endif;?>
        </div>
      </div>
    </div>
  </div>

  <?php if($this->visibleTrainerNotes):?>
    <div class="card mt-4">
      <div class="card-header"><h3 class="h4 mb-0"><?php echo \Joomla\CMS\Language\Text::_('COM_JUGENDTRAINING_TRAINER_FEEDBACK'); ?></h3></div>
      <div class="list-group list-group-flush">
        <?php foreach($this->visibleTrainerNotes as $note):?>
          <div class="list-group-item">
            <div class="small text-muted"><?php echo \Joomla\CMS\HTML\HTMLHelper::_('date',$note->note_date,\Joomla\CMS\Language\Text::_('DATE_FORMAT_LC4'));?> · <?php echo \Joomla\CMS\Language\Text::_('COM_JUGENDTRAINING_NOTE_'.strtoupper($note->category));?></div>
            <div><?php echo nl2br(htmlspecialchars($note->note,ENT_QUOTES,'UTF-8'));?></div>
          </div>
        <?php endforeach;?>
      </div>
    </div>
  <?php endif;?>
</section>


<section class="mt-5 jt-dashboard-block" data-dashboard-key="performance">

<?php
\defined('_JEXEC') or die;
$rows=$this->myResultDevelopment;
$width=max(640,count($rows)*70);$height=340;$left=55;$top=25;$bottom=55;$plotH=$height-$top-$bottom;$plotW=$width-$left-20;
$maxScore=$rows?max(array_map(fn($r)=>(float)$r->score,$rows)):1;
$maxAvg=10.0;$n=max(1,count($rows));$step=$plotW/$n;$points=[];
?>
<h2><?php echo Text::_('COM_JUGENDTRAINING_PERFORMANCE_DEVELOPMENT');?></h2>
<div class="card"><div class="card-body overflow-auto">
<svg width="<?php echo $width;?>" height="<?php echo $height;?>" viewBox="0 0 <?php echo $width;?> <?php echo $height;?>" role="img" aria-label="<?php echo Text::_('COM_JUGENDTRAINING_PERFORMANCE_CHART');?>">
<line x1="<?php echo $left;?>" y1="<?php echo $top+$plotH;?>" x2="<?php echo $left+$plotW;?>" y2="<?php echo $top+$plotH;?>" stroke="currentColor"/>
<line x1="<?php echo $left;?>" y1="<?php echo $top;?>" x2="<?php echo $left;?>" y2="<?php echo $top+$plotH;?>" stroke="currentColor"/>
<?php foreach($rows as $i=>$r):
$x=$left+$i*$step+$step*.18;$barW=$step*.45;$barH=$maxScore>0?((float)$r->score/$maxScore)*$plotH:0;$y=$top+$plotH-$barH;
$px=$left+$i*$step+$step*.405;$py=$top+$plotH-((float)$r->average/$maxAvg)*$plotH;$points[]=$px.','.$py;
?>
<rect x="<?php echo round($x,1);?>" y="<?php echo round($y,1);?>" width="<?php echo round($barW,1);?>" height="<?php echo round($barH,1);?>" fill="var(--cassiopeia-color-primary,#1f5b9c)" opacity=".72"><title><?php echo (int)$r->score;?> <?php echo Text::_('COM_JUGENDTRAINING_SCORE_UNIT');?></title></rect>
<text x="<?php echo round($x+$barW/2,1);?>" y="<?php echo $height-30;?>" text-anchor="middle" font-size="11"><?php echo htmlspecialchars(substr((string)$r->result_date,5),ENT_QUOTES,'UTF-8');?></text>
<?php endforeach;?>
<?php if($points):?><polyline points="<?php echo implode(' ',$points);?>" fill="none" stroke="#d63384" stroke-width="3"/><?php foreach($points as $i=>$point):[$cx,$cy]=explode(',',$point);?><circle cx="<?php echo $cx;?>" cy="<?php echo $cy;?>" r="4" fill="#d63384"><title>Ø <?php echo number_format((float)$rows[$i]->average,2,',','.');?></title></circle><?php endforeach;?><?php endif;?>
<text x="<?php echo $left+10;?>" y="18" font-size="12"><?php echo Text::_('COM_JUGENDTRAINING_SCORE_SERIES');?></text>
<line x1="<?php echo $left+145;?>" y1="14" x2="<?php echo $left+175;?>" y2="14" stroke="#d63384" stroke-width="3"/><text x="<?php echo $left+180;?>" y="18" font-size="12"><?php echo Text::_('COM_JUGENDTRAINING_AVERAGE_SERIES');?></text>
</svg>
</div></div>

</section>
</div>
<script>
document.addEventListener('DOMContentLoaded',function(){
 const config=<?php echo json_encode($this->athleteDashboardConfig,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); ?>;
 const root=document.getElementById('jt-athlete-dashboard');if(!root)return;
 config.forEach((row,index)=>{const el=root.querySelector('[data-dashboard-key="'+row.key+'"]');if(!el)return;el.style.order=String(index);el.hidden=!Number(row.visible);});
});
</script>
