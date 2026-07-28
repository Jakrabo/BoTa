<?php
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->useScript('keepalive')
    ->useScript('form.validate')
    ->useScript('showon')
    ->useStyle('com_jugendtraining.site');

$statusOptions = [
    '' => [
        'label' => Text::_('COM_JUGENDTRAINING_ATTENDANCE_NOT_RECORDED'),
        'icon' => '○',
    ],
    'present' => [
        'label' => Text::_('COM_JUGENDTRAINING_ATTENDANCE_PRESENT'),
        'icon' => '✓',
    ],
    'late' => [
        'label' => Text::_('COM_JUGENDTRAINING_ATTENDANCE_LATE'),
        'icon' => '◷',
    ],
    'excused' => [
        'label' => Text::_('COM_JUGENDTRAINING_ATTENDANCE_EXCUSED'),
        'icon' => 'i',
    ],
    'absent' => [
        'label' => Text::_('COM_JUGENDTRAINING_ATTENDANCE_ABSENT'),
        'icon' => '×',
    ],
];

$tokenName = Factory::getApplication()->getSession()->getFormToken();
$ajaxUrl = Route::_(
    'index.php?option=com_jugendtraining&task=trainertraining.saveAttendance&format=json',
    false
);
?>
<h1>
  <?php echo $this->item->id
    ? Text::_('COM_JUGENDTRAINING_TRAINING_EDIT')
    : Text::_('COM_JUGENDTRAINING_TRAINING_NEW'); ?>
</h1>

<form
  action="<?php echo Route::_('index.php?option=com_jugendtraining&view=trainertraining&layout=edit&id=' . (int) $this->item->id); ?>"
  method="post"
  name="adminForm"
  id="adminForm"
  class="form-validate"
