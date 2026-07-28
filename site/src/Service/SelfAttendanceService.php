<?php
namespace Jugendtraining\Component\Jugendtraining\Site\Service;
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;

final class SelfAttendanceService
{
    private DatabaseInterface $db;
    private AccessService $access;

    public function __construct()
    {
        $this->db=Factory::getContainer()->get(DatabaseInterface::class);
        $this->access=new AccessService();
    }

    public function settings(): object
    {
        $defaults=[
            'enabled'=>'1',
            'deadline_minutes'=>'60',
            'late_penalty_enabled'=>'0',
            'late_penalty_definition_id'=>'0',
            'late_cancel_set_excused'=>'1',
        ];

        $q=$this->db->getQuery(true)
            ->select(['setting_key','setting_value'])
            ->from('#__jt_settings')
            ->where('setting_key IN ('
                .$this->db->quote('self_cancel_enabled').','
                .$this->db->quote('self_cancel_deadline_minutes').','
                .$this->db->quote('self_cancel_late_penalty_enabled').','
                .$this->db->quote('self_cancel_late_penalty_definition_id').','
                .$this->db->quote('self_cancel_late_set_excused')
                .')');
        $this->db->setQuery($q);
        $rows=$this->db->loadAssocList('setting_key','setting_value');

        return (object)[
            'enabled'=>(int)($rows['self_cancel_enabled']??$defaults['enabled'])===1,
            'deadline_minutes'=>max(0,min(10080,(int)($rows['self_cancel_deadline_minutes']??$defaults['deadline_minutes']))),
            'late_penalty_enabled'=>(int)($rows['self_cancel_late_penalty_enabled']??$defaults['late_penalty_enabled'])===1,
            'late_penalty_definition_id'=>(int)($rows['self_cancel_late_penalty_definition_id']??$defaults['late_penalty_definition_id']),
            'late_cancel_set_excused'=>(int)($rows['self_cancel_late_set_excused']??$defaults['late_cancel_set_excused'])===1,
        ];
    }

    public function athlete(): ?object
    {
        $user=Factory::getApplication()->getIdentity();
        if($user->guest||!$this->access->isAthlete($user))return null;

        $q=$this->db->getQuery(true)
            ->select(['id','firstname','lastname','user_id'])
            ->from('#__jt_athletes')
            ->where('user_id='.(int)$user->id)
            ->where('published=1');
        $this->db->setQuery($q,0,1);
        return$this->db->loadObject()?:null;
    }

    public function upcomingSessions(int $limit=6): array
    {
        $settings=$this->settings();
        if(!$settings->enabled)return[];

        $athlete=$this->athlete();
        if(!$athlete)return[];

        $tz=$this->timezone();
        $now=new \DateTimeImmutable('now',$tz);
        $today=$now->format('Y-m-d');

        $q=$this->db->getQuery(true)
            ->select([
                's.id','s.title','s.training_date','s.start_time','s.end_time','s.location',
                'g.title group_title',
                'a.status attendance_status','a.comment attendance_comment','a.modified attendance_modified'
            ])
            ->from('#__jt_training_sessions s')
            ->innerJoin('#__jt_training_group_athletes ga ON ga.group_id=s.training_group_id AND ga.athlete_id='.(int)$athlete->id)
            ->leftJoin('#__jt_training_groups g ON g.id=s.training_group_id')
            ->leftJoin('#__jt_attendance a ON a.training_session_id=s.id AND a.athlete_id='.(int)$athlete->id)
            ->where('s.published=1')
            ->where('s.training_date>='.$this->db->quote($today))
            ->order('s.training_date ASC,s.start_time ASC,s.id ASC');

        $this->db->setQuery($q,0,max(1,min(20,$limit)));
        $rows=$this->db->loadObjectList();

        foreach($rows as$row){
            $row->can_self_cancel=false;
            $row->can_late_cancel=false;
            $row->cancel_reason=null;
            $row->deadline_at=null;

            if((string)$row->attendance_status==='excused'){
                $row->cancel_reason='already_cancelled';
                continue;
            }

            if(!$row->start_time){
                $row->cancel_reason='missing_start_time';
                continue;
            }

            $start=$this->sessionStart($row,$tz);
            $deadline=$start->modify('-'.$settings->deadline_minutes.' minutes');
            $row->deadline_at=$deadline->format('Y-m-d H:i:s');

            if($now>=$deadline){
                $row->cancel_reason='deadline_passed';
                $row->can_late_cancel=
                    $settings->late_penalty_enabled
                    && $settings->late_penalty_definition_id>0;
                continue;
            }

            $row->can_self_cancel=true;
        }

        return$rows;
    }

