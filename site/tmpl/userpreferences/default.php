<?php
\defined('_JEXEC') or die;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
 <div>
  <h1 class="mb-1"><?php echo Text::_('COM_JUGENDTRAINING_USER_PREFERENCES');?></h1>
  <p class="text-muted mb-0"><?php echo Text::_('COM_JUGENDTRAINING_THEME_INTRO');?></p>
 </div>
 <a class="btn btn-outline-secondary" href="javascript:history.back()"><?php echo Text::_('COM_JUGENDTRAINING_BACK');?></a>
</div>

<form class="card" action="<?php echo Route::_('index.php?option=com_jugendtraining&task=userpreferences.save');?>" method="post">
 <div class="card-header"><h2 class="h4 mb-0"><?php echo Text::_('COM_JUGENDTRAINING_APPEARANCE');?></h2></div>
 <div class="card-body">
  <fieldset>
   <legend class="h6"><?php echo Text::_('COM_JUGENDTRAINING_COLOR_SCHEME');?></legend>
   <div class="row g-3">
    <?php foreach([
      'auto'=>['COM_JUGENDTRAINING_THEME_AUTO','COM_JUGENDTRAINING_THEME_AUTO_DESC','◐'],
      'light'=>['COM_JUGENDTRAINING_THEME_LIGHT','COM_JUGENDTRAINING_THEME_LIGHT_DESC','☀'],
      'dark'=>['COM_JUGENDTRAINING_THEME_DARK','COM_JUGENDTRAINING_THEME_DARK_DESC','☾']
    ] as$value=>$cfg):?>
     <div class="col-12 col-md-4">
      <label class="card h-100 p-3 jt-theme-choice" for="theme-<?php echo$value;?>">
       <div class="form-check">
        <input class="form-check-input" type="radio" name="theme" id="theme-<?php echo$value;?>" value="<?php echo$value;?>" <?php echo$this->theme===$value?'checked':'';?>>
        <span class="form-check-label">
         <span class="fs-3 d-block mb-2"><?php echo$cfg[2];?></span>
         <strong class="d-block"><?php echo Text::_($cfg[0]);?></strong>
         <span class="small text-muted"><?php echo Text::_($cfg[1]);?></span>
        </span>
       </div>
      </label>
     </div>
    <?php endforeach;?>
   </div>
  </fieldset>
  <button class="btn btn-primary mt-4" type="submit"><?php echo Text::_('JSAVE');?></button>
  <?php echo HTMLHelper::_('form.token');?>
 </div>
</form>
