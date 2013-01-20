<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.framework');

$n			= count($this->items);
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

?>

<?php if (empty($this->items)) : ?>
	<p><?php echo JText::_('COM_NEWSFEEDS_NO_ARTICLES'); ?></p>
<?php else : ?>

<form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm">
	
	<?php if ($this->params->get('show_pagination_limit')) : ?>
	<div class="filter">
		<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>
		<?php echo $this->pagination->getLimitBox(); ?>
	</div>
	<?php endif; ?>

	<table class="zebra">
		<?php if ($this->params->get('show_headings')==1) : ?>
		<thead>
			<tr>

				<th class="item-title" id="tableOrdering">
					<?php echo JHtml::_('grid.sort', 'COM_NEWSFEEDS_FEED_NAME', 'a.name', $listDirn, $listOrder); ?>
				</th>

				<?php if ($this->params->get('show_articles')) : ?>
				<th class="item-num-art" id="tableOrdering2">
					<?php echo JHtml::_('grid.sort', 'COM_NEWSFEEDS_NUM_ARTICLES', 'a.numarticles', $listDirn, $listOrder); ?>
				</th>
				<?php endif; ?>

				<?php if ($this->params->get('show_link')) : ?>
				<th class="item-link" id="tableOrdering3">
					<?php echo JHtml::_('grid.sort', 'COM_NEWSFEEDS_FEED_LINK', 'a.link', $listDirn, $listOrder); ?>
				</th>
				<?php endif; ?>

			</tr>
		</thead>
		<?php endif; ?>

		<tbody>
			<?php foreach ($this->items as $i => $item) : ?>
			<tr class="<?php if ($i % 2 == 1) { echo 'even'; } else { echo 'odd'; } ?>">

				<td class="item-title">
					<a href="<?php echo JRoute::_(NewsFeedsHelperRoute::getNewsfeedRoute($item->slug, $item->catid)); ?>"><?php echo $item->name; ?></a>
				</td>

				<?php  if ($this->params->get('show_articles')) : ?>
				<td class="item-num-art">
					<?php echo $item->numarticles; ?>
				</td>
				<?php endif; ?>

				<?php if ($this->params->get('show_link')) : ?>
				<td class="item-link">
					<a href="<?php echo $item->link; ?>"><?php echo $item->link; ?></a>
				</td>
				<?php endif; ?>

			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<?php if ($this->params->get('show_pagination')) : ?>
	<?php echo $this->pagination->getPagesLinks(); ?>
	<?php endif; ?>
	
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
</form>
<?php endif;