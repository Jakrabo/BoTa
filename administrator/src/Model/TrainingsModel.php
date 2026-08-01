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
            'cancelled', 's.cancelled',
            'group_title', 'g.title',
            'location_name', 'l.name',
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

        $this->setState('filter.group_id', $this->getUserStateFromRequest($this->context . '.filter.group_id', 'filter_group_id', 0, 'int'));
        $this->setState('filter.location_id', $this->getUserStateFromRequest($this->context . '.filter.location_id', 'filter_location_id', 0, 'int'));
        $this->setState('filter.status', $this->getUserStateFromRequest($this->context . '.filter.status', 'filter_status', ''));
        $this->setState('filter.date_from', $this->getUserStateFromRequest($this->context . '.filter.date_from', 'filter_date_from', ''));
        $this->setState('filter.date_to', $this->getUserStateFromRequest($this->context . '.filter.date_to', 'filter_date_to', ''));

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
                'COALESCE(l.name, s.location) AS location_name',
                'COUNT(a.id) AS attendance_total',
                "SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) AS present_total",
                "SUM(CASE WHEN a.status = 'excused' THEN 1 ELSE 0 END) AS excused_total",
                "SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) AS absent_total",
                "SUM(CASE WHEN a.status = 'late' THEN 1 ELSE 0 END) AS late_total",
            ])
            ->from($db->quoteName('#__jt_training_sessions', 's'))
            ->leftJoin($db->quoteName('#__jt_training_groups', 'g') . ' ON g.id = s.training_group_id')
            ->leftJoin($db->quoteName('#__jt_training_locations', 'l') . ' ON l.id = s.location_id')
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

        $groupId = (int) $this->getState('filter.group_id');
        if ($groupId > 0) {
            $query->where('s.training_group_id = ' . $groupId);
        }

        $locationId = (int) $this->getState('filter.location_id');
        if ($locationId > 0) {
            $query->where('s.location_id = ' . $locationId);
        }

        $status = (string) $this->getState('filter.status');
        if ($status === 'cancelled') {
            $query->where('s.cancelled = 1');
        } elseif ($status === 'planned') {
            $query->where('s.cancelled = 0')->where('s.published = 1');
        } elseif ($status === 'unpublished') {
            $query->where('s.published = 0');
        }

        $dateFrom = (string) $this->getState('filter.date_from');
        if ($dateFrom !== '') {
            $query->where('s.training_date >= ' . $db->quote($dateFrom));
        }

        $dateTo = (string) $this->getState('filter.date_to');
        if ($dateTo !== '') {
            $query->where('s.training_date <= ' . $db->quote($dateTo));
        }

        $ordering = $db->escape((string) $this->getState('list.ordering', 's.training_date'));
        $direction = strtoupper((string) $this->getState('list.direction', 'DESC'));
        $direction = $direction === 'ASC' ? 'ASC' : 'DESC';

        $query->order($ordering . ' ' . $direction);

        return $query;
    }

    public function getTrainingGroups(): array
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true)->select(['id', 'title'])->from($db->quoteName('#__jt_training_groups'))->where('published = 1')->order('title');
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    public function getTrainingLocations(): array
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true)->select(['id', 'name'])->from($db->quoteName('#__jt_training_locations'))->where('published = 1')->order('ordering, name');
        $db->setQuery($query);
        return $db->loadObjectList();
    }
}
