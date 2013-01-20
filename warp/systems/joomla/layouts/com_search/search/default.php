<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// get application
$app = JFactory::getApplication();

if ($app->input->getWord('type', '') == 'json' && $app->input->getWord('tmpl', '') == 'raw') :

	// set defaults
	$res_limit  = 6;		
	$char_limit = 100;		

	// get search
	$search = $app->input->getString('searchword', '');
	$search = JString::strtolower($search);

	// search results
	$res_items = array();
	if (!$this->error && count($this->results) > 0) {
		foreach ($this->results as $result) {
			
			// strip text
			$text = str_replace(array("\r\n", "\n", "\r", "\t"), "", $result->text);
			$text = html_entity_decode($text, ENT_COMPAT, 'UTF-8');
			$text = preg_replace('/{.+?}/', '', $text);
			$text = substr(trim(strip_tags($text)), 0, $char_limit);
			
			// create item
			$item          = array();
			$item['title'] = $result->title;
			$item['text']  = substr_replace($text, '...', strrpos($text, ' '));
			$item['url']   = JRoute::_($result->href, false);
			$res_items[]   = $item;
		}
	}

	echo json_encode(array('results' => array_slice($res_items, 0, $res_limit), 'count'=> count($this->results), 'error' => $this->error));

else :

?>

<div id="system">

	<?php if ($this->params->get('show_page_heading')) : ?>
	<h1 class="title">
		<?php if ($this->escape($this->params->get('page_heading'))) :?>
			<?php echo $this->escape($this->params->get('page_heading')); ?>
		<?php else : ?>
			<?php echo $this->escape($this->params->get('page_title')); ?>
		<?php endif; ?>
	</h1>
	<?php endif; ?>

	<?php echo $this->loadTemplate('form'); ?>
		
	<?php
		if ($this->error) {
			echo '<p>'.$this->escape($this->error).'</p>';
		} elseif (count($this->results)) {
			echo $this->loadTemplate('results');
		}
	?>

</div>

<?php endif;