>
  <div class="row g-4">
    <div class="col-xl-5">
      <section class="card h-100">
        <div class="card-header">
          <h2 class="h4 mb-0"><?php echo Text::_('COM_JUGENDTRAINING_TRAINING_DETAILS'); ?></h2>
        </div>
        <div class="card-body">
          <?php echo $this->form->renderFieldset('details'); ?>
        </div>
      </section>
    </div>

    <div class="col-xl-7">
      <section class="card jt-attendance-panel">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
          <div>
            <h2 class="h4 mb-0"><?php echo Text::_('COM_JUGENDTRAINING_ATTENDANCE'); ?></h2>
            <?php if ($this->item->id) : ?>
              <div class="small text-muted mt-1">
                <?php echo Text::_('COM_JUGENDTRAINING_ATTENDANCE_AUTOSAVE_HINT'); ?>
              </div>
            <?php endif; ?>
          </div>
          <span class="badge bg-secondary jt-athlete-count">
            <?php echo count($this->athletes); ?>
            <?php echo Text::_('COM_JUGENDTRAINING_ATHLETES'); ?>
          </span>
        </div>

        <div class="card-body">
          <?php if (!$this->item->id) : ?>
            <div class="alert alert-info mb-0">
              <?php echo Text::_('COM_JUGENDTRAINING_SAVE_TRAINING_FOR_ROSTER'); ?>
            </div>
          <?php elseif (!$this->athletes) : ?>
            <div class="alert alert-info mb-0">
              <?php echo Text::_('COM_JUGENDTRAINING_GROUP_HAS_NO_ATHLETES'); ?>
            </div>
          <?php else : ?>
            <div class="jt-attendance-toolbar mb-4">
              <div class="d-grid d-sm-flex gap-2">
                <button
                  type="button"
                  class="btn btn-success jt-set-all"
                  data-status="present"
                >
                  ✓ <?php echo Text::_('COM_JUGENDTRAINING_MARK_ALL_PRESENT'); ?>
                </button>
                <button
                  type="button"
                  class="btn btn-outline-secondary jt-set-all"
                  data-status=""
                >
                  ○ <?php echo Text::_('COM_JUGENDTRAINING_CLEAR_ATTENDANCE'); ?>
                </button>
              </div>

              <div class="jt-attendance-filters mt-3" role="group" aria-label="<?php echo Text::_('COM_JUGENDTRAINING_ATTENDANCE_FILTER'); ?>">
                <button type="button" class="btn btn-sm btn-primary jt-filter" data-filter="all">
                  <?php echo Text::_('COM_JUGENDTRAINING_FILTER_ALL'); ?>
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary jt-filter" data-filter="open">
                  <?php echo Text::_('COM_JUGENDTRAINING_FILTER_OPEN'); ?>
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary jt-filter" data-filter="absent">
                  <?php echo Text::_('COM_JUGENDTRAINING_FILTER_MISSING'); ?>
                </button>
              </div>
            </div>

            <div class="jt-save-state" aria-live="polite"></div>

            <div class="jt-attendance-cards">
              <?php foreach ($this->athletes as $athlete) :
                  $athleteId = (int) $athlete['id'];
                  $entry = $this->attendance[$athleteId] ?? [
                      'status' => '',
                      'comment' => '',
                  ];
                  $currentStatus = (string) $entry['status'];
              ?>
                <article
                  class="jt-attendance-card"
                  data-athlete-id="<?php echo $athleteId; ?>"
                  data-status="<?php echo htmlspecialchars($currentStatus ?: 'empty', ENT_QUOTES, 'UTF-8'); ?>"
                >
                  <div class="jt-athlete-head">
                    <div class="jt-athlete-avatar" aria-hidden="true">
                      <?php echo htmlspecialchars(
                          mb_strtoupper(mb_substr($athlete['firstname'], 0, 1) . mb_substr($athlete['lastname'], 0, 1)),
                          ENT_QUOTES,
                          'UTF-8'
                      ); ?>
                    </div>
                    <div class="jt-athlete-main">
                      <h3 class="jt-athlete-name">
                        <?php echo htmlspecialchars(
                            $athlete['firstname'] . ' ' . $athlete['lastname'],
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>
                      </h3>
                      <div class="jt-athlete-meta">
                        <?php if (!empty($athlete['class_name'])) : ?>
                          <span><?php echo htmlspecialchars($athlete['class_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <?php endif; ?>
                        <?php if (!empty($athlete['bow_type'])) : ?>
                          <span><?php echo htmlspecialchars($athlete['bow_type'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <?php endif; ?>
                        <?php if (!empty($athlete['club_name'])) : ?>
                          <span><?php echo htmlspecialchars($athlete['club_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <?php endif; ?>
                      </div>
                    </div>
                    <div class="jt-card-save-indicator" aria-hidden="true"></div>
                  </div>

                  <div class="jt-status-grid" role="group" aria-label="<?php echo Text::_('COM_JUGENDTRAINING_ATTENDANCE_STATUS'); ?>">
                    <?php foreach ($statusOptions as $value => $option) : ?>
                      <button
                        type="button"
                        class="jt-status-button jt-status-<?php echo $value ?: 'empty'; ?> <?php echo $currentStatus === $value ? 'is-active' : ''; ?>"
                        data-status="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>"
                        aria-pressed="<?php echo $currentStatus === $value ? 'true' : 'false'; ?>"
                      >
                        <span class="jt-status-icon" aria-hidden="true"><?php echo $option['icon']; ?></span>
                        <span><?php echo $option['label']; ?></span>
                      </button>
                    <?php endforeach; ?>
                  </div>

                  <input
                    type="hidden"
                    class="jt-attendance-status"
                    name="attendance[<?php echo $athleteId; ?>][status]"
                    value="<?php echo htmlspecialchars($currentStatus, ENT_QUOTES, 'UTF-8'); ?>"
                  >

                  <details class="jt-comment-details" <?php echo trim((string) $entry['comment']) !== '' ? 'open' : ''; ?>>
                    <summary>
                      <?php echo Text::_('COM_JUGENDTRAINING_ADD_COMMENT'); ?>
                      <span class="jt-comment-marker" aria-hidden="true">
                        <?php echo trim((string) $entry['comment']) !== '' ? '●' : ''; ?>
                      </span>
                    </summary>
                    <div class="pt-2">
                      <textarea
                        class="form-control jt-attendance-comment"
                        rows="2"
                        maxlength="500"
                        name="attendance[<?php echo $athleteId; ?>][comment]"
                        placeholder="<?php echo Text::_('COM_JUGENDTRAINING_ATTENDANCE_COMMENT_PLACEHOLDER'); ?>"
                      ><?php echo htmlspecialchars((string) $entry['comment'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>
                  </details>
                </article>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </section>
    </div>
  </div>

  <div class="d-flex gap-2 mt-4">
    <button
      type="button"
      class="btn btn-primary"
      onclick="Joomla.submitbutton('trainertraining.save')"
    >
      <?php echo Text::_('COM_JUGENDTRAINING_BUTTON_SAVE'); ?>
    </button>
    <button
      type="button"
      class="btn btn-secondary"
      onclick="Joomla.submitbutton('trainertraining.cancel')"
    >
      <?php echo Text::_('COM_JUGENDTRAINING_BUTTON_CANCEL'); ?>
    </button>
  </div>

  <input type="hidden" name="task" value="">
  <?php echo HTMLHelper::_('form.token'); ?>
</form>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const ajaxUrl = <?php echo json_encode($ajaxUrl, JSON_UNESCAPED_SLASHES); ?>;
  const tokenName = <?php echo json_encode($tokenName); ?>;
  const sessionId = <?php echo (int) $this->item->id; ?>;
  const saveState = document.querySelector('.jt-save-state');
  const pendingTimers = new Map();

  const setGlobalState = (message, type = '') => {
    if (!saveState) {
      return;
    }

    saveState.textContent = message;
    saveState.className = 'jt-save-state';

    if (type) {
      saveState.classList.add('is-' + type);
    }
  };

  const updateCardVisuals = (card, status) => {
    card.dataset.status = status || 'empty';

    card.querySelectorAll('.jt-status-button').forEach((button) => {
      const active = button.dataset.status === status;
      button.classList.toggle('is-active', active);
      button.setAttribute('aria-pressed', active ? 'true' : 'false');
    });

    const hidden = card.querySelector('.jt-attendance-status');

    if (hidden) {
      hidden.value = status;
    }
  };

  const quickSave = async (card) => {
    if (!sessionId) {
      return;
    }

    const athleteId = card.dataset.athleteId;
    const status = card.querySelector('.jt-attendance-status')?.value ?? '';
    const comment = card.querySelector('.jt-attendance-comment')?.value ?? '';
    const indicator = card.querySelector('.jt-card-save-indicator');

    card.classList.add('is-saving');
    card.classList.remove('has-save-error');

    if (indicator) {
      indicator.textContent = '…';
    }

    setGlobalState(<?php echo json_encode(Text::_('COM_JUGENDTRAINING_SAVING')); ?>, 'saving');

    const body = new URLSearchParams();
    body.set('session_id', sessionId);
    body.set('athlete_id', athleteId);
    body.set('status', status);
    body.set('comment', comment);
    body.set(tokenName, '1');

    try {
      const response = await fetch(ajaxUrl, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: body.toString()
      });

      const result = await response.json();

      if (!response.ok || result.success === false) {
        throw new Error(result.message || <?php echo json_encode(Text::_('COM_JUGENDTRAINING_SAVE_FAILED')); ?>);
      }

      card.classList.remove('is-saving');
      card.classList.add('is-saved');

      if (indicator) {
        indicator.textContent = '✓';
      }

      setGlobalState(<?php echo json_encode(Text::_('COM_JUGENDTRAINING_AUTOSAVED')); ?>, 'saved');

      window.setTimeout(() => {
        card.classList.remove('is-saved');

        if (indicator) {
          indicator.textContent = '';
        }
      }, 1200);
    } catch (error) {
      card.classList.remove('is-saving');
      card.classList.add('has-save-error');

      if (indicator) {
        indicator.textContent = '!';
      }

      setGlobalState(
        error.message || <?php echo json_encode(Text::_('COM_JUGENDTRAINING_SAVE_FAILED')); ?>,
        'error'
      );
    }
  };

  const scheduleSave = (card, delay = 0) => {
    const athleteId = card.dataset.athleteId;

    if (pendingTimers.has(athleteId)) {
      window.clearTimeout(pendingTimers.get(athleteId));
    }

    const timer = window.setTimeout(() => {
      pendingTimers.delete(athleteId);
      quickSave(card);
    }, delay);

    pendingTimers.set(athleteId, timer);
  };

  document.querySelectorAll('.jt-attendance-card').forEach((card) => {
    card.querySelectorAll('.jt-status-button').forEach((button) => {
      button.addEventListener('click', () => {
        const status = button.dataset.status ?? '';
        updateCardVisuals(card, status);
        scheduleSave(card);
      });
    });

    const comment = card.querySelector('.jt-attendance-comment');

    if (comment) {
      comment.addEventListener('input', () => {
        const marker = card.querySelector('.jt-comment-marker');

        if (marker) {
          marker.textContent = comment.value.trim() ? '●' : '';
        }

        scheduleSave(card, 700);
      });
    }
  });

  document.querySelectorAll('.jt-set-all').forEach((button) => {
    button.addEventListener('click', () => {
      const status = button.dataset.status ?? '';

      document.querySelectorAll('.jt-attendance-card').forEach((card, index) => {
        updateCardVisuals(card, status);
        window.setTimeout(() => scheduleSave(card), index * 70);
      });
    });
  });

  document.querySelectorAll('.jt-filter').forEach((button) => {
    button.addEventListener('click', () => {
      const filter = button.dataset.filter;

      document.querySelectorAll('.jt-filter').forEach((item) => {
        const active = item === button;
        item.classList.toggle('btn-primary', active);
        item.classList.toggle('btn-outline-secondary', !active);
      });

      document.querySelectorAll('.jt-attendance-card').forEach((card) => {
        const status = card.dataset.status;
        let visible = true;

        if (filter === 'open') {
          visible = status === 'empty';
        } else if (filter === 'absent') {
          visible = ['absent', 'excused', 'late'].includes(status);
        }

        card.hidden = !visible;
      });
    });
  });
});
</script>
