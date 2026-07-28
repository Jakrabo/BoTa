<?php

\defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

HTMLHelper::_('bootstrap.tab');

$token = Session::getFormToken();
$activeTab = $this->result ? 'csv-import' : 'csv-import';
?>
<div class="container-fluid">
    <ul class="nav nav-tabs" id="jugendtraining-config-tabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button
                class="nav-link active"
                id="csv-import-tab"
                data-bs-toggle="tab"
                data-bs-target="#csv-import"
                type="button"
                role="tab"
                aria-controls="csv-import"
                aria-selected="true"
            >
                <?php echo Text::_('COM_JUGENDTRAINING_CONFIG_TAB_IMPORT'); ?>
            </button>
        </li>

        <li class="nav-item" role="presentation">
            <button
                class="nav-link"
                id="diary-options-tab"
                data-bs-toggle="tab"
                data-bs-target="#diary-options"
                type="button"
                role="tab"
                aria-controls="diary-options"
                aria-selected="false"
            >
                <?php echo Text::_('COM_JUGENDTRAINING_CONFIG_TAB_DIARY'); ?>
            </button>
        </li>

        <li class="nav-item" role="presentation">
            <button
                class="nav-link"
                id="penalties-tab"
                data-bs-toggle="tab"
                data-bs-target="#penalties"
                type="button"
                role="tab"
                aria-controls="penalties"
                aria-selected="false"
            >
                <?php echo Text::_('COM_JUGENDTRAINING_CONFIG_TAB_PENALTIES'); ?>
            </button>
        </li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#dashboard-athlete" type="button"><?php echo Text::_('COM_JUGENDTRAINING_CONFIG_TAB_ATHLETE_DASHBOARD'); ?></button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#dashboard-trainer" type="button"><?php echo Text::_('COM_JUGENDTRAINING_CONFIG_TAB_TRAINER_DASHBOARD'); ?></button></li>
