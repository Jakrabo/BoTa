<?php
namespace Jugendtraining\Component\Jugendtraining\Site\Model;
\defined('_JEXEC') or die;
use Joomla\CMS\Factory; use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Jugendtraining\Component\Jugendtraining\Site\Service\CalendarService;
class DashboardModel extends BaseDatabaseModel
{
 public function getAthlete(): ?object { $user=Factory::getApplication()->getIdentity(); if($user->guest)return null; $db=$this->getDatabase();$q=$db->getQuery(true)->select('a.*,c.name club_name,cl.name class_name,t.name trainer_name')->from($db->quoteName('#__jt_athletes','a'))->leftJoin($db->quoteName('#__jt_clubs','c').' ON c.id=a.club_id')->leftJoin($db->quoteName('#__jt_classes','cl').' ON cl.id=a.class_id')->leftJoin($db->quoteName('#__users','t').' ON t.id=a.trainer_user_id')->where('a.user_id='.(int)$user->id)->where('a.published=1');$db->setQuery($q,0,1);return $db->loadObject() ?: null; }

    public function getMyResults(): array
    {
        if (!$this->hasUserColumn()) {
            return [];
        }

        $userId = (int) \Joomla\CMS\Factory::getApplication()->getIdentity()->id;

        if ($userId <= 0) {
            return [];
        }

        $db = $this->getDatabase();

        $query = $db->getQuery(true)
            ->select([
                'r.id',
                'r.result_date',
                'r.event_type',
                'r.event_name',
                'r.distance_m',
                'r.arrows',
                'r.score',
                'r.average',
                'r.verification_status',
            ])
            ->from($db->quoteName('#__jt_results', 'r'))
            ->innerJoin(
                $db->quoteName('#__jt_athletes', 'a')
                . ' ON ' . $db->quoteName('a.id') . ' = ' . $db->quoteName('r.athlete_id')
            )
            ->where($db->quoteName('a.user_id') . ' = ' . $userId)
            ->where($db->quoteName('r.published') . ' = 1')
            ->order($db->quoteName('r.result_date') . ' DESC, ' . $db->quoteName('r.id') . ' DESC');

        $db->setQuery($query, 0, 20);

        return $db->loadObjectList();
    }

    private function hasUserColumn(): bool
    {
        $db = $this->getDatabase();
        $columns = $db->getTableColumns('#__jt_athletes', false);

        return isset($columns['user_id']);
    }

    public function getMyPrograms(): array
    {
        $userId = (int) Factory::getApplication()->getIdentity()->id;
        if ($userId <= 0) return [];
        $db = $this->getDatabase();
        $q = $db->getQuery(true)
            ->select(['ap.id assignment_id','ap.due_date','p.id program_id','p.title program_title','p.description program_description','p.category',
                'e.id exercise_id','e.title exercise_title','e.description exercise_description','e.material','e.video_url','e.image_url','e.difficulty',
                'COALESCE(pp.completed,0) completed'])
            ->from($db->quoteName('#__jt_athlete_programs','ap'))
            ->innerJoin($db->quoteName('#__jt_athletes','a').' ON a.id=ap.athlete_id')
            ->innerJoin($db->quoteName('#__jt_training_programs','p').' ON p.id=ap.program_id')
            ->innerJoin($db->quoteName('#__jt_program_exercises','pe').' ON pe.program_id=p.id')
            ->innerJoin($db->quoteName('#__jt_exercises','e').' ON e.id=pe.exercise_id')
            ->leftJoin($db->quoteName('#__jt_program_progress','pp').' ON pp.athlete_program_id=ap.id AND pp.exercise_id=e.id')
            ->where('a.user_id=' . $userId)->where('a.published=1')->where('ap.active=1')->where('p.published=1')->where('e.published=1')
            ->where('(ap.completed_at IS NULL OR ap.completed_at >= DATE_SUB(NOW(), INTERVAL 14 DAY))')
            ->order('p.title, pe.ordering, e.ordering, e.title');
        $db->setQuery($q);
        $rows=$db->loadObjectList();$programs=[];
        foreach($rows as $r){
            $id=(int)$r->assignment_id;
            if(!isset($programs[$id]))$programs[$id]=(object)['assignment_id'=>$id,'program_id'=>(int)$r->program_id,'title'=>$r->program_title,'description'=>$r->program_description,'category'=>$r->category,'due_date'=>$r->due_date,'exercises'=>[],'completed_count'=>0,'exercise_count'=>0];
            $programs[$id]->exercises[]=$r;$programs[$id]->exercise_count++;if((int)$r->completed)$programs[$id]->completed_count++;
        }
        return array_values($programs);
    }

