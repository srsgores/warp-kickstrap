<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: ConfigWarpHelper
		Config helper class, configuration container
*/    
class ConfigWarpHelper extends WarpHelper {

    /*
		Variable: _data
			Config data.
    */
	protected $_data;

    /* dynamic profile get variable */
	protected $_profile = 'profile';

	/*
		Function: __construct
			Class Constructor.
	*/
	public function __construct() {
		parent::__construct();

		// get config from system
		$config = $this['system']->config;
		$data   = $config->getArrayCopy();

		// set profiles
		if ($config->has('profile_data')) {

			$profiles = array('default');

			// add default profile
			if ($default = $config->get('profile_default')) {
				$profiles[] = $default;
			}

			// add dynamic profile ?
            if ($config->get('profile_dynamic')) {
                
				if (!session_id()) session_start();

				if (isset($_GET[$this->_profile])) {
					$_SESSION['_current_preset'] = preg_replace('/[^A-Z0-9-]/i', '', $_GET[$this->_profile]);
				}

				if (isset($_SESSION['_current_preset'])) {
					$profiles[] = $_SESSION['_current_preset'];
				}

            }

			// get wordpress query
			$query = $this['system']->getQuery();

			// add query profile ?
			if ($config->has('profile_map')) {
				foreach (array_reverse($query) as $q) {
					if (isset($config['profile_map'][$q])) {
						$profiles[] = $config['profile_map'][$q];
						break;
					}
				}
			}

			// merge profile data
			foreach (array_unique($profiles) as $profile) {
				if (isset($config['profile_data'][$profile])) {
					$data = array_merge($data, $config['profile_data'][$profile]);
				}
			}

		}

		// set data
		$this->_data = $this['data']->create($data);		
	}

 	/*
		Function: __call
			Pass call to data object

		Parameters:
			$method - String
			$args - Array
			
		Returns:
			Mixed
	*/
    public function __call($method, $args) {
		return $this->_call(array($this->_data, $method), $args);
    }

}