<?php

namespace Jugendtraining\Component\Jugendtraining\Site\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Session\Session;
use Jugendtraining\Component\Jugendtraining\Site\Service\AccessService;

final class CsvexportController extends BaseController
{
    private AccessService $access;

    public function __construct($config = [], $factory = null, $app = null, $input = null)
    {
        parent::__construct($config, $factory, $app, $input);
        $this->access = new AccessService();
    }

    public function diary(): void
    {
        $this->assertRequest();
        $scope = $this->getScope();

        $rows = $scope === 'trainer'
            ? $this->getTrainerDiaryRows()
            : $this->getAthleteDiaryRows();

        $headers = [
            'Datum',
            'Schütze',
            'Dauer (Minuten)',
            'Pfeile',
            'Trainingsmethode',
            'Entfernung (m)',
            'Fokusthema',
            'Intensität',
            'Gefühl',
            'Bogensetup',
            'Revision',
            'Notizen',
        ];

        $data = array_map(
            fn(object $row): array => [
                $this->formatDate($row->training_date ?? null),
                (string) ($row->athlete_name ?? ''),
                $row->duration_minutes ?? '',
                $row->arrow_count ?? '',
                $row->training_method ?? '',
                $this->formatDecimal($row->distance_m ?? null),
                $row->focus_topic ?? '',
                $row->intensity ?? '',
                $row->feeling ?? '',
                $row->setup_title ?? '',
                $row->revision_no ?? '',
                $row->notes ?? '',
            ],
            $rows
        );

        $this->sendCsv(
            'jugendtraining-tagebuch-' . date('Y-m-d') . '.csv',
            $headers,
            $data
        );
    }

    public function results(): void
    {
        $this->assertRequest();
        $scope = $this->getScope();

        $rows = $scope === 'trainer'
            ? $this->getTrainerResultRows()
            : $this->getAthleteResultRows();

        $headers = [
            'Datum',
            'Schütze',
            'Art',
            'Veranstaltung',
            'Entfernung (m)',
            'Pfeile',
            'Ringe',
            'Durchschnitt',
            '10er',
            'X',
            'Prüfstatus',
            'Bogensetup',
            'Revision',
            'Wetter',
            'Temperatur (°C)',
            'Wind (km/h)',
            'Windrichtung',
            'Notizen',
        ];

        $data = array_map(
            fn(object $row): array => [
                $this->formatDate($row->result_date ?? null),
                (string) ($row->athlete_name ?? ''),
                $row->event_type ?? '',
                $row->event_name ?? '',
                $row->distance_m ?? '',
                $row->arrows ?? '',
                $row->score ?? '',
                $this->formatDecimal($row->average ?? null, 3),
                $row->tens ?? '',
                $row->xs ?? '',
                $row->verification_status ?? '',
                $row->setup_title ?? '',
                $row->revision_no ?? '',
                $row->weather_condition ?? '',
                $this->formatDecimal($row->temperature_c ?? null),
                $this->formatDecimal($row->wind_speed_kmh ?? null),
                $row->wind_direction ?? '',
                $row->notes ?? '',
            ],
            $rows
        );

        $this->sendCsv(
            'jugendtraining-ergebnisse-' . date('Y-m-d') . '.csv',
            $headers,
            $data
        );
    }

    private function assertRequest(): void
    {
        Session::checkToken('get') or jexit('JINVALID_TOKEN');

        $user = Factory::getApplication()->getIdentity();

        if ($user->guest) {
            throw new \RuntimeException('JERROR_ALERTNOAUTHOR', 403);
        }
    }

    private function getScope(): string
    {
        $scope = Factory::getApplication()->getInput()->getCmd('scope', 'athlete');

        if ($scope === 'trainer') {
            if (!$this->access->isTrainer()) {
                throw new \RuntimeException('JERROR_ALERTNOAUTHOR', 403);
            }

            return 'trainer';
        }

        return 'athlete';
    }

    private function getAthleteDiaryRows(): array
    {
        $userId = (int) Factory::getApplication()->getIdentity()->id;
        $db = Factory::getContainer()->get('DatabaseDriver');

        $query = $db->getQuery(true)
            ->select([
                'd.*',
                "CONCAT(a.firstname, ' ', a.lastname) AS athlete_name",
                's.title AS setup_title',
                's.revision_no',
            ])
            ->from($db->quoteName('#__jt_training_diary', 'd'))
            ->innerJoin(
                $db->quoteName('#__jt_athletes', 'a')
                . ' ON a.id = d.athlete_id'
            )
            ->leftJoin(
                $db->quoteName('#__jt_bow_setups', 's')
                . ' ON s.id = d.bow_setup_id'
            )
            ->where('a.user_id = ' . $userId)
            ->where('a.published = 1')
            ->order('d.training_date DESC, d.id DESC');

        $db->setQuery($query);

        return $db->loadObjectList();
    }