    public function getMyGoals(): array
    {
        $userId = (int) Factory::getApplication()->getIdentity()->id;

        if ($userId <= 0) {
            return [];
        }

        $db = $this->getDatabase();

        $q = $db->getQuery(true)
            ->select('g.*')
            ->from($db->quoteName('#__jt_goals', 'g'))
            ->innerJoin($db->quoteName('#__jt_athletes', 'a') . ' ON a.id = g.athlete_id')
            ->where('a.user_id = ' . $userId)
            ->where('a.published = 1')
            ->where('g.published = 1')
            ->order('g.completed ASC, g.due_date ASC, g.id DESC');

        $db->setQuery($q);
        $goals = $db->loadObjectList();

        foreach ($goals as $goal) {
            if ((string) $goal->calculation_mode === 'automatic' && (string) $goal->target_type !== 'custom') {
                $goal->current_value = $this->calculateGoalMetric($goal);
                $goal->completed = ((float) $goal->target_value > 0 && (float) $goal->current_value >= (float) $goal->target_value)
                    ? 1
                    : 0;
            }
        }

        return $goals;
    }

    private function calculateGoalMetric(object $goal): float
    {
        return match ((string) $goal->target_type) {
            'attendance' => $this->calculateGoalAttendance((int) $goal->athlete_id),
            'score' => $this->calculateGoalBestScore(
                (int) $goal->athlete_id,
                (int) $goal->distance_m,
                (int) $goal->arrows
            ),
            'average' => $this->calculateGoalBestAverage(
                (int) $goal->athlete_id,
                (int) $goal->distance_m,
                (int) $goal->arrows
            ),
            'program' => $this->calculateGoalProgram(
                (int) $goal->athlete_id,
                (int) $goal->program_id
            ),
            default => (float) $goal->current_value,
        };
    }

    private function calculateGoalAttendance(int $athleteId): float
    {
        $db = $this->getDatabase();
        $columns = $db->getTableColumns('#__jt_attendance', false);

        $q = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__jt_attendance'))
            ->where('athlete_id = ' . $athleteId);

        if (isset($columns['status'])) {
            $q->where("status IN ('present','late','anwesend','verspaetet')");
        } elseif (isset($columns['present'])) {
            $q->where('present = 1');
        }

        $db->setQuery($q);

        return (float) $db->loadResult();
    }

    private function calculateGoalBestScore(int $athleteId, int $distance, int $arrows): float
    {
        $db = $this->getDatabase();
        $q = $db->getQuery(true)
            ->select('MAX(score)')
            ->from($db->quoteName('#__jt_results'))
            ->where('athlete_id = ' . $athleteId)
            ->where('published = 1');

        if ($distance > 0) {
            $q->where('distance_m = ' . $distance);
        }

        if ($arrows > 0) {
            $q->where('arrows = ' . $arrows);
        }

        $db->setQuery($q);

        return (float) ($db->loadResult() ?: 0);
    }

    private function calculateGoalBestAverage(int $athleteId, int $distance, int $arrows): float
    {
        $db = $this->getDatabase();
        $q = $db->getQuery(true)
            ->select('MAX(average)')
            ->from($db->quoteName('#__jt_results'))
            ->where('athlete_id = ' . $athleteId)
            ->where('published = 1');

        if ($distance > 0) {
            $q->where('distance_m = ' . $distance);
        }

        if ($arrows > 0) {
            $q->where('arrows = ' . $arrows);
        }

        $db->setQuery($q);

        return (float) ($db->loadResult() ?: 0);
    }

    private function calculateGoalProgram(int $athleteId, int $programId): float
    {
        if ($programId <= 0) {
            return 0;
        }

        $db = $this->getDatabase();

        $q = $db->getQuery(true)
            ->select([
                'COUNT(DISTINCT pe.exercise_id) exercise_count',
                'SUM(CASE WHEN pp.completed = 1 THEN 1 ELSE 0 END) completed_count',
            ])
            ->from($db->quoteName('#__jt_athlete_programs', 'ap'))
            ->innerJoin($db->quoteName('#__jt_program_exercises', 'pe') . ' ON pe.program_id = ap.program_id')
            ->leftJoin(
                $db->quoteName('#__jt_program_progress', 'pp')
                . ' ON pp.athlete_program_id = ap.id AND pp.exercise_id = pe.exercise_id'
            )
            ->where('ap.athlete_id = ' . $athleteId)
            ->where('ap.program_id = ' . $programId)
            ->where('ap.active = 1');

        $db->setQuery($q);
        $row = $db->loadObject();

        if (!$row || (int) $row->exercise_count <= 0) {
            return 0;
        }

        return round(((int) $row->completed_count * 100) / (int) $row->exercise_count, 2);
    }

