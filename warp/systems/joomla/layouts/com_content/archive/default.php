<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

?>

<div id="system">

	<?php if ($this->params->get('show_page_heading')) : ?>
	<h1 class="title"><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	<?php endif; ?>

	<form id="adminForm" action="<?php echo JRoute::_('index.php')?>" method="post">
	
		<div class="filter">
			<?php if ($this->params->get('filter_field') != 'hide') : ?>
			<div>
				<label for="filter-search"><?php echo JText::_('COM_CONTENT_'.$this->params->get('filter_field').'_FILTER_LABEL'); ?></label>
				<input type="text" name="filter-search" id="filter-search" value="<?php echo $this->escape($this->filter); ?>" onchange="document.getElementById('adminForm').submit();" />
			</div>
			<?php endif; ?>
		
			<div>
				<?php echo $this->form->monthField; ?>
				<?php echo $this->form->yearField; ?>
				<?php echo $this->form->limitField; ?>
				<button type="submit" class="button"><?php echo JText::_('JGLOBAL_FILTER_BUTTON'); ?></button>
			</div>
		</div>
		
		<input type="hidden" name="view" value="archive" />
		<input type="hidden" name="option" value="com_content" />
		<input type="hidden" name="limitstart" value="0" />
	
	</form>
	
	<?php echo $this->loadTemplate('items'); ?>

</div>