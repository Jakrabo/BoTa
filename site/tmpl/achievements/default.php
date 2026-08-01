<?php \defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;use Joomla\CMS\HTML\HTMLHelper;use Joomla\CMS\Uri\Uri;?>
<h1><?php echo Text::_('COM_JUGENDTRAINING_MY_ACHIEVEMENTS');?></h1>
<?php if(!$this->items):?><div class="alert alert-info"><?php echo Text::_('COM_JUGENDTRAINING_NO_ACHIEVEMENTS_YET');?></div><?php else:?>
<div class="jt-badge-grid"><?php foreach($this->items as$b):?>
<article class="jt-badge-card jt-badge-card-large">
<?php if(!empty($b->badge_image)):?><img class="jt-badge-image" src="<?php echo htmlspecialchars(Uri::root().ltrim($b->badge_image,'/'),ENT_QUOTES,'UTF-8');?>" alt=""><?php else:?><span class="jt-badge-fallback" aria-label="<?php echo Text::_('COM_JUGENDTRAINING_ACHIEVEMENT_NO_IMAGE');?>">★</span><?php endif;?>
<div class="jt-badge-card__body"><span class="badge bg-secondary mb-2"><?php echo htmlspecialchars($b->category,ENT_QUOTES,'UTF-8');?></span><h2 class="h4"><?php echo htmlspecialchars($b->title,ENT_QUOTES,'UTF-8');?></h2><p><?php echo nl2br(htmlspecialchars((string)$b->description,ENT_QUOTES,'UTF-8'));?></p><div class="small text-muted"><?php echo Text::_('COM_JUGENDTRAINING_AWARDED_ON');?> <?php echo HTMLHelper::_('date',$b->awarded_at,Text::_('DATE_FORMAT_LC4'));?> · <?php echo Text::_($b->award_source==='automatic'?'COM_JUGENDTRAINING_AWARD_AUTOMATIC':'COM_JUGENDTRAINING_AWARD_MANUAL');?></div></div>
</article><?php endforeach;?></div><?php endif;?>
