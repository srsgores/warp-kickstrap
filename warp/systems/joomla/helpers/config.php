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

		// get application and config from system
		$app    = $this['system']->application;
		$config = $this['system']->config;
		$data   = $config->getArrayCopy();

		// set profiles
		if ($config->has('profile_data')) {

			$itemid   = $app->input->getInt('Itemid', 0);
			$profiles = array('default');

			// add default profile
			if ($default = $config->get('profile_default')) {
				$profiles[] = $default;
			}

			// add dynamic profile ?
            if ($config->get('profile_dynamic')) {
                
				if ($var = $app->input->get($this->_profile)) {
                    $app->setUserState('_current_profile', $var);
                }
                
				if ($dynamic = $app->getUserState('_current_profile')) {
					$profiles[] = $dynamic;
				}

            }

			// add menu item profile ?
			if ($config->has('profile_map') && isset($config['profile_map'][$itemid])) {
				$profiles[] = $config['profile_map'][$itemid];
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