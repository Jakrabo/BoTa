<?php

namespace Jugendtraining\Component\Jugendtraining\Administrator\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\BaseDatabaseModel;

final class DashboardModel extends BaseDatabaseModel
{
    public function getStats(): array
    {
        return [
            'athletes' => $this->count('#__jt_athletes'),
            'activeAthletes' => $this->count('#__jt_athletes', 'published = 1'),
            'clubs' => $this->count('#__jt_clubs', 'published = 1'),
            'classes' => $this->count('#__jt_classes', 'published = 1'),
            'trainings' => $this->count('#__jt_training_sessions', 'published = 1'),
            'upcomingTrainings' => $this->count(
                '#__jt_training_sessions',
                'published = 1 AND training_date >= CURRENT_DATE'
            ),
        ];
    }

    public function getCurrentSportyear(): ?object
    {
        $db = $this->getDatabase();

        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__jt_sportyears'))
            ->where('is_current = 1')
            ->where('published = 1')
            ->order('date_start DESC');

        $db->setQuery($query, 0, 1);

        return $db->loadObject() ?: null;
    }

    public function getRecentAthletes(): array
    {
        $db = $this->getDatabase();

        $query = $db->getQuery(true)
            ->select('a.id, a.firstname, a.lastname, a.created, c.name AS club_name')
            ->from($db->quoteName('#__jt_athletes', 'a'))
            ->leftJoin(
                $db->quoteName('#__jt_clubs', 'c')
                . ' ON ' . $db->quoteName('c.id') . ' = ' . $db->quoteName('a.club_id')
            )
            ->order('a.created DESC');

        $db->setQuery($query, 0, 5);

        return $db->loadObjectList();
    }

    public function getUpcomingTrainings(): array
    {
        $db = $this->getDatabase();

        $query = $db->getQuery(true)
            ->select([
                's.id',
                's.title',
                's.training_date',
                's.start_time',
                's.location',
                'u.name AS trainer_name',
            ])
            ->from($db->quoteName('#__jt_training_sessions', 's'))
            ->leftJoin(
                $db->quoteName('#__users', 'u')
                . ' ON ' . $db->quoteName('u.id') . ' = ' . $db->quoteName('s.trainer_user_id')
            )
            ->where($db->quoteName('s.published') . ' = 1')
            ->where($db->quoteName('s.training_date') . ' >= CURRENT_DATE')
            ->order($db->quoteName('s.training_date') . ' ASC, ' . $db->quoteName('s.start_time') . ' ASC');

        $db->setQuery($query, 0, 5);

        return $db->loadObjectList();
    }

    private function count(string $table, ?string $where = null): int
    {
        $db = $this->getDatabase();

        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName($table));

        if ($where !== null) {
            $query->where($where);
        }

        $db->setQuery($query);

        return (int) $db->loadResult();
    }
}
