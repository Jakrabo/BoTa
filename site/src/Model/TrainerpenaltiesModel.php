<?php
namespace Jugendtraining\Component\Jugendtraining\Site\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Jugendtraining\Component\Jugendtraining\Site\Service\AccessService;

final class TrainerpenaltiesModel extends BaseDatabaseModel
{
    private AccessService $access;

    public function __construct($config = [], $factory = null)
    {
        parent::__construct($config, $factory);
        $this->access = new AccessService();
    }

    public function getAthletes(): array
    {
        $this->guard();
        $ids = $this->access->getTrainerAthleteIds();
        if (!$ids) return [];

        $db=$this->getDatabase();
        $q=$db->getQuery(true)
            ->select(['a.id',"CONCAT(a.firstname,' ',a.lastname) AS athlete_name"])
            ->from('#__jt_athletes AS a')
            ->where('a.id IN ('.implode(',',array_map('intval',$ids)).')')
            ->where('a.published=1')
            ->order('a.lastname,a.firstname');
        $db->setQuery($q);
        return $db->loadObjectList();
    }

    public function getDefinitions(): array
    {
        $this->guard();
        $db=$this->getDatabase();
        $q=$db->getQuery(true)->select('*')->from('#__jt_penalty_definitions')
            ->where('published=1')->order('ordering,title');
        $db->setQuery($q);
        return $db->loadObjectList();
    }

    public function getEntries(): array
    {
        $this->guard();
        $ids=$this->access->getTrainerAthleteIds();
        if(!$ids) return [];

        $db=$this->getDatabase();
        $q=$db->getQuery(true)
            ->select([
                'r.*','d.title','d.penalty_type',
                "CONCAT(a.firstname,' ',a.lastname) AS athlete_name",
                'u.name AS assigned_by_name'
            ])
            ->from('#__jt_penalty_register AS r')
            ->innerJoin('#__jt_penalty_definitions AS d ON d.id=r.penalty_definition_id')
            ->innerJoin('#__jt_athletes AS a ON a.id=r.athlete_id')
            ->leftJoin('#__users AS u ON u.id=r.assigned_by')
            ->where('r.athlete_id IN ('.implode(',',array_map('intval',$ids)).')')
            ->order("CASE WHEN r.status='open' THEN 0 ELSE 1 END,r.assigned_at DESC,r.id DESC");
        $db->setQuery($q);
        return $db->loadObjectList();
    }

    public function assign(array $data): void
    {
        $this->guard();
        $athleteId=(int)($data['athlete_id']??0);
        $definitionId=(int)($data['penalty_definition_id']??0);

        if(!$this->access->canManageAthlete($athleteId)) {
            throw new \RuntimeException('JERROR_ALERTNOAUTHOR',403);
        }

        $db=$this->getDatabase();
        $q=$db->getQuery(true)->select('*')->from('#__jt_penalty_definitions')
            ->where('id='.$definitionId)->where('published=1');
        $db->setQuery($q);
        $definition=$db->loadObject();

        if(!$definition) throw new \RuntimeException('Die ausgewählte Strafe wurde nicht gefunden.');

        $obj=(object)[
            'athlete_id'=>$athleteId,
            'penalty_definition_id'=>$definitionId,
            'assigned_at'=>Factory::getDate()->toSql(),
            'assigned_by'=>(int)Factory::getApplication()->getIdentity()->id,
            'reason_note'=>trim((string)($data['reason_note']??''))?:null,
            'status'=>'open',
            'amount_snapshot'=>$definition->penalty_type==='monetary'?$definition->amount:null,
            'action_snapshot'=>$definition->penalty_type==='non_monetary'?$definition->non_monetary_action:null
        ];
        $db->insertObject('#__jt_penalty_register',$obj);
    }

    public function complete(int $id, string $note=''): void
    {
        $this->guard();
        $entry=$this->ownedEntry($id);
        if(!$entry) throw new \RuntimeException('JERROR_ALERTNOAUTHOR',403);

        $db=$this->getDatabase();
        $obj=(object)[
            'id'=>$id,
            'status'=>'completed',
            'completed_at'=>Factory::getDate()->toSql(),
            'completed_by'=>(int)Factory::getApplication()->getIdentity()->id,
            'completion_note'=>trim($note)?:null
        ];
        $db->updateObject('#__jt_penalty_register',$obj,'id');
    }

    public function reopen(int $id): void
    {
        $this->guard();
        if(!$this->ownedEntry($id)) throw new \RuntimeException('JERROR_ALERTNOAUTHOR',403);
        $db=$this->getDatabase();
        $q=$db->getQuery(true)->update('#__jt_penalty_register')
            ->set("status='open'")->set('completed_at=NULL')->set('completed_by=0')->set('completion_note=NULL')
            ->where('id='.$id);
        $db->setQuery($q)->execute();
    }

    private function ownedEntry(int $id): ?object
    {
        $ids=$this->access->getTrainerAthleteIds();
        if(!$ids) return null;
        $db=$this->getDatabase();
        $q=$db->getQuery(true)->select('*')->from('#__jt_penalty_register')
            ->where('id='.$id)->where('athlete_id IN ('.implode(',',array_map('intval',$ids)).')');
        $db->setQuery($q);
        return $db->loadObject()?:null;
    }

    private function guard(): void
    {
        if(!$this->access->isTrainer()) throw new \RuntimeException('JERROR_ALERTNOAUTHOR',403);
    }
}
