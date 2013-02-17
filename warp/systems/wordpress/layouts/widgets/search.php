<form id="<?php echo $module->id; ?>" class="searchbox" action="<?php echo home_url( '/' ); ?>" method="get" role="search">
	<input type="search" value="" name="s" placeholder="<?php _e('search...', 'warp'); ?>" />
	<button type="reset" value="Reset" class="visuallyhidden">Reset <i class = "icon-refresh"></i></button>
</form>

<script src="<?php echo $this['path']->url('js:search.js'); ?>"></script>
<script>
jQuery(function($) {
	$('#<?php echo $module->id; ?> input[name=s]').search({'url': '<?php echo site_url('wp-admin'); ?>/admin-ajax.php?action=warp_search', 'param': 's', 'msgResultsHeader': '<?php _e("Search Results", "warp"); ?>', 'msgMoreResults': '<?php _e("More Results", "warp"); ?>', 'msgNoResults': '<?php _e("No results found", "warp"); ?>'}).placeholder();
});
</script>