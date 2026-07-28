<?php
namespace Jugendtraining\Component\Jugendtraining\Administrator\Model;
\defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;
final class AthleteModel extends AdminModel
{
 public function getTable($name='Athlete', $prefix='Administrator', $options=[]) { return parent::getTable($name,$prefix,$options); }
 public function getForm($data=[], $loadData=true) { return $this->loadForm('com_jugendtraining.athlete','athlete', ['control'=>'jform','load_data'=>$loadData]); }
 protected function loadFormData() { $data=Factory::getApplication()->getUserState('com_jugendtraining.edit.athlete.data',[]); return $data ?: $this->getItem(); }
 
 protected function prepareTable($table): void
 {
  $date=Factory::getDate()->toSql(); $user=Factory::getApplication()->getIdentity();
  if (empty($table->id)) { if (property_exists($table,'created')) $table->created=$date; if (property_exists($table,'created_by')) $table->created_by=(int)$user->id; }
  else { if (property_exists($table,'modified')) $table->modified=$date; if (property_exists($table,'modified_by')) $table->modified_by=(int)$user->id; }
 }


    public function save($data): bool
    {
        $classId = (int) ($data['class_id'] ?? 0);

        if ($classId === 0 && !empty($data['birthdate'])) {
            $calculatedClassId = $this->findMatchingClassId(
                (string) $data['birthdate'],
                (string) ($data['gender'] ?? '')
            );

            if ($calculatedClassId > 0) {
                $data['class_id'] = $calculatedClassId;
            }
        }

        return parent::save($data);
    }

    private function findMatchingClassId(string $birthdate, string $gender = ''): int
    {
        try {
            $birth = new \DateTimeImmutable($birthdate);
        } catch (\Throwable) {
            return 0;
        }

        $db = $this->getDatabase();

        $sportYearQuery = $db->getQuery(true)
            ->select($db->quoteName('date_end'))
            ->from($db->quoteName('#__jt_sportyears'))
            ->where($db->quoteName('published') . ' = 1')
            ->order($db->quoteName('is_current') . ' DESC, ' . $db->quoteName('date_end') . ' DESC');

        $db->setQuery($sportYearQuery, 0, 1);
        $sportYearEnd = (string) $db->loadResult();
        $sportYear = $sportYearEnd !== ''
            ? (int) substr($sportYearEnd, 0, 4)
            : (int) date('Y');

        $age = $sportYear - (int) $birth->format('Y');

        $query = $db->getQuery(true)
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__jt_classes'))
            ->where($db->quoteName('published') . ' = 1')
            ->where('(' . $db->quoteName('min_age') . ' IS NULL OR ' . $db->quoteName('min_age') . ' <= ' . $age . ')')
            ->where('(' . $db->quoteName('max_age') . ' IS NULL OR ' . $db->quoteName('max_age') . ' = 0 OR ' . $db->quoteName('max_age') . ' >= ' . $age . ')');

        if ($gender !== '') {
            $query->where(
                '('
                . $db->quoteName('gender') . ' = ' . $db->quote('')
                . ' OR ' . $db->quoteName('gender') . ' = ' . $db->quote($gender)
                . ')'
            );
        }

        $query->order($db->quoteName('ordering') . ' ASC, ' . $db->quoteName('min_age') . ' DESC');

        $db->setQuery($query, 0, 1);

        return (int) $db->loadResult();
    }

}