    public function cancel(int $sessionId): void
    {
        $settings=$this->settings();
        if(!$settings->enabled)throw new \RuntimeException('COM_JUGENDTRAINING_SELF_CANCEL_DISABLED');

        $athlete=$this->athlete();
        if(!$athlete)throw new \RuntimeException('JERROR_ALERTNOAUTHOR',403);

        $session=$this->sessionForAthlete($sessionId,(int)$athlete->id);
        if(!$session)throw new \RuntimeException('JERROR_ALERTNOAUTHOR',403);

        if(!$session->start_time){
            throw new \RuntimeException('COM_JUGENDTRAINING_SELF_CANCEL_NO_START_TIME');
        }

        $tz=$this->timezone();
        $now=new \DateTimeImmutable('now',$tz);
        $start=$this->sessionStart($session,$tz);
        $deadline=$start->modify('-'.$settings->deadline_minutes.' minutes');

        $isLate=$now>=$deadline;

        if($isLate){
            $this->audit('self_cancel_late_attempt',(int)$athlete->id,$sessionId,[
                'deadline'=>$deadline->format(DATE_ATOM),
                'attempted_at'=>$now->format(DATE_ATOM),
            ]);

            if($settings->late_penalty_enabled&&$settings->late_penalty_definition_id>0){
                $this->createLatePenaltyOnce(
                    (int)$athlete->id,
                    $session,
                    $settings->late_penalty_definition_id
                );

                if(!$settings->late_cancel_set_excused){
                    throw new \RuntimeException('COM_JUGENDTRAINING_SELF_CANCEL_TOO_LATE');
                }
            }else{
                throw new \RuntimeException('COM_JUGENDTRAINING_SELF_CANCEL_TOO_LATE');
            }
        }

        $q=$this->db->getQuery(true)
            ->select('*')
            ->from('#__jt_attendance')
            ->where('training_session_id='.(int)$sessionId)
            ->where('athlete_id='.(int)$athlete->id);
        $this->db->setQuery($q,0,1);
        $existing=$this->db->loadObject();

        if($existing&&(string)$existing->status==='excused'){
            return;
        }

        $uid=(int)Factory::getApplication()->getIdentity()->id;
        $nowSql=Factory::getDate()->toSql();

        if($existing){
            $obj=(object)[
                'id'=>(int)$existing->id,
                'status'=>'excused',
                'modified'=>$nowSql,
                'modified_by'=>$uid,
            ];
            $this->db->updateObject('#__jt_attendance',$obj,'id');
        }else{
            $obj=(object)[
                'training_session_id'=>(int)$sessionId,
                'athlete_id'=>(int)$athlete->id,
                'status'=>'excused',
                'comment'=>null,
                'created'=>$nowSql,
                'created_by'=>$uid,
                'modified'=>$nowSql,
                'modified_by'=>$uid,
            ];
            $this->db->insertObject('#__jt_attendance',$obj);
        }

        $this->audit($isLate?'self_cancel_late_excused':'self_cancel',(int)$athlete->id,$sessionId,[
            'status'=>'excused',
            'deadline'=>$deadline->format(DATE_ATOM),
            'late'=>$isLate?1:0,
        ]);
    }

    private function sessionForAthlete(int $sessionId,int $athleteId): ?object
    {
        if($sessionId<=0)return null;

        $q=$this->db->getQuery(true)
            ->select(['s.*','g.title group_title'])
            ->from('#__jt_training_sessions s')
            ->innerJoin('#__jt_training_group_athletes ga ON ga.group_id=s.training_group_id AND ga.athlete_id='.$athleteId)
            ->leftJoin('#__jt_training_groups g ON g.id=s.training_group_id')
            ->where('s.id='.$sessionId)
            ->where('s.published=1');
        $this->db->setQuery($q,0,1);
        return$this->db->loadObject()?:null;
    }

    private function sessionStart(object $session,\DateTimeZone $tz): \DateTimeImmutable
    {
        return new \DateTimeImmutable(
            (string)$session->training_date.' '.substr((string)$session->start_time,0,8),
            $tz
        );
    }

    private function timezone(): \DateTimeZone
    {
        $name=(string)Factory::getApplication()->get('offset','UTC');
        try{return new \DateTimeZone($name?:'UTC');}
        catch(\Throwable){return new \DateTimeZone('UTC');}
    }

    private function createLatePenaltyOnce(int $athleteId,object $session,int $definitionId): void
    {
        $reason='Automatisch: verspäteter Abmeldeversuch Training #'.(int)$session->id;

        $q=$this->db->getQuery(true)
            ->select('COUNT(*)')
            ->from('#__jt_penalty_register')
            ->where('athlete_id='.$athleteId)
            ->where('penalty_definition_id='.$definitionId)
            ->where('reason_note='.$this->db->quote($reason));
        $this->db->setQuery($q);
        if((int)$this->db->loadResult()>0)return;

        $q=$this->db->getQuery(true)
            ->select('*')
            ->from('#__jt_penalty_definitions')
            ->where('id='.$definitionId)
            ->where('published=1');
        $this->db->setQuery($q,0,1);
        $definition=$this->db->loadObject();
        if(!$definition)return;

        $obj=(object)[
            'athlete_id'=>$athleteId,
            'penalty_definition_id'=>$definitionId,
            'assigned_at'=>Factory::getDate()->toSql(),
            'assigned_by'=>0,
            'reason_note'=>$reason,
            'status'=>'open',
            'amount_snapshot'=>$definition->penalty_type==='monetary'?$definition->amount:null,
            'action_snapshot'=>$definition->penalty_type==='non_monetary'?$definition->non_monetary_action:null,
        ];
        $this->db->insertObject('#__jt_penalty_register',$obj);
    }

    private function audit(string $action,int $athleteId,int $sessionId,array $payload=[]): void
    {
        $obj=(object)[
            'user_id'=>(int)Factory::getApplication()->getIdentity()->id,
            'action'=>$action,
            'entity_type'=>'attendance',
            'entity_id'=>$sessionId,
            'payload'=>json_encode(
                ['athlete_id'=>$athleteId,'training_session_id'=>$sessionId]+$payload,
                JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES
            ),
            'created'=>Factory::getDate()->toSql(),
        ];
        $this->db->insertObject('#__jt_audit_log',$obj);
    }
}
