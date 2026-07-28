<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\Service;
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Jugendtraining\Component\Jugendtraining\Site\Service\AccessService;

final class CsvImportService
{
    private DatabaseInterface $db;
    private AccessService $access;
    private int $userId;

    public function __construct()
    {
        $this->db = Factory::getContainer()->get(DatabaseInterface::class);
        $this->access = new AccessService();
        $this->userId = (int) Factory::getApplication()->getIdentity()->id;
    }

    public function import(string $type, string $file): array
    {
        $handle = fopen($file, 'rb');
        if (!$handle) throw new \RuntimeException('CSV konnte nicht geöffnet werden.');

        $first = fgets($handle);
        if ($first === false) throw new \RuntimeException('CSV ist leer.');
        $first = preg_replace('/^\xEF\xBB\xBF/', '', $first);
        $delimiter = substr_count($first, ';') >= substr_count($first, ',') ? ';' : ',';
        rewind($handle);

        $headers = fgetcsv($handle, 0, $delimiter, '"', '\\');
        $headers = array_map([$this,'normaliseHeader'], $headers ?: []);
        $required = $this->required($type);
        foreach ($required as $field) {
            if (!in_array($field, $headers, true)) {
                throw new \RuntimeException('Pflichtspalte fehlt: ' . $field);
            }
        }

        $result=['success'=>0,'failed'=>0,'errors'=>[],'total'=>0];
        $line=1;
        $this->db->transactionStart();
        try {
            while (($values=fgetcsv($handle,0,$delimiter,'"','\\')) !== false) {
                $line++;
                if (count(array_filter($values,fn($v)=>trim((string)$v)!==''))===0) continue;
                $result['total']++;
                $row=[];
                foreach ($headers as $i=>$h) $row[$h]=trim((string)($values[$i]??''));
                try {
                    match($type) {
                        'results' => $this->importResult($row),
                        'diary' => $this->importDiary($row),
                        'achievements' => $this->importAchievement($row),
                        default => throw new \RuntimeException('Unbekannter Importtyp.')
                    };
                    $result['success']++;
                } catch (\Throwable $e) {
                    $result['failed']++;
                    $result['errors'][]='Zeile '.$line.': '.$e->getMessage();
                }
            }
            $this->db->transactionCommit();
        } catch (\Throwable $e) {
            $this->db->transactionRollback();
            throw $e;
        } finally { fclose($handle); }
        return $result;
    }

    public function template(string $type): array
    {
        return match($type) {
            'results' => [
                ['athlete_id','membership_number','firstname','lastname','result_date','event_type','event_name','distance_m','arrows','score','tens','xs','verification_status','notes'],
                ['','M-1001','Max','Mustermann','2026-07-28','competition','Vereinsmeisterschaft','18','60','512','18','5','verified','Import Altsystem']
            ],
            'diary' => [
                ['athlete_id','membership_number','firstname','lastname','training_date','duration_minutes','arrow_count','training_method','distance_m','focus_topic','intensity','feeling','notes'],
                ['','M-1001','Max','Mustermann','2026-07-28','90','120','Techniktraining','18','Ankerpunkt','3','4','Import Altsystem']
            ],
            'achievements' => [
                ['athlete_id','membership_number','firstname','lastname','achievement_code','awarded_at','notes'],
                ['','M-1001','Max','Mustermann','first-vm','2026-07-28','Aus Altsystem übernommen']
            ],
            default => throw new \RuntimeException('Unbekannter Vorlagentyp.')
        };
    }

    private function importResult(array $r): void
    {
        $aid=$this->athlete($r);
        $arrows=$this->uint($r['arrows']??'', 'arrows', true);
        $score=$this->uint($r['score']??'', 'score', false);
        $avg=$arrows>0 ? $score/$arrows : 0;
        $status=in_array($r['verification_status']??'pending',['pending','verified','rejected'],true)?$r['verification_status']:'pending';
        $now=Factory::getDate()->toSql();
        $obj=(object)[
            'athlete_id'=>$aid,'result_date'=>$this->date($r['result_date']??''),
            'event_type'=>$r['event_type']?:'training','event_name'=>$r['event_name']?:null,
            'distance_m'=>$this->uint($r['distance_m']??'', 'distance_m', true),
            'arrows'=>$arrows,'score'=>$score,'average'=>$avg,
            'tens'=>$this->uint($r['tens']??'0','tens',false),
            'xs'=>$this->uint($r['xs']??'0','xs',false),
            'notes'=>$r['notes']?:null,'verification_status'=>$status,
            'verified_by'=>$status==='verified'?$this->userId:0,
            'verified_at'=>$status==='verified'?$now:null,
            'published'=>1,'created'=>$now,'created_by'=>$this->userId,
            'bow_setup_id'=>$this->activeSetup($aid),
        ];
        $this->db->insertObject('#__jt_results',$obj);
    }

