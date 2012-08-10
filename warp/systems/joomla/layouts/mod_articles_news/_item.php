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

<?php if ($params->get('item_title')) : ?>
<h3>
	<?php if ($params->get('link_titles') && $item->link != '') : ?>
		<a href="<?php echo $item->link; ?>"><?php echo $item->title;?></a>
	<?php else : ?>
		<?php echo $item->title; ?>
	<?php endif; ?>
</h3>
<?php endif; ?>

<?php if (!$params->get('intro_only')) echo $item->afterDisplayTitle; ?>

<?php echo $item->beforeDisplayContent; ?>

<?php echo $item->introtext; ?>

<?php if (isset($item->link) && $item->readmore && $params->get('readmore')) :
	echo '<a class="button-default" href="'.$item->link.'">'.$item->linkText.'</a>';
endif;