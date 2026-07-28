<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\Model;
\defined('_JEXEC') or die;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\QueryInterface;
final class TraininggroupsModel extends ListModel {
 protected function getListQuery():QueryInterface{
  $db=$this->getDatabase();
  return $db->getQuery(true)->select(['g.*','COUNT(DISTINCT ga.athlete_id) athlete_count','COUNT(DISTINCT gt.user_id) trainer_count'])
   ->from($db->quoteName('#__jt_training_groups','g'))
   ->leftJoin($db->quoteName('#__jt_training_group_athletes','ga').' ON ga.group_id=g.id')
   ->leftJoin($db->quoteName('#__jt_training_group_trainers','gt').' ON gt.group_id=g.id')
   ->group('g.id')->order('g.title ASC');
 }
}
