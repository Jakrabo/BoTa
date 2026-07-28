<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\Table;
\defined('_JEXEC') or die;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
final class ProgramTable extends Table {
  public function __construct(DatabaseDriver $db) { parent::__construct('#__jt_training_programs', 'id', $db); }
  public function check(): bool {
    if (trim((string) $this->title) === '') { $this->setError('COM_JUGENDTRAINING_ERROR_TITLE_REQUIRED'); return false; }
    return parent::check();
  }
}
