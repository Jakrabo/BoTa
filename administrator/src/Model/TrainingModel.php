<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\Database\ParameterType;
use Throwable;

final class TrainingModel extends AdminModel
{
    public function getTable($name = 'Training', $prefix = 'Administrator', $options = [])
    {
        return parent::getTable($name, $prefix, $options);
    }

    public function getForm($data = [], $loadData = true)
    {
        return $this->loadForm('com_jugendtraining.training', 'training', [
            'control' => 'jform',
            'load_data' => $loadData,
        ]);
    }

    protected function loadFormData()
    {
        $data = Factory::getApplication()->getUserState('com_jugendtraining.edit.training.data', []);
        return $data ?: $this->getItem();
    }

    protected function prepareTable($table): void
    {
        $date = Factory::getDate()->toSql();
        $userId = (int) Factory::getApplication()->getIdentity()->id;

        if (empty($table->id)) {
            $table->created = $date;
            $table->created_by = $userId;
        } else {
            $table->modified = $date;
            $table->modified_by = $userId;
        }
    }

    public function getAthletes(): array
    {
        $groupId = $this->getCurrentGroupId();

        if ($groupId <= 0) {
            return [];
        }

        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select([
                'a.id', 'a.firstname', 'a.lastname', 'a.bow_type',
                'c.name AS club_name', 'cl.name AS class_name',
            ])
            ->from($db->quoteName('#__jt_training_group_athletes', 'ga'))
            ->innerJoin($db->quoteName('#__jt_athletes', 'a') . ' ON a.id = ga.athlete_id')
            ->leftJoin($db->quoteName('#__jt_clubs', 'c') . ' ON c.id = a.club_id')
            ->leftJoin($db->quoteName('#__jt_classes', 'cl') . ' ON cl.id = a.class_id')
            ->where('ga.group_id = ' . $groupId)
            ->where('a.published = 1')
            ->order('a.lastname ASC, a.firstname ASC');

        $db->setQuery($query);
        return $db->loadAssocList();
    }

    public function getAttendance(): array
    {
        $sessionId = $this->getCurrentSessionId();

        if ($sessionId <= 0) {
            return [];
        }

        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select(['athlete_id', 'status', 'comment'])
            ->from($db->quoteName('#__jt_attendance'))
            ->where('training_session_id = ' . $sessionId);

        $db->setQuery($query);
        $attendance = [];

        foreach ($db->loadAssocList() as $row) {
            $attendance[(int) $row['athlete_id']] = [
                'status' => (string) $row['status'],
                'comment' => (string) ($row['comment'] ?? ''),
            ];
        }

        return $attendance;
    }

    public function save($data): bool
    {
        $groupId = (int) ($data['training_group_id'] ?? 0);

        if ($groupId <= 0 || !$this->groupExists($groupId)) {
            $this->setError('COM_JUGENDTRAINING_ERROR_TRAINING_GROUP_REQUIRED');
            return false;
        }

        $db = $this->getDatabase();
        $db->transactionStart();

        try {
            $isNew = empty($data['id']);
            $createSeries = $isNew && (int) ($data['create_series'] ?? 0) === 1;
            $seriesCount = max(2, min(100, (int) ($data['series_count'] ?? 10)));
            $interval = max(1, min(365, (int) ($data['series_interval_days'] ?? 7)));

            unset($data['create_series'], $data['series_count'], $data['series_interval_days']);

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

            $attendanceInput = Factory::getApplication()->getInput()->get('attendance', [], 'array');
            $this->saveAttendance($sessionId, $groupId, $attendanceInput);

            if ($createSeries) {
                $this->createSeriesAppointments($data, $seriesCount, $interval);
            }

            $db->transactionCommit();
            return true;
        } catch (Throwable $exception) {
            $db->transactionRollback();
            $this->setError($exception->getMessage());
            return false;
        }
    }

    private function createSeriesAppointments(array $baseData, int $count, int $interval): void
    {
        if (empty($baseData['training_date'])) {
            return;
        }

        $baseDate = new \DateTimeImmutable((string) $baseData['training_date']);

        for ($index = 1; $index < $count; $index++) {
            $table = $this->getTable();
            $row = $baseData;
            $row['id'] = 0;
            $row['training_date'] = $baseDate->modify('+' . ($interval * $index) . ' days')->format('Y-m-d');

            if (!$table->bind($row)) {
                throw new \RuntimeException($table->getError());
            }

            $this->prepareTable($table);

            if (!$table->check() || !$table->store()) {
                throw new \RuntimeException($table->getError());
            }
        }
    }

    private function saveAttendance(int $sessionId, int $groupId, array $input): void
    {
        $allowed = $this->getGroupAthleteIds($groupId);
        $allowedLookup = array_fill_keys($allowed, true);
        $db = $this->getDatabase();
        $userId = (int) Factory::getApplication()->getIdentity()->id;
        $now = Factory::getDate()->toSql();
        $valid = ['present', 'excused', 'absent', 'late'];

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

            if (!in_array($status, $valid, true)) {
                continue;
            }

            $find = $db->getQuery(true)
                ->select('id')
                ->from($db->quoteName('#__jt_attendance'))
                ->where('training_session_id = ' . $sessionId)
                ->where('athlete_id = ' . $athleteId);
            $db->setQuery($find);
            $id = (int) $db->loadResult();

            if ($id > 0) {
                $query = $db->getQuery(true)
                    ->update($db->quoteName('#__jt_attendance'))
                    ->set('status = ' . $db->quote($status))
                    ->set('comment = ' . $db->quote($comment))
                    ->set('modified = ' . $db->quote($now))
                    ->set('modified_by = ' . $userId)
                    ->where('id = ' . $id);
            } else {
                $query = $db->getQuery(true)
                    ->insert($db->quoteName('#__jt_attendance'))
                    ->columns(['training_session_id','athlete_id','status','comment','created','created_by'])
                    ->values(implode(',', [
                        $sessionId, $athleteId, $db->quote($status), $db->quote($comment),
                        $db->quote($now), $userId,
                    ]));
            }

            $db->setQuery($query)->execute();
        }
    }

    private function removeAttendanceOutsideGroup(int $sessionId, int $groupId): void
    {
        $ids = $this->getGroupAthleteIds($groupId);
        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->delete($db->quoteName('#__jt_attendance'))
            ->where('training_session_id = ' . $sessionId);

        if ($ids) {
            $query->where('athlete_id NOT IN (' . implode(',', $ids) . ')');
        }

        $db->setQuery($query)->execute();
    }

    private function getGroupAthleteIds(int $groupId): array
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('athlete_id')
            ->from($db->quoteName('#__jt_training_group_athletes'))
            ->where('group_id = ' . $groupId);
        $db->setQuery($query);
        return array_map('intval', $db->loadColumn());
    }

    private function groupExists(int $groupId): bool
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__jt_training_groups'))
            ->where('id = ' . $groupId)
            ->where('published = 1');
        $db->setQuery($query);
        return (int) $db->loadResult() === 1;
    }

    private function getCurrentSessionId(): int
    {
        $id=(int)$this->getState('training.id');

        if($id>0){
            return $id;
        }

        return (int) Factory::getApplication()->getInput()->getInt('id',0);
    }

    private function getCurrentGroupId(): int
    {
        $sessionId=$this->getCurrentSessionId();

        if($sessionId>0){
            $item=$this->getItem($sessionId);

            if(!empty($item->training_group_id)){
                return (int)$item->training_group_id;
            }
        }

        return (int) Factory::getApplication()->getInput()->getInt('training_group_id',0);
    }
}
