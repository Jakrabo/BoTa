<?php

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\Database\DatabaseInterface;

$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->useScript('keepalive')->useScript('form.validate');

/** @var DatabaseInterface $db */
$db = Factory::getContainer()->get(DatabaseInterface::class);

$sportYearQuery = $db->getQuery(true)
    ->select($db->quoteName('date_end'))
    ->from($db->quoteName('#__jt_sportyears'))
    ->where($db->quoteName('published') . ' = 1')
    ->order($db->quoteName('is_current') . ' DESC, ' . $db->quoteName('date_end') . ' DESC');

$db->setQuery($sportYearQuery, 0, 1);
$activeSportYearEnd = (string) $db->loadResult();
$activeSportYear = $activeSportYearEnd !== ''
    ? (int) substr($activeSportYearEnd, 0, 4)
    : (int) date('Y');

$classQuery = $db->getQuery(true)
    ->select($db->quoteName(['id', 'min_age', 'max_age', 'gender', 'ordering']))
    ->from($db->quoteName('#__jt_classes'))
    ->where($db->quoteName('published') . ' = 1')
    ->order($db->quoteName('ordering') . ' ASC, ' . $db->quoteName('min_age') . ' DESC');

$db->setQuery($classQuery);
$classRules = $db->loadAssocList();
?>
<form
    action="<?php echo Route::_('index.php?option=com_jugendtraining&view=athlete&layout=edit&id=' . (int) $this->item->id); ?>"
    method="post"
    name="adminForm"
    id="adminForm"
    class="form-validate"
>
    <div class="card">
        <div class="card-body">
            <?php echo $this->form->renderFieldset('details'); ?>
        </div>
    </div>

    <input type="hidden" name="task" value="">
    <?php echo HTMLHelper::_('form.token'); ?>
</form>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const birthdateField = document.getElementById('jform_birthdate');
    const genderField = document.getElementById('jform_gender');
    const classField = document.getElementById('jform_class_id');

    if (!birthdateField || !classField) {
        return;
    }

    const sportYear = <?php echo (int) $activeSportYear; ?>;
    const classRules = <?php echo json_encode($classRules, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

    const determineClass = () => {
        if (!birthdateField.value) {
            return;
        }

        const birthYear = Number.parseInt(birthdateField.value.substring(0, 4), 10);

        if (!Number.isInteger(birthYear)) {
            return;
        }

        const age = sportYear - birthYear;
        const gender = genderField ? genderField.value : '';

        const match = classRules.find((rule) => {
            const minAge = Number.parseInt(rule.min_age, 10) || 0;
            const maxAgeRaw = rule.max_age;
            const maxAge = maxAgeRaw === null || maxAgeRaw === ''
                ? 0
                : Number.parseInt(maxAgeRaw, 10);
            const classGender = rule.gender || '';

            const ageMatches = age >= minAge && (maxAge === 0 || age <= maxAge);
            const genderMatches =
                !gender ||
                !classGender ||
                classGender === 'all' ||
                classGender === gender;

            return ageMatches && genderMatches;
        });

        if (match) {
            classField.value = String(match.id);
            classField.dispatchEvent(new Event('change', { bubbles: true }));
        }
    };

    birthdateField.addEventListener('change', determineClass);

    if (genderField) {
        genderField.addEventListener('change', determineClass);
    }

    if (!classField.value || classField.value === '0') {
        determineClass();
    }
});
</script>
