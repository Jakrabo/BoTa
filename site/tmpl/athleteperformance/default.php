<?php
\defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;
$rows=$this->myResultDevelopment;
$width=max(640,count($rows)*70);$height=340;$left=55;$top=25;$bottom=55;$plotH=$height-$top-$bottom;$plotW=$width-$left-20;
$maxScore=$rows?max(array_map(fn($r)=>(float)$r->score,$rows)):1;
$maxAvg=10.0;$n=max(1,count($rows));$step=$plotW/$n;$points=[];
?>
<h1><?php echo Text::_('COM_JUGENDTRAINING_PERFORMANCE_DEVELOPMENT');?></h1>
<div class="card card-body mb-4"><form class="d-flex flex-wrap align-items-end gap-2" method="get">
   <input type="hidden" name="option" value="com_jugendtraining">
   <input type="hidden" name="view" value="athleteperformance">
   <div>
    <label class="form-label mb-1" for="jt-arrow-period"><?php echo Text::_('COM_JUGENDTRAINING_PERIOD'); ?></label>
    <select class="form-select" id="jt-arrow-period" name="period">
     <option value="lastweek" <?php echo ($this->myDiaryArrowSeries->period_key??'last12')==='lastweek'?'selected':''; ?>><?php echo Text::_('COM_JUGENDTRAINING_LAST_WEEK'); ?></option>
<option value="lastmonth" <?php echo ($this->myDiaryArrowSeries->period_key??'last12')==='lastmonth'?'selected':''; ?>><?php echo Text::_('COM_JUGENDTRAINING_LAST_MONTH'); ?></option>
<option value="last12" <?php echo ($this->myDiaryArrowSeries->period_key??'last12')==='last12'?'selected':''; ?>>
      <?php echo Text::_('COM_JUGENDTRAINING_LAST_12_MONTHS'); ?>
     </option>
     <?php foreach($this->availableSportYears as $sportYear): ?>
      <?php $value='sportyear_'.(int)$sportYear->id; ?>
      <option value="<?php echo $value; ?>" <?php echo ($this->myDiaryArrowSeries->period_key??'')===$value?'selected':''; ?>>
       <?php echo htmlspecialchars($sportYear->name,ENT_QUOTES,'UTF-8'); ?>
       (<?php echo \Joomla\CMS\HTML\HTMLHelper::_('date',$sportYear->date_start,Text::_('DATE_FORMAT_LC4')); ?>
       – <?php echo \Joomla\CMS\HTML\HTMLHelper::_('date',$sportYear->date_end,Text::_('DATE_FORMAT_LC4')); ?>)
      </option>
     <?php endforeach; ?>
    </select>
   </div>
   <button class="btn btn-primary" type="submit"><?php echo Text::_('COM_JUGENDTRAINING_APPLY_FILTER'); ?></button>
  </form></div>
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

<section class="mt-4 jt-arrow-volume-chart">
 <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-3">
  <div>
   <h2 class="mb-1"><?php echo Text::_('COM_JUGENDTRAINING_ARROW_VOLUME_DEVELOPMENT'); ?></h2>
   <p class="text-muted mb-0"><?php echo Text::_('COM_JUGENDTRAINING_ARROW_VOLUME_INTRO'); ?></p>
  </div>
  
 </div>

 <div class="d-flex flex-wrap gap-2 mb-3" role="group" aria-label="<?php echo Text::_('COM_JUGENDTRAINING_CHART_INTERVAL'); ?>">
  <button class="btn btn-primary jt-arrow-mode" type="button" data-mode="monthly"><?php echo Text::_('COM_JUGENDTRAINING_ARROWS_PER_MONTH'); ?></button>
  <button class="btn btn-outline-primary jt-arrow-mode" type="button" data-mode="weekly"><?php echo Text::_('COM_JUGENDTRAINING_ARROWS_PER_CALENDAR_WEEK'); ?></button>
 </div>

 <div class="card">
  <div class="card-body">
   <div class="small text-muted mb-3">
    <?php echo \Joomla\CMS\HTML\HTMLHelper::_('date',$this->myDiaryArrowSeries->date_start,Text::_('DATE_FORMAT_LC4')); ?>
    – <?php echo \Joomla\CMS\HTML\HTMLHelper::_('date',$this->myDiaryArrowSeries->date_end,Text::_('DATE_FORMAT_LC4')); ?>
   </div>
   <div class="jt-arrow-chart-wrap" aria-live="polite">
    <svg id="jt-arrow-chart" class="jt-arrow-chart" viewBox="0 0 1000 420" preserveAspectRatio="xMidYMid meet" role="img" aria-labelledby="jt-arrow-chart-title jt-arrow-chart-desc">
     <title id="jt-arrow-chart-title"><?php echo Text::_('COM_JUGENDTRAINING_ARROW_VOLUME_DEVELOPMENT'); ?></title>
     <desc id="jt-arrow-chart-desc"><?php echo Text::_('COM_JUGENDTRAINING_ARROW_VOLUME_CHART_DESCRIPTION'); ?></desc>
    </svg>
   </div>
   <p id="jt-arrow-chart-empty" class="alert alert-info mt-3 d-none"><?php echo Text::_('COM_JUGENDTRAINING_NO_DIARY_DATA_FOR_PERIOD'); ?></p>
  </div>
 </div>
