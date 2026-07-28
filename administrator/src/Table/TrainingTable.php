<?php

namespace Jugendtraining\Component\Jugendtraining\Administrator\Table;

\defined('_JEXEC') or die;

use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;

final class TrainingTable extends Table
{
    public function __construct(DatabaseDriver $db)
    {
        parent::__construct('#__jt_training_sessions', 'id', $db);
    }
}
