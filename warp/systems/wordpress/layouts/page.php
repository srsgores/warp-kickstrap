<div id="system">

	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
		
		<article class="item">
		
			<?php if (has_post_thumbnail()) : ?>
				<?php
				$width = get_option('thumbnail_size_w'); //get the width of the thumbnail setting
				$height = get_option('thumbnail_size_h'); //get the height of the thumbnail setting
				?>
				<?php the_post_thumbnail(array($width, $height), array('class' => 'size-auto')); ?>
			<?php endif; ?>
		
			<header>
		
				<h1 class="title"><?php the_title(); ?></h1>
				
			</header>
			
			<div class="content clearfix"><?php the_content(''); ?></div>

			<?php edit_post_link(__('Edit this post.', 'warp'), '<p class="edit">','</p>'); ?>
	
		</article>
		
		<?php endwhile; ?>
	<?php endif; ?>
	
	<?php comments_template(); ?>

</div>