    private function importDiary(array $r): void
    {
        $aid=$this->athlete($r); $now=Factory::getDate()->toSql();
        $obj=(object)[
            'athlete_id'=>$aid,'training_date'=>$this->date($r['training_date']??''),
            'duration_minutes'=>$this->nullableUInt($r['duration_minutes']??''),
            'arrow_count'=>$this->nullableUInt($r['arrow_count']??''),
            'training_method'=>$r['training_method']?:null,
            'distance_m'=>$this->decimal($r['distance_m']??''),
            'focus_topic'=>$r['focus_topic']?:null,
            'intensity'=>$this->range($r['intensity']??'',1,5),
            'feeling'=>$this->range($r['feeling']??'',1,5),
            'bow_setup_id'=>$this->activeSetup($aid),'notes'=>$r['notes']?:null,
            'created'=>$now,'created_by'=>$this->userId
        ];
        $this->db->insertObject('#__jt_training_diary',$obj);
    }

    private function importAchievement(array $r): void
    {
        $aid=$this->athlete($r); $code=trim($r['achievement_code']??'');
        if ($code==='') throw new \RuntimeException('achievement_code fehlt.');
        $q=$this->db->getQuery(true)->select('id')->from('#__jt_achievements')->where('code='.$this->db->quote($code));
        $this->db->setQuery($q); $achievementId=(int)$this->db->loadResult();
        if (!$achievementId) throw new \RuntimeException('Achievement-Code nicht gefunden: '.$code);
        $date=$this->date($r['awarded_at']?:date('Y-m-d'));
        $obj=(object)[
            'athlete_id'=>$aid,'achievement_id'=>$achievementId,
            'awarded_at'=>$date.' 00:00:00','awarded_by'=>$this->userId,
            'notes'=>$r['notes']?:null
        ];
        try {$this->db->insertObject('#__jt_athlete_achievements',$obj);}
        catch (\Throwable $e) {
            if (str_contains(strtolower($e->getMessage()),'duplicate')) return;
            throw $e;
        }
    }

    private function athlete(array $r): int
    {
        $id=(int)($r['athlete_id']??0);
        if ($id>0) {
            if (!$this->access->canManageAthlete($id)) throw new \RuntimeException('Keine Berechtigung für athlete_id '.$id);
            return $id;
        }
        $conditions=['published=1'];
        if (($r['membership_number']??'')!=='') $conditions[]='membership_number='.$this->db->quote($r['membership_number']);
        else {
            if (($r['firstname']??'')===''||($r['lastname']??'')==='') throw new \RuntimeException('Athlet nicht identifizierbar.');
            $conditions[]='firstname='.$this->db->quote($r['firstname']);
            $conditions[]='lastname='.$this->db->quote($r['lastname']);
        }
        $q=$this->db->getQuery(true)->select('id')->from('#__jt_athletes')->where($conditions);
        $this->db->setQuery($q); $ids=array_map('intval',$this->db->loadColumn());
        $ids=array_values(array_intersect($ids,$this->access->getTrainerAthleteIds()));
        if (count($ids)!==1) throw new \RuntimeException(count($ids)?'Athlet nicht eindeutig.':'Athlet nicht gefunden oder nicht zugeordnet.');
        return $ids[0];
    }

    private function activeSetup(int $aid): ?int
    {
        $q=$this->db->getQuery(true)->select('id')->from('#__jt_bow_setups')
            ->where('athlete_id='.$aid)->where('is_active=1')->order('revision_no DESC');
        $this->db->setQuery($q,0,1); $id=(int)$this->db->loadResult(); return $id?:null;
    }
    private function required(string $t): array { return match($t){'results'=>['result_date','distance_m','arrows','score'],'diary'=>['training_date'],'achievements'=>['achievement_code'],default=>[]};}
    private function normaliseHeader(string $h): string {return strtolower(trim(str_replace(["\xEF\xBB\xBF",' ','-'],['','_','_'],$h)));}
    private function date(string $v): string {
        $v=trim($v); foreach(['Y-m-d','d.m.Y','d/m/Y'] as $f){$d=\DateTimeImmutable::createFromFormat('!'.$f,$v);if($d&&$d->format($f)===$v)return$d->format('Y-m-d');}
        throw new \RuntimeException('Ungültiges Datum: '.$v);
    }
    private function uint(string $v,string $field,bool $positive): int {$v=str_replace(',','.',$v);if(!is_numeric($v))throw new \RuntimeException($field.' ist ungültig.');$n=(int)$v;if($n<($positive?1:0))throw new \RuntimeException($field.' ist ungültig.');return$n;}
    private function nullableUInt(string $v): ?int{return trim($v)===''?null:$this->uint($v,'Zahl',false);}
    private function decimal(string $v): ?float{return trim($v)===''?null:(is_numeric(str_replace(',','.',$v))?(float)str_replace(',','.',$v):throw new \RuntimeException('Dezimalwert ungültig.'));}
    private function range(string $v,int $min,int $max): ?int{if(trim($v)==='')return null;$n=(int)$v;if($n<$min||$n>$max)throw new \RuntimeException('Bewertung außerhalb '.$min.'-'.$max.'.');return$n;}
}
