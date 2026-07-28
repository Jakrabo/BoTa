<?php
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\Database\DatabaseInterface;

final class Com_JugendtrainingInstallerScript
{

    public function preflight(string $type, InstallerAdapter $parent): bool
    {
        if (!in_array($type, ['install', 'update'], true)) {
            return true;
        }

        try {
            $this->removeStaleAdministratorMenuEntries();
        } catch (\Throwable $exception) {
            Factory::getApplication()->enqueueMessage(
                'Alte BoTa-Menüeinträge konnten nicht vollständig bereinigt werden: '
                . $exception->getMessage(),
                'warning'
            );
        }

        return true;
    }

    private function removeStaleAdministratorMenuEntries(): void
    {
        /** @var DatabaseInterface $db */
        $db = Factory::getContainer()->get(DatabaseInterface::class);

        $query = $db->getQuery(true)
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__menu'))
            ->where($db->quoteName('client_id') . ' = 1')
            ->where(
                '('
                . $db->quoteName('alias') . ' = ' . $db->quote('com-jugendtraining')
                . ' OR ' . $db->quoteName('link') . ' LIKE ' . $db->quote('index.php?option=com_jugendtraining%')
                . ')'
            );

        $db->setQuery($query);
        $ids = array_map('intval', $db->loadColumn());

        if (!$ids) {
            return;
        }

        // Include descendants so no orphaned submenu entries remain.
        $allIds = $ids;

        do {
            $childrenQuery = $db->getQuery(true)
                ->select($db->quoteName('id'))
                ->from($db->quoteName('#__menu'))
                ->where($db->quoteName('client_id') . ' = 1')
                ->where($db->quoteName('parent_id') . ' IN (' . implode(',', $allIds) . ')');

            $db->setQuery($childrenQuery);
            $children = array_map('intval', $db->loadColumn());
            $newChildren = array_values(array_diff($children, $allIds));
            $allIds = array_values(array_unique(array_merge($allIds, $newChildren)));
        } while ($newChildren);

        $delete = $db->getQuery(true)
            ->delete($db->quoteName('#__menu'))
            ->where($db->quoteName('id') . ' IN (' . implode(',', $allIds) . ')');

        $db->setQuery($delete)->execute();
    }

    public function postflight(string $type, InstallerAdapter $parent): void
    {
        $this->ensureUserGroup('BoTa - Trainer');
        $this->ensureUserGroup('BoTa - Schütze');
    }

    private function ensureUserGroup(string $title): void
    {
        /** @var DatabaseInterface $db */
        $db = Factory::getContainer()->get(DatabaseInterface::class);

        $query = $db->getQuery(true)
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__usergroups'))
            ->where($db->quoteName('title') . ' = :title')
            ->bind(':title', $title);

        $db->setQuery($query);

        if ((int) $db->loadResult() > 0) {
            return;
        }

        $parentQuery = $db->getQuery(true)
            ->select([
                $db->quoteName('id'),
                $db->quoteName('rgt'),
            ])
            ->from($db->quoteName('#__usergroups'))
            ->where($db->quoteName('title') . ' = ' . $db->quote('Registered'));

        $db->setQuery($parentQuery);
        $registered = $db->loadObject();

        if (!$registered) {
            return;
        }

        $insertPosition = (int) $registered->rgt;

        $db->transactionStart();

        try {
            $updateRight = $db->getQuery(true)
                ->update($db->quoteName('#__usergroups'))
                ->set($db->quoteName('rgt') . ' = ' . $db->quoteName('rgt') . ' + 2')
                ->where($db->quoteName('rgt') . ' >= ' . $insertPosition);

            $db->setQuery($updateRight)->execute();

            $updateLeft = $db->getQuery(true)
                ->update($db->quoteName('#__usergroups'))
                ->set($db->quoteName('lft') . ' = ' . $db->quoteName('lft') . ' + 2')
                ->where($db->quoteName('lft') . ' > ' . $insertPosition);

            $db->setQuery($updateLeft)->execute();

            $columns = [
                $db->quoteName('parent_id'),
                $db->quoteName('lft'),
                $db->quoteName('rgt'),
                $db->quoteName('title'),
            ];

            $values = [
                (int) $registered->id,
                $insertPosition,
                $insertPosition + 1,
                $db->quote($title),
            ];

            $insert = $db->getQuery(true)
                ->insert($db->quoteName('#__usergroups'))
                ->columns($columns)
                ->values(implode(',', $values));

            $db->setQuery($insert)->execute();
            $db->transactionCommit();
        } catch (\Throwable $exception) {
            $db->transactionRollback();

            Factory::getApplication()->enqueueMessage(
                'Die Joomla-Benutzergruppe "' . $title . '" konnte nicht automatisch angelegt werden: '
                . $exception->getMessage(),
                'warning'
            );
        }
    }
}