    private function getAthleteResultRows(): array
    {
        $userId = (int) Factory::getApplication()->getIdentity()->id;
        $db = Factory::getContainer()->get('DatabaseDriver');

        $query = $db->getQuery(true)
            ->select([
                'r.*',
                "CONCAT(a.firstname, ' ', a.lastname) AS athlete_name",
                's.title AS setup_title',
                's.revision_no',
            ])
            ->from($db->quoteName('#__jt_results', 'r'))
            ->innerJoin(
                $db->quoteName('#__jt_athletes', 'a')
                . ' ON a.id = r.athlete_id'
            )
            ->leftJoin(
                $db->quoteName('#__jt_bow_setups', 's')
                . ' ON s.id = r.bow_setup_id'
            )
            ->where('a.user_id = ' . $userId)
            ->where('a.published = 1')
            ->where('r.published = 1')
            ->order('r.result_date DESC, r.id DESC');

        $db->setQuery($query);

        return $db->loadObjectList();
    }

    private function getTrainerDiaryRows(): array
    {
        $ids = $this->access->getTrainerAthleteIds();

        if (!$ids) {
            return [];
        }

        $db = Factory::getContainer()->get('DatabaseDriver');
        $idList = implode(',', array_map('intval', $ids));

        $query = $db->getQuery(true)
            ->select([
                'd.*',
                "CONCAT(a.firstname, ' ', a.lastname) AS athlete_name",
                's.title AS setup_title',
                's.revision_no',
            ])
            ->from($db->quoteName('#__jt_training_diary', 'd'))
            ->innerJoin(
                $db->quoteName('#__jt_athletes', 'a')
                . ' ON a.id = d.athlete_id'
            )
            ->leftJoin(
                $db->quoteName('#__jt_bow_setups', 's')
                . ' ON s.id = d.bow_setup_id'
            )
            ->where('d.athlete_id IN (' . $idList . ')')
            ->where('a.published = 1')
            ->order('d.training_date DESC, a.lastname, a.firstname, d.id DESC');

        $db->setQuery($query);

        return $db->loadObjectList();
    }

    private function getTrainerResultRows(): array
    {
        $ids = $this->access->getTrainerAthleteIds();

        if (!$ids) {
            return [];
        }

        $db = Factory::getContainer()->get('DatabaseDriver');
        $idList = implode(',', array_map('intval', $ids));

        $query = $db->getQuery(true)
            ->select([
                'r.*',
                "CONCAT(a.firstname, ' ', a.lastname) AS athlete_name",
                's.title AS setup_title',
                's.revision_no',
            ])
            ->from($db->quoteName('#__jt_results', 'r'))
            ->innerJoin(
                $db->quoteName('#__jt_athletes', 'a')
                . ' ON a.id = r.athlete_id'
            )
            ->leftJoin(
                $db->quoteName('#__jt_bow_setups', 's')
                . ' ON s.id = r.bow_setup_id'
            )
            ->where('r.athlete_id IN (' . $idList . ')')
            ->where('a.published = 1')
            ->where('r.published = 1')
            ->order('r.result_date DESC, a.lastname, a.firstname, r.id DESC');

        $db->setQuery($query);

        return $db->loadObjectList();
    }

    private function sendCsv(string $filename, array $headers, array $rows): void
    {
        $app = Factory::getApplication();

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        $app->setHeader('Content-Type', 'text/csv; charset=UTF-8', true);
        $app->setHeader(
            'Content-Disposition',
            'attachment; filename="' . $filename . '"',
            true
        );
        $app->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate', true);
        $app->sendHeaders();

        $stream = fopen('php://output', 'wb');

        if ($stream === false) {
            throw new \RuntimeException('CSV stream could not be opened.');
        }

        // UTF-8 BOM helps Microsoft Excel recognise umlauts correctly.
        fwrite($stream, "\xEF\xBB\xBF");
        fputcsv($stream, $headers, ';', '"', '\\');

        foreach ($rows as $row) {
            $safeRow = array_map([$this, 'protectCsvCell'], $row);
            fputcsv($stream, $safeRow, ';', '"', '\\');
        }

        fclose($stream);
        $app->close();
    }

    private function protectCsvCell(mixed $value): string
    {
        $value = (string) ($value ?? '');

        // Prevent spreadsheet formula injection.
        if ($value !== '' && preg_match('/^[=+\-@]/u', ltrim($value))) {
            return "'" . $value;
        }

        return $value;
    }

    private function formatDate(?string $value): string
    {
        if (!$value || $value === '0000-00-00') {
            return '';
        }

        $date = \DateTimeImmutable::createFromFormat('Y-m-d', substr($value, 0, 10));

        return $date ? $date->format('d.m.Y') : $value;
    }

    private function formatDecimal(mixed $value, int $decimals = 2): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        return number_format((float) $value, $decimals, ',', '');
    }
}
