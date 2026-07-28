<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\Table;
\defined('_JEXEC') or die;
use Joomla\CMS\Table\Table;use Joomla\Database\DatabaseDriver;
final class GoalTable extends Table {
 public function __construct(DatabaseDriver $db){parent::__construct('#__jt_goals','id',$db);}
 public function check():bool{
  $this->target_value = ($this->target_value === '' || $this->target_value === null) ? 0 : (float) $this->target_value;
  $this->current_value = ($this->current_value === '' || $this->current_value === null) ? 0 : (float) $this->current_value;
  $this->distance_m = ($this->distance_m === '' || $this->distance_m === null) ? 0 : (int) $this->distance_m;
  $this->arrows = ($this->arrows === '' || $this->arrows === null) ? 0 : (int) $this->arrows;
  if(trim((string)$this->title)===''){ $this->setError('COM_JUGENDTRAINING_ERROR_TITLE_REQUIRED'); return false; }
  return parent::check();
 }
}
