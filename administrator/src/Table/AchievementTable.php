<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\Table;
\defined('_JEXEC') or die;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
final class AchievementTable extends Table{
 public function __construct(DatabaseDriver $db){parent::__construct('#__jt_achievements','id',$db);}
 public function check():bool{
  $this->title=trim((string)$this->title);$this->code=trim((string)$this->code);$this->category=trim((string)$this->category);
  if($this->title===''){$this->setError('COM_JUGENDTRAINING_ERROR_TITLE_REQUIRED');return false;}
  if($this->code===''||!preg_match('/^[a-z0-9_-]+$/',$this->code)){$this->setError('COM_JUGENDTRAINING_ERROR_ACHIEVEMENT_CODE');return false;}
  if($this->award_mode==='automatic'&&empty($this->rule_type)){$this->setError('COM_JUGENDTRAINING_ERROR_RULE_REQUIRED');return false;}
  if($this->rule_type==='event_name_contains')$this->requires_verified_result=1;
  return parent::check();
 }
}
