<?php
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

Factory::getApplication()->getDocument()->getWebAssetManager()->useStyle('com_jugendtraining.site');
$selectedAthleteId=Factory::getApplication()->input->getInt('athlete_id');
?>
<div class="jt-penalty-register">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="mb-1"><?php echo Text::_('COM_JUGENDTRAINING_PENALTY_REGISTER'); ?></h1>
            <p class="text-muted mb-0"><?php echo Text::_('COM_JUGENDTRAINING_PENALTY_REGISTER_INTRO'); ?></p>
        </div>
        <a class="btn btn-outline-secondary" href="<?php echo Route::_('index.php?option=com_jugendtraining&view=trainerdashboard'); ?>">
            <?php echo Text::_('COM_JUGENDTRAINING_BACK_TO_DASHBOARD'); ?>
        </a>
    </div>

    <div class="card mb-4" id="penalty-editor">
        <div class="card-body">
            <h2 class="h4"><?php echo Text::_('COM_JUGENDTRAINING_ASSIGN_PENALTY'); ?></h2>

            <?php if (!$this->definitions) : ?>
                <div class="alert alert-warning"><?php echo Text::_('COM_JUGENDTRAINING_NO_ACTIVE_PENALTIES'); ?></div>
            <?php else : ?>
                <form action="<?php echo Route::_('index.php?option=com_jugendtraining&task=trainerpenalties.assign'); ?>" method="post">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label" for="penalty-athlete"><?php echo Text::_('COM_JUGENDTRAINING_FIELD_ATHLETE'); ?></label>
                            <select class="form-select" id="penalty-athlete" name="jform[athlete_id]" required>
                                <option value=""><?php echo Text::_('COM_JUGENDTRAINING_SELECT_OPTION'); ?></option>
                                <?php foreach ($this->athletes as $athlete) : ?>
                                    <option value="<?php echo (int)$athlete->id; ?>" <?php echo $selectedAthleteId===(int)$athlete->id?'selected':''; ?>>
                                        <?php echo htmlspecialchars($athlete->athlete_name,ENT_QUOTES,'UTF-8'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label" for="penalty-definition"><?php echo Text::_('COM_JUGENDTRAINING_PENALTY'); ?></label>
                            <select class="form-select" id="penalty-definition" name="jform[penalty_definition_id]" required>
                                <option value=""><?php echo Text::_('COM_JUGENDTRAINING_SELECT_OPTION'); ?></option>
                                <?php foreach ($this->definitions as $definition) : ?>
                                    <option value="<?php echo (int)$definition->id; ?>">
                                        <?php
                                        $value=$definition->penalty_type==='monetary'
                                            ? number_format((float)$definition->amount,2,',','.').' €'
                                            : $definition->non_monetary_action;
                                        echo htmlspecialchars($definition->title.' — '.$value,ENT_QUOTES,'UTF-8');
                                        ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label" for="penalty-note"><?php echo Text::_('COM_JUGENDTRAINING_REASON_NOTE'); ?></label>
                            <input class="form-control" id="penalty-note" name="jform[reason_note]" maxlength="500">
                        </div>
                    </div>

                    <button class="btn btn-primary mt-3" type="submit">
                        <?php echo Text::_('COM_JUGENDTRAINING_ASSIGN_PENALTY'); ?>
                    </button>
                    <?php echo HTMLHelper::_('form.token'); ?>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h2 class="h4"><?php echo Text::_('COM_JUGENDTRAINING_PENALTY_ENTRIES'); ?></h2>

            <?php if (!$this->entries) : ?>
                <p class="text-muted mb-0"><?php echo Text::_('COM_JUGENDTRAINING_NO_PENALTY_ENTRIES'); ?></p>
            <?php else : ?>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th><?php echo Text::_('JDATE'); ?></th>
                                <th><?php echo Text::_('COM_JUGENDTRAINING_FIELD_ATHLETE'); ?></th>
                                <th><?php echo Text::_('COM_JUGENDTRAINING_PENALTY'); ?></th>
                                <th><?php echo Text::_('COM_JUGENDTRAINING_PENALTY_VALUE'); ?></th>
                                <th><?php echo Text::_('JSTATUS'); ?></th>
                                <th><?php echo Text::_('COM_JUGENDTRAINING_REASON_NOTE'); ?></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($this->entries as $entry) : ?>
                            <tr class="<?php echo $entry->status==='open'?'table-warning':''; ?>">
                                <td><?php echo HTMLHelper::_('date',$entry->assigned_at,Text::_('DATE_FORMAT_LC4')); ?></td>
                                <td><?php echo htmlspecialchars($entry->athlete_name,ENT_QUOTES,'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($entry->title,ENT_QUOTES,'UTF-8'); ?></td>
                                <td>
                                    <?php if ($entry->penalty_type==='monetary') : ?>
                                        <?php echo number_format((float)$entry->amount_snapshot,2,',','.'); ?> €
                                    <?php else : ?>
                                        <?php echo htmlspecialchars((string)$entry->action_snapshot,ENT_QUOTES,'UTF-8'); ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge <?php echo $entry->status==='open'?'text-bg-warning':'text-bg-success'; ?>">
                                        <?php echo Text::_($entry->status==='open'?'COM_JUGENDTRAINING_STATUS_OPEN':'COM_JUGENDTRAINING_STATUS_COMPLETED'); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars((string)$entry->reason_note,ENT_QUOTES,'UTF-8'); ?></td>
                                <td class="text-end">
                                    <?php if ($entry->status==='open') : ?>
                                        <form class="d-flex gap-2 justify-content-end" action="<?php echo Route::_('index.php?option=com_jugendtraining&task=trainerpenalties.complete'); ?>" method="post">
                                            <input type="hidden" name="id" value="<?php echo (int)$entry->id; ?>">
                                            <input class="form-control form-control-sm" name="completion_note" placeholder="<?php echo Text::_('COM_JUGENDTRAINING_COMPLETION_NOTE'); ?>">
                                            <button class="btn btn-sm btn-success" type="submit"><?php echo Text::_('COM_JUGENDTRAINING_MARK_COMPLETED'); ?></button>
                                            <?php echo HTMLHelper::_('form.token'); ?>
                                        </form>
                                    <?php else : ?>
                                        <a class="btn btn-sm btn-outline-secondary" href="<?php echo Route::_('index.php?option=com_jugendtraining&task=trainerpenalties.reopen&id='.(int)$entry->id.'&'.Session::getFormToken().'=1'); ?>">
                                            <?php echo Text::_('COM_JUGENDTRAINING_REOPEN'); ?>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
