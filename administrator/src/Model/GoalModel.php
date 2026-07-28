<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;

final class GoalModel extends AdminModel
{
    public function getTable($name = 'Goal', $prefix = 'Administrator', $options = [])
    {
        return parent::getTable($name, $prefix, $options);
    }

    public function getForm($data = [], $loadData = true)
    {
        return $this->loadForm(
            'com_jugendtraining.goal',
            'goal',
            ['control' => 'jform', 'load_data' => $loadData]
        );
    }

    public function save($data): bool
    {
        foreach (['target_value', 'current_value'] as $field) {
            if (!isset($data[$field]) || $data[$field] === '' || $data[$field] === null) {
                $data[$field] = 0;
            } else {
                $data[$field] = (float) str_replace(',', '.', (string) $data[$field]);
            }
        }

        foreach (['distance_m', 'arrows', 'program_id', 'completed', 'published'] as $field) {
            $data[$field] = (!isset($data[$field]) || $data[$field] === '' || $data[$field] === null)
                ? 0
                : (int) $data[$field];
        }

        if (empty($data['due_date'])) {
            $data['due_date'] = null;
        }

        $data['calculation_mode'] = in_array(($data['calculation_mode'] ?? ''), ['automatic', 'manual'], true)
            ? $data['calculation_mode']
            : 'automatic';

        if ($data['calculation_mode'] === 'automatic' && ($data['target_type'] ?? '') !== 'custom') {
            $data['current_value'] = $this->calculateMetric($data);
        }

        $data['completed'] = ((float) $data['target_value'] > 0 && (float) $data['current_value'] >= (float) $data['target_value'])
            ? 1
            : (int) $data['completed'];

        return parent::save($data);
    }

    public function calculateMetric(array $goal): float
    {
        $athleteId = (int) ($goal['athlete_id'] ?? 0);
        $type = (string) ($goal['target_type'] ?? 'custom');

        if ($athleteId <= 0) {
            return 0;
        }

        return match ($type) {
            'attendance' => $this->calculateAttendance($athleteId),
            'score' => $this->calculateBestScore(
                $athleteId,
                (int) ($goal['distance_m'] ?? 0),
                (int) ($goal['arrows'] ?? 0)
            ),
            'average' => $this->calculateBestAverage(
                $athleteId,
                (int) ($goal['distance_m'] ?? 0),
                (int) ($goal['arrows'] ?? 0)
            ),
            'program' => $this->calculateProgramProgress(
                $athleteId,
                (int) ($goal['program_id'] ?? 0)
            ),
            default => (float) ($goal['current_value'] ?? 0),
        };
    }

    private function calculateAttendance(int $athleteId): float
    {
        $db = $this->getDatabase();
        $columns = $db->getTableColumns('#__jt_attendance', false);

        $q = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__jt_attendance'))
            ->where($db->quoteName('athlete_id') . ' = ' . $athleteId);

        if (isset($columns['status'])) {
            $q->where($db->quoteName('status') . " IN ('present','late','anwesend','verspaetet')");
        } elseif (isset($columns['present'])) {
            $q->where($db->quoteName('present') . ' = 1');
        }

        $db->setQuery($q);

        return (float) $db->loadResult();
    }

    private function calculateBestScore(int $athleteId, int $distance, int $arrows): float
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

    private function calculateBestAverage(int $athleteId, int $distance, int $arrows): float
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

    private function calculateProgramProgress(int $athleteId, int $programId): float
    {
        if ($programId <= 0) {
            return 0;
        }

        $db = $this->getDatabase();

        $q = $db->getQuery(true)
            ->select([
                'COUNT(DISTINCT pe.exercise_id) AS exercise_count',
                'SUM(CASE WHEN pp.completed = 1 THEN 1 ELSE 0 END) AS completed_count',
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

    protected function loadFormData()
    {
        $data = Factory::getApplication()->getUserState('com_jugendtraining.edit.goal.data', []);

        return $data ?: $this->getItem();
    }

    protected function prepareTable($table): void
    {
        $date = Factory::getDate()->toSql();
        $uid = (int) Factory::getApplication()->getIdentity()->id;

        if (empty($table->id)) {
            $table->created = $date;
            $table->created_by = $uid;
        } else {
            $table->modified = $date;
            $table->modified_by = $uid;
        }
    }
}
