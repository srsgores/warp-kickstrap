<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

global $comments, $comment;

// init vars
$number   = (int) max(isset($module->params['number']) ? $module->params['number'] : 5, 1);
$comments = get_comments(array('number' => $number, 'status' => 'approve'));

if ($comments) : ?>
<section class="comments-list">

	<?php foreach ((array) $comments as $comment) : ?>
	<article>
		
		<?php echo get_avatar($comment, $size = '35', get_bloginfo('template_url').'/images/comments_avatar.png'); ?>
		
		<h4 class="author"><?php echo get_comment_author_link(); ?></h4>

		<p class="meta">
			<time datetime="<?php echo get_comment_date('Y-m-d'); ?>" pubdate><?php comment_date(); ?></time>
			| <a class="permalink" href="<?php echo htmlspecialchars(get_comment_link($comment->comment_ID)) ?>">#</a>
		</p>
		
		<div class="content"><?php comment_text(); ?></div>
	
	</article>
	<?php endforeach; ?>
	
</section>
<?php endif;