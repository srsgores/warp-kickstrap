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

<div class="items">

	<?php foreach ($this->results as $result) : ?>
	<article class="item">
		
		<header>
		
			<?php if ( $result->href ) : ?>
			<h1 class="title"><a href="<?php echo JRoute::_($result->href); ?>" <?php if ($result->browsernav == 1 ) echo 'target="_blank"'; ?>><?php  echo $this->escape($result->title); ?></a></h1>
			<?php else : ?>
			<h1 class="title"><?php echo $this->escape($result->title); ?></h1>
			<?php endif; ?>
	
			<?php if ($result->section && $this->params->get('show_date')) : ?>
			<p class="meta">
				<?php if ($this->params->get('show_date')) echo JText::sprintf('JGLOBAL_CREATED_DATE_ON', $result->created).'. '; ?>
				<?php if ($result->section) echo JText::_('TPL_WARP_POSTED_IN').' '.$this->escape($result->section); ?>
			</p>
			<?php endif; ?>
			
		</header>
		
		<div class="content clearfix"><?php echo $result->text; ?></div>

	</article>
	<?php endforeach; ?>

</div>

<?php echo $this->pagination->getPagesLinks();