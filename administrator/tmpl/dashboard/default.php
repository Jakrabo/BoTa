<?php

\defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$cards = [
    ['COM_JUGENDTRAINING_ACTIVE_ATHLETES', $this->stats['activeAthletes'], 'athletes'],
    ['COM_JUGENDTRAINING_UPCOMING_TRAININGS', $this->stats['upcomingTrainings'], 'trainings'],
    ['COM_JUGENDTRAINING_CLUBS', $this->stats['clubs'], 'clubs'],
    ['COM_JUGENDTRAINING_CLASSES', $this->stats['classes'], 'classes'],
];
?>
<div class="jt-dashboard">
    <div class="row g-3 mb-4">
        <?php foreach ($cards as [$label, $value, $view]) : ?>
            <div class="col-sm-6 col-xl-3">
                <a
                    class="card text-decoration-none h-100"
                    href="<?php echo Route::_('index.php?option=com_jugendtraining&view=' . $view); ?>"
                >
                    <div class="card-body">
                        <div class="text-muted"><?php echo Text::_($label); ?></div>
                        <div class="display-5"><?php echo (int) $value; ?></div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="row g-3">
        <div class="col-xl-7">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><?php echo Text::_('COM_JUGENDTRAINING_NEXT_TRAININGS'); ?></span>
                    <a href="<?php echo Route::_('index.php?option=com_jugendtraining&task=training.add'); ?>">
                        <?php echo Text::_('COM_JUGENDTRAINING_ADD_TRAINING'); ?>
                    </a>
                </div>

                <?php if ($this->upcomingTrainings) : ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($this->upcomingTrainings as $training) : ?>
                            <a
                                class="list-group-item list-group-item-action"
                                href="<?php echo Route::_('index.php?option=com_jugendtraining&task=training.edit&id=' . (int) $training->id); ?>"
                            >
                                <div class="d-flex justify-content-between gap-3">
                                    <div>
                                        <strong><?php echo htmlspecialchars((string) $training->title, ENT_QUOTES, 'UTF-8'); ?></strong>
                                        <div class="small text-muted">
                                            <?php echo htmlspecialchars((string) ($training->location ?: '–'), ENT_QUOTES, 'UTF-8'); ?>
                                            <?php if ($training->trainer_name) : ?>
                                                · <?php echo htmlspecialchars((string) $training->trainer_name, ENT_QUOTES, 'UTF-8'); ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="text-end text-nowrap">
                                        <?php echo HTMLHelper::_('date', $training->training_date, Text::_('DATE_FORMAT_LC4')); ?>
                                        <?php if ($training->start_time) : ?>
                                            <div class="small text-muted">
                                                <?php echo htmlspecialchars(substr((string) $training->start_time, 0, 5), ENT_QUOTES, 'UTF-8'); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <div class="card-body text-muted">
                        <?php echo Text::_('COM_JUGENDTRAINING_NO_UPCOMING_TRAININGS'); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-xl-5">
            <div class="card mb-3">
                <div class="card-header">
                    <?php echo Text::_('COM_JUGENDTRAINING_CURRENT_SPORTYEAR'); ?>
                </div>
                <div class="card-body">
                    <?php if ($this->currentSportyear) : ?>
                        <h3><?php echo htmlspecialchars((string) $this->currentSportyear->name, ENT_QUOTES, 'UTF-8'); ?></h3>
                        <p class="mb-0">
                            <?php echo HTMLHelper::_('date', $this->currentSportyear->date_start, Text::_('DATE_FORMAT_LC4')); ?>
                            –
                            <?php echo HTMLHelper::_('date', $this->currentSportyear->date_end, Text::_('DATE_FORMAT_LC4')); ?>
                        </p>
                    <?php else : ?>
                        <p class="text-warning mb-0">
                            <?php echo Text::_('COM_JUGENDTRAINING_NO_CURRENT_SPORTYEAR'); ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <span><?php echo Text::_('COM_JUGENDTRAINING_RECENT_ATHLETES'); ?></span>
                    <a href="<?php echo Route::_('index.php?option=com_jugendtraining&task=athlete.add'); ?>">
                        <?php echo Text::_('COM_JUGENDTRAINING_ADD_ATHLETE'); ?>
                    </a>
                </div>

                <ul class="list-group list-group-flush">
                    <?php foreach ($this->recentAthletes as $athlete) : ?>
                        <li class="list-group-item">
                            <a href="<?php echo Route::_('index.php?option=com_jugendtraining&task=athlete.edit&id=' . (int) $athlete->id); ?>">
                                <?php echo htmlspecialchars($athlete->firstname . ' ' . $athlete->lastname, ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                            <span class="text-muted float-end">
                                <?php echo htmlspecialchars((string) ($athlete->club_name ?: '–'), ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>
