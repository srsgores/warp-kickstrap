<?php
/*------------------------------------------------------------------------------------------------------------------------
    Author: Sean Goresht
    www: http://seangoresht.com/
    github: https://github.com/srsgores

    twitter: http://twitter.com/S.Goresht

     warp-kickstrap Joomla Template
     Licensed under the GNU Public License

	=============================================================================
	Filename:  warp.php
	=============================================================================
	 This file is the main warp class, which sets attributes for the class (variables accessible in all of warp).
	 This file does the following:
	 	--Set branding string
	 	--Register all file paths (ex. CSS/JS/helper file paths, etc.)
	 	--Declare functions to add/remove helpers

--------------------------------------------------------------------------------------------------------------------- */


// init vars
$path = dirname(__FILE__);

// load classes
require_once($path.'/classes/helper.php');
require_once($path.'/helpers/path.php');

class Warp implements ArrayAccess {

	/* branding */
	protected $_branding = 'Powered by <a href="https://github.com/srsgores/warp-kickstrap">Warp KickStrap Framework</a>'; //insert the string you want to be displayed at the bottom.  NOTE: you can turn this feature off in the template options!

    /* helpers */
	protected $_helpers = array();

    /* instance */
	protected static $_instance;
    
	/*
		Function: getInstance
			Retrieve warp instance

		Returns:
			Template
	*/
	public static function getInstance() {      

        if (!isset(self::$_instance)) {

            // init vars
            $path = dirname(__FILE__);
            self::$_instance = new Warp();

            // add default helper
            self::$_instance->addHelper(new PathWarpHelper());

            // set default paths
            self::$_instance['path']->register($path, 'warp');
            self::$_instance['path']->register($path.'/classes', 'classes');
            self::$_instance['path']->register($path.'/helpers', 'helpers');
            self::$_instance['path']->register($path.'/libraries', 'lib');
            self::$_instance['path']->register($path.'/css', 'css');
            self::$_instance['path']->register($path.'/js', 'js');
            self::$_instance['path']->register($path.'/layouts', 'layouts');
            self::$_instance['path']->register($path.'/menus', 'menu');
            self::$_instance['path']->register(dirname($path), 'template');
        }

        return self::$_instance;
    }

    /*
		Function: getBranding
			Retrieve branding

		Returns:
			String
	*/
	public function getBranding() {
		return $this->_branding;
	}
    
    /*
		Function: getHelper
			Retrieve a helper

		Parameters:
			$name - Helper name
	*/
	public function getHelper($name) {

		// try to load helper, if not found
		if (!isset($this->_helpers[$name])) {
		    $this->loadHelper($name);
		}

		// get helper
		if (isset($this->_helpers[$name])) {
			return $this->_helpers[$name];
		}
		
		return null;
	}

	/*
		Function: addHelper
			Adds a helper

		Parameters:
			$helper - Helper object
			$alias - Helper alias (optional)
	*/
	public function addHelper($helper, $alias = null) {

		// add to helpers
		$name = $helper->getName();
		$this->_helpers[$name] = $helper;

		// add alias
		if (!empty($alias)) {
			$this->_helpers[$alias] = $helper;
		}
	}

	/*
		Function: loadHelper
			Load helper from path

		Parameters:
			$helpers - Helper names
			$prefix - Helper class suffix
	*/
	public function loadHelper($helpers, $suffix = 'WarpHelper') {
		$helpers = (array) $helpers;
		
		foreach ($helpers as $name) {
			$class = $name.$suffix;

			// autoload helper class
			if (!class_exists($class) && ($file = $this['path']->path('helpers:'.$name.'.php'))) {
			    require_once($file);
			}

			// add helper, if not exists
			if (!isset($this->_helpers[$name])) {
				$this->addHelper(new $class());
			}
		}
	}
	
	/* ArrayAccess interface implementation */

	public function offsetGet($name)	{
		return $this->getHelper($name);
	}

	public function offsetSet($name, $helper) {
		$this->_helpers[$name] = $helper;
	}

	public function offsetUnset($name) {
		unset($this->_helpers[$name]);
	}

	public function offsetExists($name) {
		return !empty($this[$name]);
	}

}