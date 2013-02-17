<div id="warp" class="wrap" data-warp="theme">

	<h2>Theme Options</h2>
	<p><?php echo $xml->first('description')->text(); ?></p>

	<form id="theme-options" method="post" action="">

		<?php echo $this->render('config:layouts/config'); ?>
		<?php settings_fields('template-parameters'); ?>

		<input type="hidden" name="task" value="theme-options" />
		<input type="hidden" name="warp-ajax-save" value="1" />
		<p>
			<input type="submit" value="Save changes" class="button-primary"/><span></span>
		</p>
	</form>

</div>