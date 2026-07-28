<?php
\defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;use Joomla\CMS\Router\Route;use Joomla\CMS\HTML\HTMLHelper;
$a=$this->athlete;$period=$this->arrows->period_key??'last12';
$resultRows=$this->results;
?>
<div class="d-flex justify-content-between align-items-start gap-3 mb-4"><div><h1 class="mb-1"><?php echo htmlspecialchars($a->firstname.' '.$a->lastname,ENT_QUOTES,'UTF-8');?></h1><p class="text-muted mb-0"><?php echo htmlspecialchars((string)$a->group_names,ENT_QUOTES,'UTF-8');?></p></div><a class="btn btn-outline-secondary" href="<?php echo Route::_('index.php?option=com_jugendtraining&view=trainerathletes');?>"><?php echo Text::_('COM_JUGENDTRAINING_BACK');?></a></div>

<form class="card card-body mb-4" method="get"><input type="hidden" name="option" value="com_jugendtraining"><input type="hidden" name="view" value="trainerathletedetail"><input type="hidden" name="id" value="<?php echo(int)$a->id;?>"><div class="row g-3 align-items-end"><div class="col-md-8"><label class="form-label" for="detail-period"><?php echo Text::_('COM_JUGENDTRAINING_PERIOD');?></label><select class="form-select" id="detail-period" name="period">
<option value="lastweek" <?php echo$period==='lastweek'?'selected':'';?>><?php echo Text::_('COM_JUGENDTRAINING_LAST_WEEK');?></option>
<option value="lastmonth" <?php echo$period==='lastmonth'?'selected':'';?>><?php echo Text::_('COM_JUGENDTRAINING_LAST_MONTH');?></option>
<option value="last12" <?php echo$period==='last12'?'selected':'';?>><?php echo Text::_('COM_JUGENDTRAINING_LAST_12_MONTHS');?></option>
<?php foreach($this->sportYears as$sy):$v='sportyear_'.(int)$sy->id;?><option value="<?php echo$v;?>" <?php echo$period===$v?'selected':'';?>><?php echo htmlspecialchars($sy->name,ENT_QUOTES,'UTF-8');?></option><?php endforeach;?></select></div><div class="col-md-4"><button class="btn btn-primary w-100" type="submit"><?php echo Text::_('COM_JUGENDTRAINING_APPLY_FILTER');?></button></div></div></form>

<div class="row g-4 mb-4">
<div class="col-lg-4"><div class="card h-100"><div class="card-header d-flex justify-content-between align-items-center"><h2 class="h4 mb-0"><?php echo Text::_('COM_JUGENDTRAINING_CONTACT');?></h2><a class="btn btn-sm btn-outline-primary" href="<?php echo Route::_('index.php?option=com_jugendtraining&view=trainerathleteedit&id='.(int)$a->id);?>"><?php echo Text::_('JACTION_EDIT');?></a></div><div class="card-body"><dl><dt><?php echo Text::_('COM_JUGENDTRAINING_FIELD_PHONE');?></dt><dd><?php echo htmlspecialchars((string)$a->phone,ENT_QUOTES,'UTF-8');?></dd><dt>E-Mail</dt><dd><?php echo htmlspecialchars((string)$a->email,ENT_QUOTES,'UTF-8');?></dd><dt><?php echo Text::_('COM_JUGENDTRAINING_FIELD_CLASS');?></dt><dd><?php echo htmlspecialchars((string)$a->class_name,ENT_QUOTES,'UTF-8');?></dd><dt><?php echo Text::_('COM_JUGENDTRAINING_FIELD_BOW_TYPE');?></dt><dd><?php echo htmlspecialchars((string)$a->bow_type,ENT_QUOTES,'UTF-8');?></dd></dl></div></div></div>
<div class="col-lg-8"><div class="card h-100"><div class="card-header"><h2 class="h4 mb-0"><?php echo Text::_('COM_JUGENDTRAINING_RECENT_ATTENDANCE');?></h2></div><div class="table-responsive"><table class="table mb-0"><thead><tr><th><?php echo Text::_('JDATE');?></th><th><?php echo Text::_('COM_JUGENDTRAINING_TRAINING');?></th><th><?php echo Text::_('JSTATUS');?></th></tr></thead><tbody><?php foreach($this->attendance as$r):?><tr><td><?php echo HTMLHelper::_('date',$r->training_date,Text::_('DATE_FORMAT_LC4'));?></td><td><?php echo htmlspecialchars($r->title,ENT_QUOTES,'UTF-8');?></td><td><?php echo htmlspecialchars($r->status,ENT_QUOTES,'UTF-8');?></td></tr><?php endforeach;?></tbody></table></div></div></div>
</div>

