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

<div id="system">

	<?php if ($this->params->get('show_page_heading')) : ?>
	<h1 class="page-title"><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	<?php endif; ?>

	<h1 class="title"><a href="<?php echo $this->newsfeed->channel['link']; ?>" target="_blank"><?php echo str_replace('&apos;', "'", $this->newsfeed->channel['title']); ?></a></h1>

	<?php if ($this->params->get('show_feed_description') || (isset($this->newsfeed->image['url']) && isset($this->newsfeed->image['title']) && $this->params->get('show_feed_image'))) :?>
	<div class="description">
		<?php if (isset($this->newsfeed->image['url']) && isset($this->newsfeed->image['title']) && $this->params->get('show_feed_image')) : ?>
			<img src="<?php echo $this->newsfeed->image['url']; ?>" alt="<?php echo $this->newsfeed->image['title']; ?>" class="size-auto align-right" />
		<?php endif; ?>
		<?php if ($this->params->get('show_feed_description')) echo str_replace('&apos;', "'", $this->newsfeed->channel['description']); ?>
	</div>
	<?php endif; ?>

	<ul class="space">
		<?php foreach ($this->newsfeed->items as $item) :  ?>
		<li>
		
			<?php if (!is_null($item->get_link())) : ?>
			<a href="<?php echo $item->get_link(); ?>" target="_blank"><?php echo $item->get_title(); ?></a>
			<?php endif; ?>
			
			<?php if ($this->params->get('show_item_description') && $item->get_description()) : ?>
				<?php
					$text = $item->get_description();
					if ($this->params->get('show_feed_image', 0) == 0) {
						$text = JFilterOutput::stripImages($text);
					}
					$text = JHtml::_('string.truncate', $text, $this->params->get('feed_character_count'));
				?>
				<div><?php echo str_replace('&apos;', "'", $text);	?></div>
			<?php endif; ?>
			
		</li>
		<?php endforeach; ?>
	</ul>

</div>
