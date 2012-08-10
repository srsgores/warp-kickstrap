<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die;

$fieldsets = $this->form->getFieldsets();
if (isset($fieldsets['core']))   unset($fieldsets['core']);
if (isset($fieldsets['params'])) unset($fieldsets['params']);

JLoader::register('JHtmlUsers', JPATH_COMPONENT . '/helpers/html/users.php');
JHtml::register('users.spacer', array('JHtmlUsers', 'spacer'));

?>

<?php foreach ($fieldsets as $group => $fieldset): ?>
	<?php $fields = $this->form->getFieldset($fieldset->name); ?>
	<?php if (count($fields)): ?>

		<?php if (isset($fieldset->label)): ?>
		<h3><?php echo JText::_($fieldset->label); ?></h3>
		<?php endif;?>
		
		<ul>
		<?php foreach ($fields as $field): ?>
			<?php if (!$field->hidden): ?>
			<li>
				<strong><?php echo $field->title; ?>:</strong>
				<?php if (JHtml::isRegistered('users.'.$field->id)):?>
					<?php echo JHtml::_('users.'.$field->id, $field->value);?>
				<?php elseif (JHtml::isRegistered('users.'.$field->fieldname)):?>
					<?php echo JHtml::_('users.'.$field->fieldname, $field->value);?>
				<?php elseif (JHtml::isRegistered('users.'.$field->type)):?>
					<?php echo JHtml::_('users.'.$field->type, $field->value);?>
				<?php else:?>
					<?php echo JHtml::_('users.value', $field->value);?>
				<?php endif;?>
			</li>
			<?php endif; ?>
		<?php endforeach; ?>
		</ul>

	<?php endif; ?>
<?php endforeach;