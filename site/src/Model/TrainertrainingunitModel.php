<?php
namespace Jugendtraining\Component\Jugendtraining\Site\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;
use Jugendtraining\Component\Jugendtraining\Site\Service\AccessService;
use Throwable;

final class TrainertrainingunitModel extends AdminModel
{
    private AccessService $access;

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->access = new AccessService();
    }

    public function getTable($name = 'Trainingunit', $prefix = 'Administrator', $options = [])
    {
        return parent::getTable($name, $prefix, $options);
    }

    public function getForm($data = [], $loadData = true)
    {
        return $this->loadForm('com_jugendtraining.trainertrainingunit', 'trainertrainingunit', [
            'control' => 'jform',
            'load_data' => $loadData,
        ]);
    }

    protected function loadFormData()
    {
        $this->guard();
        return Factory::getApplication()->getUserState('com_jugendtraining.edit.trainertrainingunit.data', []) ?: $this->getItem();
    }

    public function getUnitItems(): array
    {
        $this->guard();
        $unitId = (int) ($this->getItem()->id ?? 0);
        if ($unitId <= 0) return [];

        $db = $this->getDatabase();
        $query = $db->getQuery(true)->select('*')->from($db->quoteName('#__jt_training_unit_items'))
            ->where('training_unit_id = ' . $unitId)->order('ordering, id');
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    public function getExercises(): array
    {
        $this->guard();
        $db = $this->getDatabase();
        $query = $db->getQuery(true)->select(['id', 'title'])
            ->from($db->quoteName('#__jt_exercises'))->where('published = 1')->order('title');
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    public function save($data): bool
    {
        $this->guard();
        $data['published'] = isset($data['published']) ? (int) $data['published'] : 1;
        $data['modified'] = Factory::getDate()->toSql();
        $data['modified_by'] = (int) Factory::getApplication()->getIdentity()->id;
        if (empty($data['id'])) {
            $data['created'] = $data['modified'];
            $data['created_by'] = $data['modified_by'];
        }

        $db = $this->getDatabase();
        $db->transactionStart();
        try {
            if (!parent::save($data)) throw new \RuntimeException((string) $this->getError());
            $unitId = (int) $this->getState($this->getName() . '.id') ?: (int) ($data['id'] ?? 0);
            if ($unitId <= 0) throw new \RuntimeException('Training unit ID could not be determined.');
            $input = Factory::getApplication()->getInput()->post->get('unit_items', [], 'array');
            $delete = $db->getQuery(true)->delete($db->quoteName('#__jt_training_unit_items'))->where('training_unit_id = ' . $unitId);
            $db->setQuery($delete)->execute();

            foreach (array_values($input) as $ordering => $row) {
                if (!is_array($row)) continue;
                $title = mb_substr(trim((string) ($row['exercise_title'] ?? '')), 0, 190);
                if ($title === '') continue;
                $item = (object) [
                    'training_unit_id' => $unitId,
                    'exercise_id' => max(0, (int) ($row['exercise_id'] ?? 0)) ?: null,
                    'exercise_title' => $title,
                    'duration_minutes' => max(0, min(1440, (int) ($row['duration_minutes'] ?? 0))),
                    'goal' => mb_substr(trim((string) ($row['goal'] ?? '')), 0, 500) ?: null,
                    'content' => trim((string) ($row['content'] ?? '')) ?: null,
                    'method' => mb_substr(trim((string) ($row['method'] ?? '')), 0, 255) ?: null,
                    'material' => trim((string) ($row['material'] ?? '')) ?: null,
                    'remarks' => mb_substr(trim((string) ($row['remarks'] ?? '')), 0, 1000) ?: null,
                    'ordering' => $ordering,
                ];
                $db->insertObject('#__jt_training_unit_items', $item);
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
        $this->guard();
        $db = $this->getDatabase();
        $db->transactionStart();
        try {
            $query = $db->getQuery(true)->delete($db->quoteName('#__jt_training_unit_items'))->where('training_unit_id = ' . $id);
            $db->setQuery($query)->execute();
            $table = $this->getTable();
            if (!$table->load($id) || !$table->delete($id)) throw new \RuntimeException((string) $table->getError());
            $db->transactionCommit();
            return true;
        } catch (Throwable $exception) {
            $db->transactionRollback();
            $this->setError($exception->getMessage());
            return false;
        }
    }

    private function guard(): void
    {
        if (!$this->access->isTrainer()) throw new \RuntimeException('JERROR_ALERTNOAUTHOR', 403);
    }
}
