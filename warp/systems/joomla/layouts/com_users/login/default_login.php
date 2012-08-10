<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
	
?>

<div id="system">
	
	<?php if ($this->params->get('show_page_heading')) : ?>
	<h1 class="title"><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	<?php endif; ?>

	<?php if (($this->params->get('logindescription_show') == 1 && str_replace(' ', '', $this->params->get('login_description')) != '') || $this->params->get('login_image') != '') : ?>
	<div class="description">
		<?php if ($this->params->get('login_image')) : ?>
			<img src="<?php echo $this->escape($this->params->get('login_image')); ?>" alt="<?php echo JText::_('COM_USER_LOGIN_IMAGE_ALT')?>" class="size-auto" />
		<?php endif; ?>
		<?php if ($this->params->get('logindescription_show')) echo $this->params->get('login_description'); ?>
	</div>
	<?php endif; ?>

	<form class="submission small style" action="<?php echo JRoute::_('index.php?option=com_users&task=user.login'); ?>" method="post">
	
		<fieldset>
			<?php foreach ($this->form->getFieldset('credentials') as $field): ?>
				<?php if (!$field->hidden): ?>
					<div><?php echo $field->label.$field->input; ?></div>
				<?php endif; ?>
			<?php endforeach; ?>


			<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
			<div>
				<label for="remember"><?php echo JText::_('JGLOBAL_REMEMBER_ME') ?></label>
				<input type="checkbox" name="remember" class="inputbox" value="yes"  alt="<?php echo JText::_('JGLOBAL_REMEMBER_ME') ?>" />
			</div>
			<?php endif; ?>

			<div>
				<button type="submit" class="button"><?php echo JText::_('JLOGIN'); ?></button>
			</div>

			<ul>
				<li><a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>"><?php echo JText::_('COM_USERS_LOGIN_RESET'); ?></a></li>
				<li><a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>"><?php echo JText::_('COM_USERS_LOGIN_REMIND'); ?></a></li>
				<?php $usersConfig = JComponentHelper::getParams('com_users'); ?>
				<?php if ($usersConfig->get('allowUserRegistration')) : ?>
				<li><a href="<?php echo JRoute::_('index.php?option=com_users&view=registration'); ?>"><?php echo JText::_('COM_USERS_LOGIN_REGISTER'); ?></a></li>
				<?php endif; ?>
			</ul>

		</fieldset>

		<input type="hidden" name="return" value="<?php echo base64_encode($this->params->get('login_redirect_url', $this->form->getValue('return'))); ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</form>

</div>