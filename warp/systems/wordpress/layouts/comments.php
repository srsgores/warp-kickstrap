<?php if (comments_open()) : ?>

	<section id="comments">

		<?php if (get_comments_number() > 0) : ?>
		<h3 class="comments-meta"><?php comments_open() ? printf(__('Comments (%s)', 'warp'), get_comments_number()) : _e('Comments are closed', 'warp'); ?></h3>
		<?php endif; ?>

		<?php if (have_comments()) : ?>

			<?php
				
				$classes = array("level1");
			
				if (get_option('comment_registration') && !is_user_logged_in()) {
					$classes[] = "no-response";
				}
				
				if (get_option('thread_comments')) {
					$classes[] = "nested";
				}
			
			?>

			<ul class="<?php echo implode(" ", $classes);?>">
			<?php 
				
				// single comment
				function mytheme_comment($comment, $args, $depth) {
					global $user_identity;
					
					$GLOBALS['comment'] = $comment;
					$warp = Warp::getInstance();
					
					$_GET['replytocom'] = get_comment_ID();
					?>
					<li>
						<article id="comment-<?php comment_ID(); ?>" class="comment <?php echo ($comment->user_id > 0) ? 'comment-byadmin' : '';?>">
					 
							<header class="comment-head">
							
								<?php echo get_avatar($comment, $size='50', get_bloginfo('template_url').'/images/comments_avatar.png'); ?>
								
								<h4 class="author"><?php echo get_comment_author_link(); ?></h4>
							
								<p class="meta">
									<time datetime="<?php echo get_comment_date('Y-m-d'); ?>" pubdate><?php printf(__('%1$s at %2$s', 'warp'), get_comment_date(), get_comment_time()) ?></time>
									| <a class="permalink" href="<?php echo htmlspecialchars(get_comment_link($comment->comment_ID)) ?>">#</a>
									<?php edit_comment_link(__('Edit'),'| ','') ?>
								</p>
							
							</header>
							
							<div class="comment-body">
							
								<div class="content"><?php comment_text(); ?></div>
								
								<?php if (comments_open()) : ?>
								<p class="reply"><a href="#" rel="<?php comment_ID(); ?>"><?php echo __('Reply', 'warp'); ?></a></p>
								<?php endif; ?>
									
								<?php if ($comment->comment_approved == '0') : ?>
								<p class="moderation"><?php _e('Your comment is awaiting moderation.', 'warp'); ?></p>
								<?php endif; ?>
								
							</div>
							
						</article>
					<?php
					unset($_GET['replytocom']);
					
					// </li> is rendered by system
				}

				wp_list_comments('type=all&callback=mytheme_comment');
			?>
			</ul>

		<?php echo $this->render("_pagination", array("type"=>"comments")); ?>

	<?php endif; ?>

		<div id="respond">
		
			<h3><?php comment_form_title(__('Leave a comment', 'warp')); ?></h3>
		
			<?php if (get_option('comment_registration') && !is_user_logged_in()) : ?>
			<p class="user"><?php printf(__('You must be <a href="%s">logged in</a> to post a comment.', 'warp'), wp_login_url(get_permalink())); ?></p>
			<?php else : ?>
			
				<form class="short style" action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post">
			
					<?php if (is_user_logged_in()) : ?>

						<?php global $user_identity; ?>
						
						<p class="user"><?php printf(__('Logged in as <a href="%s">%s</a>.', 'warp'), get_option('siteurl').'/wp-admin/profile.php', $user_identity); ?> <a href="<?php echo wp_logout_url(get_permalink()); ?>" title="<?php _e('Log out of this account', 'warp'); ?>"><?php _e('Log out &raquo;', 'warp'); ?></a></p>
						
					<?php else : ?>
						
						<?php $req = get_option('require_name_email');?>
						
						<div class="author <?php if ($req) echo "required"; ?>">
							<input type="text" name="author" placeholder="<?php _e('Name', 'warp'); ?> <?php if ($req) echo "*"; ?>" value="<?php echo esc_attr($comment_author); ?>" size="22" <?php if ($req) echo "aria-required='true'"; ?> />
						</div>
						
						<div class="email <?php if ($req) echo "required"; ?>">
							<input type="text" name="email" placeholder="<?php _e('E-mail', 'warp'); ?> <?php if ($req) echo "*"; ?>" value="<?php echo esc_attr($comment_author_email); ?>" size="22" <?php if ($req) echo "aria-required='true'"; ?> />
						</div>
						
						<div class="url">
							<input type="text" name="url" placeholder="<?php _e('Website', 'warp'); ?>" value="<?php echo esc_attr($comment_author_url); ?>" size="22" />
						</div>
		
					<?php endif; ?>
					
					<div class="content">
						<textarea name="comment" id="comment" cols="58" rows="10" tabindex="4"></textarea>
					</div>
					
					<div class="actions">
						<input name="submit" type="submit" id="submit" tabindex="5" value="<?php _e('Submit Comment', 'warp'); ?>" />
						<?php comment_id_fields(); ?>
					</div>
					<?php do_action('comment_form', $post->ID); ?>

				</form>

			<?php endif; ?>
			
		</div>

	</section>
	
	
	<script type="text/javascript">
			
		jQuery(function($) {
			
			var respond = $("#respond");
			
			$("p.reply > a").bind("click", function(){
				
				var id = $(this).attr('rel');
				
				respond.find(".comment-cancelReply:first").remove();
				
				var cancel = $('<a><?php echo __("Cancel");?></a>').addClass('comment-cancelReply').attr('href', "#respond").bind("click", function(){
					respond.find(".comment-cancelReply:first").remove();
					respond.appendTo($('#comments')).find("[name=comment_parent]").val(0);
					return false;
				}).appendTo(respond.find(".actions:first"));
				
				respond.find("[name=comment_parent]").val(id);
				respond.appendTo($("#comment-"+id));
				
				return false;
				
			})
			
			$('form.short input[placeholder]').placeholder();
		});
			
	</script>
	

<?php endif;