<div class="row g-4 mb-4">
<div class="col-lg-6"><div class="card h-100"><div class="card-header d-flex justify-content-between align-items-center"><h2 class="h4 mb-0"><?php echo Text::_('COM_JUGENDTRAINING_TRAINER_NOTES');?></h2><a class="btn btn-sm btn-primary" href="<?php echo Route::_('index.php?option=com_jugendtraining&view=trainernotesfront&athlete_id='.(int)$a->id.'#note-editor');?>"><?php echo Text::_('COM_JUGENDTRAINING_ADD_NOTE_FOR_ATHLETE');?></a></div><div class="list-group list-group-flush"><?php foreach($this->notes as$n):?><a class="list-group-item list-group-item-action" href="<?php echo Route::_('index.php?option=com_jugendtraining&view=trainernotesfront&edit_id='.(int)$n->id.'&athlete_id='.(int)$a->id.'#note-editor');?>"><div class="small text-muted"><?php echo HTMLHelper::_('date',$n->note_date,Text::_('DATE_FORMAT_LC4'));?> · <?php echo Text::_($n->status==='done'?'COM_JUGENDTRAINING_NOTE_DONE':'COM_JUGENDTRAINING_NOTE_CURRENT');?></div><?php echo nl2br(htmlspecialchars($n->note,ENT_QUOTES,'UTF-8'));?></a><?php endforeach;?></div></div></div>
<div class="col-lg-6"><div class="card h-100"><div class="card-header d-flex justify-content-between align-items-center"><h2 class="h4 mb-0"><?php echo Text::_('COM_JUGENDTRAINING_TRAINING_TASKS');?></h2><a class="btn btn-sm btn-primary" href="<?php echo Route::_('index.php?option=com_jugendtraining&view=trainerathletetask&athlete_id='.(int)$a->id);?>"><?php echo Text::_('COM_JUGENDTRAINING_ADD_TRAINING_TASK');?></a></div><div class="list-group list-group-flush"><?php foreach($this->tasks as$t):?><a class="list-group-item list-group-item-action" href="<?php echo Route::_('index.php?option=com_jugendtraining&view=trainerathletetask&athlete_id='.(int)$a->id.'&assignment_id='.(int)$t->assignment_id);?>"><strong><?php echo htmlspecialchars($t->program_title,ENT_QUOTES,'UTF-8');?></strong><div><?php echo(int)$t->completed?'✓':'○';?> <?php echo htmlspecialchars($t->exercise_title,ENT_QUOTES,'UTF-8');?></div></a><?php endforeach;?></div></div></div>
</div>

<div class="card mb-4">
 <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
  <div class="d-flex align-items-center gap-2">
   <h2 class="h4 mb-0"><?php echo Text::_('COM_JUGENDTRAINING_PENALTIES');?></h2>
   <span class="badge <?php echo $this->openPenalties?'text-bg-warning':'text-bg-success';?>"><?php echo count($this->openPenalties);?> <?php echo Text::_('COM_JUGENDTRAINING_OPEN');?></span>
  </div>
  <a class="btn btn-sm btn-primary" href="<?php echo Route::_('index.php?option=com_jugendtraining&view=trainerpenalties&athlete_id='.(int)$a->id.'#penalty-editor');?>">
   <?php echo Text::_('COM_JUGENDTRAINING_ADD_PENALTY');?>
  </a>
 </div>
 <div class="card-body">
  <?php if($this->openPenalties):?>
   <div class="table-responsive">
    <table class="table align-middle mb-0">
     <thead><tr>
      <th><?php echo Text::_('JDATE');?></th>
      <th><?php echo Text::_('COM_JUGENDTRAINING_PENALTY');?></th>
      <th><?php echo Text::_('COM_JUGENDTRAINING_PENALTY_VALUE');?></th>
      <th><?php echo Text::_('COM_JUGENDTRAINING_REASON_NOTE');?></th>
     </tr></thead>
     <tbody>
     <?php foreach($this->openPenalties as$penalty):?>
      <tr class="table-warning">
       <td><?php echo HTMLHelper::_('date',$penalty->assigned_at,Text::_('DATE_FORMAT_LC4'));?></td>
       <td><strong><?php echo htmlspecialchars($penalty->title,ENT_QUOTES,'UTF-8');?></strong></td>
       <td>
        <?php if($penalty->penalty_type==='monetary'):?>
         <?php echo number_format((float)$penalty->amount_snapshot,2,',','.');?> €
        <?php else:?>
         <?php echo htmlspecialchars((string)$penalty->action_snapshot,ENT_QUOTES,'UTF-8');?>
        <?php endif;?>
       </td>
       <td><?php echo htmlspecialchars((string)$penalty->reason_note,ENT_QUOTES,'UTF-8');?></td>
      </tr>
     <?php endforeach;?>
     </tbody>
    </table>
   </div>
  <?php else:?>
   <p class="text-muted mb-0"><?php echo Text::_('COM_JUGENDTRAINING_NO_OPEN_PENALTIES_FOR_ATHLETE');?></p>
  <?php endif;?>
 </div>
