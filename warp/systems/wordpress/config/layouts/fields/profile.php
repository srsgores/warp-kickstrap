<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// profile options
$select   = array();
$selected = array();
$profiles = $config->get('profile_data', array('default' => array()));

foreach ($profiles as $profile => $values) {
	$attr = array('value' => $profile);

	// is checked ?
	if ((string) $config->get('profile_default') == $profile) {
		$attr = array_merge($attr, array('selected' => 'selected'));
	}

	$select[]   = sprintf('<option %s >%s</option>', $control->attributes($attr, array('selected')), $profile);
	$selected[] = sprintf('<option %s >%s</option>', $control->attributes($attr), $profile);
}

// pages & section options


$options  = array();
$defaults = array(
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

<select class="profile" name="<?php echo $name; ?>"><?php echo implode("\n", $selected); ?></select>			

<div id="profile">

	<select><?php echo implode("\n", $select); ?></select>			

	<a class="add" href="#">Add</a>
	<a class="rename" href="#">Rename</a>
	<a class="remove" href="#">Remove</a>
	<a class="assign" href="#">Assign Pages</a>

	<div class="items">
		<select name="items" style="width:220px;height:120px;" multiple="multiple">
			<?php echo implode("", $options); ?>
		</select>
	</div>

	<?php foreach ($config->get('profile_map', array()) as $page => $profile): ?>
		<?php printf('<input %s />', $control->attributes(array('type' => 'hidden', 'name' => "profile_map[$page]", 'value' => $profile))); ?>
	<?php endforeach; ?>

</div>