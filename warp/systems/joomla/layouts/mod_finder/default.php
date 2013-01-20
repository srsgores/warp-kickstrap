<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// get warp
$warp = Warp::getInstance();

?>

<form id="searchbox-<?php echo $module->id; ?>" class="searchbox" action="<?php echo JRoute::_($route); ?>" method="get" role="search">
	<input type="text" value="" name="q" placeholder="<?php echo JText::_('TPL_WARP_SEARCH'); ?>" autocomplete="off" />
	<button type="reset" value="Reset"></button>
	<?php echo modFinderHelper::getGetFields($route); ?>
</form>

<script src="<?php echo $warp['path']->url('js:search.js'); ?>"></script>
<script>
jQuery(function($) {

	$('#searchbox-<?php echo $module->id; ?> input[name=q]').search({
		'url': '<?php echo JRoute::_("index.php?option=com_finder&task=suggestions.display&format=json&tmpl=component");?>', 
		'param': 'q', 
		'msgResultsHeader': false, 
		'msgMoreResults': false, 
		'msgNoResults': false,
		'onSelect': function(selected){
			this.input.val(selected.data('choice').title);
			this.input.parent('form').submit();
		},
		'onLoadedResults': function(data){

			var results = [];

			$.each(data, function(i, val){
				results.push({'title': val, 'text': '', 'url': ''});
			});

			return {'results': results, 'count': data.length, 'error': null};
		}
	}).placeholder();

});
</script>