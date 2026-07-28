<?php

namespace Jugendtraining\Component\Jugendtraining\Administrator\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\QueryInterface;

final class TrainingsModel extends ListModel
{
    public function __construct($config = [])
    {
        $config['filter_fields'] = [
            'id', 's.id',
            'title', 's.title',
            'training_date', 's.training_date',
            'location', 's.location',
            'published', 's.published',
            'trainer_name',
        ];

        parent::__construct($config);
    }

    protected function populateState($ordering = 's.training_date', $direction = 'DESC'): void
    {
        $this->setState(
            'filter.search',
            $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search')
        );

        $this->setState(
            'filter.published',
            $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '')
        );

        parent::populateState($ordering, $direction);
    }

    protected function getListQuery(): QueryInterface
    {
        $db = $this->getDatabase();

        $query = $db->getQuery(true)
            ->select([
                's.*',
                'u.name AS trainer_name',
                'g.title AS group_title',
                'COUNT(a.id) AS attendance_total',
                "SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) AS present_total",
                "SUM(CASE WHEN a.status = 'excused' THEN 1 ELSE 0 END) AS excused_total",
                "SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) AS absent_total",
                "SUM(CASE WHEN a.status = 'late' THEN 1 ELSE 0 END) AS late_total",
            ])
            ->from($db->quoteName('#__jt_training_sessions', 's'))
            ->leftJoin($db->quoteName('#__jt_training_groups', 'g') . ' ON g.id = s.training_group_id')
            ->leftJoin(
                $db->quoteName('#__users', 'u')
                . ' ON ' . $db->quoteName('u.id') . ' = ' . $db->quoteName('s.trainer_user_id')
            )
            ->leftJoin(
                $db->quoteName('#__jt_attendance', 'a')
                . ' ON ' . $db->quoteName('a.training_session_id') . ' = ' . $db->quoteName('s.id')
            )
            ->group('s.id');

        $search = trim((string) $this->getState('filter.search'));

        if ($search !== '') {
            $like = $db->quote('%' . $db->escape($search, true) . '%');
            $query->where(
                '('
                . $db->quoteName('s.title') . ' LIKE ' . $like
                . ' OR ' . $db->quoteName('s.location') . ' LIKE ' . $like
                . ' OR ' . $db->quoteName('u.name') . ' LIKE ' . $like
                . ')'
            );
        }

        $published = $this->getState('filter.published');

        if ($published !== '') {
            $query->where($db->quoteName('s.published') . ' = ' . (int) $published);
        }

        $ordering = $db->escape((string) $this->getState('list.ordering', 's.training_date'));
        $direction = strtoupper((string) $this->getState('list.direction', 'DESC'));
        $direction = $direction === 'ASC' ? 'ASC' : 'DESC';

        $query->order($ordering . ' ' . $direction);

        return $query;
    }
}
