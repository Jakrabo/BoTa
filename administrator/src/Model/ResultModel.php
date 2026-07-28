<?php

namespace Jugendtraining\Component\Jugendtraining\Administrator\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;

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
        $data = Factory::getApplication()->getUserState('com_jugendtraining.edit.result.data', []);
        return $data ?: $this->getItem();
    }


    public function save($data): bool
    {
        $data = $this->normaliseOptionalWeatherFields($data);

        return parent::save($data);
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

    protected function prepareTable($table): void
    {
        $date = Factory::getDate()->toSql();
        $user = Factory::getApplication()->getIdentity();

        if (empty($table->id)) {
            $table->created = $date;
            $table->created_by = (int) $user->id;
        } else {
            $table->modified = $date;
            $table->modified_by = (int) $user->id;
        }

        $table->average = (int) $table->arrows > 0
            ? round(((int) $table->score / (int) $table->arrows), 3)
            : 0;

        if ((string) $table->verification_status === 'verified') {
            $table->verified_by = (int) $user->id;
            $table->verified_at = $date;
        } elseif ((string) $table->verification_status !== 'verified') {
            $table->verified_by = 0;
            $table->verified_at = null;
        }
    }
}