</div>

<section class="mb-4">
<h2><?php echo Text::_('COM_JUGENDTRAINING_PERFORMANCE_DEVELOPMENT');?></h2>
<p class="text-muted"><?php echo Text::_('COM_JUGENDTRAINING_RESULT_CHART_DESCRIPTION');?></p>
<div class="card"><div class="card-body"><div class="jt-detail-chart-wrap"><svg id="jt-detail-result-chart" class="jt-detail-chart" viewBox="0 0 1000 420" role="img" aria-label="<?php echo Text::_('COM_JUGENDTRAINING_PERFORMANCE_CHART');?>"></svg></div></div></div>
</section>

<section>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3"><div><h2 class="mb-1"><?php echo Text::_('COM_JUGENDTRAINING_ARROW_VOLUME_DEVELOPMENT');?></h2><p class="text-muted mb-0"><?php echo Text::_('COM_JUGENDTRAINING_ARROW_VOLUME_INTRO');?></p></div><div class="btn-group"><button class="btn btn-primary jt-detail-arrow-mode" data-mode="monthly" type="button"><?php echo Text::_('COM_JUGENDTRAINING_ARROWS_PER_MONTH');?></button><button class="btn btn-outline-primary jt-detail-arrow-mode" data-mode="weekly" type="button"><?php echo Text::_('COM_JUGENDTRAINING_ARROWS_PER_CALENDAR_WEEK');?></button></div></div>
<div class="card"><div class="card-body"><div class="jt-detail-chart-wrap"><svg id="jt-detail-arrow-chart" class="jt-detail-chart" viewBox="0 0 1000 420" role="img" aria-label="<?php echo Text::_('COM_JUGENDTRAINING_ARROW_VOLUME_CHART_DESCRIPTION');?>"></svg></div></div></div>
</section>

