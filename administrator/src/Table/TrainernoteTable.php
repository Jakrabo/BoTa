<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\Table;
\defined('_JEXEC') or die;
use Joomla\CMS\Table\Table;use Joomla\Database\DatabaseDriver;
final class TrainernoteTable extends Table {
 public function __construct(DatabaseDriver $db){parent::__construct('#__jt_trainer_notes','id',$db);}
 public function check():bool{
  if(trim((string)$this->note)===''){ $this->setError('COM_JUGENDTRAINING_ERROR_NOTE_REQUIRED'); return false; }
  return parent::check();
 }
}
