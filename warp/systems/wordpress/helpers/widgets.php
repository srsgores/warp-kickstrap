<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: WidgetsWarpHelper
		Wordpress widget helper class, provides simplyfied access to wordpress widgets
*/
class WidgetsWarpHelper extends WarpHelper {

	/* widgets */
	public $widgets;
    
	/* options */
	public $options;

	/*
		Function: Constructor
			Class Constructor.
	*/
	public function __construct() {
		parent::__construct();

		// set options
        $this->options = get_option('warp_widget_options', array());
	}

	/*
		Function: get
			Retrieve a widget by id

		Parameters:
			$id - Widget ID

		Returns:
			Object
	*/
	public function get($id) {
		global $wp_registered_widgets;
		
	    $widget = null;

		if (isset($wp_registered_widgets[$id]) && ($data = $wp_registered_widgets[$id])) {
			$widget = new stdClass();
			
			foreach (array('id', 'name', 'classname', 'description') as $var) {
				$widget->$var = isset($data[$var]) ? $data[$var] : null;
			}

			if (isset($data['callback']) && is_array($data['callback']) && ($object = current($data['callback']))) {
				if (is_a($object, 'WP_Widget')) {

					$widget->type = $object->id_base;

					if (isset($data['params'][0]['number'])) {

						$number = $data['params'][0]['number'];
						$params = get_option($object->option_name);

						if (false === $params && isset($object->alt_option_name)) {
							$params = get_option($object->alt_option_name);
						}

						if (isset($params[$number])) {
							$widget->params = $params[$number];
						}
					}
				}
			} else if ($id == 'nav_menu-0') {
			    $widget->type = 'nav_menu';
			}
			
			if (empty($widget->name)) {
				$widget->name = ucfirst($widget->type);
			}

			if (empty($widget->params)) {
				$widget->params = array();
			}

			$widget->options = isset($this->options[$id]) ? $this->options[$id] : array();
			$widget->display = $this->_display($widget);
		}
		
		return $widget;
	}

	/*
		Function: getWidgets
			Retrieve widgets

		Parameters:
			$position - Position

		Returns:
			Array
	*/
	public function getWidgets($position = null) {

		if (empty($this->widgets)) {
		    foreach (wp_get_sidebars_widgets() as $pos => $ids) {

				if (!is_array($ids) || empty($ids)) {
					continue;
				}

				$this->widgets[$pos] = array();

				foreach ($ids as $id) {
					$this->widgets[$pos][$id] = $this->get($id);
				}
			}
		}

		if (!is_null($position)) {
			return isset($this->widgets[$position]) ? $this->widgets[$position] : array();
		}
		
		return $this->widgets;
	}

	/*
		Function: _display
			Checks if a widget should be displayed

		Returns:
			Boolean
	*/
	protected function _display($widget) {

	    if (!isset($widget->options['display']) || in_array('*', $widget->options['display'])) {
			return true;		
		}
		
		$query = $this['system']->getQuery();

		foreach ($query as $q) {
		      
		   if (in_array($q, $widget->options['display'])) {
				
				switch ($q) {
			    	case "page":
			    		
			    		if (is_home()) {
			    			return in_array('home', $widget->options['display']);
			    		}

			    		if (is_front_page()) {
			    			return in_array('front_page', $widget->options['display']);
			    		}

			    	default:
						return true;
			    }
			}
		}

		return false;
	}

}