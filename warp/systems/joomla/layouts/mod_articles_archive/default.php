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

<?php if (!empty($list)) :?>
<ul class="line line-icon">
<?php foreach ($list as $item) : ?>
	<li><a href="<?php echo $item->link; ?>"><?php echo $item->text; ?></a></li>
<?php endforeach; ?>
</ul>
<?php endif;