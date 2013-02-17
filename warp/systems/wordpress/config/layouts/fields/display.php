<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

	$options  = array();
	$defaults = array(
		'*'          => 'All',
		'front_page' => 'Frontpage',
		'home'       => 'Home (Posts page)',
		'archive'    => 'Archive',
		'search'     => 'Search',
		'single'     => 'Single',
		'page'      => 'Pages',
	);

	$post_types = array_keys(get_post_types());

	foreach (array_keys(get_post_types()) as $posttype) {
		if (!in_array($posttype, array("post","page","attachment","revision","nav_menu_item"))) {
			$defaults[$posttype] = ucfirst(str_replace(array("_","-")," ",$posttype));
		}
	}

	$selected = is_array($value) ? $value : array('*');

	if (count($selected) > 1 && in_array('*', $selected)) {
	    $selected = array('*');
	}

	// set default options
	foreach ($defaults as $val => $label) {
		$attributes = in_array($val, $selected) ? array('value' => $val, 'selected' => 'selected') : array('value' => $val);
		$options[]  = sprintf('<option %s />%s</option>', $control->attributes($attributes), $label);
	}
	
	// set pages
	if ($pages = get_pages()) {
		$options[] = '<optgroup label="Pages">';

		foreach ($pages as $page) {
			$val        = 'page-'.$page->ID;
			$attributes = in_array($val, $selected) ? array('value' => $val, 'selected' => 'selected') : array('value' => $val);
			$options[]  = sprintf('<option %s />%s</option>', $control->attributes($attributes), $page->post_title);
		}

		$options[] = '</optgroup>';                  
	}

	// set categories

	foreach (array_keys(get_taxonomies()) as $tax) {
		
		if(in_array($tax, array("post_tag", "nav_menu"))) continue;

		if ($categories = get_categories(array( 'taxonomy' => $tax ))) {
			$options[] = '<optgroup label="Categories ('.ucfirst(str_replace(array("_","-")," ",$tax)).')">';

			foreach ($categories as $category) {
				$val        = 'cat-'.$category->cat_ID;
				$attributes = in_array($val, $selected) ? array('value' => $val, 'selected' => 'selected') : array('value' => $val);
				$options[]  = sprintf('<option %s />%s</option>', $control->attributes($attributes), $category->cat_name);
			}

			$options[] = '</optgroup>';                  
		}
	}

	

?>
<select name="<?php echo $name;?>[]" style="width:220px;height:120px;" multiple="multiple">
	<?php echo implode("", $options); ?>
</select>