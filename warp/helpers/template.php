<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: TemplateWarpHelper
		Template helper class, render layouts
*/
class TemplateWarpHelper extends WarpHelper {

	/* slots */
	protected $_slots = array();
    
	/*
		Function: render
			Render a layout file

		Parameters:
			$resource - Layout resource
			$args - Array of arguments

		Returns:
			String
	*/	
	public function render($resource, $args = array()) {

		// default namespace
		if (strpos($resource, ':') === false) {
			$resource = 'layouts:'.$resource;
		}

		// trigger event
		$this['event']->trigger('render.'.$resource, array(&$resource, &$args));

		// set resource and layout file
		$__resource = $resource;
		$__layout   = $this['path']->path($__resource.'.php');

		// render layout
		if ($__layout != false) {
			
			// import vars and get content
			extract($args);
			ob_start();
			include($__layout);
			return ob_get_clean();
		}
		
		trigger_error('<b>'.$__resource.'</b> not found in paths: ['.implode(', ', $this['path']->_paths['layouts']).']');
		
		return null;
	}
    
	/*
		Function: has
			Slot exists ?

		Parameters:
			$name - Slot name

		Returns:
			Boolean
	*/	
	public function has($name) {
		return isset($this->_slots[$name]);
	}

	/*
		Function: get
			Retrieve a slot

		Parameters:
			$name - Slot name
			$default - Default content

		Returns:
			Mixed
	*/	
	public function get($name, $default = false) {
		return isset($this->_slots[$name]) ? $this->_slots[$name] : $default;
	}

	/*
		Function: set
			Set a slot

		Parameters:
			$name - Slot name
			$content - Content

		Returns:
			Void
	*/	
	public function set($name, $content) {
		$this->_slots[$name] = $content;
	}

	/*
		Function: output
			Outputs slot content

		Parameters:
			$name - Slot name
			$default - Default content

		Returns:
			Boolean
	*/	
	public function output($name, $default = false) {

		if (!isset($this->_slots[$name])) {
		
			if (false !== $default) {
				echo $default;
				return true;
			}

			return false;
		}

		echo $this->_slots[$name];
		return true;
	}

}