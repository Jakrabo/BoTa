<?php
namespace Jugendtraining\Component\Jugendtraining\Site\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;
use Jugendtraining\Component\Jugendtraining\Site\Service\AccessService;
use Throwable;

final class TrainertrainingModel extends AdminModel
{
    private AccessService $access;

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->access = new AccessService();
    }

    public function getTable($name = 'Training', $prefix = 'Administrator', $options = [])
    {
        return parent::getTable($name, $prefix, $options);
    }

    public function getForm($data = [], $loadData = true)
    {
        $form = $this->loadForm('com_jugendtraining.trainertraining', 'trainertraining', [
            'control' => 'jform',
            'load_data' => $loadData,
        ]);

        if ($form) {
            $field = $form->getField('training_group_id');
            $field->addOption('COM_JUGENDTRAINING_SELECT_TRAINING_GROUP', ['value' => '']);

            foreach ($this->getOwnGroups() as $group) {
                $field->addOption($group->title, ['value' => (int) $group->id]);
            }
        }

        return $form;
    }

    protected function loadFormData()
    {
        $data = Factory::getApplication()->getUserState('com_jugendtraining.edit.trainertraining.data', []);
        $item = $data ?: $this->getItem();

        if (!empty($item->id) && !$this->canManageSession((int) $item->id)) {
            throw new \RuntimeException('JERROR_ALERTNOAUTHOR', 403);
        }

        return $item;
    }

    public function save($data): bool
    {
        $userId = (int) Factory::getApplication()->getIdentity()->id;
        $groupId = (int) ($data['training_group_id'] ?? 0);
        $id = (int) ($data['id'] ?? 0);

        if (!$this->access->isTrainer() || !$this->ownsGroup($groupId)) {
            $this->setError('JERROR_ALERTNOAUTHOR');
            return false;
        }

        if ($id > 0 && !$this->canManageSession($id)) {
            $this->setError('JERROR_ALERTNOAUTHOR');
            return false;
        }

        $createSeries = $id === 0 && (int) ($data['create_series'] ?? 0) === 1;
        $count = max(2, min(100, (int) ($data['series_count'] ?? 10)));
        $interval = max(1, min(365, (int) ($data['series_interval_days'] ?? 7)));
        unset($data['create_series'], $data['series_count'], $data['series_interval_days']);

        $data['trainer_user_id'] = $userId;
        $data['published'] = 1;

        $db = $this->getDatabase();
        $db->transactionStart();

        try {
            if (!parent::save($data)) {
                $db->transactionRollback();
                return false;
            }

            $sessionId = (int) $this->getState($this->getName() . '.id');

            if ($sessionId <= 0) {
                $sessionId = (int) ($data['id'] ?? 0);
            }

            if ($sessionId <= 0) {
                throw new \RuntimeException('Training session ID could not be determined.');
            }

            $this->removeAttendanceOutsideGroup($sessionId, $groupId);

            $attendanceInput = Factory::getApplication()
                ->getInput()
                ->get('attendance', [], 'array');

            $this->saveAttendance($sessionId, $groupId, $attendanceInput);

            if ($createSeries && !empty($data['training_date'])) {
                $base = new \DateTimeImmutable((string) $data['training_date']);

                for ($i = 1; $i < $count; $i++) {
                    $table = $this->getTable();
                    $row = $data;
                    $row['id'] = 0;
                    $row['training_date'] = $base
                        ->modify('+' . ($interval * $i) . ' days')
                        ->format('Y-m-d');
                    $row['created'] = Factory::getDate()->toSql();
                    $row['created_by'] = $userId;

                    if (!$table->bind($row) || !$table->check() || !$table->store()) {
                        throw new \RuntimeException($table->getError());
                    }
                }
            }

            $db->transactionCommit();
            return true;
        } catch (Throwable $exception) {
            $db->transactionRollback();
            $this->setError($exception->getMessage());
            return false;
        }
    }

    public function deleteOwn(int $id): bool
    {
        if (!$this->canManageSession($id)) {
            $this->setError('JERROR_ALERTNOAUTHOR');
            return false;
        }

        return $this->delete($id);
    }

    public function getAthletes(): array
    {
        $item = $this->getItem();
        $groupId = (int) ($item->training_group_id ?? 0);

        if ($groupId <= 0 || !$this->ownsGroup($groupId)) {
            return [];
        }

        $db = $this->getDatabase();
        $q = $db->getQuery(true)
            ->select([
                'a.id',
                'a.firstname',
                'a.lastname',
                'a.bow_type',
                'c.name club_name',
                'cl.name class_name',
            ])
            ->from($db->quoteName('#__jt_training_group_athletes', 'ga'))
            ->innerJoin($db->quoteName('#__jt_athletes', 'a') . ' ON a.id = ga.athlete_id')
            ->leftJoin($db->quoteName('#__jt_clubs', 'c') . ' ON c.id = a.club_id')
            ->leftJoin($db->quoteName('#__jt_classes', 'cl') . ' ON cl.id = a.class_id')
            ->where('ga.group_id = ' . $groupId)
            ->where('a.published = 1')
            ->order('a.lastname, a.firstname');

        $db->setQuery($q);
        return $db->loadAssocList();
    }

    public function getAttendance(): array
    {
        $sessionId = (int) ($this->getItem()->id ?? 0);

        if ($sessionId <= 0 || !$this->canManageSession($sessionId)) {
            return [];
        }

        $db = $this->getDatabase();
        $q = $db->getQuery(true)
            ->select(['athlete_id', 'status', 'comment'])
            ->from($db->quoteName('#__jt_attendance'))
            ->where('training_session_id = ' . $sessionId);

        $db->setQuery($q);
        $attendance = [];

        foreach ($db->loadAssocList() as $row) {
            $attendance[(int) $row['athlete_id']] = [
                'status' => (string) $row['status'],
                'comment' => (string) ($row['comment'] ?? ''),
            ];
        }

        return $attendance;
    }

    public function saveSingleAttendance(
        int $sessionId,
        int $athleteId,
        string $status,
        string $comment = ''
    ): bool {
        if (
            $sessionId <= 0
            || !$this->access->isTrainer()
            || !$this->canManageSession($sessionId)
        ) {
            $this->setError('JERROR_ALERTNOAUTHOR');
            return false;
        }

        $item = $this->getItem($sessionId);
        $groupId = (int) ($item->training_group_id ?? 0);

        if ($groupId <= 0) {
            $this->setError('COM_JUGENDTRAINING_ERROR_TRAINING_GROUP_REQUIRED');
            return false;
        }

        try {
            $this->saveAttendance(
                $sessionId,
                $groupId,
                [
                    $athleteId => [
                        'status' => $status,
                        'comment' => $comment,
                    ],
                ]
            );

            return true;
        } catch (Throwable $exception) {
            $this->setError($exception->getMessage());
            return false;
        }
    }

    private function saveAttendance(int $sessionId, int $groupId, array $input): void
    {
        $allowed = $this->getGroupAthleteIds($groupId);
        $allowedLookup = array_fill_keys($allowed, true);
        $db = $this->getDatabase();
        $userId = (int) Factory::getApplication()->getIdentity()->id;
        $now = Factory::getDate()->toSql();
        $validStatuses = ['present', 'late', 'excused', 'absent'];

        foreach ($input as $athleteId => $entry) {
            $athleteId = (int) $athleteId;

            if (!isset($allowedLookup[$athleteId]) || !is_array($entry)) {
                continue;
            }

            $status = (string) ($entry['status'] ?? '');
            $comment = mb_substr(trim((string) ($entry['comment'] ?? '')), 0, 500);

            if ($status === '') {
                $delete = $db->getQuery(true)
                    ->delete($db->quoteName('#__jt_attendance'))
                    ->where('training_session_id = ' . $sessionId)
                    ->where('athlete_id = ' . $athleteId);
                $db->setQuery($delete)->execute();
                continue;
            }

            if (!in_array($status, $validStatuses, true)) {
                continue;
            }

            $find = $db->getQuery(true)
                ->select('id')
                ->from($db->quoteName('#__jt_attendance'))
                ->where('training_session_id = ' . $sessionId)
                ->where('athlete_id = ' . $athleteId);
            $db->setQuery($find);
            $attendanceId = (int) $db->loadResult();

            if ($attendanceId > 0) {
                $q = $db->getQuery(true)
                    ->update($db->quoteName('#__jt_attendance'))
                    ->set('status = ' . $db->quote($status))
                    ->set('comment = ' . $db->quote($comment))
                    ->set('modified = ' . $db->quote($now))
                    ->set('modified_by = ' . $userId)
                    ->where('id = ' . $attendanceId);
            } else {
                $q = $db->getQuery(true)
                    ->insert($db->quoteName('#__jt_attendance'))
                    ->columns([
                        'training_session_id',
                        'athlete_id',
                        'status',
                        'comment',
                        'created',
                        'created_by',
                    ])
                    ->values(implode(',', [
                        $sessionId,
                        $athleteId,
                        $db->quote($status),
                        $db->quote($comment),
                        $db->quote($now),
                        $userId,
                    ]));
            }

            $db->setQuery($q)->execute();
        }
    }

    private function removeAttendanceOutsideGroup(int $sessionId, int $groupId): void
    {
        $athleteIds = $this->getGroupAthleteIds($groupId);
        $db = $this->getDatabase();
        $q = $db->getQuery(true)
            ->delete($db->quoteName('#__jt_attendance'))
            ->where('training_session_id = ' . $sessionId);

        if ($athleteIds) {
            $q->where('athlete_id NOT IN (' . implode(',', $athleteIds) . ')');
        }

        $db->setQuery($q)->execute();
    }

    private function getGroupAthleteIds(int $groupId): array
    {
        $db = $this->getDatabase();
        $q = $db->getQuery(true)
            ->select('athlete_id')
            ->from($db->quoteName('#__jt_training_group_athletes'))
            ->where('group_id = ' . $groupId);

        $db->setQuery($q);
        return array_map('intval', $db->loadColumn());
    }

    private function getOwnGroups(): array
    {
        $userId = (int) Factory::getApplication()->getIdentity()->id;
        $db = $this->getDatabase();
        $q = $db->getQuery(true)
            ->select(['g.id','g.title'])
            ->from($db->quoteName('#__jt_training_groups','g'))
            ->innerJoin($db->quoteName('#__jt_training_group_trainers','gt').' ON gt.group_id=g.id')
            ->where('gt.user_id='.$userId)->where('g.published=1')->order('g.title');
        $db->setQuery($q);
        return $db->loadObjectList();
    }

    private function ownsGroup(int $groupId): bool
    {
        if ($groupId <= 0) {
            return false;
        }

        $userId = (int) Factory::getApplication()->getIdentity()->id;
        $db = $this->getDatabase();
        $q = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__jt_training_group_trainers'))
            ->where('group_id='.$groupId)->where('user_id='.$userId);
        $db->setQuery($q);
        return (int) $db->loadResult() === 1;
    }

    private function canManageSession(int $id): bool
    {
        $userId = (int) Factory::getApplication()->getIdentity()->id;
        $db = $this->getDatabase();
        $q = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__jt_training_sessions','s'))
            ->innerJoin($db->quoteName('#__jt_training_group_trainers','gt').' ON gt.group_id=s.training_group_id')
            ->where('s.id='.$id)->where('gt.user_id='.$userId);
        $db->setQuery($q);
        return (int) $db->loadResult() === 1;
    }
}