    public function getMyPersonalBests(): array
    {
        $userId=(int)Factory::getApplication()->getIdentity()->id;
        if($userId<=0)return [];
        $db=$this->getDatabase();
        $q=$db->getQuery(true)
          ->select(['r.distance_m','r.arrows','MAX(r.score) best_score','MAX(r.average) best_average'])
          ->from($db->quoteName('#__jt_results','r'))
          ->innerJoin($db->quoteName('#__jt_athletes','a').' ON a.id=r.athlete_id')
          ->where('a.user_id='.(int)$userId)->where('a.published=1')->where('r.published=1')
          ->group(['r.distance_m','r.arrows'])->order('r.distance_m ASC, r.arrows ASC');
        $db->setQuery($q);return $db->loadObjectList();
    }

    public function getMyResultDevelopment(): array
    {
        $userId=(int)Factory::getApplication()->getIdentity()->id;
        if($userId<=0)return [];
        $db=$this->getDatabase();
        $q=$db->getQuery(true)
          ->select(['r.result_date','r.distance_m','r.arrows','r.score','r.average','r.event_name'])
          ->from($db->quoteName('#__jt_results','r'))
          ->innerJoin($db->quoteName('#__jt_athletes','a').' ON a.id=r.athlete_id')
          ->where('a.user_id='.(int)$userId)->where('a.published=1')->where('r.published=1');
        $period=Factory::getApplication()->input->getCmd('period','last12');
        $today=new \DateTimeImmutable('today');if($period==='lastweek'){$start=$today->modify('monday last week');$end=$start->modify('+6 days');}elseif($period==='lastmonth'){$start=$today->modify('first day of last month');$end=$today->modify('last day of last month');}else{$end=$today;$start=$today->modify('first day of this month')->modify('-11 months');}
        if(str_starts_with($period,'sportyear_')){
         $sid=(int)substr($period,10);$sq=$db->getQuery(true)->select(['date_start','date_end'])->from('#__jt_sportyears')->where('id='.$sid)->where('published=1');$db->setQuery($sq);$sy=$db->loadObject();
         if($sy){$start=new \DateTimeImmutable($sy->date_start);$end=new \DateTimeImmutable($sy->date_end);}
        }
        $q->where('r.result_date>='.$db->quote($start->format('Y-m-d')))->where('r.result_date<='.$db->quote($end->format('Y-m-d')))
          ->order('r.result_date ASC, r.id ASC');
        $db->setQuery($q,0,30);return $db->loadObjectList();
    }

