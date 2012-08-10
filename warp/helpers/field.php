<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: FieldWarpHelper
		Field helper class. Renders HTML input fields.
*/
class FieldWarpHelper extends WarpHelper {

	/*
		Function: render
			Render a field like text, select or radio button

		Returns:
			String
	*/
	public function render($type, $name, $value, $node, $args = array()) {
		
		// set vars
		$args['control'] = $this;
		$args['name']    = $name;
		$args['value']   = $value;
		$args['node']    = $node;
		
		return $this['template']->render('config:layouts/fields/'.$type, $args);
	}

	/*
		Function: attributes
			Create html attribute string from array

		Returns:
			String
	*/
	public function attributes($attributes, $ignore = array()) {

		$attribs = array();
		$ignore  = (array) $ignore;
		
		foreach ($attributes as $name => $value) {
			if (in_array($name, $ignore)) continue;

			$attribs[] = sprintf('%s="%s"', $name, htmlspecialchars($value));
		}
		
		return implode(' ', $attribs);
	}

}