<li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#calendar-config" type="button"><?php echo Text::_('COM_JUGENDTRAINING_CONFIG_TAB_CALENDAR'); ?></button></li>
<li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#language" type="button"><?php echo Text::_('COM_JUGENDTRAINING_CONFIG_TAB_LANGUAGE'); ?></button></li>
    </ul>

    <div class="tab-content pt-4" id="jugendtraining-config-tabs-content">
        <div
            class="tab-pane fade show active"
            id="csv-import"
            role="tabpanel"
            aria-labelledby="csv-import-tab"
            tabindex="0"
        >
            <div class="alert alert-info">
                <strong><?php echo Text::_('COM_JUGENDTRAINING_CONFIG_TAB_IMPORT'); ?>:</strong>
                <?php echo Text::_('COM_JUGENDTRAINING_CONFIG_IMPORT_INTRO'); ?>
            </div>

            <div class="row g-4">
                <?php
                $importTypes = [
                    'results' => Text::_('COM_JUGENDTRAINING_CONFIG_RESULTS'),
                    'diary' => Text::_('COM_JUGENDTRAINING_CONFIG_DIARY_ENTRIES'),
                    'achievements' => Text::_('COM_JUGENDTRAINING_CONFIG_ACHIEVEMENTS'),
                ];
                ?>

                <?php foreach ($importTypes as $type => $title) : ?>
                    <div class="col-lg-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h2 class="h4"><?php echo $title; ?></h2>

                                <p>
                                    <a
                                        class="btn btn-outline-secondary"
                                        href="<?php echo Route::_(
                                            'index.php?option=com_jugendtraining'
                                            . '&task=import.template'
                                            . '&type=' . $type
                                            . '&' . $token . '=1'
                                        ); ?>"
                                    >
                                        <?php echo Text::_('COM_JUGENDTRAINING_CONFIG_TEMPLATE_DOWNLOAD'); ?>
                                    </a>
                                </p>

                                <form
                                    action="<?php echo Route::_(
                                        'index.php?option=com_jugendtraining&task=import.upload'
                                    ); ?>"
                                    method="post"
                                    enctype="multipart/form-data"
                                >
                                    <input
                                        type="hidden"
                                        name="import_type"
                                        value="<?php echo htmlspecialchars($type, ENT_QUOTES, 'UTF-8'); ?>"
                                    >

                                    <input
                                        class="form-control mb-3"
                                        type="file"
                                        name="csv_file"
                                        accept=".csv,text/csv"
                                        required
                                    >

                                    <button class="btn btn-primary" type="submit">
                                        <?php echo Text::_('COM_JUGENDTRAINING_CONFIG_IMPORT_BUTTON'); ?>
                                    </button>

                                    <?php echo HTMLHelper::_('form.token'); ?>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($this->result) : ?>
                <div class="card mt-4">
                    <div class="card-body">
                        <h2 class="h4">
                            <?php echo Text::_('COM_JUGENDTRAINING_CONFIG_IMPORT_RESULT'); ?>
                        </h2>

                        <p>
                            <strong><?php echo (int) $this->result['success']; ?></strong>
                            erfolgreich,
                            <strong><?php echo (int) $this->result['failed']; ?></strong>
                            fehlerhaft.
                        </p>

                        <?php if (!empty($this->result['errors'])) : ?>
                            <div class="alert alert-warning">
                                <ul class="mb-0">
                                    <?php foreach (array_slice($this->result['errors'], 0, 100) as $error) : ?>
                                        <li>
                                            <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div
            class="tab-pane fade"
            id="diary-options"
            role="tabpanel"
            aria-labelledby="diary-options-tab"
            tabindex="0"
        >
            <div class="alert alert-info">
                <?php echo Text::_('COM_JUGENDTRAINING_CONFIG_DIARY_INTRO'); ?>
            </div>

            <form
                action="<?php echo Route::_(
                    'index.php?option=com_jugendtraining&task=import.saveOptions'
                ); ?>"
                method="post"
            >
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <label class="form-label h4" for="methods">
                                    <?php echo Text::_('COM_JUGENDTRAINING_CONFIG_METHODS'); ?>
                                </label>

                                <textarea
                                    class="form-control"
                                    id="methods"
                                    name="jform[methods]"
                                    rows="14"
                                ><?php echo htmlspecialchars(
                                    implode("\n", $this->options['methods']),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <label class="form-label h4" for="focus">
                                    <?php echo Text::_('COM_JUGENDTRAINING_CONFIG_FOCUS'); ?>
                                </label>

                                <textarea
                                    class="form-control"
                                    id="focus"
                                    name="jform[focus]"
                                    rows="14"
                                ><?php echo htmlspecialchars(
                                    implode("\n", $this->options['focus']),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <button class="btn btn-success mt-3" type="submit">
                    <?php echo Text::_('COM_JUGENDTRAINING_CONFIG_SAVE'); ?>
                </button>

                <?php echo HTMLHelper::_('form.token'); ?>
            </form>
        </div>
<div
            class="tab-pane fade"
            id="penalties"
            role="tabpanel"
            aria-labelledby="penalties-tab"
            tabindex="0"
        >
            <div class="alert alert-info">
                <?php echo Text::_('COM_JUGENDTRAINING_CONFIG_PENALTIES_INTRO'); ?>
            </div>


<div class="card mb-4 border-success">
    <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h2 class="h4 mb-1"><?php echo Text::_('COM_JUGENDTRAINING_PENALTY_BALANCE'); ?></h2>
            <strong class="display-6"><?php echo number_format($this->penaltyBalance, 2, ',', '.'); ?> €</strong>
            <p class="small text-muted mb-0"><?php echo Text::_('COM_JUGENDTRAINING_PENALTY_BALANCE_COMPLETED'); ?></p>
        </div>
        <form action="<?php echo Route::_('index.php?option=com_jugendtraining&task=import.resetPenaltyBalance'); ?>" method="post" onsubmit="return confirm('<?php echo Text::_('COM_JUGENDTRAINING_RESET_BALANCE_CONFIRM'); ?>');">
            <button class="btn btn-outline-danger" type="submit"><?php echo Text::_('COM_JUGENDTRAINING_RESET_BALANCE'); ?></button>
            <?php echo HTMLHelper::_('form.token'); ?>
        </form>
    </div>
</div>

            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="h4"><?php echo Text::_('COM_JUGENDTRAINING_CONFIG_PENALTY_NEW'); ?></h2>

                    <form action="<?php echo Route::_('index.php?option=com_jugendtraining&task=import.savePenalty'); ?>" method="post">
                        <input type="hidden" name="jform[id]" value="0">

                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label" for="penalty-title"><?php echo Text::_('COM_JUGENDTRAINING_PENALTY_TITLE'); ?></label>
                                <input class="form-control" id="penalty-title" name="jform[title]" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label" for="penalty-type"><?php echo Text::_('COM_JUGENDTRAINING_PENALTY_TYPE'); ?></label>
                                <select class="form-select" id="penalty-type" name="jform[penalty_type]">
                                    <option value="monetary"><?php echo Text::_('COM_JUGENDTRAINING_PENALTY_MONETARY'); ?></option>
                                    <option value="non_monetary"><?php echo Text::_('COM_JUGENDTRAINING_PENALTY_NON_MONETARY'); ?></option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label" for="penalty-amount"><?php echo Text::_('COM_JUGENDTRAINING_PENALTY_AMOUNT'); ?></label>
                                <div class="input-group">
                                    <input class="form-control" id="penalty-amount" name="jform[amount]" inputmode="decimal">
                                    <span class="input-group-text">€</span>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label" for="penalty-ordering"><?php echo Text::_('JGLOBAL_FIELD_ORDERING_LABEL'); ?></label>
                                <input class="form-control" type="number" id="penalty-ordering" name="jform[ordering]" value="0">
                            </div>

                            <div class="col-12">
                                <label class="form-label" for="penalty-action"><?php echo Text::_('COM_JUGENDTRAINING_PENALTY_ACTION'); ?></label>
                                <input class="form-control" id="penalty-action" name="jform[non_monetary_action]" placeholder="z. B. 15 Liegestütze">
                            </div>

                            <div class="col-12">
                                <label class="form-label" for="penalty-description"><?php echo Text::_('JGLOBAL_DESCRIPTION'); ?></label>
                                <textarea class="form-control" id="penalty-description" name="jform[description]" rows="2"></textarea>
                            </div>
                        </div>

                        <input type="hidden" name="jform[published]" value="1">
                        <button class="btn btn-success mt-3" type="submit">
                            <?php echo Text::_('COM_JUGENDTRAINING_CONFIG_PENALTY_SAVE'); ?>
                        </button>
                        <?php echo HTMLHelper::_('form.token'); ?>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h2 class="h4"><?php echo Text::_('COM_JUGENDTRAINING_CONFIG_PENALTIES_EXISTING'); ?></h2>

                    <?php if ($this->penalties) : ?>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th><?php echo Text::_('COM_JUGENDTRAINING_PENALTY_TITLE'); ?></th>
                                        <th><?php echo Text::_('COM_JUGENDTRAINING_PENALTY_TYPE'); ?></th>
                                        <th><?php echo Text::_('COM_JUGENDTRAINING_PENALTY_VALUE'); ?></th>
                                        <th><?php echo Text::_('JPUBLISHED'); ?></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($this->penalties as $penalty) : ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($penalty->title, ENT_QUOTES, 'UTF-8'); ?></strong>
                                            <?php if ($penalty->description) : ?>
                                                <div class="small text-muted"><?php echo htmlspecialchars($penalty->description, ENT_QUOTES, 'UTF-8'); ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php echo Text::_(
                                                $penalty->penalty_type === 'monetary'
                                                    ? 'COM_JUGENDTRAINING_PENALTY_MONETARY'
                                                    : 'COM_JUGENDTRAINING_PENALTY_NON_MONETARY'
                                            ); ?>
                                        </td>
                                        <td>
                                            <?php if ($penalty->penalty_type === 'monetary') : ?>
                                                <?php echo number_format((float) $penalty->amount, 2, ',', '.'); ?> €
                                            <?php else : ?>
                                                <?php echo htmlspecialchars($penalty->non_monetary_action, ENT_QUOTES, 'UTF-8'); ?>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo (int) $penalty->published ? Text::_('JYES') : Text::_('JNO'); ?></td>
                                        <td class="text-end">
                                            <a
                                                class="btn btn-sm btn-outline-danger"
                                                href="<?php echo Route::_(
                                                    'index.php?option=com_jugendtraining'
                                                    . '&task=import.deletePenalty'
                                                    . '&id=' . (int) $penalty->id
                                                    . '&' . $token . '=1'
                                                ); ?>"
                                                onclick="return confirm('<?php echo Text::_('COM_JUGENDTRAINING_PENALTY_DELETE_CONFIRM'); ?>');"
                                            >
                                                <?php echo Text::_('JACTION_DELETE'); ?>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else : ?>
                        <p class="text-muted mb-0"><?php echo Text::_('COM_JUGENDTRAINING_NO_PENALTIES_CONFIGURED'); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
<?php
$dashboardLabels=[
 'profile'=>'COM_JUGENDTRAINING_DASHBOARD_PROFILE','results'=>'COM_JUGENDTRAINING_DASHBOARD_RESULTS',
 'penalties'=>'COM_JUGENDTRAINING_DASHBOARD_PENALTIES','achievements'=>'COM_JUGENDTRAINING_DASHBOARD_ACHIEVEMENTS',
 'programs'=>'COM_JUGENDTRAINING_DASHBOARD_PROGRAMS','overview'=>'COM_JUGENDTRAINING_DASHBOARD_OVERVIEW',
 'performance'=>'COM_JUGENDTRAINING_DASHBOARD_PERFORMANCE','groups'=>'COM_JUGENDTRAINING_DASHBOARD_GROUPS',
 'penalty_summary'=>'COM_JUGENDTRAINING_DASHBOARD_PENALTY_SUMMARY','open_penalties'=>'COM_JUGENDTRAINING_DASHBOARD_OPEN_PENALTIES',
 'signals'=>'COM_JUGENDTRAINING_DASHBOARD_SIGNALS','class_changes'=>'COM_JUGENDTRAINING_DASHBOARD_CLASS_CHANGES',
 'navigation'=>'COM_JUGENDTRAINING_DASHBOARD_NAVIGATION'
];
?>
<?php foreach(['athlete','trainer'] as $dashboardType): ?>
<div class="tab-pane fade" id="dashboard-<?php echo $dashboardType; ?>" role="tabpanel">
 <div class="alert alert-info"><?php echo Text::_('COM_JUGENDTRAINING_DASHBOARD_CONFIG_INTRO'); ?></div>
 <form action="<?php echo Route::_('index.php?option=com_jugendtraining&task=import.saveDashboardConfig'); ?>" method="post">
  <input type="hidden" name="dashboard_type" value="<?php echo $dashboardType; ?>">
  <div class="table-responsive"><table class="table align-middle"><thead><tr>
   <th style="width:110px"><?php echo Text::_('JGRID_HEADING_ORDERING'); ?></th>
   <th><?php echo Text::_('COM_JUGENDTRAINING_DASHBOARD_ELEMENT'); ?></th>
   <th style="width:140px"><?php echo Text::_('COM_JUGENDTRAINING_VISIBLE'); ?></th>
  </tr></thead><tbody>
  <?php foreach(($this->dashboardConfigs[$dashboardType]??[]) as $index=>$row): ?>
   <tr>
    <td><input class="form-control" type="number" min="1" name="dashboard[<?php echo htmlspecialchars($row['key'],ENT_QUOTES,'UTF-8'); ?>][ordering]" value="<?php echo $index+1; ?>"></td>
    <td><strong><?php echo Text::_($dashboardLabels[$row['key']]??$row['key']); ?></strong></td>
    <td>
     <div class="form-check form-switch">
      <input type="hidden" name="dashboard[<?php echo htmlspecialchars($row['key'],ENT_QUOTES,'UTF-8'); ?>][visible]" value="0">
      <input class="form-check-input" type="checkbox" name="dashboard[<?php echo htmlspecialchars($row['key'],ENT_QUOTES,'UTF-8'); ?>][visible]" value="1" <?php echo !empty($row['visible'])?'checked':''; ?>>
     </div>
    </td>
   </tr>
  <?php endforeach; ?>
  </tbody></table></div>
  <button class="btn btn-success" type="submit"><?php echo Text::_('COM_JUGENDTRAINING_SAVE_DASHBOARD_CONFIG'); ?></button>
  <?php echo HTMLHelper::_('form.token'); ?>
 </form>
</div>
<?php endforeach; ?>
<div class="tab-pane fade" id="calendar-config" role="tabpanel">
 <div class="alert alert-info"><?php echo Text::_('COM_JUGENDTRAINING_CALENDAR_CONFIG_INTRO');?></div>
 <form action="<?php echo Route::_('index.php?option=com_jugendtraining&task=import.saveCalendarCategories');?>" method="post">
  <div class="table-responsive"><table class="table align-middle" id="jt-calendar-category-table"><thead><tr><th><?php echo Text::_('COM_JUGENDTRAINING_CATEGORY');?></th><th style="width:150px"><?php echo Text::_('COM_JUGENDTRAINING_COLOR');?></th><th style="width:120px"><?php echo Text::_('COM_JUGENDTRAINING_ACTIVE');?></th><th style="width:80px"></th></tr></thead><tbody>
  <?php foreach($this->calendarCategories as$i=>$c):?><tr><td><input class="form-control" name="jform[categories][<?php echo$i;?>][name]" value="<?php echo htmlspecialchars($c['name'],ENT_QUOTES,'UTF-8');?>" maxlength="100"></td><td><input class="form-control form-control-color" type="color" name="jform[categories][<?php echo$i;?>][color]" value="<?php echo htmlspecialchars($c['color'],ENT_QUOTES,'UTF-8');?>"></td><td><input type="hidden" name="jform[categories][<?php echo$i;?>][active]" value="0"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="jform[categories][<?php echo$i;?>][active]" value="1" <?php echo!empty($c['active'])?'checked':'';?>></div></td><td><button class="btn btn-sm btn-outline-danger jt-remove-category" type="button">×</button></td></tr><?php endforeach;?>
  </tbody></table></div>
  <button class="btn btn-outline-primary mb-3" id="jt-add-category" type="button"><?php echo Text::_('COM_JUGENDTRAINING_ADD_CATEGORY');?></button>
  <div><button class="btn btn-success" type="submit"><?php echo Text::_('JSAVE');?></button></div><?php echo HTMLHelper::_('form.token');?>
 </form>
</div>
<div class="tab-pane fade" id="language" role="tabpanel">
 <div class="alert alert-info"><?php echo Text::_('COM_JUGENDTRAINING_LANGUAGE_INTRO'); ?></div>
 <ul class="nav nav-pills mb-3" role="tablist">
 <?php foreach(($this->languageOverview['languages']??[]) as$i=>$lang):?><li class="nav-item"><button class="nav-link <?php echo$i===0?'active':'';?>" data-bs-toggle="tab" data-bs-target="#language-<?php echo htmlspecialchars($lang,ENT_QUOTES,'UTF-8');?>" type="button"><?php echo htmlspecialchars($lang,ENT_QUOTES,'UTF-8');?></button></li><?php endforeach;?>
 </ul>
 <div class="tab-content">
 <?php foreach(($this->languageOverview['languages']??[]) as$i=>$lang):?>
  <div class="tab-pane fade <?php echo$i===0?'show active':'';?>" id="language-<?php echo htmlspecialchars($lang,ENT_QUOTES,'UTF-8');?>">
   <form action="<?php echo Route::_('index.php?option=com_jugendtraining&task=import.saveLanguage');?>" method="post">
    <input type="hidden" name="language" value="<?php echo htmlspecialchars($lang,ENT_QUOTES,'UTF-8');?>">
    <div class="mb-3"><input class="form-control jt-language-search" type="search" placeholder="<?php echo Text::_('COM_JUGENDTRAINING_SEARCH_PLACEHOLDER');?>"></div>
    <div class="table-responsive" style="max-height:65vh;overflow:auto"><table class="table table-sm align-middle jt-language-table"><thead class="sticky-top bg-white"><tr><th style="width:38%"><?php echo Text::_('COM_JUGENDTRAINING_LANGUAGE_KEY');?></th><th><?php echo Text::_('COM_JUGENDTRAINING_TRANSLATION');?></th></tr></thead><tbody>
    <?php foreach(($this->languageOverview['keys']??[]) as$key):?><tr><td><code><?php echo htmlspecialchars($key,ENT_QUOTES,'UTF-8');?></code></td><td><input class="form-control" name="translations[<?php echo htmlspecialchars($key,ENT_QUOTES,'UTF-8');?>]" value="<?php echo htmlspecialchars((string)($this->languageOverview['values'][$lang][$key]??''),ENT_QUOTES,'UTF-8');?>"></td></tr><?php endforeach;?>
    </tbody></table></div>
    <button class="btn btn-success mt-3" type="submit"><?php echo Text::_('COM_JUGENDTRAINING_SAVE_TRANSLATIONS');?></button><?php echo HTMLHelper::_('form.token');?>
   </form>
  </div>
 <?php endforeach;?>
 </div>
</div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.jt-language-search').forEach(function(input){
        input.addEventListener('input',function(){
            const term=this.value.toLowerCase();
            const table=this.closest('form').querySelector('.jt-language-table');
            table.querySelectorAll('tbody tr').forEach(function(row){row.hidden=!row.textContent.toLowerCase().includes(term);});
        });
    });
    const storageKey = 'com_jugendtraining.configuration.activeTab';
    const tabButtons = document.querySelectorAll('#jugendtraining-config-tabs button[data-bs-toggle="tab"]');

    tabButtons.forEach(function (button) {
        button.addEventListener('shown.bs.tab', function (event) {
            window.sessionStorage.setItem(storageKey, event.target.getAttribute('data-bs-target'));
        });
    });

    const savedTarget = window.sessionStorage.getItem(storageKey);

    if (savedTarget) {
        const savedButton = document.querySelector(
            '#jugendtraining-config-tabs button[data-bs-target="' + savedTarget + '"]'
        );

        if (savedButton && window.bootstrap && window.bootstrap.Tab) {
            window.bootstrap.Tab.getOrCreateInstance(savedButton).show();
        }
    }
});

document.getElementById('jt-add-category')?.addEventListener('click',function(){
 const tbody=document.querySelector('#jt-calendar-category-table tbody');const i=Date.now();
 const tr=document.createElement('tr');tr.innerHTML='<td><input class="form-control" name="jform[categories]['+i+'][name]" maxlength="100"></td><td><input class="form-control form-control-color" type="color" name="jform[categories]['+i+'][color]" value="#6c757d"></td><td><input type="hidden" name="jform[categories]['+i+'][active]" value="0"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="jform[categories]['+i+'][active]" value="1" checked></div></td><td><button class="btn btn-sm btn-outline-danger jt-remove-category" type="button">×</button></td>';tbody.appendChild(tr);
});
document.addEventListener('click',function(e){if(e.target.classList.contains('jt-remove-category'))e.target.closest('tr')?.remove();});
</script>
