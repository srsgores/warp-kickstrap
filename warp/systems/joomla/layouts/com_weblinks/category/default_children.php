<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die;

?>

<?php if (count($this->children[$this->category->id]) > 0 && $this->maxLevel != 0) : ?>
<ul>
	<?php foreach($this->children[$this->category->id] as $id => $child) : ?>
		<?php if ($this->params->get('show_empty_categories') || $child->numitems || count($child->getChildren())) : ?>
			<li>
				<a href="<?php echo JRoute::_(WeblinksHelperRoute::getCategoryRoute($child->id));?>"><?php echo $this->escape($child->title); ?></a>
	
				<?php if ($this->params->get('show_cat_items') == 1) :?>
				<small>(<?php echo $child->numitems; ?>)</small>
				<?php endif; ?>
	
				<?php if (($this->params->get('show_subcat_desc') == 1) && $child->description) : ?>
				<div><?php echo JHtml::_('content.prepare', $child->description, '', 'com_weblinks.category'); ?></div>
				<?php endif; ?>

				<?php
					if (count($child->getChildren()) > 0 ) {
						$this->children[$child->id] = $child->getChildren();
						$this->category = $child;
						$this->maxLevel--;
						echo $this->loadTemplate('children');
						$this->category = $child->getParent();
						$this->maxLevel++;
					}
				?>
			</li>
		<?php endif; ?>
	<?php endforeach; ?>
</ul>
<?php endif;