</section>

<script>
document.addEventListener('DOMContentLoaded',function(){
 const monthly=<?php echo json_encode(array_map(static fn($r)=>['label'=>(string)$r->period_label,'value'=>(int)$r->arrows],(array)($this->myDiaryArrowSeries->monthly??[])),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); ?>;
 const weekly=<?php echo json_encode(array_map(static fn($r)=>['label'=>(string)$r->period_label,'value'=>(int)$r->arrows],(array)($this->myDiaryArrowSeries->weekly??[])),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); ?>;
 const svg=document.getElementById('jt-arrow-chart');
 const empty=document.getElementById('jt-arrow-chart-empty');
 const buttons=document.querySelectorAll('.jt-arrow-mode');
 const ns='http://www.w3.org/2000/svg';

 function el(name,attrs,text){
  const node=document.createElementNS(ns,name);
  Object.entries(attrs||{}).forEach(([key,value])=>node.setAttribute(key,String(value)));
  if(text!==undefined)node.textContent=text;
  return node;
 }

 function render(mode){
  const rows=mode==='weekly'?weekly:monthly;
  svg.querySelectorAll(':scope > :not(title):not(desc)').forEach(node=>node.remove());
  buttons.forEach(button=>{
   const active=button.dataset.mode===mode;
   button.classList.toggle('btn-primary',active);
   button.classList.toggle('btn-outline-primary',!active);
  });

  const hasValues=rows.some(row=>Number(row.value)>0);
  empty.classList.toggle('d-none',hasValues);
  if(!rows.length)return;

  const width=1000,height=420,left=72,right=24,top=25;
  const bottom=mode==='weekly'?92:68;
  const plotWidth=width-left-right;
  const plotHeight=height-top-bottom;
  const maxValue=Math.max(1,...rows.map(row=>Number(row.value)||0));
  const scaleStep=maxValue<=100?20:(maxValue<=500?100:250);
  const roundedMax=Math.max(scaleStep,Math.ceil(maxValue/scaleStep)*scaleStep);

  for(let i=0;i<=5;i++){
   const value=Math.round(roundedMax*i/5);
   const y=top+plotHeight-(plotHeight*i/5);
   svg.appendChild(el('line',{x1:left,y1:y,x2:left+plotWidth,y2:y,class:'jt-arrow-grid-line'}));
   svg.appendChild(el('text',{x:left-12,y:y+4,'text-anchor':'end',class:'jt-arrow-axis-label'},value.toLocaleString('de-DE')));
  }

  svg.appendChild(el('line',{x1:left,y1:top,x2:left,y2:top+plotHeight,class:'jt-arrow-axis'}));
  svg.appendChild(el('line',{x1:left,y1:top+plotHeight,x2:left+plotWidth,y2:top+plotHeight,class:'jt-arrow-axis'}));

  const step=plotWidth/Math.max(rows.length,1);
  const barWidth=Math.max(3,Math.min(mode==='weekly'?14:42,step*.68));
  const labelEvery=mode==='weekly'?Math.max(1,Math.ceil(rows.length/14)):1;

  rows.forEach((row,index)=>{
   const value=Number(row.value)||0;
   const barHeight=value/roundedMax*plotHeight;
   const x=left+index*step+(step-barWidth)/2;
   const y=top+plotHeight-barHeight;
   const bar=el('rect',{x:x,y:y,width:barWidth,height:Math.max(barHeight,value>0?2:0),rx:3,class:'jt-arrow-bar'});
   bar.appendChild(el('title',{},`${row.label}: ${value.toLocaleString('de-DE')} <?php echo addslashes(Text::_('COM_JUGENDTRAINING_ARROWS')); ?>`));
   svg.appendChild(bar);

   if(index%labelEvery===0||index===rows.length-1){
    const labelX=x+barWidth/2,labelY=top+plotHeight+18;
    const attrs={x:labelX,y:labelY,'text-anchor':mode==='weekly'?'end':'middle',class:'jt-arrow-axis-label'};
    if(mode==='weekly')attrs.transform=`rotate(-45 ${labelX} ${labelY})`;
    svg.appendChild(el('text',attrs,row.label));
   }
  });

  svg.appendChild(el('text',{
   x:18,y:top+plotHeight/2,
   transform:`rotate(-90 18 ${top+plotHeight/2})`,
   'text-anchor':'middle',class:'jt-arrow-axis-title'
  },'<?php echo addslashes(Text::_('COM_JUGENDTRAINING_ARROWS')); ?>'));
 }

 buttons.forEach(button=>button.addEventListener('click',()=>render(button.dataset.mode)));
 render('monthly');
});
</script>

