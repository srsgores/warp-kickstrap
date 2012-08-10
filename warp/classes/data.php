<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: WarpData
		Read/Write data in various formats.
*/
class WarpData extends ArrayObject {

	/*
		Function: __construct
			Constructor
	*/
	public function __construct($data = array()) {
		parent::__construct((empty($data) ? array() : $data));
	}

	/*
		Function: has
			Has a key ?

		Parameters:
			$name - String

		Returns:
			Boolean
	*/
	public function has($name) {
		return $this->offsetExists($name);
	}

	/*
		Function: get
			Get a value from array

		Parameters:
			$key - Array key
			$default - Default value, return if key was not found

		Returns:
			Mixed
	*/
	public function get($key, $default = null) {

		if ($this->offsetExists($key)) {
			return $this->offsetGet($key);
		}

		return $default;
	}

 	/*
		Function: set
			Set a value

		Parameters:
			$name - String
			$value - Mixed
			
		Returns:
			ArrayObject
	*/
	public function set($name, $value) {
		$this->offsetSet($name, $value);
		return $this;
	}
	
	/*
		Function: remove
			Remove a value

		Parameters:
			$name - String
			
		Returns:
			ArrayObject
	*/
	public function remove($name) {
		$this->offsetUnset($name);
		return $this;
	}

	/*
		Function: merge
			Merge with values from a other array

		Parameters:
			$array - Array to merge
			
		Returns:
			ArrayObject
	*/
	public function merge($array) {
		$this->exchangeArray(array_merge($this->getArrayCopy(), $array)); 
		return $this;
	}

	/*
		Function: __isset
			Has a key ? (via magic method)

		Parameters:
			$name - String

		Returns:
			Boolean
	*/
	public function __isset($name) {
		return $this->offsetExists($name);
	}

	/*
		Function: __get
			Get a value (via magic method)

		Parameters:
			$name - String

		Returns:
			Mixed
	*/
	public function __get($name) {
		return $this->offsetGet($name);
	}

 	/*
		Function: __set
			Set a value (via magic method)

		Parameters:
			$name - String
			$value - Mixed
			
		Returns:
			Void
	*/
	public function __set($name, $value) {
		$this->offsetSet($name, $value);
	}

 	/*
		Function: __unset
			Unset a value (via magic method)

		Parameters:
			$name - String
			
		Returns:
			Void
	*/
	public function __unset($name) {
		$this->offsetUnset($name);
	}

 	/*
		Function: __toString
			Get string (via magic method)
			
		Returns:
			String
	*/
    public function __toString() {
        return empty($this) ? '' : $this->_write($this->getArrayCopy());
    }

	/*
		Function: _read
			Read array
	*/	
	protected function _read($array = array()) {
		return $array;
	}

	/*
		Function: _write
			Serialize array
	*/
	protected function _write($data) {
		return serialize($data);
	}

}

/*
	Class: JSONWarpData
		Read/Write data in JSON format.
*/
class JSONWarpData extends WarpData {

    /*
		Variable: _assoc
			Returned object's will be converted into associative array's.
    */
	protected $_assoc = true;

	/*
		Function: __construct
			Constructor
	*/	
	public function __construct($data = array()) {
		
		// decode JSON string
		if (is_string($data)) {
			$data = $this->_read($data);
		}
		
		parent::__construct($data);
	}

	/*
		Function: _read
			Decode JSON string
	*/	
	protected function _read($json = '') {
		return json_decode($json, $this->_assoc);
	}

	/*
		Function: _write
			Encode JSON string
	*/
	protected function _write($data) {
		return json_encode($data);
	}
	
}