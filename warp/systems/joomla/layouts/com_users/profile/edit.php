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
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

// load user_profile plugin language
$lang = JFactory::getLanguage();
$lang->load( 'plg_user_profile', JPATH_ADMINISTRATOR);
?>

<div id="system">
	
	<?php if ($this->params->get('show_page_heading')) : ?>
	<h1 class="title"><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	<?php endif; ?>

	<form class="submission box style" action="<?php echo JRoute::_('index.php?option=com_users&task=profile.save'); ?>" method="post" enctype="multipart/form-data">
		<?php foreach ($this->form->getFieldsets() as $fieldset): ?>
			<?php $fields = $this->form->getFieldset($fieldset->name); ?>
			<?php if (count($fields)): ?>
				<fieldset>
					<?php if (isset($fieldset->label)): ?>
					<legend><?php echo JText::_($fieldset->label); ?></legend>
					<?php endif;?>
					<?php foreach ($fields as $field): ?>
						<?php if ($field->hidden): ?>
							<?php echo $field->input; ?>
						<?php else: ?>
							<div><?php echo $field->label.$field->input; ?>
								<?php if (!$field->required && $field->type!='Spacer' && $field->name!='jform[username]'): ?>
									<span class="optional"><?php echo JText::_('COM_USERS_OPTIONAL');?></span>
								<?php endif; ?>
							</div>
					<?php endif; ?>
					<?php endforeach; ?>
				</fieldset>
			<?php endif; ?>
		<?php endforeach; ?>

		<div class="submit">
			<button class="validate" type="submit"><?php echo JText::_('JSUBMIT'); ?></button>
		</div>
		
		<input type="hidden" name="option" value="com_users" />
		<input type="hidden" name="task" value="profile.save" />
		<?php echo JHtml::_('form.token'); ?>

	</form>

</div>