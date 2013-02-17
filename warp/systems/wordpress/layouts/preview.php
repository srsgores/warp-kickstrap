<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

global $wp_registered_widgets;

$default_options = array(
    'style' => '',
    'icon' => '',
    'badge' => '',
    'display' => array('*') 
);

// render default modules
switch ($position) {

	case 'logo':

	    $wp_registered_widgets['text-0'] = array(
	        'id' => 'text-0',
	        'name' => 'Text'
	    ); 

	    $this['system']->widget_options['text-0'] = $default_options;

	    echo '<!--widget-text-0-->Logo<!--widget-end-->';
		break;
		
	case 'sidebar-a':
		
	    $wp_registered_widgets['search-0'] = array(
	        'id' => 'search-0',
	        'name' => 'Search'
	    );

		$wp_registered_widgets['archives-0'] = array(
	        'id' => 'archives-0',
	        'name' => 'Archives'
	    );
	    
	    $this['system']->widget_options['search-0'] = $default_options;
		$this['system']->widget_options['archives-0'] = $default_options;
		
	    echo "<!--widget-search-0--><!--title-start-->Search<!--title-end-->";
		get_search_form();
		echo "<!--widget-end-->\n";
		echo "<!--widget-archives-0--><!--title-start-->Archive<!--title-end--><ul>";
		wp_get_archives('type=monthly');
		echo "</ul><!--widget-end-->";
		break;

}