    public function getVisibleTrainerNotes(): array
    {
        $userId=(int)Factory::getApplication()->getIdentity()->id;
        if($userId<=0)return [];
        $db=$this->getDatabase();
        $q=$db->getQuery(true)->select(['n.note_date','n.category','n.note'])
          ->from($db->quoteName('#__jt_trainer_notes','n'))
          ->innerJoin($db->quoteName('#__jt_athletes','a').' ON a.id=n.athlete_id')
          ->where('a.user_id='.(int)$userId)->where('a.published=1')->where('n.private_note=0')
          ->order('n.note_date DESC, n.id DESC');
        $db->setQuery($q,0,10);return $db->loadObjectList();
    }


public function getMyOpenPenalties(): array
{
 $athlete=$this->getAthlete();if(!$athlete)return[];
 $db=$this->getDatabase();
 $q=$db->getQuery(true)->select(['r.*','d.title','d.penalty_type'])
  ->from($db->quoteName('#__jt_penalty_register','r'))
  ->innerJoin($db->quoteName('#__jt_penalty_definitions','d').' ON d.id=r.penalty_definition_id')
  ->where('r.athlete_id='.(int)$athlete->id)->where("r.status='open'")
  ->order('r.assigned_at DESC,r.id DESC');
 $db->setQuery($q);
 return$db->loadObjectList();
}


public function getUpcomingCalendarEvents(): array
{
 $calendar=new CalendarService();
 if(!$calendar->canReadCalendar())return[];
 $events=$calendar->events(['mode'=>'future'],true,false);
 return array_slice($events,0,3);
}

public function getCalendarCategoryMap(): array
{
 $calendar=new CalendarService();
 return $calendar->categoryMap(false);
}

public function getAthleteDashboardConfig(): array
{
 $defaults=[['key'=>'profile','visible'=>1],['key'=>'calendar','visible'=>1],['key'=>'results','visible'=>1],['key'=>'penalties','visible'=>1],['key'=>'achievements','visible'=>1],['key'=>'programs','visible'=>1],['key'=>'overview','visible'=>1],['key'=>'performance','visible'=>1]];
 $db=$this->getDatabase();$q=$db->getQuery(true)->select('setting_value')->from('#__jt_settings')->where('setting_key='.$db->quote('athlete_dashboard_config'));
 $db->setQuery($q);$saved=json_decode((string)$db->loadResult(),true);return is_array($saved)?$saved:$defaults;
}
public function getAvailableSportYears(): array
{
 $db=$this->getDatabase();
 $q=$db->getQuery(true)->select(['id','name','date_start','date_end','is_current'])
  ->from($db->quoteName('#__jt_sportyears'))
  ->where('published=1')
  ->order('date_start DESC,id DESC');
 $db->setQuery($q);
 return$db->loadObjectList();
}

public function getMyDiaryArrowSeries(): object
{
 $athlete=$this->getAthlete();
 $result=(object)[
  'period_key'=>'last12',
  'date_start'=>null,
  'date_end'=>null,
  'monthly'=>[],
  'weekly'=>[]
 ];
 if(!$athlete)return$result;

 $app=Factory::getApplication();
 $period=$app->input->getCmd('period','last12');
 $today=new \DateTimeImmutable('today');
 $start=null;$end=null;

 if(str_starts_with($period,'sportyear_')){
  $sportYearId=(int)substr($period,10);
  $db=$this->getDatabase();
  $q=$db->getQuery(true)->select(['id','date_start','date_end'])
   ->from($db->quoteName('#__jt_sportyears'))
   ->where('id='.$sportYearId)->where('published=1');
  $db->setQuery($q);
  $sportYear=$db->loadObject();
  if($sportYear){
   $start=new \DateTimeImmutable($sportYear->date_start);
   $end=new \DateTimeImmutable($sportYear->date_end);
   $result->period_key='sportyear_'.$sportYearId;
  }
 }

 if(!$start||!$end){
  if($period==='lastweek'){$start=$today->modify('monday last week');$end=$start->modify('+6 days');$result->period_key='lastweek';}
  elseif($period==='lastmonth'){$start=$today->modify('first day of last month');$end=$today->modify('last day of last month');$result->period_key='lastmonth';}
  else{$end=$today;$start=$today->modify('first day of this month')->modify('-11 months');$result->period_key='last12';}
 }

 $result->date_start=$start->format('Y-m-d');
 $result->date_end=$end->format('Y-m-d');
 $db=$this->getDatabase();

 $q=$db->getQuery(true)->select([
  "DATE_FORMAT(training_date,'%Y-%m') period_key",
  "DATE_FORMAT(training_date,'%m.%Y') period_label",
  'COALESCE(SUM(arrow_count),0) arrows'
 ])->from($db->quoteName('#__jt_training_diary'))
  ->where('athlete_id='.(int)$athlete->id)
  ->where('training_date>='.$db->quote($result->date_start))
  ->where('training_date<='.$db->quote($result->date_end))
  ->group("DATE_FORMAT(training_date,'%Y-%m')")
  ->order("DATE_FORMAT(training_date,'%Y-%m')");
 $db->setQuery($q);
 $monthlyRows=$db->loadObjectList('period_key');

 $monthCursor=$start->modify('first day of this month');
 $monthEnd=$end->modify('first day of this month');
 while($monthCursor<=$monthEnd){
  $key=$monthCursor->format('Y-m');
  $result->monthly[]=(object)[
   'period_key'=>$key,
   'period_label'=>$monthCursor->format('m.Y'),
   'arrows'=>isset($monthlyRows[$key])?(int)$monthlyRows[$key]->arrows:0
  ];
  $monthCursor=$monthCursor->modify('+1 month');
 }

 $q=$db->getQuery(true)->select([
  "DATE_FORMAT(training_date,'%x-%v') period_key",
  "CONCAT('KW ',DATE_FORMAT(training_date,'%v'),' / ',DATE_FORMAT(training_date,'%x')) period_label",
  'COALESCE(SUM(arrow_count),0) arrows'
 ])->from($db->quoteName('#__jt_training_diary'))
  ->where('athlete_id='.(int)$athlete->id)
  ->where('training_date>='.$db->quote($result->date_start))
  ->where('training_date<='.$db->quote($result->date_end))
  ->group("DATE_FORMAT(training_date,'%x-%v')")
  ->order("DATE_FORMAT(training_date,'%x-%v')");
 $db->setQuery($q);
 $weeklyRows=$db->loadObjectList('period_key');

 $weekCursor=$start->modify('monday this week');
 $weekEnd=$end->modify('monday this week');
 while($weekCursor<=$weekEnd){
  $isoYear=$weekCursor->format('o');
  $isoWeek=$weekCursor->format('W');
  $key=$isoYear.'-'.$isoWeek;
  $result->weekly[]=(object)[
   'period_key'=>$key,
   'period_label'=>'KW '.$isoWeek.' / '.$isoYear,
   'arrows'=>isset($weeklyRows[$key])?(int)$weeklyRows[$key]->arrows:0
  ];
  $weekCursor=$weekCursor->modify('+1 week');
 }

 return$result;
}

public function getMyDiaryStatistics(): object
 {
  $athlete=$this->getAthlete();$stats=(object)['total_arrows'=>0,'total_minutes'=>0,'arrows_per_hour'=>0.0,'entry_count'=>0,'methods'=>[],'focus_topics'=>[]];if(!$athlete)return$stats;
  $period=Factory::getApplication()->input->getCmd('period','last12');$today=new \DateTimeImmutable('today');if($period==='lastweek'){$start=$today->modify('monday last week');$end=$start->modify('+6 days');}elseif($period==='lastmonth'){$start=$today->modify('first day of last month');$end=$today->modify('last day of last month');}else{$end=$today;$start=$today->modify('first day of this month')->modify('-11 months');}
  $db=$this->getDatabase();
  if(str_starts_with($period,'sportyear_')){$sid=(int)substr($period,10);$sq=$db->getQuery(true)->select(['date_start','date_end'])->from('#__jt_sportyears')->where('id='.$sid)->where('published=1');$db->setQuery($sq);$sy=$db->loadObject();if($sy){$start=new \DateTimeImmutable($sy->date_start);$end=new \DateTimeImmutable($sy->date_end);}}
  $where=['athlete_id='.(int)$athlete->id,'training_date>='.$db->quote($start->format('Y-m-d')),'training_date<='.$db->quote($end->format('Y-m-d'))];
  $q=$db->getQuery(true)->select(['COUNT(*) entry_count','COALESCE(SUM(arrow_count),0) total_arrows','COALESCE(SUM(duration_minutes),0) total_minutes'])->from('#__jt_training_diary')->where($where);$db->setQuery($q);$s=$db->loadObject();
  if($s){$stats->entry_count=(int)$s->entry_count;$stats->total_arrows=(int)$s->total_arrows;$stats->total_minutes=(int)$s->total_minutes;$stats->arrows_per_hour=$stats->total_minutes>0?round($stats->total_arrows/($stats->total_minutes/60),1):0;}
  foreach(['methods'=>['training_method','training_method'],'focus_topics'=>['focus_topic','focus_topic']] as$target=>$cfg){$q=$db->getQuery(true)->select([$cfg[0].' label','COUNT(*) entry_count','COALESCE(SUM(arrow_count),0) arrows','COALESCE(SUM(duration_minutes),0) minutes'])->from('#__jt_training_diary')->where($where)->where($cfg[0].' IS NOT NULL')->where($cfg[0]."<>''")->group($cfg[1])->order('entry_count DESC,'.$cfg[1]);$db->setQuery($q);$stats->$target=$db->loadObjectList();}
  return$stats;
 }
 public function getMyAchievements(): array
 {
  $athlete=$this->getAthlete();if(!$athlete)return[];
  (new \Jugendtraining\Component\Jugendtraining\Site\Service\AchievementService($this->getDatabase()))->evaluateAthlete((int)$athlete->id);
  $db=$this->getDatabase();$q=$db->getQuery(true)->select(['aa.*','b.title','b.description','b.category','b.badge_image','b.award_mode'])
   ->from($db->quoteName('#__jt_athlete_achievements','aa'))->innerJoin($db->quoteName('#__jt_achievements','b').' ON b.id=aa.achievement_id')
   ->where('aa.athlete_id='.(int)$athlete->id)->where('aa.revoked_at IS NULL')->where('b.published=1')->order('aa.awarded_at DESC,aa.id DESC');
  $db->setQuery($q,0,3);return$db->loadObjectList();
 }
 public function getMyAchievementCount(): int
 {
  $athlete=$this->getAthlete();if(!$athlete)return 0;$db=$this->getDatabase();$q=$db->getQuery(true)->select('COUNT(*)')->from($db->quoteName('#__jt_athlete_achievements','aa'))->innerJoin($db->quoteName('#__jt_achievements','b').' ON b.id=aa.achievement_id')->where('aa.athlete_id='.(int)$athlete->id)->where('aa.revoked_at IS NULL')->where('b.published=1');$db->setQuery($q);return(int)$db->loadResult();
 }

}
