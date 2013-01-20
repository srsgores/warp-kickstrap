<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die;

$params = &$this->item->params;
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.framework');

$user = JFactory::getUser();

$canEdit = $user->authorise('core.edit', 'com_weblinks');
$canCreate = $user->authorise('core.create', 'com_weblinks');
$canEditState = $user->authorise('core.edit.state', 'com_weblinks');

$n = count($this->items);
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

?>

<?php if (empty($this->items)) : ?>
	<p><?php echo JText::_('COM_WEBLINKS_NO_WEBLINKS'); ?></p>
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

				<th class="item-title">
					<?php echo JHtml::_('grid.sort', 'COM_WEBLINKS_GRID_TITLE', 'title', $listDirn, $listOrder); ?>
				</th>
				
				<?php if ($this->params->get('show_link_hits')) : ?>
				<th class="item-hits" width="5%">
					<?php echo JHtml::_('grid.sort', 'JGLOBAL_HITS', 'hits', $listDirn, $listOrder); ?>
				</th>
				<?php endif; ?>
				
			</tr>
		</thead>
		<?php endif; ?>
		
		<tbody>
			<?php foreach ($this->items as $i => $item) : ?>
			<tr class="<?php if ($i % 2 == 1) { echo 'even'; } else { echo 'odd'; } ?>">
	
				<td class="item-title">

					<?php if ($this->params->get('icons') == 1) : ?>
						<?php if (!$this->params->get('link_icons')) : ?>
							<?php echo JHtml::_('image', 'system/'.$this->params->get('link_icons', 'weblink.png'), JText::_('COM_WEBLINKS_LINK'), NULL, true); ?>
						<?php else: ?>
							<?php echo '<img src="'.$this->params->get('link_icons').'" alt="'.JText::_('COM_WEBLINKS_LINK').'" />'; ?>
						<?php endif; ?>
					<?php endif; ?>
					
					<?php
						// Compute the correct link
						$menuclass = 'category'.$this->pageclass_sfx;
						$link = $item->link;
						$width	= $item->params->get('width');
						$height	= $item->params->get('height');
						if ($width == null || $height == null) {
							$width	= 600;
							$height	= 500;
						}
	
						switch ($item->params->get('target', $this->params->get('target')))
						{
							case 1:
								// open in a new window
								echo '<a href="'. $link .'" target="_blank" class="'. $menuclass .'" rel="nofollow">'.
									$this->escape($item->title) .'</a>';
								break;
	
							case 2:
								// open in a popup window
								$attribs = 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width='.$this->escape($width).',height='.$this->escape($height).'';
								echo "<a href=\"$link\" onclick=\"window.open(this.href, 'targetWindow', '".$attribs."'); return false;\">".
									$this->escape($item->title).'</a>';
								break;
							case 3:
								// open in a modal window
								JHtml::_('behavior.modal', 'a.modal'); ?>
								<a class="modal" href="<?php echo $link;?>"  rel="{handler: 'iframe', size: {x:<?php echo $this->escape($width);?>, y:<?php echo $this->escape($height);?>}}">
									<?php echo $this->escape($item->title). ' </a>' ;
								break;
	
							default:
								// open in parent window
								echo '<a href="'.  $link . '" class="'. $menuclass .'" rel="nofollow">'.
									$this->escape($item->title) . ' </a>';
								break;
						}
					?>
					
					<?php // Code to add the edit link for the weblink. ?>
					<?php if ($canEdit) : ?>
						<?php echo JHtml::_('icon.edit', $item, $params); ?>
					<?php endif; ?>

	
					<?php if (($this->params->get('show_link_description')) and ($item->description !='')): ?>
					<div><?php echo $item->description; ?></div>
					<?php endif; ?>
					
				</td>
				
				<?php if ($this->params->get('show_link_hits')) : ?>
				<td class="item-hits" width="5%">
					<?php echo $item->hits; ?>
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