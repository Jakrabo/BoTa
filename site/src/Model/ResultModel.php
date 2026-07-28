<?php

namespace Jugendtraining\Component\Jugendtraining\Site\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\Database\ParameterType;
use Throwable;

final class ResultModel extends AdminModel
{
    public function getTable($name = 'Result', $prefix = 'Administrator', $options = [])
    {
        return parent::getTable($name, $prefix, $options);
    }

    public function getForm($data = [], $loadData = true)
    {
        return $this->loadForm(
            'com_jugendtraining.result',
            'result',
            ['control' => 'jform', 'load_data' => $loadData]
        );
    }

    protected function loadFormData()
    {
        $app = Factory::getApplication();
        $data = $app->getUserState('com_jugendtraining.edit.result.data', []);

        if ($data) {
            return $data;
        }

        $item = $this->getItem();

        if (!empty($item->id) && !$this->ownsResult((int) $item->id)) {
            throw new \RuntimeException('JERROR_ALERTNOAUTHOR', 403);
        }

        return $item;
    }

    public function save($data): bool
    {
        $data = $this->normaliseOptionalWeatherFields($data);
        $athleteId = $this->getCurrentAthleteId();

        if ($athleteId <= 0) {
            $this->setError('COM_JUGENDTRAINING_NO_LINKED_ATHLETE');
            return false;
        }

        $id = (int) ($data['id'] ?? 0);

        if ($id > 0 && !$this->ownsResult($id)) {
            $this->setError('JERROR_ALERTNOAUTHOR');
            return false;
        }

        $data['athlete_id'] = $athleteId;
        $setupId = (int) ($data['bow_setup_id'] ?? 0);
        if ($setupId <= 0) {
            $db = $this->getDatabase();
            $query = $db->getQuery(true)->select('id')->from($db->quoteName('#__jt_bow_setups'))->where('athlete_id=' . $athleteId)->where('is_active=1');
            $db->setQuery($query, 0, 1);
            $setupId = (int) $db->loadResult();
        } else {
            $db = $this->getDatabase();
            $query = $db->getQuery(true)->select('COUNT(*)')->from($db->quoteName('#__jt_bow_setups'))->where('id=' . $setupId)->where('athlete_id=' . $athleteId);
            $db->setQuery($query);
            if ((int) $db->loadResult() !== 1) { $setupId = 0; }
        }
        $data['bow_setup_id'] = $setupId ?: null;
        $data['verification_status'] = 'pending';
        $data['verified_by'] = 0;
        $data['verified_at'] = null;
        $data['published'] = 1;

        return parent::save($data);
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

        $table->average = (int) $table->arrows > 0
            ? round(((int) $table->score / (int) $table->arrows), 3)
            : 0;
    }


    public function canCreateResult(): bool
    {
        return $this->getCurrentAthleteId() > 0;
    }

    public function canEditResult(int $id): bool
    {
        return $this->ownsResult($id);
    }

    public function deleteOwnResult(int $id): bool
    {
        if (!$this->ownsResult($id)) {
            $this->setError('JERROR_ALERTNOAUTHOR');
            return false;
        }

        try {
            return $this->delete($id);
        } catch (Throwable $exception) {
            $this->setError($exception->getMessage());
            return false;
        }
    }


    private function normaliseOptionalWeatherFields(array $data): array
    {
        foreach (['temperature_c', 'wind_speed_kmh'] as $field) {
            if (!array_key_exists($field, $data)) {
                continue;
            }

            $value = is_string($data[$field]) ? trim($data[$field]) : $data[$field];

            if ($value === '' || $value === null) {
                $data[$field] = null;
                continue;
            }

            // Accept both German decimal commas and decimal points.
            if (is_string($value)) {
                $value = str_replace(',', '.', $value);
            }

            $data[$field] = is_numeric($value) ? (float) $value : null;
        }

        foreach (['weather_condition', 'wind_direction'] as $field) {
            if (array_key_exists($field, $data) && trim((string) $data[$field]) === '') {
                $data[$field] = null;
            }
        }

        return $data;
    }

    private function ownsResult(int $id): bool
    {
        $athleteId = $this->getCurrentAthleteId();

        if ($athleteId <= 0 || $id <= 0) {
            return false;
        }

        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__jt_results'))
            ->where($db->quoteName('id') . ' = :id')
            ->where($db->quoteName('athlete_id') . ' = :athleteId')
            ->bind(':id', $id, ParameterType::INTEGER)
            ->bind(':athleteId', $athleteId, ParameterType::INTEGER);

        $db->setQuery($query);

        return (int) $db->loadResult() === 1;
    }

    private function getCurrentAthleteId(): int
    {
        if (!$this->hasUserColumn()) {
            return 0;
        }

        $userId = (int) Factory::getApplication()->getIdentity()->id;

        if ($userId <= 0) {
            return 0;
        }

        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__jt_athletes'))
            ->where($db->quoteName('user_id') . ' = :userId')
            ->where($db->quoteName('published') . ' = 1')
            ->bind(':userId', $userId, ParameterType::INTEGER);

        $db->setQuery($query, 0, 1);

        return (int) $db->loadResult();
    }

    private function hasUserColumn(): bool
    {
        $db = $this->getDatabase();
        $columns = $db->getTableColumns('#__jt_athletes', false);

        return isset($columns['user_id']);
    }

}
