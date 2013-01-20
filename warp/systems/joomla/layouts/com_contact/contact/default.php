<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

$cparams = JComponentHelper::getParams ('com_media');

?>

<div id="system">

	<?php if ($this->params->get('show_page_heading')) : ?>
	<h1 class="title"><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	<?php endif; ?>

	<?php if ($this->params->get('show_contact_category') == 'show_no_link') : ?>
	<h3><?php echo $this->contact->category_title; ?></h3>
	<?php elseif ($this->params->get('show_contact_category') == 'show_with_link') : ?>
	<?php $contactLink = ContactHelperRoute::getCategoryRoute($this->contact->catid);?>
	<h3><a href="<?php echo $contactLink; ?>"><?php echo $this->escape($this->contact->category_title); ?></a></h3>
	<?php endif; ?>

	<?php if ($this->params->get('show_contact_list') && count($this->contacts) > 1) : ?>
	<div class="filter">
		<form action="#" method="get" name="selectForm" id="selectForm">
			<?php echo JText::_('COM_CONTACT_SELECT_CONTACT'); ?>
			<?php echo JHtml::_('select.genericlist',  $this->contacts, 'id', 'class="inputbox" onchange="document.location.href = this.value"', 'link', 'name', $this->contact->link);?>
		</form>
	</div>
	<?php endif; ?>

	<div class="item">
	
		<?php if ($this->contact->name && $this->params->get('show_name')) : ?>
		<h1 class="title"><?php echo $this->contact->name; ?></h1>
		<?php endif;  ?>
		
		<?php if ($this->contact->con_position && $this->params->get('show_position')) : ?>
		<h2 class="subtitle"><?php echo $this->contact->con_position; ?></h2>
		<?php endif; ?>

		<?php if ($this->contact->image && $this->params->get('show_image')) : ?>
		<?php echo JHtml::_('image', $this->contact->image, JText::_('COM_CONTACT_IMAGE_DETAILS'), array('class' => 'align-right')); ?>
		<?php endif; ?>

		<?php echo $this->loadTemplate('address'); ?>

		<?php if ($this->params->get('allow_vcard')) : ?>
		<p>
			<?php echo JText::_('COM_CONTACT_DOWNLOAD_INFORMATION_AS');?>
			<a href="<?php echo JRoute::_('index.php?option=com_contact&amp;view=contact&amp;id='.$this->contact->id . '&amp;format=vcf'); ?>"><?php echo JText::_('COM_CONTACT_VCARD');?></a>
		</p>
		<?php endif; ?>

		<?php if ($this->contact->misc && $this->params->get('show_misc')) : ?>
			<h3><?php echo JText::_('COM_CONTACT_OTHER_INFORMATION'); ?></h3>
			<p><?php echo $this->contact->misc; ?></p>
		<?php endif; ?>

		<?php if ($this->params->get('show_links')) : ?>
			<h3><?php echo JText::_('COM_CONTACT_LINKS'); ?></h3>
			<?php echo $this->loadTemplate('links'); ?>
		<?php endif; ?>

		<?php if ($this->params->get('show_articles') && $this->contact->user_id && $this->contact->articles) : ?>
			<h3><?php echo JText::_('JGLOBAL_ARTICLES'); ?></h3>
			<?php echo $this->loadTemplate('articles'); ?>
		<?php endif; ?>

		<?php if ($this->params->get('show_profile') && $this->contact->user_id && JPluginHelper::isEnabled('user', 'profile')) : ?>
			<h3><?php echo JText::_('COM_CONTACT_PROFILE'); ?></h3>
			<?php echo $this->loadTemplate('profile'); ?>
		<?php endif; ?>

		<?php if ($this->params->get('show_email_form') && ($this->contact->email_to || $this->contact->user_id)) : ?>
			<h3><?php  echo JText::_('COM_CONTACT_EMAIL_FORM'); ?></h3>
			<?php  echo $this->loadTemplate('form');  ?>
		<?php endif; ?>
		
	</div>
	
</div>
