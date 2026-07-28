<?php

namespace Jugendtraining\Component\Jugendtraining\Administrator\Table;

\defined('_JEXEC') or die;

use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;

final class ResultTable extends Table
{
    public function __construct(DatabaseDriver $db)
    {
        parent::__construct('#__jt_results', 'id', $db);
    }

    public function check(): bool
    {
        if ((int) $this->arrows <= 0) {
            $this->setError('COM_JUGENDTRAINING_ERROR_ARROWS_REQUIRED');
            return false;
        }

        if ((int) $this->score < 0) {
            $this->setError('COM_JUGENDTRAINING_ERROR_SCORE_INVALID');
            return false;
        }

        return parent::check();
    }
}
