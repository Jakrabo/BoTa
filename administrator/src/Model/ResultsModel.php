<?php

namespace Jugendtraining\Component\Jugendtraining\Administrator\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\QueryInterface;

final class ResultsModel extends ListModel
{
    public function __construct($config = [])
    {
        $config['filter_fields'] = [
            'id', 'r.id',
            'result_date', 'r.result_date',
            'score', 'r.score',
            'distance_m', 'r.distance_m',
            'athlete_name',
            'event_type', 'r.event_type',
        ];
        parent::__construct($config);
    }

    protected function populateState($ordering = 'r.result_date', $direction = 'DESC'): void
    {
        $this->setState('filter.search', $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search'));
        $this->setState('filter.athlete_id', $this->getUserStateFromRequest($this->context . '.filter.athlete_id', 'filter_athlete_id', ''));
        $this->setState('filter.event_type', $this->getUserStateFromRequest($this->context . '.filter.event_type', 'filter_event_type', ''));
        parent::populateState($ordering, $direction);
    }

    protected function getListQuery(): QueryInterface
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select([
                'r.*',
                "CONCAT(a.firstname, ' ', a.lastname) AS athlete_name",
                'c.name AS club_name',
                'cl.name AS class_name',
            ])
            ->from($db->quoteName('#__jt_results', 'r'))
            ->leftJoin($db->quoteName('#__jt_athletes', 'a') . ' ON a.id = r.athlete_id')
            ->leftJoin($db->quoteName('#__jt_clubs', 'c') . ' ON c.id = a.club_id')
            ->leftJoin($db->quoteName('#__jt_classes', 'cl') . ' ON cl.id = a.class_id');

        $search = trim((string) $this->getState('filter.search'));
        if ($search !== '') {
            $like = $db->quote('%' . $db->escape($search, true) . '%');
            $query->where(
                '('
                . "CONCAT(a.firstname, ' ', a.lastname) LIKE " . $like
                . ' OR r.event_name LIKE ' . $like
                . ')'
            );
        }

        $athleteId = (int) $this->getState('filter.athlete_id');
        if ($athleteId > 0) {
            $query->where('r.athlete_id = ' . $athleteId);
        }

        $eventType = (string) $this->getState('filter.event_type');
        if ($eventType !== '') {
            $query->where('r.event_type = ' . $db->quote($eventType));
        }

        $ordering = $db->escape((string) $this->getState('list.ordering', 'r.result_date'));
        $direction = strtoupper((string) $this->getState('list.direction', 'DESC')) === 'ASC' ? 'ASC' : 'DESC';
        $query->order($ordering . ' ' . $direction);

        return $query;
    }

    public function getAthleteOptions(): array
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select("id AS value, CONCAT(lastname, ', ', firstname) AS text")
            ->from($db->quoteName('#__jt_athletes'))
            ->where('published = 1')
            ->order('lastname, firstname');
        $db->setQuery($query);
        return $db->loadObjectList();
    }
}
