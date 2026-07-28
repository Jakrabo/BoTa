<?php
namespace Jugendtraining\Component\Jugendtraining\Site\Service;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;

final class AccessService
{
    private DatabaseInterface $db;

    public function __construct()
    {
        $this->db = Factory::getContainer()->get(DatabaseInterface::class);
    }

    public function isSuperUser(?object $user = null): bool
    {
        $user ??= Factory::getApplication()->getIdentity();
        return $user->authorise('core.admin');
    }

    public function isTrainer(?object $user = null): bool
    {
        $user ??= Factory::getApplication()->getIdentity();

        if ($user->guest) {
            return false;
        }

        if ($this->isSuperUser($user) || $user->authorise('trainer.access', 'com_jugendtraining')) {
            return true;
        }

        return $this->hasTrainerGroupMembership((int) $user->id)
            || $this->userInNamedGroup((int) $user->id, 'BoTa - Trainer')
            || $this->userInNamedGroup((int) $user->id, 'Jugendtraining - Trainer');
    }

    public function isAthlete(?object $user = null): bool
    {
        $user ??= Factory::getApplication()->getIdentity();

        if ($user->guest) {
            return false;
        }

        return $user->authorise('athlete.access', 'com_jugendtraining')
            || $this->userInNamedGroup((int) $user->id, 'BoTa - Schütze')
            || $this->userInNamedGroup((int) $user->id, 'Jugendtraining - Schütze')
            || $this->hasAthleteRecord((int) $user->id);
    }

    public function getTrainerAthleteIds(?int $userId = null): array
    {
        $user = Factory::getApplication()->getIdentity();
        $currentUserId = (int) $user->id;
        $userId ??= $currentUserId;

        if (!$this->isSuperUser($user) && $userId !== $currentUserId) {
            return [];
        }

        if ($this->isSuperUser($user)) {
            $query = $this->db->getQuery(true)
                ->select($this->db->quoteName('id'))
                ->from($this->db->quoteName('#__jt_athletes'))
                ->where($this->db->quoteName('published') . ' = 1');
            $this->db->setQuery($query);
            return array_map('intval', $this->db->loadColumn());
        }

        $query = $this->db->getQuery(true)
            ->select('DISTINCT ga.athlete_id')
            ->from($this->db->quoteName('#__jt_training_group_trainers', 'gt'))
            ->innerJoin(
                $this->db->quoteName('#__jt_training_group_athletes', 'ga')
                . ' ON ga.group_id = gt.group_id'
            )
            ->innerJoin(
                $this->db->quoteName('#__jt_training_groups', 'g')
                . ' ON g.id = gt.group_id AND g.published = 1'
            )
            ->where('gt.user_id = ' . (int) $userId);

        $this->db->setQuery($query);
        return array_map('intval', $this->db->loadColumn());
    }

    public function canManageAthlete(int $athleteId): bool
    {
        if ($athleteId <= 0) {
            return false;
        }

        return in_array($athleteId, $this->getTrainerAthleteIds(), true);
    }

    private function hasTrainerGroupMembership(int $userId): bool
    {
        $query = $this->db->getQuery(true)
            ->select('COUNT(*)')
            ->from($this->db->quoteName('#__jt_training_group_trainers'))
            ->where('user_id = ' . $userId);
        $this->db->setQuery($query);
        return (int) $this->db->loadResult() > 0;
    }

    private function hasAthleteRecord(int $userId): bool
    {
        $query = $this->db->getQuery(true)
            ->select('COUNT(*)')
            ->from($this->db->quoteName('#__jt_athletes'))
            ->where('user_id = ' . $userId)
            ->where('published = 1');
        $this->db->setQuery($query);
        return (int) $this->db->loadResult() > 0;
    }

    private function userInNamedGroup(int $userId, string $groupTitle): bool
    {
        $query = $this->db->getQuery(true)
            ->select('COUNT(*)')
            ->from($this->db->quoteName('#__user_usergroup_map', 'm'))
            ->innerJoin($this->db->quoteName('#__usergroups', 'g') . ' ON g.id = m.group_id')
            ->where('m.user_id = ' . $userId)
            ->where('g.title = ' . $this->db->quote($groupTitle));
        $this->db->setQuery($query);
        return (int) $this->db->loadResult() > 0;
    }
}
