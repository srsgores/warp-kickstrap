<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

?>

<ul class="blank">
<?php foreach(range('a', 'e') as $char) : ?>

	<?php
		$link = $this->contact->params->get('link'.$char);
		$label = $this->contact->params->get('link'.$char.'_name');

		if( ! $link) continue;

		// Add 'http://' if not present
		$link = (0 === strpos($link, 'http')) ? $link : 'http://'.$link;

		// If no label is present, take the link
		$label = ($label) ? $label : $link;
		
	?>
	
	<li><a href="<?php echo $link; ?>"><?php echo $label; ?></a></li>
	
<?php endforeach; ?>
</ul>
