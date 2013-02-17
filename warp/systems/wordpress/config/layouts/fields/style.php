<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

printf('<select %s>', $control->attributes(array('class' => 'widget-style', 'name' => $name)));

foreach ($node->children('option') as $option) {

	// set attributes
	$attributes = array('value' => $option->attr('value'));
	
	// is checked ?
	if ($option->attr('value') == $value) {
		$attributes = array_merge($attributes, array('selected' => 'selected'));
	}

	printf('<option %s>%s</option>', $control->attributes($attributes), $option->attr('name'));
	
	// has colors ?
	if ($colors = $option->children('color')) {
		$selects[] = sprintf('<select class="widget-color" data-style="%s">', $option->attr('value'));

		foreach ($colors as $color) {

			// set attributes
			$attributes = array('value' => $color->attr('value'));

			// is checked ?
			if (isset($widget->options['color']) && $color->attr('value') == $widget->options['color']) {
				$attributes = array_merge($attributes, array('selected' => 'selected'));
			}

			$selects[] = sprintf('<option %s>%s</option>', $control->attributes($attributes), $color->attr('name'));
		}

		$selects[] = '</select>';
	}
	
}

printf('</select>');

if (isset($selects)) {
	echo implode("", $selects);
}
