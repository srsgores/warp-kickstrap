<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: EventWarpHelper
		Event helper class. Create and manage Events.
*/
class EventWarpHelper extends WarpHelper {

	/* events */
	protected $_events = array();

	/*
		Function: bind
			Bind a function to an event.

		Parameters:
			$event - Event name
			$callback - Function callback

		Returns:
			Void
	*/
	public function bind($event, $callback) {
		
		if (!isset($this->_events[$event])) {
			$this->_events[$event] = array();
		}
		
		$this->_events[$event][] = $callback;
	}

	/*
		Function: trigger
			Trigger Event

		Parameters:
			$event - Event name
			$parameters - Function arguments

		Returns:
			Void
 	*/
	public function trigger($event, $args = array()) {
		
		if (isset($this->_events[$event])) {
			foreach ($this->_events[$event] as $callback) {
				$this->_call($callback, $args);
			}
		}

	}
	
}