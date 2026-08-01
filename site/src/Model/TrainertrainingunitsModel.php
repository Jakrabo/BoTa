<?php
namespace Jugendtraining\Component\Jugendtraining\Site\Model;

\defined('_JEXEC') or die;

final class TrainertrainingunitsModel extends TrainerModel
{
    public function getTrainingUnits(): array
    {
        $this->requireTrainer();
        $db = $this->getDatabase();
        $query = $db->getQuery(true)->select([
            'unit.*',
            'COUNT(item.id) AS item_count',
            'COALESCE(SUM(item.duration_minutes), 0) AS duration_total',
        ])->from($db->quoteName('#__jt_training_units', 'unit'))
            ->leftJoin($db->quoteName('#__jt_training_unit_items', 'item') . ' ON item.training_unit_id = unit.id')
            ->group('unit.id')->order('unit.title');
        $db->setQuery($query);
        return $db->loadObjectList();
    }
}
