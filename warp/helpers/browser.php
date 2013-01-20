<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: BrowserWarpHelper
		Browser helper class.
*/
class BrowserWarpHelper extends WarpHelper {
	
	protected $is_mobile;

	/*
		Function: isMobile
			Check for mobile device

		Returns:
			Boolean
	*/
	public function isMobile() {


		if (is_null($this->is_mobile)) {
			
			$this->is_mobile = false;

			if (in_array($this['useragent']->os(), array('iphone', 'ipod', 'android'))) {
				
				$this['asset']->addFile('js', 'js:mobile.js');

				if ($this->get('mobile') !== null) {
					$this->cookie('WarpMobile', (int) $this->get('mobile') == 0 ? 'no' : 'yes');
				}

				$this->is_mobile = $this->cookie('WarpMobile') != 'no';
			}
		}
		
		return $this->is_mobile;
    }

	/*
		Function: outdatedBrowser
			Check for outdated browser

		Returns:
			Boolean
	*/
	public function outdatedBrowser() {

		if ($this['useragent']->browser() == 'msie' && ((int) $this['useragent']->version()) < 8) {
			
			if ($this->get('forwardOutdatedBrowser') !== null) {
				$this->cookie('WarpForwardOutdatedBrowser', (int) $this->get('forwardOutdatedBrowser') == 0 ? 'no' : 'yes');
			}

			return $this->cookie('WarpForwardOutdatedBrowser') != 'yes';
		}
		
		return false;
    }

	/*
		Function: isIE6
			Check for IE6 browser

		Returns:
			Boolean
	*/
	public function isIE6() {

		if ($this['useragent']->browser() == 'msie' && ((int) $this['useragent']->version()) < 7) {
			
			if ($this->get('forwardOutdatedBrowser') !== null) {
				$this->cookie('WarpForwardOutdatedBrowser', (int) $this->get('forwardOutdatedBrowser') == 0 ? 'no' : 'yes');
			}

			return $this->cookie('WarpForwardOutdatedBrowser') != 'yes';
		}
		
		return false;
    }

	/*
		Function: get
			Get a value from request

		Parameters:
			$name - Name

		Returns:
			Mixed
	*/	
    public function get($name) {
		return isset($_GET[$name]) ? $_GET[$name] : null;
    }

	/*
		Function: cookie
			Get/Set a cookie

		Parameters:
			$name - Name
			$value - Value

		Returns:
			Mixed
	*/	
    public function cookie($name) {
		$args = func_get_args();

		if (count($args) == 1) {
			return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null;
		}

		setcookie($name, $args[1], time() + 60 * 60 * 24 * 30, '/'); // expire in 30 days
		$_COOKIE[$name] = $args[1];
    }

}