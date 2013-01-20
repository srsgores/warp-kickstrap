<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: WarpHelper
		Helper base class
*/
class WarpHelper implements ArrayAccess {

	/* warp */
	protected $warp;

	/* name */
	public $name;

	/*
		Function: Constructor
			Class Constructor.
	*/
	public function __construct() {

		// set default name
		$this->warp = Warp::getInstance();
		$this->name = strtolower(basename(get_class($this), 'WarpHelper'));

	}

	/*
		Function: getName
			Get helper name

		Returns:
			String
	*/	
	public function getName() {
		return $this->name;
	}

	/*
		Function: _call
			Execute function call

		Returns:
			Mixed
	*/	
	protected function _call($function, $args = array()) {

		if (is_array($function)) {

			list($object, $method) = $function;

			if (is_object($object)) {
				switch (count($args)) { 
					case 0 :
						return $object->$method();
						break;
					case 1 : 
						return $object->$method($args[0]); 
						break; 
					case 2: 
						return $object->$method($args[0], $args[1]); 
						break; 
					case 3: 
						return $object->$method($args[0], $args[1], $args[2]); 
						break; 
					case 4: 
						return $object->$method($args[0], $args[1], $args[2], $args[3]); 
						break; 
				} 
			}

		}

		return call_user_func_array($function, $args);                               
	}
	
	/* ArrayAccess interface implementation */

	public function offsetGet($name) {
		return $this->warp[$name];
	}

	public function offsetSet($name, $helper) {
		$this->warp[$name] = $helper;
	}

	public function offsetUnset($name) {
		unset($this->warp[$name]);
	}

	public function offsetExists($name) {
		return !empty($this->warp[$name]);
	}

}