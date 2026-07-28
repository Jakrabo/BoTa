<?php \defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;use Joomla\CMS\Router\Route;use Joomla\CMS\HTML\HTMLHelper;use Joomla\CMS\Uri\Uri;$c=$this->cockpit;
$manual=array_values(array_filter($c->achievements,fn($a)=>$a->award_mode==='manual'));
$automatic=array_values(array_filter($c->achievements,fn($a)=>$a->award_mode==='automatic'));
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"><div><h1 class="mb-1"><?php echo Text::_('COM_JUGENDTRAINING_ACHIEVEMENT_COCKPIT');?></h1><p class="text-muted mb-0"><?php echo Text::_('COM_JUGENDTRAINING_ACHIEVEMENT_COCKPIT_DESC');?></p><a class="btn btn-outline-primary" href="<?php echo Route::_('index.php?option=com_jugendtraining&view=trainerachievementdefinitions');?>"><?php echo Text::_('COM_JUGENDTRAINING_MANAGE_ACHIEVEMENTS');?></a></div><form action="<?php echo Route::_('index.php?option=com_jugendtraining&task=achievement.evaluate');?>" method="post"><button class="btn btn-outline-primary" type="submit"><?php echo Text::_('COM_JUGENDTRAINING_EVALUATE_AUTOMATIC');?></button><?php echo HTMLHelper::_('form.token');?></form></div>
<section class="card mb-4"><div class="card-header"><h2 class="h4 mb-0"><?php echo Text::_('COM_JUGENDTRAINING_GRANT_ACHIEVEMENT');?></h2></div><div class="card-body">
<form action="<?php echo Route::_('index.php?option=com_jugendtraining&task=achievement.grant');?>" method="post" class="row g-3">
<div class="col-md-4"><label class="form-label" for="athlete_id"><?php echo Text::_('COM_JUGENDTRAINING_FIELD_ATHLETE');?></label><select class="form-select" id="athlete_id" name="athlete_id" required><option value=""><?php echo Text::_('COM_JUGENDTRAINING_SELECT_ATHLETE');?></option><?php foreach($c->athletes as$a):?><option value="<?php echo(int)$a->id;?>"><?php echo htmlspecialchars($a->firstname.' '.$a->lastname,ENT_QUOTES,'UTF-8');?> · <?php echo htmlspecialchars((string)$a->group_names,ENT_QUOTES,'UTF-8');?></option><?php endforeach;?></select></div>
<div class="col-md-4"><label class="form-label" for="achievement_id"><?php echo Text::_('COM_JUGENDTRAINING_ACHIEVEMENT');?></label><select class="form-select" id="achievement_id" name="achievement_id" required><option value=""><?php echo Text::_('COM_JUGENDTRAINING_SELECT_ACHIEVEMENT');?></option><?php foreach($manual as$b):?><option value="<?php echo(int)$b->id;?>"><?php echo htmlspecialchars($b->title,ENT_QUOTES,'UTF-8');?> (<?php echo htmlspecialchars($b->category,ENT_QUOTES,'UTF-8');?>)</option><?php endforeach;?></select></div>
<div class="col-md-4"><label class="form-label" for="note"><?php echo Text::_('COM_JUGENDTRAINING_FIELD_NOTE');?></label><input class="form-control" id="note" name="note" maxlength="500"></div>
<div class="col-12"><button class="btn btn-primary" type="submit"><?php echo Text::_('COM_JUGENDTRAINING_GRANT');?></button></div><?php echo HTMLHelper::_('form.token');?></form>
</div></section>
<section class="card mb-4">
<div class="card-header"><h2 class="h4 mb-0"><?php echo Text::_('COM_JUGENDTRAINING_AVAILABLE_ACHIEVEMENTS');?></h2></div>
<div class="card-body">
 <div class="row g-3">
 <?php foreach(array_slice($c->achievements,0,6) as$b):?>
  <div class="col-12 col-md-4">
   <article class="card h-100 text-center">
    <div class="card-body d-flex flex-column align-items-center justify-content-center">
     <?php if($b->badge_image):?><img src="<?php echo htmlspecialchars(Uri::root().ltrim($b->badge_image,'/'),ENT_QUOTES,'UTF-8');?>" alt="" style="width:72px;height:72px;max-width:72px;max-height:72px;object-fit:contain"><?php endif;?>
     <h3 class="h6 mt-3 mb-2"><?php echo htmlspecialchars($b->title,ENT_QUOTES,'UTF-8');?></h3>
     <span class="badge <?php echo$b->award_mode==='automatic'?'bg-success':'bg-secondary';?>"><?php echo Text::_($b->award_mode==='automatic'?'COM_JUGENDTRAINING_AWARD_AUTOMATIC':'COM_JUGENDTRAINING_AWARD_MANUAL');?></span>
    </div>
   </article>
  </div>
 <?php endforeach;?>
 </div>
 <div class="mt-3 d-flex justify-content-between align-items-center gap-3">
  <span class="text-muted"><?php echo Text::sprintf('COM_JUGENDTRAINING_ACHIEVEMENT_EXCERPT_COUNT',min(6,count($c->achievements)),count($c->achievements));?></span>
  <a class="btn btn-outline-primary" href="<?php echo Route::_('index.php?option=com_jugendtraining&view=trainerachievementdefinitions');?>"><?php echo Text::_('COM_JUGENDTRAINING_VIEW_ALL_ACHIEVEMENTS');?></a>
 </div>
