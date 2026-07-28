<?php
namespace Jugendtraining\Component\Jugendtraining\Site\Service;
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;

final class AchievementService
{
    private DatabaseInterface $db;

    public function __construct(?DatabaseInterface $db = null)
    {
        $this->db = $db ?: Factory::getContainer()->get(DatabaseInterface::class);
    }

    public function evaluateAthlete(int $athleteId): array
    {
        if ($athleteId <= 0 || !$this->tablesExist()) {
            return [];
        }

        $query = $this->db->getQuery(true)
            ->select('*')
            ->from($this->db->quoteName('#__jt_achievements'))
            ->where($this->db->quoteName('published') . ' = 1')
            ->where($this->db->quoteName('award_mode') . ' = ' . $this->db->quote('automatic'))
            ->order($this->db->quoteName('ordering') . ', ' . $this->db->quoteName('id'));
        $this->db->setQuery($query);
        $definitions = $this->db->loadObjectList();

        $awarded = [];

        foreach ($definitions as $achievement) {
            [$met, $evidence] = $this->evaluateRule($athleteId, $achievement);

            if ($met && $this->grant($athleteId, (int) $achievement->id, 0, 'automatic', null, $evidence)) {
                $awarded[] = $achievement;
            }
        }

        return $awarded;
    }

    public function evaluateAthletes(array $athleteIds): int
    {
        $count = 0;
        foreach (array_unique(array_map('intval', $athleteIds)) as $athleteId) {
            $count += count($this->evaluateAthlete($athleteId));
        }
        return $count;
    }

    public function grant(int $athleteId, int $achievementId, int $userId, string $source = 'manual', ?string $note = null, ?float $evidence = null): bool
    {
        if ($athleteId <= 0 || $achievementId <= 0) {
            return false;
        }

        $query = $this->db->getQuery(true)
            ->select('id, revoked_at')
            ->from($this->db->quoteName('#__jt_athlete_achievements'))
            ->where('athlete_id = ' . $athleteId)
            ->where('achievement_id = ' . $achievementId);
        $this->db->setQuery($query);
        $existing = $this->db->loadObject();

        $now = Factory::getDate()->toSql();

        if ($existing) {
            if ($existing->revoked_at === null) {
                return false;
            }
            $query = $this->db->getQuery(true)
                ->update($this->db->quoteName('#__jt_athlete_achievements'))
                ->set([
                    'awarded_at = ' . $this->db->quote($now),
                    'awarded_by = ' . $userId,
                    'award_source = ' . $this->db->quote($source),
                    'note = ' . ($note !== null ? $this->db->quote($note) : 'NULL'),
                    'evidence_value = ' . ($evidence !== null ? (float) $evidence : 'NULL'),
                    'revoked_at = NULL',
                    'revoked_by = 0',
                    'revoke_note = NULL',
                ])
                ->where('id = ' . (int) $existing->id);
            $this->db->setQuery($query)->execute();
            return true;
        }

        $columns = ['athlete_id','achievement_id','awarded_at','awarded_by','award_source','note','evidence_value'];
        $values = [
            $athleteId,
            $achievementId,
            $this->db->quote($now),
            $userId,
            $this->db->quote($source),
            $note !== null ? $this->db->quote($note) : 'NULL',
            $evidence !== null ? (float) $evidence : 'NULL',
        ];
        $query = $this->db->getQuery(true)
            ->insert($this->db->quoteName('#__jt_athlete_achievements'))
            ->columns($this->db->quoteName($columns))
            ->values(implode(',', $values));
        $this->db->setQuery($query)->execute();
        return true;
    }

    public function revoke(int $athleteId, int $achievementId, int $userId, ?string $note = null): bool
    {
        $query = $this->db->getQuery(true)
            ->update($this->db->quoteName('#__jt_athlete_achievements'))
            ->set([
                'revoked_at = ' . $this->db->quote(Factory::getDate()->toSql()),
                'revoked_by = ' . $userId,
                'revoke_note = ' . ($note !== null ? $this->db->quote($note) : 'NULL'),
            ])
            ->where('athlete_id = ' . $athleteId)
            ->where('achievement_id = ' . $achievementId)
            ->where('revoked_at IS NULL');
        $this->db->setQuery($query)->execute();
        return $this->db->getAffectedRows() > 0;
    }