<script>
document.addEventListener('DOMContentLoaded',function(){
 const ns='http://www.w3.org/2000/svg';
 const results=<?php echo json_encode(array_map(static fn($r)=>['label'=>(string)$r->result_date,'score'=>(float)$r->score,'average'=>(float)$r->average],$resultRows),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);?>;
 const monthly=<?php echo json_encode(array_map(static fn($r)=>['label'=>(string)$r->period_label,'value'=>(int)$r->arrows],(array)$this->arrows->monthly),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);?>;
 const weekly=<?php echo json_encode(array_map(static fn($r)=>['label'=>(string)$r->period_label,'value'=>(int)$r->arrows],(array)$this->arrows->weekly),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);?>;
 function el(name,attrs,text){const n=document.createElementNS(ns,name);Object.entries(attrs||{}).forEach(([k,v])=>n.setAttribute(k,String(v)));if(text!==undefined)n.textContent=text;return n;}
 function axes(svg,max,labelCount){const l=65,t=25,pw=900,ph=315;for(let i=0;i<=5;i++){const y=t+ph-ph*i/5;svg.appendChild(el('line',{x1:l,y1:y,x2:l+pw,y2:y,class:'jt-arrow-grid-line'}));svg.appendChild(el('text',{x:l-10,y:y+4,'text-anchor':'end',class:'jt-arrow-axis-label'},Math.round(max*i/5)));}svg.appendChild(el('line',{x1:l,y1:t,x2:l,y2:t+ph,class:'jt-arrow-axis'}));svg.appendChild(el('line',{x1:l,y1:t+ph,x2:l+pw,y2:t+ph,class:'jt-arrow-axis'}));return{l,t,pw,ph,step:pw/Math.max(labelCount,1)};}
 function renderResults(){
  const svg=document.getElementById('jt-detail-result-chart');
  svg.innerHTML='';
  const maxScore=Math.max(1,...results.map(r=>Number(r.score)||0));
  const roundedMax=Math.max(50,Math.ceil(maxScore/50)*50);
  const a=axes(svg,roundedMax,results.length);
  const pts=[];
  results.forEach((r,i)=>{
   const score=Number(r.score)||0;
   const average=Math.max(0,Math.min(10,Number(r.average)||0));
   const bw=Math.min(42,a.step*.55);
   const x=a.l+i*a.step+(a.step-bw)/2;
   const bh=score/roundedMax*a.ph;
   const y=a.t+a.ph-bh;
   const rect=el('rect',{x,y,width:bw,height:bh,rx:3,class:'jt-result-score-bar',fill:'#2f6fb0',stroke:'none',opacity:'0.82'});
   rect.appendChild(el('title',{},score+' <?php echo addslashes(Text::_('COM_JUGENDTRAINING_SCORE_UNIT'));?>'));
   svg.appendChild(rect);

   const px=x+bw/2;
   const py=a.t+a.ph-(average/10)*a.ph;
   pts.push(px+','+py);

   const point=el('circle',{cx:px,cy:py,r:5,class:'jt-result-average-point',fill:'#f05a28',stroke:'#ffffff','stroke-width':2});
   point.appendChild(el('title',{},'Ø '+average.toLocaleString('de-DE',{minimumFractionDigits:2,maximumFractionDigits:2})));
   svg.appendChild(point);

   if(i%Math.max(1,Math.ceil(results.length/12))===0){
    svg.appendChild(el('text',{x:px,y:a.t+a.ph+20,'text-anchor':'middle',class:'jt-arrow-axis-label'},r.label.slice(5)));
   }
  });
  if(pts.length>1){
   svg.appendChild(el('polyline',{points:pts.join(' '),fill:'none',stroke:'#f05a28','stroke-width':4,'stroke-linecap':'round','stroke-linejoin':'round',class:'jt-result-average-line'}));
  }
  svg.appendChild(el('rect',{x:75,y:7,width:16,height:10,rx:2,fill:'#2f6fb0'}));
  svg.appendChild(el('text',{x:98,y:17,class:'jt-arrow-axis-title'},'<?php echo addslashes(Text::_('COM_JUGENDTRAINING_SCORE_SERIES'));?>'));
  svg.appendChild(el('line',{x1:245,y1:12,x2:275,y2:12,stroke:'#f05a28','stroke-width':4,'stroke-linecap':'round'}));
  svg.appendChild(el('circle',{cx:260,cy:12,r:4,fill:'#f05a28',stroke:'#ffffff','stroke-width':1.5}));
  svg.appendChild(el('text',{x:285,y:17,class:'jt-arrow-axis-title'},'<?php echo addslashes(Text::_('COM_JUGENDTRAINING_AVERAGE_SERIES'));?>'));
}
 function renderArrows(mode){const svg=document.getElementById('jt-detail-arrow-chart'),rows=mode==='weekly'?weekly:monthly;svg.innerHTML='';document.querySelectorAll('.jt-detail-arrow-mode').forEach(b=>{const active=b.dataset.mode===mode;b.classList.toggle('btn-primary',active);b.classList.toggle('btn-outline-primary',!active);});const max=Math.max(1,...rows.map(r=>r.value)),rounded=Math.max(50,Math.ceil(max/50)*50),a=axes(svg,rounded,rows.length);rows.forEach((r,i)=>{const bw=Math.max(4,Math.min(mode==='weekly'?14:42,a.step*.65)),x=a.l+i*a.step+(a.step-bw)/2,bh=r.value/rounded*a.ph,y=a.t+a.ph-bh;const rect=el('rect',{x,y,width:bw,height:bh,rx:3,class:'jt-arrow-bar'});rect.appendChild(el('title',{},r.label+': '+r.value+' <?php echo addslashes(Text::_('COM_JUGENDTRAINING_ARROWS'));?>'));svg.appendChild(rect);if(i%Math.max(1,Math.ceil(rows.length/12))===0)svg.appendChild(el('text',{x:x+bw/2,y:a.t+a.ph+20,'text-anchor':'middle',class:'jt-arrow-axis-label'},r.label));});}
 renderResults();renderArrows('monthly');document.querySelectorAll('.jt-detail-arrow-mode').forEach(b=>b.addEventListener('click',()=>renderArrows(b.dataset.mode)));
});
</script>
