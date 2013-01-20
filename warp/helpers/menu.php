<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: MenuWarpHelper
		Menu helper class
*/    
class MenuWarpHelper extends WarpHelper {
	
    /*
		Variable: _renderers
			Menu renderers.
    */	
	protected $_renderers = array();
	
	/*
		Function: Constructor
			Class Constructor.
	*/
	public function __construct(){
		parent::__construct();

		// load menu class
		require_once($this['path']->path('warp:classes/menu.php'));
	}	

	/*
		Function: process
			Process menu module and apply renderers

		Parameters:
			$module - Menu module
			$renderers - Array of renderers

		Returns:
			String
	*/	
	public function process($module, $renderers){

		// init vars
		$menu = $this['dom']->create($module->content);
		
		foreach ((array) $renderers as $renderer) {
			
			if (!isset($this->_renderers[$renderer])) {
				$classname = 'WarpMenu'.$renderer;
				
				if (!class_exists($classname) && ($path = $this['path']->path('menu:'.$renderer.'.php'))) {				
					require_once($path);
				}

				if (class_exists($classname)) {
					$this->_renderers[$renderer] = new $classname();
				}
			}
			
			if (isset($this->_renderers[$renderer])) {
				$menu = $this->_renderers[$renderer]->process($module, $menu);
			}
			
			if (!$menu) {
				return $module->content;
			}
		}

		return $menu->first('ul:first')->html();
	}

}