<section class="mt-4">
 <h2><?php echo Text::_('COM_JUGENDTRAINING_DIARY_STATISTICS'); ?></h2>
 <div class="row g-3 mb-4">
  <div class="col-sm-6 col-xl-3"><div class="card h-100"><div class="card-body"><div class="small text-muted"><?php echo Text::_('COM_JUGENDTRAINING_DIARY_ENTRIES'); ?></div><strong class="display-6"><?php echo (int)($this->myDiaryStatistics->entry_count??0); ?></strong></div></div></div>
  <div class="col-sm-6 col-xl-3"><div class="card h-100"><div class="card-body"><div class="small text-muted"><?php echo Text::_('COM_JUGENDTRAINING_TOTAL_ARROWS'); ?></div><strong class="display-6"><?php echo number_format((int)($this->myDiaryStatistics->total_arrows??0),0,',','.'); ?></strong></div></div></div>
  <div class="col-sm-6 col-xl-3"><div class="card h-100"><div class="card-body"><div class="small text-muted"><?php echo Text::_('COM_JUGENDTRAINING_TOTAL_TRAINING_TIME'); ?></div><strong class="display-6"><?php echo number_format(((int)($this->myDiaryStatistics->total_minutes??0))/60,1,',','.'); ?> h</strong></div></div></div>
  <div class="col-sm-6 col-xl-3"><div class="card h-100"><div class="card-body"><div class="small text-muted"><?php echo Text::_('COM_JUGENDTRAINING_ARROWS_PER_HOUR'); ?></div><strong class="display-6"><?php echo number_format((float)($this->myDiaryStatistics->arrows_per_hour??0),1,',','.'); ?></strong></div></div></div>
 </div>
 <div class="row g-4">
  <div class="col-lg-6"><div class="card h-100"><div class="card-header"><h3 class="h4 mb-0"><?php echo Text::_('COM_JUGENDTRAINING_USED_TRAINING_METHODS'); ?></h3></div><div class="table-responsive"><table class="table mb-0"><thead><tr><th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_TRAINING_METHOD'); ?></th><th><?php echo Text::_('COM_JUGENDTRAINING_DIARY_ENTRIES'); ?></th><th><?php echo Text::_('COM_JUGENDTRAINING_ARROWS'); ?></th><th><?php echo Text::_('COM_JUGENDTRAINING_MINUTES'); ?></th></tr></thead><tbody>
   <?php foreach(($this->myDiaryStatistics->methods??[]) as $row): ?><tr><td><?php echo htmlspecialchars($row->label,ENT_QUOTES,'UTF-8'); ?></td><td><?php echo (int)$row->entry_count; ?></td><td><?php echo (int)$row->arrows; ?></td><td><?php echo (int)$row->minutes; ?></td></tr><?php endforeach; ?>
  </tbody></table></div></div></div>
  <div class="col-lg-6"><div class="card h-100"><div class="card-header"><h3 class="h4 mb-0"><?php echo Text::_('COM_JUGENDTRAINING_TRAINED_FOCUS_TOPICS'); ?></h3></div><div class="table-responsive"><table class="table mb-0"><thead><tr><th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_FOCUS_TOPIC'); ?></th><th><?php echo Text::_('COM_JUGENDTRAINING_DIARY_ENTRIES'); ?></th><th><?php echo Text::_('COM_JUGENDTRAINING_ARROWS'); ?></th><th><?php echo Text::_('COM_JUGENDTRAINING_MINUTES'); ?></th></tr></thead><tbody>
   <?php foreach(($this->myDiaryStatistics->focus_topics??[]) as $row): ?><tr><td><?php echo htmlspecialchars($row->label,ENT_QUOTES,'UTF-8'); ?></td><td><?php echo (int)$row->entry_count; ?></td><td><?php echo (int)$row->arrows; ?></td><td><?php echo (int)$row->minutes; ?></td></tr><?php endforeach; ?>
  </tbody></table></div></div></div>
 </div>
</section>
