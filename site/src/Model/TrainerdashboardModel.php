<?php
namespace Jugendtraining\Component\Jugendtraining\Site\Model;

\defined('_JEXEC') or die;

final class TrainerdashboardModel extends TrainerModel
{
    public function getCalendarEventCount(): int
    {
        $this->requireTrainer();
        $calendar = new \Jugendtraining\Component\Jugendtraining\Site\Service\CalendarService();

        return count($calendar->events(['mode' => 'all'], true));
    }

    public function getTodayTrainings(): array
    {
        $this->requireTrainer();

        $app = \Joomla\CMS\Factory::getApplication();
        $userId = (int) $app->getIdentity()->id;
        $timezone = (string) $app->get('offset', 'UTC');

        try {
            $today = new \DateTimeImmutable('today', new \DateTimeZone($timezone ?: 'UTC'));
        } catch (\Throwable) {
            $today = new \DateTimeImmutable('today');
        }

        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select([
                's.id',
                's.title',
                's.start_time',
                's.end_time',
                's.location',
                's.training_unit_id',
                'g.title AS group_title',
                'COUNT(DISTINCT CASE WHEN athlete.published = 1 THEN athlete.id END) AS expected_total',
                "COUNT(DISTINCT CASE WHEN athlete.published = 1 AND (attendance.id IS NULL OR attendance.status IS NULL OR attendance.status = '') THEN athlete.id END) AS open_total",
                "COUNT(DISTINCT CASE WHEN athlete.published = 1 AND attendance.status = 'present' THEN athlete.id END) AS present_total",
                "COUNT(DISTINCT CASE WHEN athlete.published = 1 AND attendance.status = 'excused' THEN athlete.id END) AS excused_total",
                "COUNT(DISTINCT CASE WHEN athlete.published = 1 AND attendance.status = 'late' THEN athlete.id END) AS late_total",
                "COUNT(DISTINCT CASE WHEN athlete.published = 1 AND attendance.status = 'absent' THEN athlete.id END) AS absent_total",
            ])
            ->from($db->quoteName('#__jt_training_sessions', 's'))
            ->leftJoin($db->quoteName('#__jt_training_groups', 'g') . ' ON g.id = s.training_group_id')
            ->leftJoin($db->quoteName('#__jt_training_group_trainers', 'trainer_group') . ' ON trainer_group.group_id = s.training_group_id')
            ->leftJoin($db->quoteName('#__jt_training_group_athletes', 'group_athlete') . ' ON group_athlete.group_id = s.training_group_id')
            ->leftJoin($db->quoteName('#__jt_athletes', 'athlete') . ' ON athlete.id = group_athlete.athlete_id')
            ->leftJoin(
                $db->quoteName('#__jt_attendance', 'attendance')
                . ' ON attendance.training_session_id = s.id AND attendance.athlete_id = athlete.id'
            )
            ->where('s.training_date = ' . $db->quote($today->format('Y-m-d')))
            ->where('s.published = 1')
            ->where('s.cancelled = 0')
            ->where('(trainer_group.user_id = ' . $userId . ' OR s.trainer_user_id = ' . $userId . ')')
            ->group('s.id')
            ->order('s.start_time ASC, s.id ASC');

        $db->setQuery($query);

        return $db->loadObjectList();
    }
}
