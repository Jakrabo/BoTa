<?php
\defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;use Joomla\CMS\Router\Route;use Joomla\CMS\HTML\HTMLHelper;$a=$this->athlete;
?>
<div class="d-flex justify-content-between align-items-center mb-4"><h1 class="mb-0"><?php echo Text::_('COM_JUGENDTRAINING_EDIT_ATHLETE');?></h1><a class="btn btn-outline-secondary" href="<?php echo Route::_('index.php?option=com_jugendtraining&view=trainerathletedetail&id='.(int)$a->id);?>"><?php echo Text::_('COM_JUGENDTRAINING_BACK');?></a></div>
<form class="card card-body" action="<?php echo Route::_('index.php?option=com_jugendtraining&task=trainerathleteedit.save');?>" method="post">
<input type="hidden" name="jform[id]" value="<?php echo(int)$a->id;?>">
<div class="row g-3">
<div class="col-md-6"><label class="form-label"><?php echo Text::_('COM_JUGENDTRAINING_FIELD_FIRSTNAME');?></label><input class="form-control" name="jform[firstname]" value="<?php echo htmlspecialchars($a->firstname,ENT_QUOTES,'UTF-8');?>" required></div>
<div class="col-md-6"><label class="form-label"><?php echo Text::_('COM_JUGENDTRAINING_FIELD_LASTNAME');?></label><input class="form-control" name="jform[lastname]" value="<?php echo htmlspecialchars($a->lastname,ENT_QUOTES,'UTF-8');?>" required></div>
<div class="col-md-6"><label class="form-label">E-Mail</label><input class="form-control" type="email" name="jform[email]" value="<?php echo htmlspecialchars((string)$a->email,ENT_QUOTES,'UTF-8');?>"></div>
<div class="col-md-6"><label class="form-label"><?php echo Text::_('COM_JUGENDTRAINING_FIELD_PHONE');?></label><input class="form-control" name="jform[phone]" value="<?php echo htmlspecialchars((string)$a->phone,ENT_QUOTES,'UTF-8');?>"></div>
<div class="col-md-4"><label class="form-label"><?php echo Text::_('COM_JUGENDTRAINING_FIELD_CLASS');?></label><select class="form-select" name="jform[class_id]"><option value="">–</option><?php foreach($this->classes as$c):?><option value="<?php echo(int)$c->id;?>" <?php echo(int)$a->class_id===(int)$c->id?'selected':'';?>><?php echo htmlspecialchars($c->name,ENT_QUOTES,'UTF-8');?></option><?php endforeach;?></select></div>
<div class="col-md-4"><label class="form-label"><?php echo Text::_('COM_JUGENDTRAINING_FIELD_BOW_TYPE');?></label><input class="form-control" name="jform[bow_type]" value="<?php echo htmlspecialchars((string)$a->bow_type,ENT_QUOTES,'UTF-8');?>"></div>
<div class="col-md-4"><label class="form-label"><?php echo Text::_('COM_JUGENDTRAINING_FIELD_MEMBERSHIP_NUMBER');?></label><input class="form-control" name="jform[membership_number]" value="<?php echo htmlspecialchars((string)$a->membership_number,ENT_QUOTES,'UTF-8');?>"></div>
<div class="col-12"><label class="form-label"><?php echo Text::_('COM_JUGENDTRAINING_FIELD_NOTES');?></label><textarea class="form-control" name="jform[notes]" rows="4"><?php echo htmlspecialchars((string)$a->notes,ENT_QUOTES,'UTF-8');?></textarea></div>
</div><button class="btn btn-primary mt-3" type="submit"><?php echo Text::_('COM_JUGENDTRAINING_SAVE');?></button><?php echo HTMLHelper::_('form.token');?></form>