</div>
</section>
<section class="card"><div class="card-header"><h2 class="h4 mb-0"><?php echo Text::_('COM_JUGENDTRAINING_RECENT_AWARDS');?></h2></div><div class="table-responsive"><table class="table"><thead><tr><th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_ATHLETE');?></th><th><?php echo Text::_('COM_JUGENDTRAINING_ACHIEVEMENT');?></th><th><?php echo Text::_('COM_JUGENDTRAINING_AWARDED_ON');?></th><th><?php echo Text::_('COM_JUGENDTRAINING_SOURCE');?></th><th><?php echo Text::_('COM_JUGENDTRAINING_COLUMN_ACTIONS');?></th></tr></thead><tbody><?php foreach($c->awards as$a):?><tr><td><?php echo htmlspecialchars($a->athlete_name,ENT_QUOTES,'UTF-8');?></td><td><div class="d-flex align-items-center gap-2"><img src="<?php echo htmlspecialchars(Uri::root().ltrim($a->badge_image,'/'),ENT_QUOTES,'UTF-8');?>" width="44" height="44" alt=""><strong><?php echo htmlspecialchars($a->achievement_title,ENT_QUOTES,'UTF-8');?></strong></div></td><td><?php echo HTMLHelper::_('date',$a->awarded_at,Text::_('DATE_FORMAT_LC4'));?></td><td><?php echo Text::_($a->award_source==='automatic'?'COM_JUGENDTRAINING_AWARD_AUTOMATIC':'COM_JUGENDTRAINING_AWARD_MANUAL');?></td><td><form action="<?php echo Route::_('index.php?option=com_jugendtraining&task=achievement.revoke');?>" method="post" onsubmit="return confirm('<?php echo htmlspecialchars(Text::_('COM_JUGENDTRAINING_CONFIRM_REVOKE_ACHIEVEMENT'),ENT_QUOTES,'UTF-8');?>');"><input type="hidden" name="athlete_id" value="<?php echo(int)$a->athlete_id;?>"><input type="hidden" name="achievement_id" value="<?php echo(int)$a->achievement_id;?>"><button class="btn btn-sm btn-outline-danger" type="submit"><?php echo Text::_('COM_JUGENDTRAINING_REVOKE');?></button><?php echo HTMLHelper::_('form.token');?></form></td></tr><?php endforeach;?></tbody></table></div></section>