    private function evaluateRule(int $athleteId, object $achievement): array
    {
        $value = (float) ($achievement->rule_value ?? 0);

        return match ((string) $achievement->rule_type) {
            'arrows_single_day' => $this->maxArrowsSingleDay($athleteId, $value),
            'arrows_calendar_week' => $this->maxArrowsCalendarWeek($athleteId, $value),
            'diary_week_streak' => $this->diaryWeekStreak($athleteId, (int) $value),
            'event_name_contains' => $this->eventNameContains($athleteId, $achievement),
            default => [false, null],
        };
    }

    private function maxArrowsSingleDay(int $athleteId, float $threshold): array
    {
        $query = $this->db->getQuery(true)
            ->select('COALESCE(MAX(day_arrows),0)')
            ->from('(SELECT training_date, SUM(arrow_count) day_arrows FROM #__jt_training_diary WHERE athlete_id=' . $athleteId . ' GROUP BY training_date) x');
        $this->db->setQuery($query);
        $actual = (float) $this->db->loadResult();
        return [$actual >= $threshold, $actual];
    }

    private function maxArrowsCalendarWeek(int $athleteId, float $threshold): array
    {
        $query = $this->db->getQuery(true)
            ->select('COALESCE(MAX(week_arrows),0)')
            ->from('(SELECT YEARWEEK(training_date,3) yw, SUM(arrow_count) week_arrows FROM #__jt_training_diary WHERE athlete_id=' . $athleteId . ' GROUP BY YEARWEEK(training_date,3)) x');
        $this->db->setQuery($query);
        $actual = (float) $this->db->loadResult();
        return [$actual >= $threshold, $actual];
    }

    private function diaryWeekStreak(int $athleteId, int $threshold): array
    {
        $query = $this->db->getQuery(true)
            ->select('DISTINCT YEARWEEK(training_date,3) week_key')
            ->from($this->db->quoteName('#__jt_training_diary'))
            ->where('athlete_id = ' . $athleteId)
            ->order('week_key');
        $this->db->setQuery($query);
        $weeks = array_map('intval', $this->db->loadColumn());

        $max = 0;
        $current = 0;
        $previousMonday = null;

        foreach ($weeks as $weekKey) {
            $year = intdiv($weekKey, 100);
            $week = $weekKey % 100;
            $monday = (new \DateTimeImmutable())->setISODate($year, $week, 1);

            if ($previousMonday !== null && $previousMonday->modify('+7 days')->format('Y-m-d') === $monday->format('Y-m-d')) {
                $current++;
            } else {
                $current = 1;
            }
            $max = max($max, $current);
            $previousMonday = $monday;
        }

        return [$max >= $threshold, (float) $max];
    }

    private function eventNameContains(int $athleteId, object $achievement): array
    {
        $terms = array_values(array_filter(array_map(
            static fn(string $term): string => trim(mb_strtolower($term)),
            explode(',', (string)($achievement->rule_terms ?? ''))
        )));

        if (!$terms) {
            $decoded = json_decode((string)($achievement->rule_config ?? ''), true);
            $terms = is_array($decoded['terms'] ?? null)
                ? array_values(array_filter(array_map('trim', $decoded['terms'])))
                : [];
        }

        if (!$terms) {
            return [false, null];
        }

        $conditions = [];
        foreach ($terms as $term) {
            $conditions[] = 'LOWER(CONCAT(" ",COALESCE(event_name,"")," ")) LIKE '
                . $this->db->quote('%' . mb_strtolower((string)$term) . '%');
        }

        $query = $this->db->getQuery(true)
            ->select('COUNT(*)')
            ->from($this->db->quoteName('#__jt_results'))
            ->where('athlete_id = ' . $athleteId)
            ->where('published = 1')
            ->where('(' . implode(' OR ', $conditions) . ')');

        if ((int)($achievement->requires_verified_result ?? 0) === 1) {
            $query->where('verification_status = ' . $this->db->quote('verified'));
        }

        $this->db->setQuery($query);
        $count = (int)$this->db->loadResult();

        return [$count > 0, (float)$count];
    }

    private function tablesExist(): bool
    {
        try {
            $this->db->getTableColumns('#__jt_achievements', false);
            $this->db->getTableColumns('#__jt_athlete_achievements', false);
            return true;
        } catch (\Throwable) {
            return false;
        }
    }
}
