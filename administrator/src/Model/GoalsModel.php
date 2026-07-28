<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\QueryInterface;

final class GoalsModel extends ListModel
{
    public function __construct($config = [])
    {
        $config['filter_fields'] = [
            'id', 'g.id',
            'title', 'g.title',
            'athlete', 'a.lastname',
            'due_date', 'g.due_date',
            'completed', 'g.completed',
        ];

        parent::__construct($config);
    }

    protected function populateState($ordering = 'g.completed', $direction = 'ASC'): void
    {
        $this->setState(
            'filter.search',
            $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search')
        );

        parent::populateState($ordering, $direction);
    }

    protected function getListQuery(): QueryInterface
    {
        $db = $this->getDatabase();

        $q = $db->getQuery(true)
            ->select([
                'g.*',
                "CONCAT(a.firstname,' ',a.lastname) athlete_name",
                'p.title AS program_title',
            ])
            ->from($db->quoteName('#__jt_goals', 'g'))
            ->leftJoin($db->quoteName('#__jt_athletes', 'a') . ' ON a.id = g.athlete_id')
            ->leftJoin($db->quoteName('#__jt_training_programs', 'p') . ' ON p.id = g.program_id');

        $search = trim((string) $this->getState('filter.search'));

        if ($search !== '') {
            $like = $db->quote('%' . $db->escape($search, true) . '%');
            $q->where('(g.title LIKE ' . $like . ' OR a.firstname LIKE ' . $like . ' OR a.lastname LIKE ' . $like . ')');
        }

        $ordering = $db->escape((string) $this->getState('list.ordering', 'g.completed'));
        $direction = strtoupper((string) $this->getState('list.direction', 'ASC')) === 'DESC'
            ? 'DESC'
            : 'ASC';

        return $q->order($ordering . ' ' . $direction . ', g.due_date ASC');
    }

    public function getItems()
    {
        $items = parent::getItems();
        $calculator = Factory::getApplication()
            ->bootComponent('com_jugendtraining')
            ->getMVCFactory()
            ->createModel('Goal', 'Administrator', ['ignore_request' => true]);

        foreach ($items as $item) {
            if ((string) $item->calculation_mode === 'automatic' && (string) $item->target_type !== 'custom') {
                $item->current_value = $calculator->calculateMetric((array) $item);
                $item->completed = ((float) $item->target_value > 0 && (float) $item->current_value >= (float) $item->target_value)
                    ? 1
                    : 0;
            }
        }

        return $items;
    }
}
