<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: OptionWarpHelper
		Option helper class, store option data
*/
class OptionWarpHelper extends WarpHelper {

    /*
		Variable: prefix
			Option prefix.
    */
	protected $prefix;

	/*
		Function: __construct
			Class Constructor.
	*/
	public function __construct() {
		parent::__construct();

		// set prefix
		$this->prefix = basename($this['path']->path('template:'));
	}

	/*
		Function: get
			Get a value from data

		Parameters:
			$name - String
			$default - Mixed
		Returns:
			Mixed
	*/
	public function get($name, $default = null) {
		return get_option($this->prefix.$name, $default);
	}

 	/*
		Function: set
			Set a value

		Parameters:
			$name - String
			$value - Mixed

		Returns:
			Void
	*/
	public function set($name, $value) {
		return update_option($this->prefix.$name, $value);
	}

}